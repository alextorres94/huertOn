//capturar imagenes subidas
function previewImage(file) {
    var galleryId = "gallery";
    var gallery = document.getElementById(galleryId);
    var imageType = /image.*/;

    if (!file.type.match(imageType)) {
        throw "El archivo debe ser una imagen";
    }
    if(file.size>10240){
      throw "El archivo supera el tamaño maximo";
    }
    //añado al fichero una nueva propiedad descripción
    file.des="";
    file.id=id++;

    var lector = new FileReader();
    lector.onloadend = function() {
      file.src = lector.result;
      imagenes.push(file);
      console.log(imagenes);
      //createImageElement(file,id);
      checkNumImage();
      createImageElement(file);
    }
    lector.readAsDataURL(file);


}

function sendAllImages() {
  $.ajax({
    type: "POST",
    contentType: 'application/json',
    url:"/orchard/images/upload",
    data: JSON.stringify(imagenes),
     success: function(step){console.log(step);return step},
     error: function(){alert("error al añadir imagenes. ajax sendAllImages")}
   });
}
function sendImage(file){
  $.ajax({
    type: "POST",
    url:"/orchard/images/upload",
    data: {
      'name':file.name,
      'src':file.src,
      'description':file.des
    },
     success: function(id){alert("imagen "+file.name+" enviada con id "+id);},
     error: function(){alert("Error al subir la imagen "+file.name);}
   });
}

function createImageElement(file){
  var thumb = document.createElement("div");
  thumb.classList.add('thumbnail'); // Add the class thumbnail to the created div

  var img = document.createElement("img");
  var del = document.createElement("span");
  var contador = document.createElement("span");
  var des = document.createElement("textarea");
  //var name = document.createElement("p");

  //name.append(document.createTextNode(file.name));
  //name.setAttribute("title",file.name);

  contador.append('100');

  des.setAttribute("type","text");
  des.setAttribute("placeholder","Describe la imagene");
  des.classList.add("descripcion");
  des.setAttribute("maxlength","100");
  des.setAttribute("onkeyup","textAreaAdjust(this)");

  img.file = file;

  del.setAttribute("class","fa fa-times-circle topRight deleteImage");
  del.setAttribute("id",file.id);

  //funcion que se ejecuta al eliminar una imagen de la interfaz
  del.onclick=function() {
    //alert("eliminando imagen");
    imagenes=removeItemWithId(imagenes,$(this).attr('id'));
    $(this).parent().remove();

    console.log(imagenes);
    checkNumImage();
  };

  thumb.appendChild(del);
  thumb.appendChild(img);
  //thumb.appendChild(name);
  thumb.appendChild(contador);
  thumb.appendChild(des);
//añade la imagen al principio del div, antes que todas las imagenes
  gallery.prepend(thumb);

  // Using FileReader to display the image content
  var reader = new FileReader();
  reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
  reader.readAsDataURL(file);
}


//Drag&drop cargar imagenes
function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  console.log("dragevent");
  ev.dataTransfer.setData("text", 'gallery');
}

function drop(ev) {
  console.log('dropevent');
  ev.preventDefault();
  $("#loading-images").fadeIn("slow");
  var data = ev.dataTransfer.getData("text");
  var f = ev.dataTransfer.files;
  //var file;
  //console.log(ev.dataTransfer.files);

  if (f.length+imagenes.length<maxfiles+1) {
    //[].forEach.call(files, readAndPreview);
    [].forEach.call(f, previewImage);
  }else{
    alert("solo se permite un maximo de "+maxfiles+" imagenes");
  }
  $("#loading-images").fadeOut("slow");
}
//resize textarea
function textAreaAdjust(o) {
  //caracteres restantes
  var numchar=$(o).val().length;
  $(o).prev().text(100-numchar);
  o.style.height = "1px";
  o.style.height = (0+o.scrollHeight)+"px";
}
function checkNumImage() {
  if(imagenes.length>0){
    $("#btn-siguiente").text("Siguiente");
  }else {
    $("#btn-siguiente").text("Ahora no");
  }
  if (imagenes.length<10) {
    $(".more").removeAttr("hidden");
  }else{
    $(".more").attr("hidden","hidden");
  }
}

function removeItemWithId(array,id) {
  return array.filter(function( obj ) {
    return obj.id !== parseInt(id);
  });
}

function mostrarEditor(element) {
  console.log("dentro del metodo");
  console.log($(element).parent().last());
  $(element).parent().children().last().removeAttr("hidden");
}

function deleteUpload(element) {
  deleteFromServer($(element).attr("id"),element);
}
function deleteFromServer(id,element) {
  $.ajax({
    type: "POST",
    url:"/orchard/upload/images/delete/"+id,
     success: function(){
       $(element).parent().remove();console.log($(element).attr("id"));
       if ($('.upload').children().length<10) {
         $("#more").removeAttr("hidden");
       }
     },
     error: function(){console.log("imagen no borrada");}
   });
}

function sendNewDescription(description,id) {
  $.ajax({
    type: "POST",
    url:"/orchard/upload/images/modify/"+id,
    data:{description},
     success: function(){alert("descripción modificada")},
     error: function(){console.log("descripcion NO modificada");}
   });
}

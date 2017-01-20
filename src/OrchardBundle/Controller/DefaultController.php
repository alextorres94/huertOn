<?php

namespace OrchardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OrchardBundle\Entity\Orchard;
use OrchardBundle\Entity\OrchardType;
use OrchardBundle\Entity\Image;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class DefaultController extends Controller
{
  //Método utilizado para redirigir a un paso en concreto de la creación de un huerto
  //Recibe el id del huerto que se está creando mediante el método GET (el número del paso al que se redirigirá se gestiona según este id)
  public function indexAction(Request $request)
  {
    // //Recogemos el usuario para mostrar el mensaje de bienvenida
    // //Id de usuario a piñón hasta crear login y registro
    // $id_user = 1;
    // $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($id_user);
    $userName = 'David';
    //
    // if($user != null) {
    //   $userName = $user->getName();
    // }

    $step_orchard = 0;

    //Comprovar si existe la cookie del id del huerto
    //Si existe significa que se està editando el huerto y sinó se está creando uno nuevo
    $cookies = $request->cookies;

    $id_orchard = null;

    if ($cookies->has('ID_ORCHARD')) {
      $id_orchard = $cookies->get('ID_ORCHARD');
    }

    //Si existe un id de huerto en la cookie recogemos el paso por el que va
    if($id_orchard != null){
      $step_orchard = $this->getDoctrine()->getRepository('OrchardBundle:Orchard')->findOneById($id_orchard)->getStep();
      //Devolvemos la plantilla de pasos marcando los pasos que ya están completados con un icono (falta incluir textos en gris), y pasándole el id del usuario para mostrar el mensaje de bienvenida.
      return $this->render('OrchardBundle:Default:steps.html.twig', array('userName' => $userName, 'idOrchard'=> $id_orchard, 'step' => $step_orchard));
    }else {
      //Si no existe id de huerto pasamos el paso 0 (si el paso es 0 se ejecuta la ruta para acceder al paso por defecto, es decir, al 11) y empezamos a crear el huerto
      return $this->render('OrchardBundle:Default:steps.html.twig', array('userName' => $userName, 'step' => $step_orchard));
    }
  }

  //Método utilizado para redirigir a un paso en concreto de la creación un huerto
  //Recibe el número del paso al que se quiere acceder (por defecto al primero) mediante el método GET
  public function createAction(Request $request, $step)
  {
    //Comprovar si existe la cookie del id del huerto
    //Si existe significa que se està editando el huerto y sinó se está creando uno nuevo
    $cookies = $request->cookies;

    $orchard = new Orchard();

    $step_orchard = 11;

    if ($cookies->has('ID_ORCHARD')) {
      //Si se está editando (si existe la cookie) obtenemos el paso por el que se ha quedado y le cambiamos el tipo
      $repository = $this->getDoctrine()->getRepository('OrchardBundle:Orchard');

      $orchard = $repository->findOneById($cookies->get('ID_ORCHARD'));

      $step_orchard = $orchard->getStep();

      settype($step_orchard, 'integer');
    }

    //Step pasado por parámetro que se ha definido en el href del botón en steps.html.twig o en la ruta
    switch ($step) {
      case 11:
      if($step < $step_orchard) {
        return $this->render('OrchardBundle:Default:step11.html.twig', array('orchard' => $orchard));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard));
        break;
      }
      case 12:
      if($step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step12.html.twig', array('orchard' => $orchard));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard));
        break;
      }
      case 13:
      if( $step <= $step_orchard) {
        //recojo todas las imagenes relacionadas con el huerto que tengo guardado en la cookie
        $repository = $this->getDoctrine()->getRepository('OrchardBundle:Image');
        $images = $repository->findByOrchard($orchard);

        return $this->render('OrchardBundle:Default:step13.html.twig', array('orchard' => $orchard,"images" =>$images));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard));
        break;
      }
      case 14:
      $repository = $this->getDoctrine()->getRepository('OrchardBundle:OrchardType');
      $orchard_types = $repository->findAll();

      if( $step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step14.html.twig', array('orchard' => $orchard, 'orchard_types' => $orchard_types));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard, 'orchard_types' => $orchard_types));
        break;
      }
      case 15:
      if( $step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step15.html.twig', array('orchard' => $orchard));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard));
        break;
      }
      case 21:
      $repository = $this->getDoctrine()->getRepository('OrchardBundle:OrchardActivity');
      $orchard_activities = $repository->findAll();

      if( $step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step21.html.twig', array('orchard' => $orchard, 'orchard_activities' => $orchard_activities));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard, 'orchard_activities' => $orchard_activities));
        break;
      }
      case 22:
      $repository = $this->getDoctrine()->getRepository('OrchardBundle:OrchardService');
      $orchard_services = $repository->findAll();

      if( $step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step22.html.twig', array('orchard' => $orchard, 'orchard_services' => $orchard_services));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard, 'orchard_services' => $orchard_services));
        break;
      }
      case 23:
      $repository = $this->getDoctrine()->getRepository('OrchardBundle:OrchardParticipate');
      $orchard_participates = $repository->findAll();

      if( $step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step23.html.twig', array('orchard' => $orchard, 'orchard_participates' => $orchard_participates));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard, 'orchard_participates' => $orchard_participates));
        break;
      }
      case 24:
      if( $step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step24.html.twig', array('orchard' => $orchard));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard));
        break;
      }
      case 25:
      if( $step <= $step_orchard) {
        return $this->render('OrchardBundle:Default:step25.html.twig', array('orchard' => $orchard));
        break;
      }else{
        return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard));
        break;
      }


      default:
      return $this->render('OrchardBundle:Default:step' . $step_orchard . '.html.twig', array('orchard' => $orchard));
      break;
    }
  }

  //Método utilizado para insertar un huerto en la BBDD
  //Recibe los campos del formulario correspondiente mediante el método POST
  public function insertAction(Request $request)
  {

    $cookies = $request->cookies;

    $orchard = null;

    if($cookies->has('ID_ORCHARD')) {
      //El huerto está creado así que recuperamos el objeto de BBDD
      $orchard = $this->getDoctrine()->getRepository('OrchardBundle:Orchard')->findOneById($cookies->get('ID_ORCHARD'));
    }else {
      //El huerto no está creado así que creamos el objeto
      $orchard = new Orchard();
    }

    // Recoje todos los valores del form
    $params = $request->request->all();

    // Recorremos el $key y $value del formulario para separar el id y su valor para añadirlo en la bd
    if ($params != null) {
      foreach($params as $key => $value) {
        if (!empty($value)) {
          $setterName = 'set'.$key;
          $orchard->$setterName($value);
        }
      }
    }
    // Meter datos en la bd
    $em = $this->getDoctrine()->getManager();
    $em->persist($orchard);
    $em->flush();

    $id = $orchard->getId();

    //Seteamos la cookie con el id del huerto acabado de crear o de actualizar (en el caso de estar actualizando el registro no haria falta pero lo dejamos para que sea un método más genérico).
    $response = new JsonResponse();
    $response->headers->setCookie(new Cookie("ID_ORCHARD", $id));
    $response->sendHeaders();

    $response = new JsonResponse();
    $response->setData(array(
        'redirect' => $orchard->getStep()
    ));

    return $response;

  }

  public function sendAction($orchard_type)
  {

    // $id_user = 1;
    // $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($id_user);
    $userName = 'David';
    //
    // if($user != null) {
    //   $userName = $user->getName();
    // }

    $message = \Swift_Message::newInstance()
        ->setContentType("text/html")
        ->setSubject('Nueva sugerencia para tipos de huerto')
        ->setFrom('parcellesflorida@gmail.com')
        ->setTo('ab95david@gmail.com')
        ->setBody(
            $this->renderView(
                'OrchardBundle:Default:email.html.twig',
                array('orchard_type' => $orchard_type, 'userName' => $userName)
            )
        )
    ;
    $this->get('mailer')->send($message);

    return new Response();
  }

  public function suggestAction(Request $request, $orchard_type, $accept)
  {

    // $id_user = 1;
    // $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($id_user);
    $userName = 'David';
    //
    // if($user != null) {
    //   $userName = $user->getName();
    // }

    $cookies = $request->cookies;

    $orchard = null;

    if($cookies->has('ID_ORCHARD')) {
      //El huerto está creado así que recuperamos el objeto de BBDD
      $orchard = $this->getDoctrine()->getRepository('OrchardBundle:Orchard')->findOneById($cookies->get('ID_ORCHARD'));
    }

    if($accept == 'true') {
      //Guardar en BBDD y relacionar con orchard
      //Enviar mail de éxito al usuario
      $orchardType = new OrchardType();
      $orchardType->setName($orchard_type);
      $orchard->addOrchardType($orchardType);
      $em = $this->getDoctrine()->getManager();
      $em->persist($orchard);
      $em->persist($orchardType);
      $em->flush();

      $message = \Swift_Message::newInstance()
          ->setContentType("text/html")
          ->setSubject('Se ha aceptado la sugerencia para tipos de huerto')
          ->setFrom('parcellesflorida@gmail.com')
          ->setTo('ab95david@gmail.com')
          ->setBody(
              $this->renderView(
                  'OrchardBundle:Default:email.html.twig',
                  array('orchard_type' => $orchard_type, 'userName' => $userName, 'accept' => $accept)
              )
          )
      ;
      $this->get('mailer')->send($message);

    }else {
      //Enviar mail de error al usuario
      $message = \Swift_Message::newInstance()
          ->setContentType("text/html")
          ->setSubject('No se ha aceptado la sugerencia para tipos de huerto')
          ->setFrom('parcellesflorida@gmail.com')
          ->setTo('ab95david@gmail.com')
          ->setBody(
              $this->renderView(
                  'OrchardBundle:Default:email.html.twig',
                  array('orchard_type' => $orchard_type, 'userName' => $userName, 'accept' => $accept)
              )
          )
      ;
      $this->get('mailer')->send($message);
    }

    return new Response();
  }

  //Método utilizado para añadir imagenes a los huertos, recibe una imagen y la mueve a la carpeta de imagenes relacionandola con el huerto
  //Recibe una imagen por request con su descripción por metodo POST

  public function uploadImageActionOld(Request $request){
    $id_orchard=$request->cookies->get('ID_ORCHARD');
    $name=$request->get("name");
    $src=$request->get("src");
    $description=$request->get("description");

    $imagen=new Image();
    $imagen->setSrc($src);
    $imagen->setDescription($description);
    $orchard=$this->getDoctrine()->getRepository("OrchardBundle:Orchard")->findOneById($id_orchard);
    $imagen->setOrchard($orchard);

    $em=$this->getDoctrine()->getManager();
    $em->persist($imagen);
    $em->flush();

   return new JsonResponse($imagen->getId());
  }

  public function uploadImageAction(Request $request){
    ini_set('memory_limit', '-1');
    //extraer id_orchard
    $id_orchard=$request->cookies->get('ID_ORCHARD');

    //extraer json con imagenes
    //$imagenes=$request->get("imgs");
    $imagenes=json_decode($request->getContent());
    //print_r($imagenes);
    //return new JsonResponse($imagenes[0]->des);

    $em=$this->getDoctrine()->getManager();

    $orchard=$this->getDoctrine()->getRepository("OrchardBundle:Orchard")->findOneById($id_orchard);
if (count($imagenes)>0) {
  foreach ($imagenes as $img) {
      $imagen=new Image();
      $imagen->setSrc($img->src);
      $imagen->setDescription($img->des);

      $imagen->setOrchard($orchard);

      $em->persist($imagen);
      $em->flush();
    }
  }
    //update step orchard
    if($orchard->getStep()<14){
      $orchard->setstep(14);
      $em->persist($orchard);
      $em->flush();
    }

return new JsonResponse(14);

  }


  public function deleteImageAction($idImage){
    if($idImage!=null){
    $em=$this->getDoctrine()->getManager();
    $image=$em->getRepository("OrchardBundle:Image")->findOneById($idImage);
    if(!$image){
      return new JsonResponse("image not found width id"+$idImage);
    }

    $em->remove($image);
    $em->flush();
    return new JsonResponse("ok");
  }else {
    return new JsonResponse("ko");
  }
  }

  public function modifyImageAction($idImage,Request $request){
    if($idImage!=null){
    $em=$this->getDoctrine()->getManager();
    $image=$em->getRepository("OrchardBundle:Image")->findOneById($idImage);

    if(!$image){
      return new JsonResponse("image not found width id"+$idImage);
    }
    $des=$request->getContent();
    $image->setDescription(explode("=",$des)[1]);
    $em->flush();
    return new JsonResponse("ok");
  }else {
    return new JsonResponse("ko");
  }
  }

  public function uploadFileAction(Request $request)
  {
    try {
      $file=$request->files->get('normas');
    } catch (Exception $e) {

    }
    return new JsonResponse(25);

  }
}

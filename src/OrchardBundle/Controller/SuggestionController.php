<?php

namespace OrchardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OrchardBundle\Entity\Orchard;
use OrchardBundle\Entity\OrchardType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class SuggestionController extends Controller
{

  public function sendAction($orchard_type)
  {

    $message = \Swift_Message::newInstance()
    ->setContentType("text/html")
    ->setSubject('Nueva sugerencia para tipos de huerto')
    ->setFrom('parcellesflorida@gmail.com')
    ->setTo('jumofe95@gmail.com')
    ->setBody(
      $this->renderView(
        'OrchardBundle:Default:email.html.twig',
        array('orchard_type' => $orchard_type)
        )
        )
        ;
        $this->get('mailer')->send($message);

        return new Response();
      }

      public function addTypeDescriptionAction($orchard_type, $accept)
      {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($accept == 'true') {
          return $this->render('OrchardBundle:Default:orchard-type_add-description.html.twig', array('orchard_type' => $orchard_type));
        }else {
          //Enviar mail de error al usuario
          $message = \Swift_Message::newInstance()
          ->setContentType("text/html")
          ->setSubject('No se ha aceptado la sugerencia para tipos de huerto')
          ->setFrom('parcellesflorida@gmail.com')
          ->setTo($user->getEmail())
          ->setBody(
            $this->renderView(
              'OrchardBundle:Default:email.html.twig',
              array('orchard_type' => $orchard_type, 'accept' => $accept)
              )
              )
              ;
              $this->get('mailer')->send($message);

              return new Response();
            }

          }

          public function suggestAction(Request $request, $orchard_type, $type_description) {
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $cookies = $request->cookies;

            $orchard = null;

            if($cookies->has('ID_ORCHARD')) {
              //El huerto está creado así que recuperamos el objeto de BBDD
              $orchard = $this->getDoctrine()->getRepository('OrchardBundle:Orchard')->findOneById($cookies->get('ID_ORCHARD'));
            }

            //Guardar en BBDD y relacionar con orchard
            //Enviar mail de éxito al usuario
            $orchardType = new OrchardType();
            $orchardType->setName($orchard_type);
            $orchardType->setDescription($type_description);
            $orchard->addOrchardType($orchardType);
            $em = $this->getDoctrine()->getManager();
            $em->persist($orchard);
            $em->persist($orchardType);
            $em->flush();

            $message = \Swift_Message::newInstance()
            ->setContentType("text/html")
            ->setSubject('Se ha aceptado la sugerencia para tipos de huerto')
            ->setFrom('parcellesflorida@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
              $this->renderView(
                'OrchardBundle:Default:email.html.twig',
                array('orchard_type' => $orchard_type, 'accept' => 'true')
                )
                )
                ;
                $this->get('mailer')->send($message);

                return new Response();
              }
            }
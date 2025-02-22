<?php

namespace EventBundle\Controller;

use AppBundle\Entity\User;
use EventBundle\Entity\Event;
use EventBundle\Entity\Participation;
use EventBundle\Repository\EventRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Swift_Attachment;
use Swift_Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * Lists all event entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EventBundle:Event')->findBy(array('state' => 'accepted'),array('created_at' => 'DESC'));

        $eventsPagniate  = $this->get('knp_paginator')->paginate(
            $events,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            4/*nbre d'éléments par page*/
        );

        return $this->render('@Event/event/index.html.twig', array(
            'events' => $eventsPagniate,
        ));
    }

    public function myEventsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $myEvents = $em->getRepository('EventBundle:Event')->findBy(array('user' => $this->getUser()));

        return $this->render('@Event/event/myevents.html.twig', array(
            'events' => $myEvents,
        ));
    }

    /**
     * Creates a new event entity.
     *
     */
    public function newAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm('EventBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $file
             */
            $file=$event->getPicture();
            $picture = $request->get('imageUpload');
            //dump($picture);die;
            $filename = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move($this->getParameter('images_directory'),$filename);
            $event->setPicture($filename);
            $event->setState('pending');
            $event->setCreatedAt(new \DateTime());
            $event->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('@Event/event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * Finds and displays a event entity.
     *
     */

    public function showAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $userParticipated = false;
        /* @var $part Participation*/
        foreach ($event->getParticipation() as $part) {
            if ($this->getUser() == $part->getParticipant()) $userParticipated = true;
        }

        return $this->render('@Event/event/show.html.twig', array(
            'event' => $event,
            'userParticipated' => $userParticipated,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     *
     */
    public function editAction(Request $request, Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('EventBundle\Form\EventTypeEdit', $event);
        $editForm->handleRequest($request);
        $users = [];
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            /* @var $eventParticipation [] Participation */
            $eventParticipation = $event->getParticipation();

            /* @var Participation $participation */
            foreach ($eventParticipation as $participation) {
                array_push($users, $participation->getParticipant());
            }


            /* Sending emails to the participants when the event is updated */
            foreach ($users as $user) {
                $this->sendEmail($user,'Event Updated','@Event/event/emailTemplate.html.twig', $event);
            }

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('@Event/event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     *
     */
    public function deleteAction(Request $request, Event $event)
    {
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);
        $users= [];
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();

            /* @var $eventParticipation [] Participation */
            $eventParticipation = $event->getParticipation();

            /* @var Participation $participation */
            foreach ($eventParticipation as $participation) {
                array_push($users, $participation->getParticipant());
            }

            /* Sending emails to the participants when the event is deleted */
            foreach ($users as $user) {
                $this->sendEmail($user,'Event Annulé !','@Event/event/emailTemplateNoEvent.html.twig', $event);
            }

        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function searchAction(Request $request)
    {
       $events          =   $this->getDoctrine()->getRepository(Event::class)->findAll();
       $titre           =   $request->get('titre');
       $lieu            =   $request->get('lieu');
       $eventsResult     =   array();

       if(($titre != null) && ($lieu == null))
       {
          foreach($events as $event)
          {
              if(strpos(strtoupper($event->getTitle()) , strtoupper($titre))!== false )
              {
                  $eventsResult[] = $event;
              }
          }
       }
       else if (($titre == null) && ($lieu != null))
       {
           foreach($events as $event)
           {
               if(strpos(strtoupper($event->getLocation()) , strtoupper($lieu))!== false )
               {
                   $eventsResult[] = $event;
               }
           }
       }
       else if (($titre != null) && ($lieu != null))
       {
           foreach($events as $event)
           {
               if((strpos(strtoupper($event->getTitle()) , strtoupper($titre))!== false ) && (strpos(strtoupper($event->getLocation()) , strtoupper($lieu))!== false ))
               {
                   $eventsResult[] = $event;
               }
           }
       }
       else
       {
           $eventsResult = $events;
       }

        return $this->render('@Event/event/search.html.twig',[
            'events'        =>  $eventsResult,
        ]);
    }

    public function searchMyEventsAction(Request $request)
    {
        $events             =   $this->getDoctrine()->getRepository(Event::class)->findAll();
        $myEvents           =  array();
        $titre              =   $request->get('titre');
        $lieu               =   $request->get('lieu');
        $eventsResult       =   array();

        foreach($events as $event)
        {
            if ($event->getUser()->getId() == $this->getUser()->getId())
            {
                $myEvents[] = $event;
            }
        }

        if(($titre != null) && ($lieu == null))
        {
            foreach($myEvents as $event)
            {
                if(strpos(strtoupper($event->getTitle()) , strtoupper($titre))!== false )
                {
                    $eventsResult[] = $event;
                }
            }
        }
        else if (($titre == null) && ($lieu != null))
        {
            foreach($myEvents as $event)
            {
                if(strpos(strtoupper($event->getLocation()) , strtoupper($lieu))!== false )
                {
                    $eventsResult[] = $event;
                }
            }
        }
        else if (($titre != null) && ($lieu != null))
        {
            foreach($myEvents as $event)
            {
                if((strpos(strtoupper($event->getTitle()) , strtoupper($titre))!== false ) && (strpos(strtoupper($event->getLocation()) , strtoupper($lieu))!== false ))
                {
                    $eventsResult[] = $event;
                }
            }
        }
        else
        {
            $eventsResult = $myEvents;
        }

        return $this->render('@Event/event/search_my_events.html.twig',[
            'events'        =>  $eventsResult,
        ]);
    }

    public function myParticipationsAction (Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $user User*/
        $user = $this->getUser();

        $userParts = [];

        /* @var $userParticipations [] Participation */
        $userParticipations = $user->getParticipation();

        /* @var Participation $participation */
        foreach ($userParticipations as $participation) {
            array_push($userParts, $participation->getEvent());
        }



        $participPagniate  = $this->get('knp_paginator')->paginate(
            $userParts,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            4/*nbre d'éléments par page*/
        );

        return $this->render('@Event/event/my_participations.html.twig', array(
            'events' => $participPagniate,
        ));
    }

    public function participateAction ($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Event $event*/
        $event = $em->getRepository(Event::class)->find($id);
        /* @var User $user*/
        $user = $this->getUser();

        $qrCode = new QrCode($user->getUsername()." with Email : ". $user->getEmail());

        $qrCode->writeFile($this->get('kernel')->getRootDir() . '/../web/'.$user->getUsername().'.png');

        $dataUri = $qrCode->writeDataUri();

        $newParticipation = new Participation();
        $newParticipation->setEvent($event);
        $newParticipation->setParticipant($user);

        $event->addParticipation($newParticipation);
        $event->setNbPlace($event->getNbPlace() - 1);
        $user->addParticipation($newParticipation);

        $em->persist($newParticipation);
        $em->flush();

        $em->persist($event);
        $em->persist($user);
        $em->flush();
        $this->sendEmail($user,'Confirmation de Participation','@Event/event/emailTemplateParticipate.html.twig', $event, $dataUri);
        return $this->redirectToRoute('event_show', array('id' => $event->getId()));
    }

    public function cancelParticipationAction ($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Event $event*/
        $event = $em->getRepository(Event::class)->find($id);
        /* @var User $user*/
        $user = $this->getUser();

        $currentParticipation = $em->getRepository(Participation::class)->findBy(['participant' => $user, 'event' => $event]);
        $event->removeParticipation($currentParticipation[0]);
        $event->setNbPlace($event->getNbPlace() + 1);

        $user->removeParticipation($currentParticipation[0]);
        $em->remove($currentParticipation[0]);
        $em->persist($event);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('event_show', array('id' => $event->getId()));
    }


    public function sendEmail(User $utilisateur, $subject, $view, $event, $qr = null)
    {
        $mail = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('baskaltyinc@gmail.com')
            ->setTo($utilisateur->getEmail());

        if($qr !== null) {
            $mail->attach(
                Swift_Attachment::fromPath($this->get('kernel')->getRootDir() . '/../web/'.$utilisateur->getUsername().'.png')
                    ->setDisposition('inline'));
        }

        $mail->setBody(
            $this->renderView(
                $view,
                array('user' => $utilisateur,
                    'event' => $event)
            ),
            'text/html'
        );

        $this->get('mailer')->send($mail);
    }

    public function pdfAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var Event $event */
        $event = $em->getRepository(Event::class)->find($id);

        $users = [];

        /* @var $eventParticipation [] Participation */
        $eventParticipation = $event->getParticipation();

        /* @var Participation $participation */
        foreach ($eventParticipation as $participation) {
            array_push($users, $participation->getParticipant());
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->render('@Event/event/toPdf.html.twig', array(
            'event' => $event, 'users' => $users
        ));

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);

    }
    /***-----------------------***/
    /* Back Office / Admin Section-----*/
    /***-----------------------***/

    public function oldEventsListAdminAction(Request $request)
    {
        $events = $this->getDoctrine()
            ->getManager()
            ->createQuery("SELECT e FROM EventBundle:Event e WHERE e.endDate < CURRENT_DATE() AND e.state = 'accepted'")
            ->getResult();

        return $this->render('@Event/admin/events_old.html.twig', array(
            'events' => $events
        ));
    }

    public function newEventsListAdminAction(Request $request)
    {
        $events = $this->getDoctrine()
            ->getManager()
            ->createQuery("SELECT e FROM EventBundle:Event e WHERE e.endDate > CURRENT_DATE()")
            ->getResult();

        return $this->render('@Event/admin/events_new.html.twig', array(
            'events' => $events
        ));
    }

    public function acceptEventAction ($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Event $event*/
        $event = $em->getRepository(Event::class)->find($id);
        $event->setState('accepted');
        $em->persist($event);
        $em->flush();

        $this->sendEmail($event->getUser(),'Votre événement est Acccepté !','@Event/event/emailTemplateAccept.html.twig', $event);

        return $this->redirectToRoute('admin_event_new_show');
    }

    public function refuseEventAction ($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Event $event*/
        $event = $em->getRepository(Event::class)->find($id);

        $event->setState('refused');
        $em->persist($event);
        $em->flush();

        $this->sendEmail($event->getUser(), 'Evénement refusé!', '@Event/event/emailTemplateRefused.html.twig', $event);

        return $this->redirectToRoute('admin_event_new_show');

    }

    public function showEventAdminAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Event $event*/
        $event = $em->getRepository(Event::class)->find($id);

        return $this->render('@Event/admin/show_event.html.twig', array(
            'event' => $event
        ));
    }

    public function newEventAdminAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm('EventBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $file
             */
            $file=$event->getPicture();
            $picture = $request->get('imageUpload');
            //dump($picture);die;
            $filename = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move($this->getParameter('images_directory'),$filename);
            $event->setPicture($filename);
            $event->setState('accepted');
            $event->setCreatedAt(new \DateTime());
            $event->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('admin_event_show', array('id' => $event->getId()));
        }

        return $this->render('@Event/admin/event_new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     *
     */
    public function adminDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var Event $event*/
        $event = $em->getRepository(Event::class)->find($id);
        $users= [];

            /* @var $eventParticipation [] Participation */
            $eventParticipation = $event->getParticipation();

            /* @var Participation $participation */
            foreach ($eventParticipation as $participation) {
                array_push($users, $participation->getParticipant());
            }

            /* Sending emails to the participants when the event is deleted */
            foreach ($users as $user) {
                $this->sendEmail($user,'Event Annulé !','@Event/event/emailTemplateNoEvent.html.twig', $event);
            }

        /* @var Participation $participation */
        foreach ($eventParticipation as $participation) {
            $event->removeParticipation($participation);
            $em->remove($participation);
        }
        $em->remove($event);
        $em->flush();

        return $this->redirectToRoute('admin_event_old_show');
    }

}

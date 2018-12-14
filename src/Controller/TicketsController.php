<?php

namespace App\Controller;
use App\Form\editTicket;
use App\Entity\Tickets;
use App\Entity\User;
use App\Entity\Projects;
use App\Entity\Tags;
use App\Entity\TicketsTags;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TicketsController extends AbstractController
{
    /**
     * @Route("/projects/tickets", name="tickets")
     */
    public function index()
    {
        return $this->render('404.html.twig', [
            'controller_name' => 'TicketsController',
        ]);
    }
    /**
     * @Route("/projects/tickets/{id}", name="ticket_montrer")
    */

    public function montrer(Request $request, $id)
    {
        $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($id);
        $tictag = $this->getDoctrine()->getRepository(TicketsTags::class)->findBy(['Ticket_id'=>$id]);
        $tagName = array();
        foreach($tictag as $key => $value){
            $object[$key] = $value;
            $tagName[$key] = $object[$key]->getTagId();
        }
        $tagsmass = array();
        foreach($tagName as $key => $value){
            $tag = $value;
            $tagss = $this->getDoctrine()->getRepository(Tags::class)->findBy(['id'=>$tag]);
            $tagsmass[$key] = $tagss;
            $tagsName = array();
            $objectN = array();
            foreach($tagss as $key => $value){
                $objectN[$key] = $value;
                $tagsName[$key] = $objectN[$key]->getName();
            }
        }
        $tag = new Tags();
        $form = $this->createFormBuilder($tag)
            ->add('Name')
            ->add('Submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {   
            $tagname = $tag->getName();
            $tags = $this->getDoctrine()->getRepository(Tags::class)->findBy(['Name'=>$tagname]);
            if (!$tags){
                $save = $this->getDoctrine()->getManager();
                $save->persist($tag);
                $save->flush();
                $tagid = $tag->getId();
                $tictag = new TicketsTags();
                $tictag->setTicketId($id);
                $tictag->setTagId($tagid);
                $save = $this->getDoctrine()->getManager();
                $save->persist($tictag);
                $save->flush();
            } else {
                $tagIdd = $tags[0]->getId();
                $tics = $this->getDoctrine()->getRepository(TicketsTags::class)->findBy(['Ticket_id'=>$id]);
                $ticks = array();
                foreach ($tics as $key => $value){
                    $ticks[$key] = $value;
                    $ticksId = $ticks[$key]->getTagId();
                    if($ticksId == $tagIdd){
                        $this->addFlash(
                            'Error_Tag',
                            'Такой тег у тикета существует!'
                        );
                        return $this->redirectToRoute("ticket_montrer", ['id'=>$id]);
                    }
                }
                
                $tictag = new TicketSTags();
                $tictag->setTicketId($id);
                $tictag->setTagId($tagIdd);
                $save = $this->getDoctrine()->getManager();
                $save->persist($tictag);
                $save->flush();

                return $this->redirectToRoute("ticket_montrer", ['id'=>$id]);
            }
        }
            if (!isset($ticket)) {
                return $this->render('404.html.twig');
            }

            return $this->render('/tickets/montrer.html.twig', [
                'ticket'=>$ticket,
                'tags'=>$tagsmass,
                'form'=>$form->createView(),
            ]);
    }

    /**
    * @Route("/projects/{id}/tickets/create", methods="GET|POST")
    */
    
    public function createTickets(Request $request,Projects $project, $id,SessionInterface $session){ 
   // $users = $this->getDoctrine()->getRepository(Users::class)->findAll();
    $ticket = new Tickets();
    $user = $this->getUser()->getId();
    $userN = $this->getUser()->getUsername();
    $users = $this->getDoctrine()->getRepository(User::class);
    $repository = $this->getDoctrine()->getRepository(User::class)->findAll();
    $userName = array("$userN"=>"$user");
           foreach ($repository as $key)
          {
            $rie = $key;
            $namee = $rie->getUsername();
            $idd = $rie->getId();
            $userName[$namee] = $idd;
          }
    $form = $this->createFormBuilder($ticket)
     ->add('Name')
     ->add('ProjectId', HiddenType::class, array('data'=>$id))
     ->add('UserId', HiddenType::class, array('data'=>$user))
     ->add('Description',TextType::class)
     ->add('AssigneeId', ChoiceType::class, array('choices'=>$userName))
     ->add('Status', ChoiceType::class, array(
     'choices'=>array(
     'New'=>'New',
     'In progress'=>'In progress',
     'Testing'=>'Testing',
     'Done'=>'Done',
),
     ))
     ->add('File', FileType::class)
     ->add('Submit', SubmitType::class)
     ->getForm();
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
    $this->addFlash(
    'sms',
    'Votre projet est enregistré'
    );
    $file = $ticket->getFile();
    $fileName = $this->uniqueName().'.'.$file->guessExtension();
    try
    {
        $file->move(
            $this->getParameter('file_directory'),
            $fileName
            );
    }
    catch (FileException $e){
    }
    $ticket->setFile($fileName);
    $ticket = $form->getData();
    $ticketid = $ticket->getId();
    $save = $this->getDoctrine()->getManager();
    $save->persist($ticket);
    $save->flush();
    return $this->redirectToRoute("projects_info", ['id'=>$id]);
    }
    return $this->render('/tickets/create.html.twig', array(
    'ticket'=>$ticket,
    'id'=>$id,
    'form'=>$form->createView(),
   ));

    }

    /**
    * @Route("/projects/tickets/del/{id}")
    */

    public function deleteTicket(Request $request, Tickets $tickets, $id)
    {
    $repository = $this->getDoctrine()->getManager();
    $ticket = $repository->getRepository(Tickets::class)->find($id);
    $repository->remove($ticket);
    $repository->flush();
    return $this->redirectToRoute('start');
    }

    /**
    * @Route("/projects/tickets/{id}/edit", name="ticket_edit", methods="GET|POST")
    */

    public function editTicket(Request $request, Tickets $tickets, $id)
    {

    $form = $this->createForm(editTicket::class, $tickets);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
    $this->getDoctrine()->getManager()->flush();
    $form = $this->createForm(editTicket::class, $tickets);
    $form->handleRequest($request);
    return $this->redirectToRoute('ticket_montrer', ['id'=>$id]);
    }
    return $this->render('tickets/edit.html.twig', [
        'ticket'=>$tickets,
        'form'=>$form->createView(),
        ]);

    }
    private function uniqueName()
    {
        // md5() уменьшает схожесть имён файлов, сгенерированных
        // uniqid(), которые основанный на временных отметках
        return md5(uniqid());
    }

    /**
    * @Route("/tickets/user", name="tickets_user")
    */


    public function ticketUser(){
        $user = $this->getUser();
        $id = $this->getUser()->getId();
        $ticket = $this->getDoctrine()->getRepository(Tickets::class)->findBy(['user_id'=>$id]);
        if(!isset($ticket)){
            return $this->render('404.html.twig');
        }
        return $this->render('/tickets/tickets_user.html.twig',[
        'user'=>$user,
        'ticket'=>$ticket,
        ]);
    }
}

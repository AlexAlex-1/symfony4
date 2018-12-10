<?php

namespace App\Controller;
use App\Form\editTicket;
use App\Entity\Tickets;
use App\Entity\Users;
use App\Entity\Projects;
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

    public function montrer($id){
    
    $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($id);
   // $project = $this->getDoctrine()->getRepository(Projects::class)->findBy();
    if(!isset($ticket)){
    return $this->render('404.html.twig');
    }
    return $this->render('/tickets/montrer.html.twig',[
    'ticket'=>$ticket,
    ]);
    }

    /**
    * @Route("/projects/{id}/tickets/create", methods="GET|POST")
    */
    
    public function createTickets(Request $request,Projects $project, $id,SessionInterface $session){ 
    $users = $this->getDoctrine()->getRepository(Users::class)->findAll();
    $ticket = new Tickets();
    $user = array();
    foreach ($users as $key => $value){
//    $user[$key['login']] = $value['id'];
    }
 //   $users = $users->getLogin();
    $form = $this->createFormBuilder($ticket)
     ->add('Name')
     ->add('ProjectId', HiddenType::class, array('data'=>$id))
     ->add('UserId')
     ->add('Description')
     ->add('AssigneeId')
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
        // ... handle exception if something happens during file upload
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
    'users'=>$users,
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
}

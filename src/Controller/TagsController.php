<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Entity\TicketsTags;
use App\Entity\Tickets;
use App\Entity\Projects;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    /**
     * @Route("/tags", name="tags")
     */
    public function index()
    {
        return $this->render('404.html.twig', [
            'controller_name' => 'TagsController',
        ]);
    }

    /**
     * @Route("/tags/{id}")
     */

    public function montrerTickets($id){
        $error = $this->getDoctrine()->getRepository(Tags::class)->findBy(['id'=>$id]);
        if (!$error){
            return $this->render('404.html.twig');
        }
        $objects = $this->getDoctrine()->getRepository(TicketsTags::class)->findBy(['Tag_id'=>$id]);
        $ticketss = array();
        foreach ($objects as $key => $value){
            $ticketsid = $objects[$key]->getTicketId();
            $tickets = $this->getDoctrine()->getRepository(Tickets::class)->findBy(['id'=>$ticketsid]);
            $ticketss[$key] = $tickets;
            $ticketsid = array();
        }
        return $this->render('tags/index.html.twig',[
            'tickets'=>$ticketss,
            'tag'=>$error,
        ]);
    }
}

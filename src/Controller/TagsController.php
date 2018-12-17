<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Entity\TicketsTags;
use App\Entity\Tickets;
use App\Entity\Projects;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class TagsController extends AbstractController
{

    /**
     * @Route("/tags/{id}")
     */

        public function show(Tags $tag): Response
        {
            $tagId = $tag->getId();
            $tickets = $this->getDoctrine()
                ->getRepository(Tags::class)
                ->findByTags($tagId);
            return $this->render('tags/index.html.twig', [
                'tickets' => $tickets,
                'tags' =>$tag,
            ]);
        }
}

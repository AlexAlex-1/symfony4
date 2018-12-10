<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PizzaController extends AbstractController
{
    /**
     * @Route("/admin", name="pizza")
     */
    public function index()
    {
    $i = random_int(1,100000000);
        return $this->render('pizza/index.html.twig', [
            'controller_name' => 'PizzaController',
            'con'=>$i,
        ]);
    }
}

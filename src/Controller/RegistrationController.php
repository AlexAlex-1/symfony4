<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration", methods="GET|POST")
     */
    public function registation(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
       // var_dump($user);
        if ($form->isSubmitted())
        {
            if($form->isValid()){
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $save = $this->getDoctrine()->getManager();
            $save->persist($user);
            $save->flush();
            $this->addFlash(
            'user_create',
            'Успешая регистрация!Войдите со своим логином и паролем!');
            return $this->redirectToRoute('registration');
            }
            else{
//foreach ($form->getErrors() as $error) {
//echo $error->getCause();
//}
            $this->addFlash(
            'user_not_create',
            'Ошибка регистрации!');
           //  var_dump($user);
             return $this->redirectToRoute('registration');
             }
        }
    return $this->render(
    'registration/index.html.twig', array(
    'form'=>$form->createView())
    );
    }
}

<?php
namespace App\Controller;
use App\Entity\Tickets;
use App\Entity\Projects;
use App\Form\CreateProject;
use App\Form\editProject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
class ProjectsController extends AbstractController
{
    /**
     * @Route("/projects", name="start")
     */
    public function montrerProjects()
    {
        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAll();
        return $this->render('projects/index.html.twig', [
            'controller_name' => 'ProjectsController',
            'projects' => $projects,
        ]);
    }
    /**
    * @Route("/projects/montrer/{id}", name="projects_info")
    */
    public function montrerProject($id){
      $project = $this->getDoctrine()->getRepository(Projects::class)->find($id);
      $tickets = $this->getDoctrine()->getRepository(Tickets::class)->findBy(['project_id'=>$id]);
      if (!isset($project)){
        return $this->render('/404.html.twig');
      }
      return $this->render('projects/montrer.html.twig',[
        'project'=>$project,
        'tickets'=>$tickets,
      ]);
    }
    /**
    * @Route("/projects/create", name="create", methods="GET|POST")
    */
    public function createProject(Request $request)
    {
        $project = new Projects();
        $form = $this->createForm(CreateProject::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
          $save = $this->getDoctrine()->getManager();
          $save->persist($project);
          $save->flush();
          $projectid = $project->getId();
          return $this->redirectToRoute("projects_info", ['id'=>$projectid]);
        }
        return $this->render('projects/create.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }
    /**
    * @Route("/projects/edit/{id}", name="project_edit", methods="GET|POST");
    */
    public function editProject(Request $request, Projects $projects, $id){
      $form = $this->createForm(editProject::class, $projects);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()){
        $this->getDoctrine()->getManager()->flush();
        $form = $this->createForm(editProject::class, $projects);
        $form->handleRequest($request);
        return $this->redirectToRoute('projects_info', ['id'=>$id]);
      }
      return $this->render('projects/edit.html.twig', [
        'project'=>$projects,
        'form'=>$form->createView(),
      ]);
    }
    /**
    * @Route("/projects/del/id={id}",methods="GET|POST");
    */
    public function deleteProject(Request $request, Projects $projects, $id){
      $repository = $this->getDoctrine()->getManager();
      $project = $repository->getRepository(Projects::class)->find($id);
      $repository->remove($project);
      $repository->flush();
      return $this->redirectToRoute('start');
    }
}

<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ){}

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $number = random_int(0, 100);
        return $this->render('Home/home.html.twig', [
            'number' => $number,
        ]);
    }

    #[Route('project/create', name:'project_create')]
    public function createProject(){

        $message = "Test create a project";
        $project = new Project;
        $project->setName('Kimas');
        $project->setArea(1);
        $project->setHardware('2');
        $project->setSoftware(3);
        $project->setDeveloper(4);
        $project->setCustomer(3);
        $project->setComment('this is a smart mobile roboter with camera modul for processing image');
  
        $error = $this->validator->validate($project);
        if (count($error)>0){
            return new Response((String) $error, 400);
        }
        
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->persist($project);
        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();

        return $this->render('Project/create.html.twig', [
            'message'=> $message
        ]);
    }


    #[Route('project/edit/{id}', name:'project_edit')]
    public function editProject(Project $project){

        return $this->render('Project/create.html.twig', [
            'project'=> $project
        ]);
    }
}
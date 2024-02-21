<?php

namespace App\Controller;

use App\Form\StudentType;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    private  $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $students = $this->em->getRepository(Student::class)->findAll();
        return $this->render('main/index.html.twig', [
            'students' => $students,
        ]);
    }


    #[Route('/create-student', name: 'create-student')]
    public function createStudent(Request $request)
    {
        $student = new Student();
        $form =  $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($student);
            $this->em->persist($student);
            $this->em->flush();
            $this->addFlash('message', 'Sucessfully Created');
            return $this->redirectToRoute('index');
        }
        return $this->render('main/student.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/edit-student/{id}', name: 'edit-student')]
    public function editStudent(Request $request, $id)
    {
        $student = $this->em->getRepository(Student::class)->find($id);
        $form =  $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($student);
            $this->em->persist($student);
            $this->em->flush();
            $this->addFlash('message', 'Sucessfully Updated');
            return $this->redirectToRoute('index');
        }
        return $this->render('main/student.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete-student/{id}', name: 'delete-student')]
    public function deleteStudent(Request $request, $id)
    {
        $student = $this->em->getRepository(Student::class)->find($id);
        $this->em->remove($student);
        $this->em->flush();
        $this->addFlash('message', 'Sucessfully deleted');
        return $this->redirectToRoute('index');
    }
}

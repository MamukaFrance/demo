<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/course', name: 'course_')]
final class CourseController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(CourseRepository $courseRepository): Response
    {
        //$courses = $courseRepository->findAll();
        //$courses = $courseRepository->findBy(['published' => true], ['name' =>'DESC'], 5);
        $courses = $courseRepository->findByDuration(5);

        return $this->render('course/list.html.twig', ['courses'=>$courses]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Course $course, CourseRepository $courseRepository): Response
    {
//        $course = $courseRepository->find($id);
//        if (!$course) {
//            throw $this->createNotFoundException('Le cours n\'existe pas');
//        }
        return $this->render('course/show.html.twig', [
            'course'=>$course,
            ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
       $course = new  Course();
       $form = $this->createForm(CourseType::class, $course);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $course->setPublished(true);
           $course->setDateCreated(new \DateTimeImmutable('now'));
            $em->persist($course);
            $em->flush();
            $this->addFlash('success', 'Le cours a été enregistré avec succès !');
            return $this->redirectToRoute('main_home');
       }

        return $this->render('course/create.html.twig', [
            'courseForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET','POST'])]
    public function edit(int $id): Response
    {
        return $this->render('course/edit.html.twig');
    }
    #[Route('/demo', name: 'demo', methods: ['GET'])]
    public function demo(EntityManagerInterface $em) :Response
    {
        $course = new Course();
        $course->setName("Symfony");
        $course->setPublished(true);
        $course->setContent("Le dévelopment web coté serveur avec Symfone");
        $course->setDuration(10);
        $course->setDateCreated(new \DateTimeImmutable("now"));

        $em->persist($course);

        dump($course);

        $em->flush();

        dump($course);

        $course->setName('PHP');

        $em->flush();

        dump($course);

        $em->remove($course);

        $em->flush();


       return $this->render('course/list.html.twig');
    }


}

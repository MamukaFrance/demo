<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Service\CourseService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/course', name: 'course_')]
final class CourseController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(CourseService $courseService ): Response
    {
        $courses = $courseService->findAll();
        //$courses = $courseService->getPublishedCourses();
        //$courses = $courseService->findByDuration(2);

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
            return $this->redirectToRoute('course_list');
       }

        return $this->render('course/create.html.twig', [
            'courseForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET','POST'])]
    public function edit(int $id, Request $request,
                         CourseService $courseService,
                         EntityManagerInterface $em): Response
    {
        $course = $courseService->findById($id);
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $course->setPublished(true);
        $course->setDateModified(new \DateTimeImmutable('now'));
        $em->persist($course);
        $em->flush();
        $this->addFlash('success', 'Cour a modifié avec succès');
        return $this->redirectToRoute('course_list');
        }

        return $this->render('course/edit.html.twig', [
            'courseForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/confirm-delete', name: 'course_confirm_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function confirmDelete(int $id, CourseRepository $courseRepository): Response
    {
        $course = $courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course introuvable.');
        }

        return $this->render('course/confirm_delete.html.twig', [
            'course' => $course,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(int $id, Request $request, CourseService $courseService): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('course_list');
        }
        if (!$courseService->delete($id)) {
            $this->addFlash('danger', "Le cours n'existe pas !");
        } else {
            $this->addFlash('success', "Le cours a été supprimé avec succès !");
        }
        return $this->redirectToRoute('course_list');
    }


//    #[Route('/demo', name: 'demo', methods: ['GET'])]
//    public function demo(EntityManagerInterface $em) :Response
//    {
//        $course = new Course();
//        $course->setName("Symfony");
//        $course->setPublished(true);
//        $course->setContent("Le dévelopment web coté serveur avec Symfone");
//        $course->setDuration(10);
//        $course->setDateCreated(new \DateTimeImmutable("now"));
//
//        $em->persist($course);
//
//        dump($course);
//
//        $em->flush();
//
//        dump($course);
//
//        $course->setName('PHP');
//
//        $em->flush();
//
//        dump($course);
//
//        $em->remove($course);
//
//        $em->flush();
//
//
//       return $this->render('course/list.html.twig');
//    }


}

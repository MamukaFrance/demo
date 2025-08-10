<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CourseService
{
    // Quand on est dans un service on peut injecter que dans le constructeur
    // Alors que dans les controllers on peut ajouter dans les routes/méthodes
    public function __construct(private CourseRepository $courseRepository,
                                private LoggerInterface $logger,
                                private EntityManagerInterface $entityManager,){
    }

    function getPublishedCourses() {

        $courses = $this->courseRepository->findBy(['published' => true], ['name' => 'DESC'], 5);
        // Logger
        $this->logger->info("La liste des cours publiés a été récupérée avec succès !");
        return $courses;
    }

    public function findByDuration(int $duration = 2){
        $courses = $this->courseRepository->findByDuration($duration);
        // Logger
        $this->logger->info("La liste des cours duration a été récupérée avec succès !");
        return $courses;
    }

    public function findAll(){
        $courses = $this->courseRepository->findAll();
        // Logger
        $this->logger->info("La liste des cours a été récupérée avec succès !");
        return $courses;
    }

    public function delete($id) : bool {
        $course = $this->courseRepository->find($id);
        if(!$course){
        $this->logger->info("Le cours n'existe pas !");
        }

        $this->entityManager->remove($course);
        $this->entityManager->flush();
        return true;
    }
    public function findById($id) : ?Course {
        return $this->courseRepository->find($id);
    }
}

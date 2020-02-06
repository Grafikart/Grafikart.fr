<?php

namespace App\Controller;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(EntityManagerInterface $em): Response
    {
        $course = new Course();
        $course->setDuration(10)
            ->setContent('Hello world content')
            ->setTitle('Hello wolrd')
            ->setSource(false)
            ->setPremium(false)
            ->setSlug('hello-world');
        $formation = new Formation();
        $formation->setTitle('Hello formation')
            ->setSlug('hello-formation')
            ->setContent('Hello content')
            ->setDuration(0)
            ->setChapters([])
            ->setYoutubePlaylist('');
        $em->persist($course);
        $em->persist($formation);
        $em->flush();
        return $this->render('pages/home.html.twig');
    }

}

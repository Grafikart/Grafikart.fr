<?php

namespace App\Http\Controller;

use App\Domain\Application\Entity\Content;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RevisionController extends AbstractController
{

    /**
     * @Route("/correct/{id}", name="correct")
     */
    public function show(Content $content)
    {
        return $this->render('content/revision.html.twig', [
            'content' => $content
        ]);
    }

}

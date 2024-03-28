<?php

namespace App\Http\Controller;

use App\Domain\School\DTO\SchoolImportDTO;
use App\Domain\School\InvalidCSVException;
use App\Domain\School\SchoolImportService;
use App\Http\Form\SchoolImportForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SchoolController extends AbstractController
{

    #[Route('/ecole', name: 'school')]
    #[IsGranted('SCHOOL_MANAGE')]
    public function index(
        Request $request,
        SchoolImportService $importer,
        EntityManagerInterface $em
    )
    {
        $school = $this->getUserOrThrow()->getSchool();

        // CSV Import
        $data = new SchoolImportDTO($school);
        $form = $this->createForm(SchoolImportForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $students = $importer->process($data);
                $this->addFlash('success', sprintf('%s étudiants ont été importés avec succcès', count($students)));
                return $this->redirectToRoute('school');
            } catch (InvalidCSVException $e) {
                $error = new FormError($e->getMessage());
                $form->get('file')->addError($error);
                dd($e);
            }
        }

        return $this->render('school/index.html.twig', [
            'school' => $school,
            'form' => $form,
        ]);
    }

}

<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Coupon\Entity\Coupon;
use App\Domain\Coupon\Repository\CouponRepository;
use App\Domain\History\HistoryService;
use App\Domain\History\Repository\ProgressRepository;
use App\Domain\School\DTO\SchoolImportDTO;
use App\Domain\School\InvalidCSVException;
use App\Domain\School\Repository\SchoolRepository;
use App\Domain\School\SchoolImportService;
use App\Http\DTO\SchoolImportConfirmRequestData;
use App\Http\Form\SchoolImportForm;
use App\Http\Requirements;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SchoolController extends AbstractController
{
    public function __construct(
        private readonly SchoolImportService $importer,
        private readonly SchoolRepository $schoolRepository,
        private readonly CouponRepository $couponRepository,
        private readonly ProgressRepository $progressRepository,
        private readonly HistoryService $historyService,
    ) {
    }

    #[Route('/ecole', name: 'school')]
    #[IsGranted('SCHOOL_MANAGE')]
    public function index(
        Request $request,
    ): Response {
        $user = $this->getUserOrThrow();
        $school = $this->schoolRepository->findAdministratedByUser($user->getId() ?? 0);

        if (!$school) {
            throw new NotFoundHttpException();
        }

        // CSV Import
        $data = new SchoolImportDTO($school);
        $form = $this->createForm(SchoolImportForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $result = $this->importer->preprocess($data);
                $months = $result->getMonths();

                return $this->render('school/confirm.html.twig', [
                    'rows' => $result->rows,
                    'content' => $result->csv,
                    'months' => $months,
                    'credits' => $school->getCredits(),
                ]);
            } catch (InvalidCSVException $e) {
                $error = new FormError($e->getMessage());
                $form->get('file')->addError($error);
            }
        }

        $students = $this->couponRepository->findClaimedForSchool($school);
        $userIds = array_map(fn (Coupon $coupon) => $coupon->getClaimedBy()?->getId() ?? 0, $students);

        return $this->render('school/index.html.twig', [
            'school' => $school,
            'form' => $form,
            'students' => $students,
            'coupons' => $this->couponRepository->findAllUnclaimedForSchool($school),
            'completions' => $this->progressRepository->findCompletionForUsers($userIds),
        ]);
    }

    #[Route('/ecole/student/{id}', name: 'school_student', requirements: ['id' => Requirements::ID])]
    public function student(
        User $user,
    ): Response {
        $me = $this->getUserOrThrow();
        $school = $this->schoolRepository->findAdministratedByUser($me->getId() ?? 0);
        $coupon = $this->couponRepository->findOneBy([
            'claimedBy' => $user,
            'school' => $school,
        ]);
        if ($coupon === null) {
            throw new AccessDeniedException('This user is not a student of your school');
        }

        $progression = $this->historyService->getFormationsProgressForUser($user);

        return $this->render('school/student.html.twig', [
            'student' => $user,
            'school' => $school,
            'progression' => $progression,
        ]);
    }

    #[Route('/ecole/confirm', name: 'school_confirm', methods: ['POST'])]
    #[IsGranted('SCHOOL_MANAGE')]
    public function confirm(
        #[MapRequestPayload]
        SchoolImportConfirmRequestData $data,
    ): RedirectResponse {
        $user = $this->getUserOrThrow();
        $school = $this->schoolRepository->findAdministratedByUser($user->getId() ?? 0);
        if (!$school) {
            throw new NotFoundHttpException();
        }

        $result = $this->importer->process($data->content, $school);
        $this->addFlash('success', sprintf('%s étudiants ont été importés avec succcès', count($result->rows)));

        return $this->redirectToRoute('school');
    }
}

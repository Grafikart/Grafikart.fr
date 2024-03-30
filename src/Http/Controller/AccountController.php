<?php

namespace App\Http\Controller;

use App\Domain\Coupon\CouponClaimerService;
use App\Domain\Coupon\DTO\CouponClaimDTO;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\Exception\TooManyEmailChangeException;
use App\Domain\Profile\ProfileService;
use App\Http\Form\CouponClaimForm;
use App\Http\Form\UpdatePasswordForm;
use App\Http\Form\UpdateProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccountController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $em,
        private readonly ProfileService $profileService,
        private readonly CouponClaimerService $couponClaimerService
    ) {
    }

    #[Route(path: '/profil/edit', name: 'user_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Request $request
    ): Response {
        $user = $this->getUserOrThrow();

        // Traitement du mot de passe
        [$formPassword, $response] = $this->createFormPassword($request);
        if ($response) {
            return $response;
        }

        [$formUpdate, $response] = $this->createFormProfile($request);
        if ($response) {
            return $response;
        }

        [$formCoupon, $response] = $this->createCouponForm($request);
        if ($response) {
            return $response;
        }

        return $this->render('account/edit.html.twig', [
            'form_coupon' => $formCoupon->createView(),
            'form_password' => $formPassword->createView(),
            'form_update' => $formUpdate->createView(),
            'user' => $user,
            'menu' => 'account',
        ]);
    }

    /**
     * Génère le formulaire de création.
     */
    private function createFormPassword(Request $request): array
    {
        $form = $this->createForm(UpdatePasswordForm::class);
        if ('password' !== $request->get('action')) {
            return [$form, null];
        }

        $user = $this->getUserOrThrow();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
            $this->em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');

            return [$form, $this->redirectToRoute('user_edit')];
        }

        return [$form, null];
    }

    /**
     * Formulaire d'édition de profil.
     */
    private function createFormProfile(Request $request): array
    {
        $user = $this->getUserOrThrow();
        $form = $this->createForm(UpdateProfileForm::class, new ProfileUpdateDto($user));
        if ('update' !== $request->get('action')) {
            return [$form, null];
        }
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $this->profileService->updateProfile($data);
                $this->em->flush();
                if ($user->getEmail() !== $data->email) {
                    $this->addFlash(
                        'info',
                        "Votre profil a bien été mis à jour, un email a été envoyé à {$data->email} pour confirmer votre changement"
                    );
                } else {
                    $this->addFlash('success', 'Votre profil a bien été mis à jour');
                }

                return [$form, $this->redirectToRoute('user_edit')];
            }
        } catch (TooManyEmailChangeException) {
            $this->addFlash('error', "Vous avez déjà un changement d'email en cours.");
        }

        return [$form, null];
    }

    /**
     * Formulaire d'ajout de code promotionnel
     */
    private function createCouponForm(Request $request): array {
        $form = $this->createForm(CouponClaimForm::class, new CouponClaimDTO($this->getUserOrThrow()));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $coupon = $this->couponClaimerService->claim($form->getData());
            $this->addFlash('success', sprintf('Votre code a bien été activé, vous avez obtenu %s mois de compte premium', $coupon->getMonths()));

            return [$form, $this->redirectToRoute('user_edit')];
        }
        return [$form, null];
    }
}

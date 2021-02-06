<?php

namespace App\Http\Controller;

use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\Exception\TooManyEmailChangeException;
use App\Domain\Profile\ProfileService;
use App\Http\Form\UpdatePasswordForm;
use App\Http\Form\UpdateProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private EntityManagerInterface $em;

    private ProfileService $profileService;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        ProfileService $profileService
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->profileService = $profileService;
    }

    /**
     * @Route("/profil/edit", name="user_edit")
     * @IsGranted("ROLE_USER")
     */
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

        return $this->render('account/edit.html.twig', [
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
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
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
        } catch (TooManyEmailChangeException $e) {
            $this->addFlash('error', "Vous avez déjà un changement d'email en cours.");
        }

        return [$form, null];
    }
}

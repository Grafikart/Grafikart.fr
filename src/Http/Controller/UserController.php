<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\History\HistoryService;
use App\Domain\Profile\DeleteAccountService;
use App\Domain\Profile\Dto\AvatarDto;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\ProfileService;
use App\Http\Form\UpdatePasswordForm;
use App\Http\Form\UpdateProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method getUser() User
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profil", name="user_profil", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(HistoryService $service, TopicRepository $topicRepository): Response
    {
        $user = $this->getUser();
        $watchlist = $service->getLastWatchedContent($user);
        $lastTopics = $topicRepository->findLastByUser($user);
        $lastMessageTopics = $topicRepository->findLastWithUser($user);

        return $this->render('profil/profil.html.twig', [
            'watchlist' => $watchlist,
            'lastTopics' => $lastTopics,
            'lastMessageTopics' => $lastMessageTopics,
        ]);
    }

    /**
     * @Route("/profil/edit", name="user_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ProfileService $service,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        // On crée les formulaires
        $formPassword = $this->createForm(UpdatePasswordForm::class);
        $formUpdate = $this->createForm(UpdateProfileForm::class, new ProfileUpdateDto($user));
        $action = $request->get('action');
        if ('update' === $action) {
            $formUpdate->handleRequest($request);
        } elseif ('password' === $action) {
            $formPassword->handleRequest($request);
        }
        // Traitement du mot de passe
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $data = $formPassword->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');

            return $this->redirectToRoute('user_edit');
        }
        // Traitement de la mise à jour de profil
        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $data = $formUpdate->getData();
            $service->updateProfile($data);
            $em->flush();
            if ($user->getEmail() !== $data->email) {
                $this->addFlash(
                    'success',
                    "Votre profil a bien été mis à jour, un email a été envoyé à {$data->email} pour confirmer votre changement"
                );
            } else {
                $this->addFlash('success', 'Votre profil a bien été mis à jour');
            }

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('profil/edit.html.twig', [
            'form_password' => $formPassword->createView(),
            'form_update' => $formUpdate->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profil/avatar", name="user_avatar", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function avatar(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        ProfileService $service
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $data = new AvatarDto($request->files->get('avatar'), $user);
        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            $this->addFlash('error', (string) $errors->get(0)->getMessage());
        } else {
            $service->updateAvatar($data);
            $em->flush();
            $this->addFlash('success', 'Avatar mis à jour avec succès');
        }

        return $this->redirectToRoute('user_edit');
    }

    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show(User $user): Response
    {
        return new Response('Hello');
    }

    /**
     * @Route("/profil", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(DeleteAccountService $service, Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        if (!$this->isCsrfTokenValid('delete-account', $data['csrf'] ?? '')) {
            return new JsonResponse([
                'title' => 'Token CSRF invalide',
            ], Response::HTTP_BAD_REQUEST);
        }
        if (!$passwordEncoder->isPasswordValid($user, $data['password'] ?? '')) {
            return new JsonResponse([
                'title' => 'Impossible de supprimer le compte, mot de passe invalide',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $service->deleteUser($user, $request);

        return new JsonResponse([
            'message' => 'Votre demande de suppression de compte a bien été prise en compte. Votre compte sera supprimé automatiquement au bout de '.DeleteAccountService::DAYS.' jours',
        ]);
    }

    /**
     * @Route("/profil/cancel-delete", methods={"POST"}, name="user_cancel_delete")
     * @IsGranted("ROLE_USER")
     */
    public function cancelDelete(EntityManagerInterface $em): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setDeleteAt(null);
        $em->flush();
        $this->addFlash('success', 'La suppression de votre compte a bien été annulée');

        return $this->redirectToRoute('user_edit');
    }
}

<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\History\HistoryService;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\ProfileService;
use App\Http\Form\UpdatePasswordForm;
use App\Http\Form\UpdateProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method getUser() User
 */
class UserController extends AbstractController
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
     * @Route("/profil", name="user_profil", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(
        HistoryService $history,
        TopicRepository $topicRepository
    ): Response {
        $user = $this->getUser();
        $watchlist = $history->getLastWatchedContent($user);
        $lastTopics = $topicRepository->findLastByUser($user);
        $lastMessageTopics = $topicRepository->findLastWithUser($user);

        return $this->render('profil/profil.html.twig', [
            'watchlist' => $watchlist,
            'lastTopics' => $lastTopics,
            'lastMessageTopics' => $lastMessageTopics,
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show(User $user): Response
    {
        return new Response('Hello');
    }

    /**
     * @Route("/profil/edit", name="user_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(
        Request $request,
        EntityManagerInterface $em
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

        return $this->render('profil/edit.html.twig', [
            'form_password' => $formPassword->createView(),
            'form_update' => $formUpdate->createView(),
            'user' => $user,
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

        $user = $this->getUser();
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
        $user = $this->getUser();
        $form = $this->createForm(UpdateProfileForm::class, new ProfileUpdateDto($user));
        if ('update' !== $request->get('action')) {
            return [$form, null];
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->profileService->updateProfile($data);
            $this->em->flush();
            if ($user->getEmail() !== $data->email) {
                $this->addFlash(
                    'success',
                    "Votre profil a bien été mis à jour, un email a été envoyé à {$data->email} pour confirmer votre changement"
                );
            } else {
                $this->addFlash('success', 'Votre profil a bien été mis à jour');
            }

            return [$form, $this->redirectToRoute('user_edit')];
        }

        return [$form, null];
    }
}

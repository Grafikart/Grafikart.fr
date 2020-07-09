<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Profile\Dto\AvatarDto;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name="user_edit")
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
            $service->updateProfile($data, $em);
            $em->flush();
            if ($user->getEmail() !== $data->email) {
                $this->addFlash('success', "Votre profil a bien été mis à jour, un email a été envoyé à {$data->email} pour confirmer votre changement");
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
}

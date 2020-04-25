<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Profile\Dto\AvatarDto;
use App\Domain\Profile\ProfileService;
use App\Http\Form\UpdatePasswordForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/profil", name="user_edit")
     */
    public function edit(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em
    ): Response {
        $formPassword = $this->createForm(UpdatePasswordForm::class);
        $formPassword->handleRequest($request);
        /** @var User $user */
        $user = $this->getUser();
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $data = $formPassword->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');
            return $this->redirectToRoute('user_edit');
        }
        return $this->render('profil/edit.html.twig', [
            'form_password' => $formPassword->createView(),
            'user'          => $user
        ]);
    }

    /**
     * @Route("/profil/avatar", name="user_avatar", methods={"POST"})
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
            $this->addFlash('error', (string)$errors->get(0)->getMessage());
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
    function show(User $user): Response
    {
        return new Response('Hello');
    }

}

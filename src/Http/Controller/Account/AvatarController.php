<?php

namespace App\Http\Controller\Account;

use App\Domain\Auth\User;
use App\Domain\Profile\Dto\AvatarDto;
use App\Domain\Profile\ProfileService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Gère le changement d'avatar de l'utilisateur.
 */
class AvatarController extends AbstractController
{
    /**
     * @Route("/profil/avatar", name="user_avatar", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function avatar(
        Request $request,
        ValidatorInterface $validator,
        ProfileService $profileService,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $data = new AvatarDto($request->files->get('avatar'), $user);
        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            $this->addFlash('error', (string) $errors->get(0)->getMessage());
        } else {
            $profileService->updateAvatar($data);
            $em->flush();
            $this->addFlash('success', 'Avatar mis à jour avec succès');
        }

        return $this->redirectToRoute('user_edit');
    }
}

<?php

namespace App\Http\Controller\Account;

use App\Domain\Auth\User;
use App\Domain\Profile\DeleteAccountService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountDeletionController extends AbstractController
{
    /**
     * @Route("/profil", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(
        DeleteAccountService $service,
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request
    ): JsonResponse {
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

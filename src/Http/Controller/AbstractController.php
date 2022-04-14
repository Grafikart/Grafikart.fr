<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @method \App\Domain\Auth\User|null getUser()
 */
abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * Affiche la liste de erreurs sous forme de message flash.
     */
    protected function flashErrors(FormInterface $form): void
    {
        /** @var FormError[] $errors */
        $errors = $form->getErrors();
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        $this->addFlash('error', implode("\n", $messages));
    }

    protected function getUserOrThrow(): User
    {
        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    /**
     * Redirige l'utilisateur vers la page précédente ou la route en cas de fallback.
     */
    protected function redirectBack(Request $request, string $route, array $params = []): RedirectResponse
    {
        if ($request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->redirectToRoute($route, $params);
    }
}

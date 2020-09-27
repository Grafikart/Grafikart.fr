<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Messenger\Envelope;
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

    /**
     * Lance la méthode d'un service de manière asynchrone.
     */
    protected function dispatchMethod(string $service, string $method, array $params = []): Envelope
    {
        return $this->dispatchMessage(new ServiceMethodMessage($service, $method, $params));
    }

    protected function getUserOrThrow(): User
    {
        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}

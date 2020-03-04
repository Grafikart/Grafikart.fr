<?php

namespace App\Http\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * @method getUser() App\Domain\Auth\User
 */
abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * Affiche la liste de erreurs sous forme de message flash
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

}

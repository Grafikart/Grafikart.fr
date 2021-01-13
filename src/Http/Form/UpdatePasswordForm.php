<?php

namespace App\Http\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 6,
                    'max' => 4096,
                ]),
            ],
            'first_options' => ['label' => false, 'attr' => ['placeholder' => 'Nouveau mot de passe']],
            'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Confirmer le mot de passe']],
        ]);
    }
}

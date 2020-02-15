<?php

namespace App\Domain\Auth\Form;

use App\Domain\Auth\Data\PasswordResetConfirmData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetConfirmForm extends AbstractType
{

    /**
     * @param FormBuilderInterface<FormBuilderInterface> $builder
     * @param array<string,mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PasswordResetConfirmData::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

}

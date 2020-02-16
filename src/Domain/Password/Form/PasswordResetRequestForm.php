<?php

namespace App\Domain\Password\Form;

use App\Domain\Password\Data\PasswordResetRequestData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetRequestForm extends AbstractType
{

    /**
     * @param FormBuilderInterface<FormBuilderInterface> $builder
     * @param array<string,mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PasswordResetRequestData::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

}

<?php

namespace App\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchType extends CheckboxType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'attr' => [
                'is' => 'input-switch',
            ],
            'row_attr' => [
                'class' => 'form-switch'
            ]
        ]);
    }

}

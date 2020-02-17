<?php

namespace App\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchType extends CheckboxType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'attr' => [
                'is' => 'input-switch'
            ]
        ]);
    }

}

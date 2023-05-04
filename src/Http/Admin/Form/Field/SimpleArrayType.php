<?php

namespace App\Http\Admin\Form\Field;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SimpleArrayType extends TextType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->addModelTransformer(new CallbackTransformer(
            function ($tagsAsArray): string {
                return implode(',', $tagsAsArray);
            },
            function ($tagsAsString): array {
                return explode(',', $tagsAsString);
            }
        ));
    }
}

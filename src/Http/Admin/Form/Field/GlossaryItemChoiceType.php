<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Glossary\Entity\GlossaryItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlossaryItemChoiceType extends EntityType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'class' => GlossaryItem::class,
            'multiple' => false,
            'choice_label' => 'name',
        ]);
    }
}

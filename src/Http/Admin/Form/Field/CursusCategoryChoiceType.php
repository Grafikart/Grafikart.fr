<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Course\Entity\CursusCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursusCategoryChoiceType extends EntityType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'class' => CursusCategory::class,
            'multiple' => false,
            'choice_label' => 'name',
        ]);
    }
}

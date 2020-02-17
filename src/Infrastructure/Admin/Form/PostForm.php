<?php

namespace App\Infrastructure\Admin\Form;

use App\Infrastructure\Admin\Data\PostData;
use App\Type\DateTimeType;
use App\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('slug', TextType::class)
            ->add('image', FileType::class, [
                'required' => false
            ])
            ->add('createdAt', DateTimeType::class)
            ->add('online', SwitchType::class)
            ->add('author', TextType::class)
            ->add('content', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostData::class,
        ]);
    }

}

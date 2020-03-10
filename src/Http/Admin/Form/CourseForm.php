<?php

namespace App\Http\Admin\Form;

use App\Domain\Attachment\Type\AttachmentType;
use App\Http\Admin\Data\CourseCrudData;
use App\Http\Admin\Field\UserChoiceType;
use App\Type\DateTimeType;
use App\Type\EditorType;
use App\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('author', UserChoiceType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('online', SwitchType::class)
            ->add('source', SwitchType::class)
            ->add('premium', SwitchType::class)
            ->add('image', AttachmentType::class)
            ->add('videoPath', TextType::class, [
                'required' => false
            ])
            ->add('deprecatedBy', NumberType::class, [
                'required' => false
            ])
            ->add('demo', TextType::class, [
                'required' => false
            ])
            ->add('content', EditorType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourseCrudData::class,
        ]);
    }

}

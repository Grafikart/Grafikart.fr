<?php

namespace App\Http\Admin\Form;

use App\Core\Type\DateTimeType;
use App\Core\Type\EditorType;
use App\Core\Type\SwitchType;
use App\Domain\Attachment\Type\AttachmentType;
use App\Domain\Blog\Category;
use App\Http\Admin\Data\PostCrudData;
use App\Http\Admin\Field\UserChoiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostForm extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('image', AttachmentType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('author', UserChoiceType::class)
            ->add('content', EditorType::class)
            ->add('online', SwitchType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostCrudData::class,
        ]);
    }
}

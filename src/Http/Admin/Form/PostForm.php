<?php

namespace App\Http\Admin\Form;

use App\Domain\Attachment\Type\AttachmentType;
use App\Http\Admin\Data\PostCrudData;
use App\Http\Admin\Field\Lol;
use App\Http\Admin\Field\UserChoiceType;
use App\Type\DateTimeType;
use App\Type\SwitchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('slug', TextType::class)
            ->add('image', AttachmentType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('online', SwitchType::class)
            ->add('author', UserChoiceType::class)
            ->add('content', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostCrudData::class,
        ]);
    }

}

<?php

namespace App\Http\Form;

use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Repository\TagRepository;
use App\Http\Type\EditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumTopicForm extends AbstractType
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tags = $this->tagRepository->findAllOrdered();
        $builder
            ->add('name', TextType::class)
            ->add('tags', EntityType::class, [
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'data-limit' => 3,
                ],
                'class' => Tag::class,
                'choices' => $tags,
                'query_builder' => null,
                'choice_label' => function (Tag $tag) {
                    $prefix = $tag->getParent() ? '⠀⠀' : '';

                    return $prefix.$tag->getName();
                },
            ])
            ->add('content', EditorType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}

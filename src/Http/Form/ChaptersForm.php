<?php

namespace App\Http\Form;

use App\Domain\Course\Entity\Chapter;
use App\Domain\Course\Entity\Course;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChaptersForm extends TextareaType implements DataTransformerInterface
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'html5' => false,
            'label' => false,
            'attr' => [
                'is' => 'chapters-editor'
            ]
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
        parent::buildForm($builder, $options);
    }

    /**
     * @param Chapter[] $value
     */
    public function transform($value): string
    {
        return json_encode(collect($value)->map(function (Chapter $chapter) {
            return [
                'title' => $chapter->getTitle(),
                'courses' => collect($chapter->getCourses())->map(function (Course $course) {
                    return [
                        'title' => $course->getTitle(),
                        'id' => $course->getId()
                    ];
                })
            ];
        })->toArray()) ?: '';
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        // TODO: Implement reverseTransform() method.
    }
}

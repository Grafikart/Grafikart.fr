<?php

namespace App\Http\Form;

use App\Domain\Course\Entity\Chapter;
use App\Domain\Course\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Champ permettant l'administration des chapitres sous forme de liste drag'n drop.
 *
 * Le champ se base sur un textarea qui représente les chapitres sous forme de JSON
 *
 * ```json
 * [
 *   {
 *     "title": "Introduction",
 *     "courses": [
 *       {
 *         "title": "Introduction et configuration",
 *         "id": 1213
 *       },
 *       {
 *         "title": "Tester avec une base de données",
 *         "id": 1214
 *       }
 *     ]
 *   },
 *   ....
 * ]
 * ```
 */
class ChaptersForm extends TextareaType implements DataTransformerInterface
{

    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $em;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em)
    {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'html5' => false,
            'label' => false,
            'attr'  => [
                'endpoint'      => $this->urlGenerator->generate('admin_course_title', ['id' => ':id']),
                'endpoint-edit' => $this->urlGenerator->generate('admin_course_edit', ['id' => ':id']),
                'is'            => 'chapters-editor'
            ]
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
        parent::buildForm($builder, $options);
    }

    /**
     * Transforme un tableau de chapitre en JSON
     *
     * @param Chapter[] $value
     */
    public function transform($value): string
    {
        return json_encode(collect($value)->map(function (Chapter $chapter) {
            return [
                'title'   => $chapter->getTitle(),
                'courses' => collect($chapter->getCourses())->map(function (Course $course) {
                    return [
                        'title' => $course->getTitle(),
                        'id'    => $course->getId()
                    ];
                })
            ];
        })->toArray()) ?: '';
    }

    /**
     * Transforme un JSON en tableau de chapitres
     *
     * @param string $value
     * @return Chapter[]
     */
    public function reverseTransform($value): array
    {
        $chapters = json_decode($value, true);
        if ($chapters === null) {
            throw new \RuntimeException('Impossible de parser le JSON des chapitres : ' . $value);
        }
        return array_map(function ($chapter) {
            $courses = array_map(function ($course) {
                return $this->em->getReference(Course::class, $course['id']);
            }, $chapter['courses']);
            return (new Chapter())
                ->setTitle($chapter['title'])
                ->setCourses($courses);
        }, $chapters);
    }
}

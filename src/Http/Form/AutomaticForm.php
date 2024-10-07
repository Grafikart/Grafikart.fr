<?php

namespace App\Http\Form;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\Type\AttachmentType;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\CursusCategory;
use App\Domain\Course\Entity\Formation;
use App\Domain\Forum\Entity\Tag;
use App\Http\Admin\Form\Field\CursusCategoryChoiceType;
use App\Http\Admin\Form\Field\ForumTagChoiceType;
use App\Http\Admin\Form\Field\IntervenantsType;
use App\Http\Admin\Form\Field\TechnologiesType;
use App\Http\Admin\Form\Field\TechnologyChoiceType;
use App\Http\Admin\Form\Field\UserChoiceType;
use App\Http\Type\DateTimeType;
use App\Http\Type\EditorType;
use App\Http\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Génère un formulaire de manière automatique en lisant les propriété d'un objet.
 */
class AutomaticForm extends AbstractType
{
    final public const TYPES = [
        'string' => TextType::class,
        'bool' => SwitchType::class,
        'int' => NumberType::class,
        'float' => NumberType::class,
        Attachment::class => AttachmentType::class,
        User::class => UserChoiceType::class,
        Tag::class => ForumTagChoiceType::class,
        \DateTimeInterface::class => DateTimeType::class,
        UploadedFile::class => FileType::class,
        CursusCategory::class => CursusCategoryChoiceType::class,
    ];

    final public const NAMES = [
        'content' => EditorType::class,
        'description' => TextareaType::class,
        'short' => TextareaType::class,
        'mainTechnologies' => TechnologiesType::class,
        'secondaryTechnologies' => TechnologiesType::class,
        'chapters' => ChaptersForm::class,
        'color' => ColorType::class,
        'level' => ChoiceType::class,
        'links' => TextareaType::class,
        'requirements' => TechnologyChoiceType::class,
        'intervenants' => IntervenantsType::class,
        'emailMessage' => TextareaType::class,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];
        $refClass = new \ReflectionClass($data);
        $classProperties = $refClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($classProperties as $property) {
            $name = $property->getName();
            /** @var \ReflectionNamedType|null $type */
            $type = $property->getType();
            if (null === $type) {
                return;
            }
            $typeName = $type->getName();
            $extra = $this->getExtraProperties($typeName, $name);
            // Input spécifique au nom du champ
            if (array_key_exists($name, self::NAMES)) {
                $builder->add($name, self::NAMES[$name], [
                    'required' => false,
                    ...$extra
                ]);
            } elseif (array_key_exists($typeName, self::TYPES)) {
                $builder->add($name, self::TYPES[$typeName], [
                    'required' => !$type->allowsNull() && 'bool' !== $typeName,
                    ...$extra
                ]);
            } else {
                throw new \RuntimeException(sprintf('Impossible de trouver le champs associé au type %s dans %s::%s', $typeName, $data::class, $name));
            }
        }
    }

    private function getExtraProperties(string $type, string $name): array
    {
        if ($type === \DateTimeInterface::class) {
            return [
                'input' => "datetime_immutable"
            ];
        }
        if ('requirements' === $name) {
            return ['multiple' => true];
        }
        if ('level' === $name) {
            return [
                'choices' => array_flip(Formation::$levels),
                'required' => true
            ];
        }
        return [];


    }
}

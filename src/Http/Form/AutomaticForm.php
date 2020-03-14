<?php

namespace App\Http\Form;

use App\Core\Type\DateTimeType;
use App\Core\Type\EditorType;
use App\Core\Type\SwitchType;
use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\Type\AttachmentType;
use App\Domain\Auth\User;
use App\Domain\Course\Type\TechnologiesType;
use App\Http\Admin\Field\UserChoiceType;
use DateTimeInterface;
use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Génère un formulaire de manière automatique en lisant les propriété d'un objet
 */
class AutomaticForm extends AbstractType
{

    const TYPES = [
        'string'                 => TextType::class,
        'bool'                   => SwitchType::class,
        'int'                    => NumberType::class,
        Attachment::class        => AttachmentType::class,
        User::class              => UserChoiceType::class,
        DateTimeInterface::class => DateTimeType::class,
        UploadedFile::class      => FileType::class,
    ];

    const NAMES = [
        'content' => EditorType::class,
        'short' => TextareaType::class,
        'mainTechnologies' => TechnologiesType::class,
        'secondaryTechnologies' => TechnologiesType::class,
        'chapters' => ChaptersForm::class
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];
        $refClass = new ReflectionClass(get_class($data));
        $classProperties = $refClass->getProperties();
        $dataProperties = get_object_vars($options['data']);
        foreach ($classProperties as $property) {
            $name = $property->getName();
            if (array_key_exists($name, $dataProperties)) {
                /** @var \ReflectionNamedType $type */
                $type = $property->getType();
                if (array_key_exists($name, self::NAMES)) {
                    $builder->add($name, self::NAMES[$name]);
                } else if (array_key_exists($type->getName(), self::TYPES)) {
                    $builder->add($name, self::TYPES[$type->getName()], [
                        'required' => !$type->allowsNull() && $type->getName() !== 'bool'
                    ]);
                } else {
                    throw new \RuntimeException(sprintf(
                        'Impossible de trouver le champs associé au type %s dans %s::%s',
                        $type->getName(),
                        get_class($data),
                        $name
                    ));
                }
            }
        }
    }

}

<?php

namespace App\Http\Form;

use App\Domain\School\DTO\SchoolImportDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolImportForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Fichier CSV',
            ])
            ->add('emailSubject', TextType::class, [
                'label' => "Sujet de l'email"
            ])
            ->add('emailMessage', TextareaType::class, [
                'label' => 'Messsage',
                'attr' => [
                    'placeholder' => 'Message envoyé avec leur code'
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Importer', 'attr' => ['class' => 'btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolImportDTO::class,
        ]);
    }

}

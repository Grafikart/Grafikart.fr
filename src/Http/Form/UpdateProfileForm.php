<?php

namespace App\Http\Form;

use App\Domain\Profile\Dto\ProfileUpdateDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method ProfileUpdateDto getData()
 */
class UpdateProfileForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('email', EmailType::class, [
            'required'       => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileUpdateDto::class
        ]);
    }

}

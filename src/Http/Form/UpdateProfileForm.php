<?php

namespace App\Http\Form;

use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Http\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'required' => true,
            ])
            ->add('username', TextType::class, [
                'required' => true,
            ])
            ->add('country', CountryType::class, [
                'required' => true,
            ])
            ->add('forumNotification', SwitchType::class, [
                'required' => false,
            ])
            ->add('useSystemTheme', SwitchType::class, [
                'required' => false,
            ])
            ->add('useDarkTheme', SwitchType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileUpdateDto::class,
        ]);
    }
}

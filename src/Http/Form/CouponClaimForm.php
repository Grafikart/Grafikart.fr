<?php

namespace App\Http\Form;

use App\Domain\Coupon\DTO\CouponClaimDTO;
use App\Domain\Coupon\Repository\CouponRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method CouponClaimDTO getData()
 */
class CouponClaimForm extends AbstractType
{

    public function __construct(private readonly CouponRepository $couponRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Utiliser le code'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CouponClaimDTO::class
        ]);
    }
}

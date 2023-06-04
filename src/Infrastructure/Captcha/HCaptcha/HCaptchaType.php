<?php

namespace App\Infrastructure\Captcha\HCaptcha;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HCaptchaType extends AbstractType
{
    public function __construct(private readonly string $apiKey)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['hcaptcha_site_key'] = $options['hcaptcha_site_key'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'empty_data' => null,
            'mapped' => false,
            'constraints' => [
                new IsValidHCaptcha(),
            ],
        ]);

        $resolver->setDefault('hcaptcha_site_key', $this->apiKey);
        $resolver->setRequired('hcaptcha_site_key');
    }

    public function getParent(): ?string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'hcaptcha';
    }
}

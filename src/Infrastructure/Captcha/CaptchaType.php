<?php

namespace App\Infrastructure\Captcha;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaptchaType extends AbstractType
{
    public function __construct(private readonly CaptchaKeyService $captchaService)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $this->captchaService->generateKey();
        $view->vars['captcha_width'] = CaptchaKeyService::CAPTCHA_WIDTH;
        $view->vars['captcha_height'] = CaptchaKeyService::CAPTCHA_HEIGHT;
        $view->vars['captcha_piece_width'] = CaptchaKeyService::CAPTCHA_PIECE_WIDTH;
        $view->vars['captcha_piece_height'] = CaptchaKeyService::CAPTCHA_PIECE_HEIGHT;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'empty_data' => null,
            'mapped' => false,
            'constraints' => [
                new IsValidCaptcha(),
            ],
        ]);
    }

    public function getParent(): ?string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'captcha';
    }
}

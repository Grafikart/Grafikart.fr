<?php

namespace App\Core\Twig;

use App\Domain\Auth\User;
use App\Infrastructure\Payment\VatService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPriceExtension extends AbstractExtension
{

    private VatService $vatService;

    public function __construct(VatService $vatService)
    {
        $this->vatService = $vatService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('vat_with_suffix', [$this, 'vatWithSuffix'], ['is_safe' => ['html']])
        ];
    }

    public function vatWithSuffix(float $price, ?User $user): string
    {
        $vat = $this->vatService->vat($user);
        $vatPrice = $this->vatService->vatPrice($price, $user);
        if ($vat > 0) {
            $suffix = '<sup>€ TTC</sup>';
        } else {
            $suffix = '<sup>€</sup>';
        }
        return $vatPrice . $suffix;
    }

}

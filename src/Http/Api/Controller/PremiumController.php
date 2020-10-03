<?php declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Domain\Premium\Entity\Plan;
use App\Domain\Premium\PremiumService;
use App\Domain\Premium\Repository\PlanRepository;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Payment\Exception\PaymentFailedException;
use App\Infrastructure\Payment\Paypal\PaypalPayment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PremiumController extends AbstractController
{
    /**
     * @Route("/premium/paypal/{orderId}", name="premium_paypal")
     * @IsGranted("ROLE_USER")
     */
    public function paypal(string $orderId, PaypalPayment $paypal, PlanRepository $planRepository, PremiumService $premiumService): JsonResponse
    {
        try {
            $payment = $paypal->createPayment($orderId);
            /** @var Plan $plan */
            $plan = $planRepository->find($payment->planId);
            if ($plan->getPrice() !== $payment->amount) {
                return $this->json(['title' => 'Montant invalide', 'detail' => 'Le montant de votre paiement ne correspond pas au montant de la formule choisie'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $payment = $paypal->capture($payment);
            $premiumService->recordPayment($payment);
            return $this->json([]);
        } catch (PaymentFailedException $e) {
            return $this->json(['title' => 'Erreur lors du paiement', 'detail' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}

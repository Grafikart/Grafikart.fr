<?php

namespace App\Http\Controller;

use Symfony\Component\Routing\Annotation\Route;

class PremiumController extends AbstractController
{

    /**
     * @Route("/premium", name="premium")
     */
    public function premium () {
        return $this->render("pages/premium.html.twig");
    }

}

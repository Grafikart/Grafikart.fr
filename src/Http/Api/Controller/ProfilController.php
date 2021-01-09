<?php

namespace App\Http\Api\Controller;

use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * Permet le changement de thème pour l'utilisateur.
     *
     * @Route("/profil/theme", name="profil_theme", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function theme(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode((string) $request->getContent(), true);
        $theme = $data['theme'] ?? null;
        if (!in_array($theme, ['light', 'dark'])) {
            return $this->json(['title' => "Ce thème n'est pas supporté"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->getUserOrThrow()->setTheme($theme);
        $em->flush();

        return $this->json(null);
    }
}

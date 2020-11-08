<?php

namespace App\Http\Controller;

use App\Http\Security\CourseVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller utilisé par nginx pour le auth_request
 * Plus d'infos : http://nginx.org/en/docs/http/ngx_http_auth_request_module.html.
 */
class NginxAuthController extends AbstractController
{
    /**
     * @Route("/stream/{video<.*>}", name="stream_video")
     */
    public function check(): Response
    {
        if ($this->isGranted(CourseVoter::STREAM_VIDEO)) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_FORBIDDEN);
    }
}

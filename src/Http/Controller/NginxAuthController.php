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
     * @Route("/downloads/videos/{video<.*>}", name="stream_video")
     */
    public function videos(): Response
    {
        if ($this->isGranted(CourseVoter::DOWNLOAD_VIDEO)) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_FORBIDDEN);
    }

    /**
     * @Route("/downloads/sources/{source<.*>}", name="download_source")
     */
    public function sources(): Response
    {
        if ($this->isGranted(CourseVoter::DOWNLOAD_SOURCE)) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_FORBIDDEN);
    }

    /**
     * @Route("/report.html", name="report_stats")
     */
    public function report(): Response
    {
        if ($this->isGranted('admin')) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_FORBIDDEN);
    }

    /**
     * @Route("/goaccessws", name="report_ws")
     */
    public function goaccess(): Response
    {
        return $this->report();
    }
}

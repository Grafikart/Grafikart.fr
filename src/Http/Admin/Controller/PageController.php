<?php

namespace App\Http\Admin\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageController extends BaseController
{

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

}

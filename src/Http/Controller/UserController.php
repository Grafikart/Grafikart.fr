<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("/user/{id}", name="user_show")
     */
    function show(User $user): Response
    {
        return new Response('Hello');
    }

}

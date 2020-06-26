<?php

namespace App\Controller\Front;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user)
    {

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * @Route("/userPresta/{slug}", name="presta_show")
     */
    public function presta(User $user)
    {

        return $this->render('user/presta.html.twig', [
            'user' => $user,
        ]);
    }
}

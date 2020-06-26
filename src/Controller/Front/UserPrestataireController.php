<?php

namespace App\Controller\Front;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserPrestataireController extends AbstractController
{
    /**
     * @Route("presta/user/{slug}", name="presta_user_show")
     */
    public function index(User $user)
    {

        return $this->render('presta/user/index.html.twig', [
            'user' => $user,
        ]);
    }
}

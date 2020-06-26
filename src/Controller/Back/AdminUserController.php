<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user/{page<\d+>?1}", name="admin_user_index")
     */
    public function index(UserRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                   ->setLimit(8)
                   ->setPage($page);
        
       // $users = $repo->findAll();

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/userIndex1/{page<\d+>?1}", name="admin_user_index1")
     */
    public function index1(UserRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                   ->setLimit(8)
                   ->setPage($page);
        
       // $users = $repo->findAll();

        return $this->render('admin/user/index1.html.twig', [
            'pagination' => $pagination
        ]);
    }

     /**
     * @Route("/admin/userPresta/{page<\d+>?1}", name="admin_user_presta")
     */
    public function presta(UserRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                   ->setPage($page);
        
       // $users = $repo->findAll();

        return $this->render('admin/user/presta.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/userClient/{page<\d+>?1}", name="admin_user_client")
     */
    public function client(UserRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                   ->setLimit(8)
                   ->setPage($page);
        
       // $users = $repo->findAll();

        return $this->render('admin/user/client.html.twig', [
            'pagination' => $pagination
        ]);
    }

    

    /**
     * @Route("/admin/userAdmin/{page<\d+>?1}", name="admin_user_admin")
     */
    public function admin(UserRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                   ->setLimit(8)
                   ->setPage($page);
        
       // $users = $repo->findAll();

        return $this->render('admin/user/admin.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier un user
     *
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * 
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager) {

        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Le user numéro {$user->getId()} a bien été modifié !"
            );
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un user
     * 
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $entityManager) {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "Le user de {$user->getFullName()} a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_user_index');
    }
}

<?php

namespace App\Controller\Back;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\PaginationService;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{   /**
     * @Route("/admin/categories/{page<\d+>?1}", name="admin_category_index")
     */
    public function index(CategoryRepository $repo, $page, PaginationService $pagination)
    {

        $pagination->setEntityClass(Category::class)
                   ->setPage($page);

        return $this->render('admin/category/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }


    /**
     * Permet de créer une catégory
     *
     * @Route("/admin/categories/new", name="admin_category_create")
     * 
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager){
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash(
                'succes',
                "La catégorie <strong> {$category->getTitle()} </strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('admin_category_show', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Permet d'éditer une catégorie
     *
     * @Route("/admin/categories/{id}/edit", name="admin_category_edit")
     * 
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager){

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "La catégorie <strong> {$category->getTitle()}</strong> a bien été modifiée !"
            );

            return $this->redirectToRoute('admin_category_show', [
                'id' => $category->getId()
            ]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de voir une seule catégorie
     *
     * @Route("/admin/categories/{id}", name="admin_category_show")
     * 
     * @param Category $category
     * @return Response
     */
    public function show(Category $category){
        return $this->render('admin/category/show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * Permet de supprimer une catégorie
     *
     *  @Route("/admin/categories/{id}/delete", name="admin_category_delete")
     * 
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Category $category, Request $request, EntityManagerInterface $entityManager){
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash(
            'success'
            ,"La catégorie <strong> {$category->getTitle()} </strong> a bien été supprimée !"
        );

        return $this->redirectToRoute('admin_category_index');
    }
}



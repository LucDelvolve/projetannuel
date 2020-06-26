<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories/{page<\d+>?1}", name="category_index")
     */
    public function index(CategoryRepository $repo, $page, PaginationService $pagination)
    {

        $pagination->setEntityClass(Category::class)
                   ->setPage($page);

        return $this->render('category/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Permet de créer une catégory
     *
     * @Route("/categories/new", name="category_create")
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

            return $this->redirectToRoute('category_show', [
                'slug' => $category->getSlug(),
            ]);
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Permet d'éditer une catégorie
     *
     * @Route("/categories/{slug}/edit", name="category_edit")
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
                "La catégorie <strong> {$category->getTitle()} a bien été modifiée !"
            );

            return $this->redirectToRoute('category_show', [
                'slug' => $category->getSlug()
            ]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de voir une seule catégorie
     *
     * @Route("/categories/{slug}", name="category_show")
     * 
     * @param Category $category
     * @return Response
     */
    public function show(Category $category){
        return $this->render('category/show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * Permet de supprimer une catégorie
     *
     *  @Route("/categories/{slug}/delete", name="category_delete")
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

        return $this->redirectToRoute('category_index');
    }
}

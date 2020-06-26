<?php

namespace App\Controller\Presta;

use App\Entity;
use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrestaAdController extends AbstractController
{

    
    
    
    /**
     * Permet de créer une annonce
     * 
     * @Route("presta/ads/new", name="presta_ads_create")
     * @IsGranted("ROLE_PRESTA")
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager){

        $ad = new Ad;

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $entityManager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $entityManager->persist($ad);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('presta/ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("presta/ads/{slug}/edit", name="presta_ads_edit")
     * 
     * @Security("is_granted('ROLE_PRESTA') and user === ad.getauthor()", message="Cette
     *  annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $entityManager){

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $entityManager->persist($image);
            }
            $entityManager->persist($ad);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('presta/ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);

    }
    
    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("presta/ads/{slug}", name="presta_ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad,Request $request, NotificationService $notification){

        $contact = new Contact();
        $contact->setAd($ad);
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success', 'Votre demande a bien été envoyer');

        return $this->redirectToRoute('ads_show',
        ['slug' => $ad->getSlug()]);
        }
        return $this->render('presta/ad/show.html.twig',[
            'ad' => $ad,
            'contactForm' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("presta/ads/{slug}/delete", name="presta_ads_delete")
     * 
     * @Security("is_granted('ROLE_PRESTA') and user == ad.getAuthor()", message="Vous n'avez pas le droit d'accéder à cette ressource")
     * 
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Ad $ad, Request $request, EntityManagerInterface $entityManager){
        $entityManager->remove($ad);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("presta_account_index");
    }

}

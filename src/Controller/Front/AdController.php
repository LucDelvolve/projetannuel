<?php

namespace App\Controller\Front;

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

class AdController extends AbstractController
{

    
    
    // les prestataires sans pagination
    //  /**
    //  * @Route("/ads", name="ads_index")
    //  */
    // public function index(AdRepository $repo)
    // {
    //     $ads = $repo->findAll();
    //     return $this->render('ad/index.html.twig', [
    //         'ads' => $ads
    //     ]);
    // }
    
    
    /**
     * @Route("/ads/{page<\d+>?1}", name="ads_index")
     * 
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Ad::class)
                   ->setPage($page)
                   ->setLimit(21);
        
        return $this->render('ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    
   
    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
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
        return $this->render('/ad/show.html.twig',[
            'ad' => $ad,
            'contactForm' => $form->createView()
        ]);
    }

}

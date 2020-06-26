<?php

namespace App\Controller\Presta;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Form\PrestaRegistrationType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PrestaAccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("presta/login", name="presta_account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();


        return $this->render('presta/account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("presta/logout", name="presta_account_logout")
     *
     * @return void
     */
    public function logout(){

    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("presta/register", name="presta_account_register")
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $form = $this->createForm(PrestaRegistrationType::class, $user);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $user->setRoles(['ROLE_PRESTA']);
          

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter !"
            );

            return $this->redirectToRoute("presta_account_login");
        }

        return $this->render('presta/account/registration.html.twig', [
            'form' => $form->createView()
        ]);

    }


    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/presta/account/profile", name="presta_account_profile")
     * 
     * @IsGranted("ROLE_PRESTA")
     *  
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $entityManager){
        $user = $this->getUser();
       

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();
            //return $this->redirectToRoute('presta_account_index',['withAlert' => true]);
            $this->addFlash(
                'success',
                "Les données du profil ont été enregistrée avec succès !"
            );
            return $this->redirectToRoute('presta_account_index');
        }

        return $this->render('presta/account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/presta/account/password-update", name="presta_account_password")
     * 
     * @IsGranted("ROLE_PRESTA")
     * 
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager){
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();
        
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           // 1. Vérifier que le oldPassword du formulaire soit le même que le password de l'user
           if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
            // Gérer l'erreur
            $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('presta_account_index');
            }
         }
        
        return $this->render('presta/account/password.html.twig',[
            'form' =>$form->createView()
        ]);

    }

    /**
     * Permet d'afficher la liste des réservations faites par l'utilisateur
     * 
     * @Route("presta/account/bookings", name="presta_account_bookings")
     *
     * @return Response
     */
    public function bookings(){
        return $this->render('presta/account/bookings.html.twig');
    }


    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/presta/account", name="presta_account_index")
     * 
     * @IsGranted("ROLE_PRESTA")
     * 
     * @return Response
     */
    public function myAccount(){
        return $this->render('presta/user/index.html.twig', [
            'user' => $this->getUser()
        ]);

    }

   
}

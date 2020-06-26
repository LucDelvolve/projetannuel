<?php


namespace App\Controller\Front;

use App\Entity\User;
use App\Form\EmailResetType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;

use App\Form\ResetPasswordType;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * handle all requests related:
 *    - password recovery
 *    - setting up a new password
 */
class ResetPasswordController extends AbstractController
{

    /**
     * Action : handle password recovery
     * @Route("/forgotten_pass", name="forgotten_password")
     */
    public function forgottenPassword(Request $request, UserRepository $repo, \Swift_Mailer $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(EmailResetType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


        //$email = $request->request->get('email');
            $user  = $repo->findOneByEmail($form->getData()['email']);


            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu');
                return $this->redirectToRoute('homepage');
            }
            $token = uniqid();
            try {
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('homepage');
            }


            //Pour le 1er setBody
            $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);


            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('lassana@gmail.com')
                ->setTo($user->getEmail())
                /*->setBody(
                    "Cliquez sur ce lien pour redéfinir un nouveau mot de passe : " . $url,
                    'text/html'
                );*/
                  ->setBody($this->render('emails/reset.html.twig',[
                    'user' => $user,
                    'token' => $token
                ]), 'text/html');
 
            $mailer->send($message);

 
            $this->addFlash('notice', 'Mail envoyé');
 
            return $this->redirectToRoute('homepage');
        }
        return $this->render('account/forgotten_password.html.twig', [
            'form' => $form->createView(), 
            'title' => 'Recupérer mot de passe']
        );
    }
    /**
     * Action : handle setting up new password
     * @Route("/reset_pass", name="reset_password")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager, UserRepository $repo)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();
        $token = $request->query->get('token');
        if ($token !== null) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $repo->findOneByResetToken($token);
            if ($user !== null) {
                $form = $this->createForm(ResetPasswordType::class, $passwordUpdate);
                $form->handleRequest($request);
      
                if ($form->isSubmitted() && $form->isValid()) {
                    $newPassword = $passwordUpdate->getNewPassword();
                    $hash = $encoder->encodePassword($user, $newPassword);
    
                    $user->setHash($hash);
                    $entityManager->persist($user);
                    $entityManager->flush();
           
                    $this->addFlash('notice', 'Mot de passe mis à jour');
                    return $this->redirectToRoute('account_login');
                }
           
                return $this->render('account/reset_password.html.twig', ['form' => $form->createView()]);
            }
        }

        $this->addFlash('danger', 'Token Inconnu');
        return $this->redirectToRoute('homepage');
    }
}

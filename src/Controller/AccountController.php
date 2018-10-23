<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer la connexion
     * 
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $errors=$utils->getLastAuthenticationError(); // récupère les erreurs d'authentification
        $username=$utils->getLastUsername();// récupère le username de l'utilisateur qui tente de se connecter pour le réinjecter dans le formulaire

        return $this->render('account/login.html.twig',[
            'hasError'=> $errors !== null,
            'username'=> $username
        ]);
    }

    /**
     * Permet de gérer la déconnexion
     * 
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    { }

    
    /**
     * Permet d'afficher le formulaire d'inscription et de s'inscrire
     * 
     * @Route("/register", name="account_register")
     */
    public function register(ObjectManager $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user=new User();
        $form=$this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request); // fait le mapping entre les champs du formulaire avec l'entité passée au formulaire ($annonce)


        if ($form->isSubmitted() && $form->isValid()) {

            $hash=$encoder->encodePassword($user, $user->getHash()); // encode le mot de passe saisi dans le formulaire
            $user->setHash($hash); // et l'affecte à l'attribut hash
            $manager->persist($user);
            $manager->flush();
            
            // ajout d'un message flash
            $this->addFlash('success', "votre compte a bien été créé");

            // rediriger vers la route permettant ...
            return $this->redirectToRoute('account_login');

        }
        return $this->render('account/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    
       /**
     * Permet de mofifier son profil pour un utilisateur connecté
     * 
     * @Route("/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request,ObjectManager $manager)
    {
        $user=$this->getUser(); // recherche le user connecté
        $form=$this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->flush();
            $this->addFlash("success","Les modifications de votre profil ont bien été enregistrées");
            return $this->redirectToRoute('homepage');
        }
        return $this->render('account/profile.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet de modifier son mot de passe
     * @Route("/account/password-update", name="account_password")      
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(ObjectManager $manager, Request $request,UserPasswordEncoderInterface $encoder){

        $passwordUpdate=new PasswordUpdate();

        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user=$this->getUser(); // recherche le user connecté
            
            if($encoder->isPasswordValid($user,$passwordUpdate->getOldPassword()) ){ // on compare l'ancien mot de passe saisi dans le formulaire avec le mot de passe actuel
                $hashNouveau=$encoder->encodePassword($user,$passwordUpdate->getNewPassword());// on encode le nouveau mot de passe
                $user->setHash($hashNouveau) ; // on affecte le nouveau mot de passe encodé
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success',"Votre mot de passe a bien été modifié");
                return $this->redirectToRoute('homepage');
            }else{
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas le mot de passe actuel ! "));
            }
        }

        return $this->render('account/password.html.twig',[
            'form'=> $form->createView()
        ]);
    }

        /**
         * Permet d'afficher le profil de l'utilisateur connecté
         * 
         * @Route("/account", name="account_index")
         * @IsGranted("ROLE_USER")
         */
        public function myAccount(){

            $user=$this->getUser();
            return $this->render('user/index.html.twig',[
                'user'=>$user
            ]);
        }
}
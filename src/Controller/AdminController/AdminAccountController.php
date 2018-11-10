<?php

namespace App\Controller\AdminController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $errors=$utils->getLastAuthenticationError(); // récupère les erreurs d'authentification
        $username=$utils->getLastUsername();// récupère le username de l'utilisateur qui tente 
        //de se connecter pour le réinjecter dans le formulaire


        return $this->render('admin/account/login.html.twig',[
            'hasError'=> $errors !== null,
            'username'=> $username
        ]);
    }

        /**
     * Permet de gérer la déconnexion
     * 
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout()
    { }

}
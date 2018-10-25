<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonces", name="annonces_index")
     */
    public function index(AnnonceRepository $repo)
    {
        $annonces = $repo->findAll();
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route("/annonces/new", name="annonces_create")
     * @IsGranted("ROLE_USER")
     * @return void
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $annonce = new Annonce();
        
        $form = $this->createForm(AnnonceType::class, $annonce); // fabrique du formulaire

        $form->handleRequest($request); // fait le mapping entre les champs du formulaire avec l'entité passée au formulaire ($annonce)


        if ($form->isSubmitted() && $form->isValid()) {
            // on fait persister les images saisies dans le formulaire
            /*foreach ($annonce->getImages() as $image) {
                $image->setAnnonce($annonce);
                $manager->persist($image);
            }*/
            $annonce->setAuthor($this->getUser());
            
            $manager->persist($annonce);
            $manager->flush();
            
            // ajout d'un message flash
            $this->addFlash('success', "L'annonce <strong>{$annonce->getTitle()}</strong> a bien été ajoutée");

            // rediriger vers la route permettant de visualiser l'annonce
            return $this->redirectToRoute('annonces_show', [
                'slug' => $annonce->getSlug() // on passe le paramètre nécessaire à la route qui est le slug
            ]);
        }
        // on retourne la route qui affiche le formulaire en lui passant le formulaire fabriqué
        //et formaté pour être une vue avec la méthode createView()
        return $this->render('annonce/new.html.twig', [
            'formAnnonce' => $form->createView(),
            'annonce' => $annonce
        ]);
    }

    /**
     * permet d'afficher une annonce
     *
     * @Route("/annonces/{slug}", name="annonces_show")
     * @return void
     */
    public function show(Annonce $annonce)
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce
        ]);
    }

    /**
     * Permet d'afficher l'annonce en formulaire d'édition
     * 
     * @Route("/annonces/{slug}/edit", name="annonces_edit")
     * @Security("is_granted('ROLE_USER') and user === annonce.getAuthor()", message = "cette annonce ne vous appartient pas, vous ne pouvez pas la modifier ! ")
     */

    public function edit(Annonce $annonce, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AnnonceType::class, $annonce); // fabrique du formulaire

        $form->handleRequest($request); // fait le mapping entre les champs du formulaire avec l'entité passée au formulaire ($annonce)

        if ($form->isSubmitted() && $form->isValid()) {
            // on fait persister les images saisies dans le formulaire
            /*foreach ($annonce->getImages() as $image) {
                $image->setAnnonce($annonce);
                $manager->persist($image);
            }*/
            $manager->persist($annonce);
            $manager->flush();
            
            // ajout d'un message flash
            $this->addFlash('success', "Les modifications de l'annonce <strong>{$annonce->getTitle()}</strong> a bien été effectuée");

            // rediriger vers la route permettant de visualiser l'annonce
            return $this->redirectToRoute('annonces_show', [
                'slug' => $annonce->getSlug() // on passe le paramètre nécessaire à la route qui est le slug
            ]);
        }
        return $this->render(
            "annonce/edit.html.twig",
            [
                'formAnnonce' => $form->createView(),
                'annonce' => $annonce
            ]
        );
    }

        /**
     * permet de supprimer une annonce
     *
     * @Route("/annonces/{slug}/delete", name="annonces_delete")
     * @Security("is_granted('ROLE_USER') and user == annonce.getAuthor()", message="Vous n'avez pas le droit du supprimer cette annonce")
     * @return void
     */
    public function delete(Annonce $annonce,ObjectManager $manager)
    {
        $manager->remove($annonce);
        $manager->flush();
        
        // ajout d'un message flash
        $this->addFlash('success', "La suppression de l'annonce <strong>{$annonce->getTitle()}</strong> a bien été effectuée");

        return $this->redirectToRoute('annonces_index');
    }
}

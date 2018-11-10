<?php

namespace App\Controller\AdminController;

use App\Entity\Comment;
use App\Form\CommentEditType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comments_list")
     */
    public function listComments(CommentRepository $repo)
    {
        $comments=$repo->findAll();
        
        return $this->render('comment/adminListComments.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * Permet de modifier le commentaire
     *
     * @Route("/admin/comment/edit/{id}", name="admin_comment_edit")
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editComment(Comment $comment, Request $request, ObjectManager $manager ){

        $form=$this->createForm(CommentEditType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success',"Le commentaire n°".$comment->getId()." a été modifié avec succès");
            return $this->redirectToRoute('admin_comments_list');
        }

        return $this->render('comment/editComment.html.twig',[
            'formComment'=>$form->createView(),
            'comment'=>$comment
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("/admin/comment/delete/{id}", name="admin_comment_delete")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteComment(Comment $comment, ObjectManager $manager){
        $manager->remove($comment);
        $manager->flush();
        $this->addflash('success', " Le commentaire n°". $comment->getId() ." a bien été supprimé !");
        return $this->redirectToRoute('admin_comments_list');
    }
}

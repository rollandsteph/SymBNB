<?php

namespace App\Controller\AdminController;

use App\Entity\Comment;
use App\Form\CommentEditType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page}", name="admin_comments_list", requirements={"page":"\d+"})
     */
    public function listComments(PaginationService $pagination, $page=1)
    {
        $pagination	->setEntityClass(Comment::class);
        
        return $this->render('comment/adminListComments.html.twig', [
            'pagination' => $pagination
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

        dump($request->query->get('target'));
        $target="";
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

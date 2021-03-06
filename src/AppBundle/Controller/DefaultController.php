<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use AppBundle\Entity\User;
use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\AddArticleType;
use AppBundle\Form\Type\AddCommentType;

class DefaultController extends Controller
{
    /**
     * @return Response $response
     *
     * @Route("/", name="homepage")
     */
    public function showAllNewsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findAll();

        return $this->render('default/show_all_news.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @param Article $article
     * @return Response $response
     *
     * @Route("/show/{id}", name="show_article")
     */
    public function showArticleAction(Article $article, Request $request)
    {
        $form = $this->createForm(AddCommentType::class);

        return $this->render('default/show_article.html.twig', [
            'article' => $article,
            'form'  =>  $form->createView(),
            'comments' => $article->getComments()->toArray()
        ]);
    }

    /**
     * @param Request $request
     * @return Response $response
     *
     * @Route("/register", name="register_user")
     */
    public function createUserAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPlainPassword($form->get('password')->getData());
            $user->setEnabled(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->render('default/user_created.html.twig', [
                'userEmail' => $user->getEmail(),
                'userName' => $user->getUsername()
            ]);
        }

        return $this->render('default/register_custom.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return Response $response
     *
     * @Route("/new", name="new_article")
     */
    public function createArticleAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }

        $article = new Article();
        $user = $this->getUser();
        $username = $user->getUserName();

        $form = $this->createForm(AddArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title_ru = $form->get('title_ru')->getData();
            $fullText_ru = $form->get('fullText_ru')->getData();

            $article->setAuthor($user);

            $em = $this->getDoctrine()->getManager();

            $em->getConnection()->beginTransaction();
            try{
                $em->persist($article);
                $em->flush();

                if(!is_null($title_ru) && !is_null($fullText_ru)){
                    $article->setTitle($title_ru);
                    $article->setFullText($fullText_ru);
                    $article->setTranslatableLocale('ru');
                    $em->persist($article);
                    $em->flush();
                }

                $em->getConnection()->commit();
            } catch(\Exception $e){
                $em->getConnection()->rollback();
                throw $e;
            }

            return $this->render('default\article_created.html.twig', array(
                'title' => $article->getTitle()
            ));
        }

        return $this->render('default/add_article.html.twig', array(
            'form' => $form->createView(),
            'username' => $username,
        ));
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response $response
     *
     * @Route("/delete/{id}", name="delete_article")
     */
    public function deleteArticleAction(Request $request, Article $article)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->render('default/article_removed.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @param Request $request
     * @param $_locale
     *
     * @return Response $response
     *
     * @Route("/set/{_locale}", name="set_locale", requirements={"_locale": "en|ru"}, defaults={"_locale": "en"})
     */
    public function setLanguage(Request $request, $_locale)
    {
        $request->getSession()->set('_locale', $_locale);

        $backLink= $request->server->get('HTTP_REFERER');

        return $this->redirect($backLink);
    }

    /**
     * @param Article $article
     * @param Request $request
     * @return Response
     *
     * @Route("/add_comment/{id}", name="add_comment")
     * @Method("POST")
     */
    public function addCommentAction(Request $request, Article $article)
    {
        // writing comments allowed only for registered users
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $flash_trans = $this->get('translator')->trans('comments.must_login');
            $this->addFlash('notice', $flash_trans);

            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AddCommentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment = new Comment();
            $comment->setCreatedByName($this->getUser()->getUserName());
            $comment->setText($text = $form->get('text')->getData());
            $comment->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('show_article', ['id' => $article->getId()]) . '#comments');
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use AppBundle\Entity\User;
use AppBundle\Entity\Article;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\AddArticleType;

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
    public function showArticleAction(Article $article)
    {
        return $this->render('default/show_article.html.twig', [
            'article' => $article
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
            return new Response('user "' . $user->getEmail() . '" created');
        }

        return $this->render('default/register.html.twig', array(
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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $article = new Article();
        $user = $this->getUser();
        $username = $user->getUserName();

        $form = $this->createForm(AddArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

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
}

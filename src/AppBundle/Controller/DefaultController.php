<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/register")
     */
    public function createUserAction(Request $request)
    {
        $user = new User();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('task_success');
        }

        return $this->render('default/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}

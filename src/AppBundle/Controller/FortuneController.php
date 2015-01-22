<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Category;

class FortuneController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        /** @var Category[] $categories */
        $categories = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Category')
            ->findAll();

        return $this->render('fortune/homepage.html.twig',[
            'categories' => $categories
        ]);
    }
}

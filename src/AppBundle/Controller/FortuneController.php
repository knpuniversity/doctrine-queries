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

    /**
     * @Route("/category/{id}", name="category_show")
     */
    public function showCategoryAction($id)
    {
        $category = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Category')
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        return $this->render('fortune/showCategory.html.twig',[
            'category' => $category
        ]);
    }
}

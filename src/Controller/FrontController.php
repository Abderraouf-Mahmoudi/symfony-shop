<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front_home')]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    #[Route('/about', name: 'front_about')]
    public function about(): Response
    {
        return $this->render('front/about.html.twig');
    }

    #[Route('/blog', name: 'front_blog')]
    public function blog(): Response
    {
        return $this->render('front/blog.html.twig');
    }

    #[Route('/blog-detail', name: 'front_blog_detail')]
    public function blogDetail(): Response
    {
        return $this->render('front/blog-detail.html.twig');
    }

    #[Route('/contact', name: 'front_contact')]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig');
    }

    #[Route('/product', name: 'front_product')]
    public function product(): Response
    {
        return $this->render('front/product.html.twig');
    }

    #[Route('/product-detail', name: 'front_product_detail')]
    public function productDetail(): Response
    {
        return $this->render('front/product-detail.html.twig');
    }

    #[Route('/cart', name: 'front_cart')]
    public function cart(): Response
    {
        return $this->render('front/shoping-cart.html.twig');
    }
}

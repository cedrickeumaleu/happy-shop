<?php

namespace App\Controller;
use App\Manager\CartManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Order;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(OrderRepository $orderRepository, ProductRepository $productRepository, CategoryRepository $categoryRepository, CartManager $cartManager): Response
    {
        $cart = $cartManager->getCurrentCart();
        $check = $cartManager->getCurrentCart();
        $cat = $categoryRepository->findAll();
        // $check = $orderRepository->findById(28);
        // dd($check);
  

       
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/filter/{id}", name="filter")
     */
    public function filter(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository, $id)
    {

        // dd($request);

        $cat = $categoryRepository->find($id);
        $productsFilter = $cat->getProduct();
        //dd($cat);

        return $this->render('product/index.html.twig', [
            'products' => $productsFilter,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    
    
}

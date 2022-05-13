<?php

namespace App\Controller;

use App\Form\CartType;
use App\Manager\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SerendipityHQ\Bundle\StripeBundle\Model\StripeLocalPlan;
use SerendipityHQ\Component\ValueObjects\Money\Money;
use Stripe\Checkout\Session;
use Stripe\Stripe;

/**
 * Class CartController
 * @package App\Controller
 */
class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartManager $cartManager, Request $request): Response
    {
        $cart = $cartManager->getCurrentCart();


        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->save($cart);



            return $this->redirectToRoute('cart');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }



 /**
     * @Route("/create-checkout-session", name="create-checkout-session")
     */
    // public function pay(CartManager $cartManager, Request $request)
    
    // {
    //     //$cart = $cartManager->getCurrentCart();
    //     // dd($cart);
        

    //     if ($request->isMethod('POST')) {
    //         $token = $request->request->get('stripeToken');
    //         //dump($token);
    //     }

    //     // if ($request->isMethod('POST')) {
    //     //     $token = $request->request->get('stripeToken');
    //     //     \Stripe\Stripe::setApiKey("sk_test_51KYRdQHP9aJV0yaGNILf0LPbozfmsAKWFOt4hGBbXIwlXHbhr7qidDmVWMBRheNfYM0g5nzeWSxYWAh7npuniwRu00ejMrQbID");
    //     //     \Stripe\Charge::create(array(
    //     //       //"amount" => $this->get('shopping_cart')->getTotal() * 100,
    //     //       "amount" => 100,
    //     //       "currency" => "eur",
    //     //       "source" => $token,
    //     //       "description" => "First test charge!"
    //     //     ));
    //     // }

    // }



}

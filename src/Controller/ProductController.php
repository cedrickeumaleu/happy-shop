<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\AddToCartType;
use App\Manager\CartManager;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, CategoryRepository $CategoryRepository): Response

    {
        

        //$test = $productRepository->findAll();
        //  dd($cat[1]->getProduct());

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $CategoryRepository->findAll(),
        ]);

        
    }

    /**
     * @Route("/new", name="product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        // $entityManager->persist($product);
        // $entityManager->flush();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($product->getimage() !== null) {
                $file = $form->get('image')->getData();
                $fileName =  uniqid(). '.' .$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'), // Le dossier dans lequel le fichier va être charger
                        $fileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $product->setimage($fileName);

        $entityManager->persist($product);
        $entityManager->flush();


            }
            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'products' => $products,
            'form' => $form
        ]);
    }

    public function admin()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(
            []
            ,
            ['name' => 'DESC']
        );

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'users' => $users
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET", "POST"})
     */
    public function show(Product $product, Request $request, CartManager $cartManager)
    {


        $form = $this->createForm(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            

            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());

            $cartManager->save($cart);


        //  /*Enregistrer le panier dans la session pour le récupérer plus tard */
        //     $session = $request->getSession();
        //     $session->set('panier', 'rempli');
            

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $oldPicture = $product->getimage();
        // dd($oldPicture);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {

         

           // $entityManager->flush();
            if ($product->getImage() !== null && $product->getimage() !== $oldPicture) {
                $file = $form->get('image')->getData();
                $fileName = uniqid(). '.' .$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $product->setImage($fileName);
            } else {
                
                $product->setImage($oldPicture);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager, CartManager $cartManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {

            $cart = $cartManager->getCurrentCart();
            //dd($cart);


            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }

    public function myFunction(Request $request, ProductRepository $productRepository, CategoryRepository $CategoryRepository)
    {

        $value = $request->request->get('filterStr');
     
        $myProducts = $this->getDoctrine()->getRepository(Product::class)->findFilter($value);


        return $this->render('product/index.html.twig', [
            'products' => $myProducts,
            'categories' => $CategoryRepository->findAll(),
        ]);

    }




}

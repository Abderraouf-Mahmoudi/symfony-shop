<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front_home')]
    public function home(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('front/home.html.twig', [
            'products' => $products
        ]);
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
    public function product(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('front/product.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/product-detail/{id}', name: 'front_product_detail')]
    public function productDetail(Product $product = null): Response
    {
        if (!$product) {
            return $this->redirectToRoute('front_product');
        }
        
        return $this->render('front/product-detail.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/cart', name: 'front_cart')]
    public function cart(Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        $cartWithData = [];
        
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
        
        $total = array_reduce($cartWithData, function($total, $item) {
            return $total + ($item['product']->getPrice() * $item['quantity']);
        }, 0);
        
        return $this->render('front/shoping-cart.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }
    
    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart($id, Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Verify product exists
        $product = $productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found');
            return $this->redirectToRoute('front_home');
        }
        
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }
    
    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart')]
    public function removeFromCart($id, Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }
    
    #[Route('/update-cart-direct/{id}/{quantity}', name: 'update_cart_item')]
    public function updateCart($id, $quantity, Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Verify product exists
        $product = $productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found');
            return $this->redirectToRoute('front_cart');
        }
        
        if (!empty($cart[$id]) && $quantity > 0) {
            $cart[$id] = $quantity;
        } elseif ($quantity <= 0) {
            unset($cart[$id]);
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }

    #[Route('/update-cart-form', name: 'update_cart_form', methods: ['POST'])]
    public function updateCartForm(Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        $quantities = $request->request->get('quantities', []);
        
        foreach ($quantities as $id => $quantity) {
            // Verify product exists
            $product = $productRepository->find($id);
            if ($product) {
                if ($quantity > 0) {
                    $cart[$id] = (int)$quantity;
                } else {
                    unset($cart[$id]);
                }
            }
        }
        
        $session->set('cart', $cart);
        $this->addFlash('success', 'Cart updated successfully');
        
        return $this->redirectToRoute('front_cart');
    }

    #[Route('/update-cart-quantity/{id}/{action}', name: 'update_cart_item_quantity')]
    public function updateCartItemQuantity($id, $action, Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Verify product exists
        $product = $productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found');
            return $this->redirectToRoute('front_cart');
        }
        
        if (!empty($cart[$id])) {
            if ($action === 'increase') {
                $cart[$id]++;
            } elseif ($action === 'decrease') {
                if ($cart[$id] > 1) {
                    $cart[$id]--;
                } else {
                    unset($cart[$id]);
                }
            }
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }
    
    #[Route('/place-order', name: 'place_order', methods: ['POST'])]
    public function placeOrder(Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Check if cart is empty
        if (empty($cart)) {
            $this->addFlash('error', 'Your cart is empty');
            return $this->redirectToRoute('front_cart');
        }
        
        // Get form data
        $fullname = $request->request->get('fullname');
        $phone = $request->request->get('phone');
        $address = $request->request->get('address');
        $city = $request->request->get('city');
        $postalCode = $request->request->get('postalCode');
        $size = $request->request->get('size');
        $notes = $request->request->get('notes');
        
        // Create new order
        $order = new Order();
        $order->setCustomerName($fullname);
        $order->setShippingAddress($address);
        $order->setPhone($phone);
        $order->setCity($city);
        $order->setPostalCode($postalCode);
        $order->setSize($size);
        $order->setNotes($notes);
        $order->setCustomerEmail(''); // Not collected in form, but required in entity
        
        // Calculate total and add order items
        $total = 0;
        $cartWithData = [];
        
        // Process cart items and create order items
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice((string)$product->getPrice());
                $order->addItem($orderItem);
                
                $total += $product->getPrice() * $quantity;
            }
        }
        
        $order->setTotalAmount((string)$total);
        $order->setStatus('pending');
        
        // Save to database
        $entityManager->persist($order);
        $entityManager->flush();
        
        // Clear cart
        $session->remove('cart');
        
        // Add success message
        $this->addFlash('success', 'Your order has been placed successfully. Order #' . $order->getId());
        
        // Redirect to home page
        return $this->redirectToRoute('front_home');
    }
}

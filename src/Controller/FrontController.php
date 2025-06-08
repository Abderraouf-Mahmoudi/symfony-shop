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
        
        foreach ($cart as $cartKey => $cartItem) {
            $id = explode('-', $cartKey)[0];
            $product = $productRepository->find($id);
            if ($product) {
                // Ensure cart item is an array before accessing elements
                if (!is_array($cartItem)) {
                    error_log("Cart item is not an array for key: " . $cartKey);
                    continue;
                }
                if (!isset($cartItem['quantity']) || !isset($cartItem['size'])) {
                    error_log("Invalid cart item structure for key: " . $cartKey);
                    continue;
                }

                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $cartItem['quantity'],
                    'size' => $cartItem['size']
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
        
        // Get size and quantity from request
        $size = $request->request->get('size', 'default');
        $quantity = (int)$request->request->get('quantity', 1);

        // Debugging: Log size and quantity
        error_log("Size: " . $size);
        error_log("Quantity: " . $quantity);

        // Use a composite key for cart items
        $cartKey = $id . '-' . $size;

        // Ensure cart item is an array
        if (!isset($cart[$cartKey]) || !is_array($cart[$cartKey])) {
            $cart[$cartKey] = ['quantity' => 0, 'size' => $size];
        }

        // Update quantity
        $cart[$cartKey]['quantity'] += $quantity;
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }
    
    #[Route('/remove-from-cart/{id}/{size}', name: 'remove_from_cart')]
    public function removeFromCart($id, $size, Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Use a composite key for cart items
        $cartKey = $id . '-' . $size;

        if (!empty($cart[$cartKey])) {
            unset($cart[$cartKey]);
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }
    
    #[Route('/update-cart-direct/{id}/{size}/{quantity}', name: 'update_cart_item')]
    public function updateCart($id, $size, $quantity, Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Verify product exists
        $product = $productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found');
            return $this->redirectToRoute('front_cart');
        }
        
        // Use a composite key for cart items
        $cartKey = $id . '-' . $size;

        // Ensure cart item is an array
        if (!isset($cart[$cartKey]) || !is_array($cart[$cartKey])) {
            $cart[$cartKey] = ['quantity' => 0, 'size' => $size];
        }

        // Update quantity
        $cart[$cartKey]['quantity'] = $quantity;
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }

    #[Route('/update-cart-form', name: 'update_cart_form', methods: ['POST'])]
    public function updateCartForm(Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        $quantities = $request->request->get('quantities', []);
        
        foreach ($quantities as $cartKey => $quantity) {
            // Verify product exists
            $id = explode('-', $cartKey)[0];
            $product = $productRepository->find($id);
            if ($product) {
                // Ensure cart item is an array
                if (!isset($cart[$cartKey]) || !is_array($cart[$cartKey])) {
                    $cart[$cartKey] = ['quantity' => 0, 'size' => explode('-', $cartKey)[1]];
                }

                if ($quantity > 0) {
                    $cart[$cartKey]['quantity'] = (int)$quantity;
                } else {
                    unset($cart[$cartKey]);
                }
            }
        }
        
        $session->set('cart', $cart);
        $this->addFlash('success', 'Cart updated successfully');
        
        return $this->redirectToRoute('front_cart');
    }

    #[Route('/update-cart-quantity/{id}/{size}/{action}', name: 'update_cart_item_quantity')]
    public function updateCartItemQuantity($id, $size, $action, Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Verify product exists
        $product = $productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found');
            return $this->redirectToRoute('front_cart');
        }
        
        // Use a composite key for cart items
        $cartKey = $id . '-' . $size;

        // Ensure cart item is an array
        if (!isset($cart[$cartKey]) || !is_array($cart[$cartKey])) {
            $cart[$cartKey] = ['quantity' => 0, 'size' => $size];
        }

        if ($action === 'increase') {
            $cart[$cartKey]['quantity']++;
        } elseif ($action === 'decrease') {
            if ($cart[$cartKey]['quantity'] > 1) {
                $cart[$cartKey]['quantity']--;
            } else {
                unset($cart[$cartKey]);
            }
        }
        
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('front_cart');
    }
    
    #[Route('/place-order', name: 'place_order', methods: ['POST'])]
    public function placeOrder(Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        try {
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
            $notes = $request->request->get('notes');
            
            // Create new order
            $order = new Order();
            $order->setCustomerName($fullname);
            $order->setShippingAddress($address);
            $order->setPhone($phone);
            $order->setCity($city);
            $order->setPostalCode($postalCode);
            $order->setNotes($notes);
            $order->setCustomerEmail(''); // Not collected in form, but required in entity
            
            // Calculate total and add order items
            $total = 0;
            
            // Process cart items and create order items
            foreach ($cart as $cartKey => $cartItem) {
                if (!is_string($cartKey)) {
                    continue; // Skip if key is not a string
                }
                
                // Validate cart key format
                $parts = explode('-', $cartKey);
                if (!isset($parts[0])) {
                    continue; // Skip if format is invalid
                }
                
                $idPart = $parts[0];
                if (!is_numeric($idPart)) {
                    continue; // Skip if ID is not numeric
                }
                
                $id = (int)$idPart; // Ensure ID is an integer
                $product = $productRepository->find($id);
                
                if (!$product) {
                    continue; // Skip if product not found
                }
                
                // Ensure cart item is an array before accessing elements
                if (!is_array($cartItem)) {
                    continue; // Skip non-array items
                }
                
                // Check if required fields exist
                if (!array_key_exists('quantity', $cartItem) || !array_key_exists('size', $cartItem)) {
                    continue; // Skip items with missing fields
                }
                
                // Convert quantity to integer and validate
                $quantity = filter_var($cartItem['quantity'], FILTER_VALIDATE_INT);
                if ($quantity === false || $quantity <= 0) {
                    continue; // Skip items with invalid quantity
                }
                
                // Get size safely
                $size = isset($cartItem['size']) ? (string)$cartItem['size'] : '';
                
                // Create order item
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice((string)$product->getPrice());
                
                // Store size information temporarily for later update
                $orderItem->tempSize = $size; // This won't be persisted but will be accessible later
                $order->addItem($orderItem);
                
                $total += $product->getPrice() * $quantity;
            }
            
            $order->setTotalAmount((string)$total);
            $order->setStatus('pending');
            
            // Save to database
            $entityManager->persist($order);
            $entityManager->flush();
            
            // After flush, get connection for direct SQL updates
            $connection = $entityManager->getConnection();
            
            // Update sizes directly in the database
            foreach ($order->getItems() as $item) {
                if (isset($item->tempSize) && !empty($item->tempSize)) {
                    $sql = 'UPDATE order_item SET size = :size WHERE id = :id';
                    $stmt = $connection->prepare($sql);
                    $stmt->bindValue('size', $item->tempSize);
                    $stmt->bindValue('id', $item->getId());
                    $stmt->executeStatement();
                }
            }
            
            // Clear cart
            $session->remove('cart');
            
            // Add success message
            $this->addFlash('success', 'Your order has been placed successfully. Order #' . $order->getId());
            
            // Redirect to home page
            return $this->redirectToRoute('front_home');
        } catch (\Exception $e) {
            // Log the error
            error_log('Error placing order: ' . $e->getMessage());
            
            // Add error message
            $this->addFlash('error', 'There was a problem placing your order. Please try again.');
            
            // Redirect back to cart
            return $this->redirectToRoute('front_cart');
        }
    }
}

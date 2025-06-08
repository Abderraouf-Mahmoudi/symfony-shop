<?php

namespace App\Twig;

use App\Repository\ProductRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CartExtension implements EventSubscriberInterface
{
    private $twig;
    private $productRepository;
    
    public function __construct(Environment $twig, ProductRepository $productRepository)
    {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
    
    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        $cartData = [
            'items' => [],
            'total' => 0
        ];
        
        foreach ($cart as $cartKey => $cartItem) {
            // Debugging: Log the cart key and item
            error_log("Cart Key: " . $cartKey);
            error_log("Cart Item: " . print_r($cartItem, true));

            // Ensure cartKey is split correctly
            $keyParts = explode('-', $cartKey);
            $productId = $keyParts[0];
            $size = isset($keyParts[1]) ? $keyParts[1] : null;

            // Check if cartItem is an array
            if (!is_array($cartItem)) {
                error_log("Invalid cart item structure for key: " . $cartKey);
                continue; // Skip this item if it's not an array
            }

            // Additional check to ensure quantity and size are set
            if (!isset($cartItem['quantity']) || !isset($cartItem['size'])) {
                error_log("Missing quantity or size for cart item with key: " . $cartKey);
                continue;
            }

            $product = $this->productRepository->find($productId);
            if ($product) {
                $cartData['items'][] = [
                    'product' => $product,
                    'quantity' => (int)$cartItem['quantity'],
                    'size' => $size
                ];
                $cartData['total'] += $product->getPrice() * (int)$cartItem['quantity'];
            }
        }
        
        $this->twig->addGlobal('cart_data', $cartData);
        $this->twig->addGlobal('cart_count', count($cartData['items']));
    }
}

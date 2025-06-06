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
        
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartData['items'][] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $cartData['total'] += $product->getPrice() * $quantity;
            }
        }
        
        $this->twig->addGlobal('cart_data', $cartData);
        $this->twig->addGlobal('cart_count', count($cartData['items']));
    }
}

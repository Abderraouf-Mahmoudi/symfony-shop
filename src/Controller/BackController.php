<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class BackController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(ProductRepository $productRepository, OrderRepository $orderRepository): Response
    {
        // Get count of products and recent orders for dashboard widgets
        $productCount = $productRepository->count([]);
        $recentOrders = $orderRepository->findBy([], ['createdAt' => 'DESC'], 5);
        
        return $this->render('back/home.html.twig', [
            'product_count' => $productCount,
            'recent_orders' => $recentOrders
        ]);
    }
    
    // Product CRUD Operations
    #[Route('/products', name: 'admin_products')]
    public function productList(ProductRepository $productRepository): Response
    {
        return $this->render('back/products/index.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }
    
    #[Route('/products/new', name: 'admin_products_new')]
    public function newProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        
        // Handle form submission
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setPrice($request->request->get('price'))
                ->setStock($request->request->get('stock'))
                ->setCategory($request->request->get('category'));
            
            // Handle image upload
            $uploadedFile = $request->files->get('image');
            if ($uploadedFile) {
                $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('product_images_directory'),
                    $newFilename
                );
                $product->setImagePath($newFilename);
            }
            
            $productRepository->add($product);
            
            $this->addFlash('success', 'Product added successfully!');
            return $this->redirectToRoute('admin_products');
        }
        
        return $this->render('back/products/new.html.twig');
    }
    
    #[Route('/products/{id}/edit', name: 'admin_products_edit')]
    public function editProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        // Handle form submission
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setPrice($request->request->get('price'))
                ->setStock($request->request->get('stock'))
                ->setCategory($request->request->get('category'))
                ->setUpdatedAt(new \DateTimeImmutable());
            
            // Handle image upload
            $uploadedFile = $request->files->get('image');
            if ($uploadedFile) {
                $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('product_images_directory'),
                    $newFilename
                );
                $product->setImagePath($newFilename);
            }
            
            $productRepository->add($product);
            
            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('admin_products');
        }
        
        return $this->render('back/products/edit.html.twig', [
            'product' => $product
        ]);
    }
    
    #[Route('/products/{id}/delete', name: 'admin_products_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product);
            $this->addFlash('success', 'Product deleted successfully!');
        }
        
        return $this->redirectToRoute('admin_products');
    }
    
    // Order CRUD Operations
    #[Route('/orders', name: 'admin_orders')]
    public function orderList(OrderRepository $orderRepository): Response
    {
        return $this->render('back/orders/index.html.twig', [
            'orders' => $orderRepository->findAll()
        ]);
    }
    
    #[Route('/orders/{id}', name: 'admin_orders_show')]
    public function orderShow(Order $order): Response
    {
        return $this->render('back/orders/show.html.twig', [
            'order' => $order
        ]);
    }
    
    #[Route('/orders/{id}/status', name: 'admin_orders_status', methods: ['POST'])]
    public function orderUpdateStatus(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        $newStatus = $request->request->get('status');
        if (in_array($newStatus, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            $order->setStatus($newStatus);
            $order->setUpdatedAt(new \DateTimeImmutable());
            $orderRepository->add($order);
            
            $this->addFlash('success', 'Order status updated successfully!');
        }
        
        return $this->redirectToRoute('admin_orders_show', ['id' => $order->getId()]);
    }
    
    #[Route('/orders/{id}/delete', name: 'admin_orders_delete', methods: ['POST'])]
    public function deleteOrder(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order);
            $this->addFlash('success', 'Order deleted successfully!');
        }
        
        return $this->redirectToRoute('admin_orders');
    }
}

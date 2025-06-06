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
        // Get count of products for dashboard widgets
        $productCount = $productRepository->count([]);
        
        // Get only pending orders
        $pendingOrders = $orderRepository->findBy(['status' => 'pending'], ['createdAt' => 'DESC']);
        $pendingOrderCount = count($pendingOrders);
        
        // Get recent orders for the orders table (limit to 5)
        $recentOrders = $orderRepository->findBy([], ['createdAt' => 'DESC'], 5);
        
        // Calculate revenue from completed orders
        $completedOrders = $orderRepository->findBy(['status' => 'done']);
        $revenue = 0;
        foreach ($completedOrders as $order) {
            $revenue += $order->getTotalAmount();
        }
        
        return $this->render('back/home.html.twig', [
            'product_count' => $productCount,
            'pending_order_count' => $pendingOrderCount,
            'recent_orders' => $recentOrders,
            'revenue' => $revenue
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
    
    // Order operations moved to OrderController - see OrderController.php for all order related functionality
}

<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class BackController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        // Get total number of products
        $productRepository = $entityManager->getRepository('App\Entity\Product');
        $totalProducts = $productRepository->count([]);
        
        // Get orders counts by status
        $orderRepository = $entityManager->getRepository('App\Entity\Order');
        $pendingOrderCount = $orderRepository->count(['status' => 'pending']);
        $canceledOrderCount = $orderRepository->count(['status' => 'canceled']);
        
        // Get revenue (income)
        $settingRepository = $entityManager->getRepository('App\Entity\Setting');
        $incomeSetting = $settingRepository->findOneBy(['name' => 'income']);
        $revenue = $incomeSetting ? $incomeSetting->getValue() : '0';
        
        // Get recent orders (limit 5)
        $recentOrders = $orderRepository->findBy([], ['createdAt' => 'DESC'], 5);
        
        // Get current date
        $now = new \DateTimeImmutable();

        return $this->render('back/home.html.twig', [
            'product_count' => $totalProducts,
            'pending_order_count' => $pendingOrderCount,
            'canceled_order_count' => $canceledOrderCount,
            'revenue' => $revenue,
            'recent_orders' => $recentOrders,
            'current_date' => $now
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

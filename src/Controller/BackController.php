<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductImage;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductImageRepository;
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
        // Calculate revenue dynamically from completed orders
        $revenue = $entityManager->createQuery(
            'SELECT SUM(o.totalAmount) FROM App\\Entity\\Order o WHERE o.status = :status'
        )
        ->setParameter('status', 'done')
        ->getSingleScalarResult() ?? '0';
        
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
    public function newProduct(Request $request, ProductRepository $productRepository, ProductImageRepository $imageRepository, ProductColorRepository $colorRepository, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        
        // Handle form submission
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setPrice($request->request->get('price'))
                ->setStock($request->request->get('stock'))
                ->setCategory($request->request->get('category'));
                
            // Handle shipping options
            $freeShipping = $request->request->get('freeShipping') === 'on';
            $product->setFreeShipping($freeShipping);
            
            // Only set shipping price if not free shipping
            if (!$freeShipping && $request->request->has('shippingPrice')) {
                $product->setShippingPrice($request->request->get('shippingPrice'));
            } else {
                $product->setShippingPrice(null);
            }
            
            // Save the product first to get an ID
            $productRepository->add($product, false);
            
            // Handle multiple image uploads
            $uploadedFiles = $request->files->get('images');
            if ($uploadedFiles && count($uploadedFiles) > 0) {
                $isPrimarySet = false;
                
                foreach ($uploadedFiles as $index => $uploadedFile) {
                    if ($uploadedFile) {
                        $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
                        $uploadedFile->move(
                            $this->getParameter('product_images_directory'),
                            $newFilename
                        );
                        
                        $productImage = new ProductImage();
                        $productImage->setImagePath($newFilename);
                        $productImage->setProduct($product);
                        
                        // Set the first image as primary if no primary is set yet
                        if (!$isPrimarySet) {
                            $productImage->setIsPrimary(true);
                            $isPrimarySet = true;
                        }
                        
                        $imageRepository->add($productImage, false);
                        $product->addImage($productImage);
                    }
                }
            }
            
            // Handle product colors
            $colorNames = $request->request->all('color_names');
            $colorCodes = $request->request->all('color_codes');
            
            if (!empty($colorNames) && !empty($colorCodes)) {
                for ($i = 0; $i < count($colorNames); $i++) {
                    if (!empty($colorNames[$i]) && !empty($colorCodes[$i])) {
                        $productColor = new ProductColor();
                        $productColor->setName($colorNames[$i]);
                        $productColor->setCode($colorCodes[$i]);
                        $productColor->setProduct($product);
                        
                        $colorRepository->add($productColor, false);
                        $product->addColor($productColor);
                    }
                }
            }
            
            // Flush all changes
            $productRepository->getEntityManager()->flush();
            
            // Check if this is an AJAX request
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => 'Produit ajouté avec succès!',
                    'productId' => $product->getId()
                ]);
            } else {
                $this->addFlash('success', 'Product added successfully!');
            }
            
            // Regular form submission - redirect
            return $this->redirectToRoute('admin_products');
        }
        
        return $this->render('back/products/new.html.twig');
    }
    
    #[Route('/products/{id}/edit', name: 'admin_products_edit')]
    public function editProduct(Request $request, Product $product, ProductRepository $productRepository, ProductImageRepository $imageRepository, ProductColorRepository $colorRepository, EntityManagerInterface $entityManager): Response
    {
        // Handle form submission
        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->setPrice($request->request->get('price'))
                ->setStock($request->request->get('stock'))
                ->setCategory($request->request->get('category'))
                ->setUpdatedAt(new \DateTimeImmutable());
                
            // Handle shipping options
            $freeShipping = $request->request->get('freeShipping') === 'on';
            $product->setFreeShipping($freeShipping);
            
            // Only set shipping price if not free shipping
            if (!$freeShipping && $request->request->has('shippingPrice')) {
                $product->setShippingPrice($request->request->get('shippingPrice'));
            } else {
                $product->setShippingPrice(null);
            }
            
            // Get entity manager
            // EntityManager already injected as parameter
            
            // Handle multiple image uploads
            $uploadedFiles = $request->files->get('images');
            if ($uploadedFiles && count($uploadedFiles) > 0) {
                $hasPrimary = count($product->getImages()) > 0 && $product->getPrimaryImage() !== null;
                
                foreach ($uploadedFiles as $index => $uploadedFile) {
                    if ($uploadedFile) {
                        $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
                        $uploadedFile->move(
                            $this->getParameter('product_images_directory'),
                            $newFilename
                        );
                        
                        $productImage = new ProductImage();
                        $productImage->setImagePath($newFilename);
                        $productImage->setProduct($product);
                        
                        // Set as primary if no primary exists
                        if (!$hasPrimary) {
                            $productImage->setIsPrimary(true);
                            $hasPrimary = true;
                        }
                        
                        $imageRepository->add($productImage, false);
                        $product->addImage($productImage);
                    }
                }
            }
            
            // Handle image deletion
            $imagesToDelete = $request->request->all('delete_images');
            if (!empty($imagesToDelete)) {
                foreach ($imagesToDelete as $imageId) {
                    $image = $imageRepository->find($imageId);
                    if ($image && $image->getProduct() === $product) {
                        // Delete the file from filesystem
                        $imagePath = $this->getParameter('product_images_directory') . '/' . $image->getImagePath();
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                        
                        $product->removeImage($image);
                        $imageRepository->remove($image, false);
                    }
                }
            }
            
            // Handle primary image selection
            $primaryImageId = $request->request->get('primary_image', 0);
            if ($primaryImageId) {
                foreach ($product->getImages() as $image) {
                    $image->setIsPrimary($image->getId() == $primaryImageId);
                }
            }
            
            // Handle product colors
            // Remove existing colors
            $colorsToKeep = $request->request->all('existing_color_ids');
            foreach ($product->getColors() as $color) {
                if (!in_array($color->getId(), $colorsToKeep)) {
                    $product->removeColor($color);
                    $colorRepository->remove($color, false);
                }
            }
            
            // Update existing colors
            $existingColorIds = $request->request->all('existing_color_ids');
            $existingColorNames = $request->request->all('existing_color_names');
            $existingColorCodes = $request->request->all('existing_color_codes');
            
            for ($i = 0; $i < count($existingColorIds); $i++) {
                if (isset($existingColorIds[$i]) && isset($existingColorNames[$i]) && isset($existingColorCodes[$i])) {
                    $color = $colorRepository->find($existingColorIds[$i]);
                    if ($color && $color->getProduct() === $product) {
                        $color->setName($existingColorNames[$i]);
                        $color->setCode($existingColorCodes[$i]);
                    }
                }
            }
            
            // Add new colors
            $colorNames = $request->request->all('color_names');
            $colorCodes = $request->request->all('color_codes');
            
            if (!empty($colorNames) && !empty($colorCodes)) {
                for ($i = 0; $i < count($colorNames); $i++) {
                    if (!empty($colorNames[$i]) && !empty($colorCodes[$i])) {
                        $productColor = new ProductColor();
                        $productColor->setName($colorNames[$i]);
                        $productColor->setCode($colorCodes[$i]);
                        $productColor->setProduct($product);
                        
                        $colorRepository->add($productColor, false);
                        $product->addColor($productColor);
                    }
                }
            }
            
            // Save all changes
            $productRepository->getEntityManager()->flush();
            
            // Check if this is an AJAX request
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => 'Produit mis à jour avec succès!',
                    'productId' => $product->getId()
                ]);
            } else {
                $this->addFlash('success', 'Product updated successfully!');
                return $this->redirectToRoute('admin_products');
            }
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
            
            // Check if this is an AJAX request
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => 'Produit supprimé avec succès!'
                ]);
            } else {
                $this->addFlash('success', 'Product deleted successfully!');
            }
        }
        
        return $this->redirectToRoute('admin_products');
    }
    
    // Order operations moved to OrderController - see OrderController.php for all order related functionality
}

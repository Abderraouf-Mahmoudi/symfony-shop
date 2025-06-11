<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/products')]
class ProductApiController extends AbstractController
{
    #[Route('/{id}', name: 'api_product_get', methods: ['GET'])]
    public function getProduct(Product $product, SerializerInterface $serializer): JsonResponse
    {
        // Return basic product info including stock
        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'stock' => $product->getStock(),
            'price' => $product->getPrice()
        ];
        
        return new JsonResponse($data);
    }
    
    #[Route('/{id}/update-stock', name: 'api_product_update_stock', methods: ['POST'])]
    public function updateStock(Request $request, Product $product, EntityManagerInterface $entityManager): JsonResponse
    {
        // Parse request data
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['stock']) || !is_numeric($data['stock'])) {
            return new JsonResponse(['error' => 'Stock value is required and must be numeric'], Response::HTTP_BAD_REQUEST);
        }
        
        $newStock = max(0, (int)$data['stock']);
        
        // Update product stock
        $product->setStock($newStock);
        $entityManager->flush();
        
        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'stock' => $product->getStock(),
            'message' => 'Stock updated successfully'
        ]);
    }
}

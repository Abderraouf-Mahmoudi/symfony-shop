<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders')]
class OrderController extends AbstractController
{
    #[Route('/{id}/update-stock', name: 'admin_orders_update_stock', methods: ['GET'])]
    public function updateStock(Order $order, EntityManagerInterface $entityManager): Response
    {
        // Process all items in the order and update stock regardless of order status
        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $quantity = $orderItem->getQuantity();
            
            if ($product) {
                // Decrease stock by the ordered quantity
                $currentStock = $product->getStock();
                $newStock = max(0, $currentStock - $quantity); // Ensure stock doesn't go below 0
                $product->setStock($newStock);
                
                // Add debug information
                $this->addFlash(
                    'success', 
                    sprintf(
                        'Product #%d (%s): Stock updated from %d to %d (-%d units)', 
                        $product->getId(),
                        $product->getName(),
                        $currentStock, 
                        $newStock, 
                        $quantity
                    )
                );
            }
        }
        
        $entityManager->flush();
        
        return $this->redirectToRoute('admin_orders_show', ['id' => $order->getId()]);
    }
    #[Route('/', name: 'admin_orders_index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository): Response
    {
        $phone = $request->query->get('phone');
        $orders = [];
        
        if ($phone) {
            // Search orders by phone number
            $orders = $orderRepository->findByPhone($phone);
        } else {
            // Get all orders sorted by creation date
            $orders = $orderRepository->findBy([], ['createdAt' => 'DESC']);
        }
        
        return $this->render('back/orders/index.html.twig', [
            'orders' => $orders,
            'search_phone' => $phone,
        ]);
    }
    
    #[Route('/{id}', name: 'admin_orders_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('back/orders/show.html.twig', [
            'order' => $order,
        ]);
    }
    
    #[Route('/{id}/update-status/{status}', name: 'admin_orders_update_status', methods: ['GET', 'POST'])]
    public function updateStatus(Order $order, string $status, EntityManagerInterface $entityManager): Response
    {
        $allowedStatuses = ['pending', 'done', 'canceled'];
        
        if (!in_array($status, $allowedStatuses)) {
            $this->addFlash('error', 'Invalid status');
            return $this->redirectToRoute('admin_orders_show', ['id' => $order->getId()]);
        }
        
        $oldStatus = $order->getStatus();
        $order->setStatus($status);
        $order->setUpdatedAt(new \DateTimeImmutable());
        
        // Update income and decrease stock if status changes to done
        if ($oldStatus !== 'done' && $status === 'done') {
            try {
                // Get current income from dashboard settings
                $settingRepository = $entityManager->getRepository('App\Entity\Setting');
                $incomeSetting = $settingRepository->findOneBy(['name' => 'income']);
                
                if ($incomeSetting) {
                    $currentIncome = (float)$incomeSetting->getValue();
                    $orderTotal = (float)$order->getTotalAmount();
                    $newIncome = $currentIncome + $orderTotal;
                    
                    // Update income in dashboard settings
                    $incomeSetting->setValue((string)$newIncome);
                } else {
                    // Create new income setting if it doesn't exist
                    $incomeSetting = new \App\Entity\Setting();
                    $incomeSetting->setName('income');
                    $incomeSetting->setValue($order->getTotalAmount());
                    $entityManager->persist($incomeSetting);
                }
                
                // Debug information to track order processing
                $this->addFlash('info', 'Processing order #' . $order->getId() . ' with ' . count($order->getItems()) . ' items');
                
                // Update product stock for each order item
                foreach ($order->getItems() as $orderItem) {
                    $product = $orderItem->getProduct();
                    $quantity = $orderItem->getQuantity();
                    
                    if ($product) {
                        // Decrease stock by the ordered quantity
                        $currentStock = $product->getStock();
                        $newStock = max(0, $currentStock - $quantity); // Ensure stock doesn't go below 0
                        $product->setStock($newStock);
                        
                        // Add debug information
                        $this->addFlash(
                            'info', 
                            sprintf(
                                'Product #%d: Stock updated from %d to %d (-%d units)', 
                                $product->getId(), 
                                $currentStock, 
                                $newStock, 
                                $quantity
                            )
                        );
                    } else {
                        $this->addFlash('warning', 'Order item found with no associated product');
                    }
                }
            } catch (\Exception $e) {
                // Log error but continue processing
                $this->addFlash('warning', 'Order status updated, but income tracking or stock update failed');
            }
        }
        
        $entityManager->flush();
        
        $this->addFlash('success', sprintf('Order status updated to %s', $status));
        
        return $this->redirectToRoute('admin_orders_index');
    }
    
    #[Route('/{id}/delete', name: 'admin_orders_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
            $this->addFlash('success', 'Order deleted successfully!');
        }
        
        return $this->redirectToRoute('admin_orders_index');
    }
    
    #[Route('/{id}/pdf', name: 'admin_orders_pdf', methods: ['GET'])]
    public function generatePdf(Order $order, PdfService $pdfService): Response
    {
        $html = $this->renderView('back/orders/receipt_pdf.html.twig', [
            'order' => $order,
        ]);
        
        // Le nom du fichier sera "recu-commande-X.pdf" où X est l'ID de la commande
        $filename = 'recu-commande-' . $order->getId() . '.pdf';
        
        // Génère le PDF et déclenche le téléchargement
        return new Response(
            $pdfService->generatePdf($html, $filename),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}

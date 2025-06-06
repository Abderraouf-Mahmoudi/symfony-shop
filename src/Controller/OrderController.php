<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'admin_orders_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('back/orders/index.html.twig', [
            'orders' => $orderRepository->findBy([], ['createdAt' => 'DESC']),
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
        
        // Update income if status changes from pending to done
        if ($oldStatus !== 'done' && $status === 'done') {
            try {
                // Get current income from dashboard settings
                $settingRepository = $entityManager->getRepository('App\\Entity\\Setting');
                $incomeSetting = $settingRepository->findOneBy(['name' => 'income']);
                
                if ($incomeSetting) {
                    $currentIncome = (float)$incomeSetting->getValue();
                    $orderTotal = (float)$order->getTotalAmount();
                    $newIncome = $currentIncome + $orderTotal;
                    
                    // Update income in dashboard settings
                    $incomeSetting->setValue((string)$newIncome);
                }
            } catch (\Exception $e) {
                // Log error but continue processing
                $this->addFlash('warning', 'Order status updated, but income tracking failed');
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
}

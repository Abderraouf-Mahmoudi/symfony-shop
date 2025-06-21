<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/statistics')]
class StatisticsController extends AbstractController
{
    private $statisticsService;
    
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }
    
    #[Route('/', name: 'admin_statistics_index', methods: ['GET'])]
    public function index(): Response
    {
        // Récupérer les 10 produits les plus vendus (toutes commandes)
        $bestSellingProductsAll = $this->statisticsService->getBestSellingProducts(10);
        
        // Récupérer les 10 produits les plus vendus (commandes terminées uniquement)
        $bestSellingProductsDone = $this->statisticsService->getBestSellingProducts(10, 'done');
        
        // Récupérer les statistiques de ventes par mois (6 derniers mois)
        $monthlySales = $this->statisticsService->getSalesByPeriod('month', 6);
        
        return $this->render('back/statistics/index.html.twig', [
            'bestsellers_all' => $bestSellingProductsAll,
            'bestsellers_done' => $bestSellingProductsDone,
            'monthly_sales' => $monthlySales,
        ]);
    }
}

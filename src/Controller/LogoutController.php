<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractController
{
    /**
     * Cette méthode ne sera jamais exécutée, elle est interceptée par le système de logout Symfony
     */
    #[Route(path: '/admin/logout', name: 'admin_logout')]
    public function logout(): never
    {
        // Cette méthode ne sera jamais exécutée car Symfony intercepte la route via la configuration firewall
        throw new \LogicException('Cette méthode ne devrait jamais être exécutée. Elle est interceptée par la clé de logout dans votre firewall.');
    }
}

<?php

namespace App\Controller;

use App\Repository\MatchsRepository;
use App\Repository\JoueurRepository;
use App\Repository\PerformanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(
        MatchsRepository $matchsRepo,
        JoueurRepository $joueurRepo,
        PerformanceRepository $perfRepo
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'matchs' => $matchsRepo->findAll(),
            'joueurs' => $joueurRepo->findAll(),
            'performances' => $perfRepo->findAll(),
        ]);
    }
}

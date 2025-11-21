<?php

namespace App\Controller;

use App\Repository\JoueurRepository;
use App\Repository\PerformanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiStatsController extends AbstractController
{
    #[Route('/api/stats', name: 'api_stats')]
    public function stats(
        JoueurRepository $joueurRepo,
        PerformanceRepository $perfRepo
    ): JsonResponse {
        
        $joueurs = [];
        foreach ($joueurRepo->findAll() as $j) {
            $joueurs[] = [
                'id' => $j->getId(),
                'nom' => $j->getNom(),
                'prenom' => $j->getPrenom(),
                'age' => $j->getAge(),
                'position' => $j->getPosition(),
            ];
        }

        $performances = [];
        foreach ($perfRepo->findAll() as $p) {
            $performances[] = [
                'id' => $p->getId(),
                'joueurId' => $p->getJoueur()->getId(),
                'matchId' => $p->getMatch()->getId(),
                'buts' => $p->getButs(),
                'passesDecisives' => $p->getPassesDecisives(),
                'noteMatch' => $p->getNoteMatch(),
                'minutesJouees' => $p->getMinutesJouees(),
                'cartonJaune' => $p->getCartonJaune(),
                'cartonRouge' => $p->getCartonRouge(),
            ];
        }

        return new JsonResponse([
            'joueurs' => $joueurs,
            'performances' => $performances
        ]);
    }
}

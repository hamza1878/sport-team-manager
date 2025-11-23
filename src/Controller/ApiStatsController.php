<?php

namespace App\Controller;

use App\Repository\JoueurRepository;
use App\Repository\PerformanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiStatsController extends AbstractController
{
    #[Route('/api/stats', name: 'api_stats', methods: ['GET'])]
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
                'numeroMaillot' => $j->getNumeroMaillot()
            ];
        }

        $performances = [];
        foreach ($perfRepo->findAll() as $p) {
            $performances[] = [
                'id' => $p->getId(),
                'joueurId' => $p->getJoueur() ? $p->getJoueur()->getId() : null,
                'matchId' => $p->getMatch() ? $p->getMatch()->getId() : null,
                'buts' => $p->getButs() ?? 0,
                'passesDecisives' => $p->getPassesDecisives() ?? 0,
                'noteMatch' => $p->getNoteMatch() ?? 0,
                'minutesJouees' => $p->getMinutesJouees() ?? 0,
                'cartonJaune' => $p->getCartonJaune() ?? 0,
                'cartonRouge' => $p->getCartonRouge() ?? 0,
            ];
        }

        return $this->json([
            'joueurs' => $joueurs,
            'performances' => $performances,
            'matchs' => [27] 
        ]);
    }
}
<?php

namespace App\Controller;

use App\Repository\JoueurRepository;
use App\Repository\MatchsRepository;
use App\Repository\PerformanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    #[Route('/api/stats', name: 'api_stats', methods: ['GET'])]
    public function getStats(
        JoueurRepository $joueurRepository,
        PerformanceRepository $performanceRepository,
        MatchsRepository $matchsRepository
    ): JsonResponse {
        // Récupérer tous les joueurs
        $joueurs = $joueurRepository->findAll();
        
        // Récupérer toutes les performances
        $performances = $performanceRepository->findAll();
        
        // Récupérer tous les matchs
        $matchs = $matchsRepository->findAll();

        // Formatter les données pour le JSON
        $joueursData = [];
        foreach ($joueurs as $joueur) {
            $joueursData[] = [
                'id' => $joueur->getId(),
                'nom' => $joueur->getNom(),
                'prenom' => $joueur->getPrenom(),
                'poste' => $joueur->getPoste(),
                'numero' => $joueur->getNumero()
            ];
        }

        $performancesData = [];
        foreach ($performances as $perf) {
            $performancesData[] = [
                'id' => $perf->getId(),
                'joueurId' => $perf->getJoueur() ? $perf->getJoueur()->getId() : null,
                'matchId' => $perf->getMatch() ? $perf->getMatch()->getId() : null,
                'buts' => $perf->getButs() ?? 0,
                'passesDecisives' => $perf->getPassesDecisives() ?? 0,
                'minutesJouees' => $perf->getMinutesJouees() ?? 0,
                'noteMatch' => $perf->getNoteMatch() ?? 0,
                'cartonJaune' => $perf->getCartonJaune() ?? 0,
                'cartonRouge' => $perf->getCartonRouge() ?? 0
            ];
        }

        $matchsData = [];
        foreach ($matchs as $match) {
            $matchsData[] = [
                'id' => $match->getId(),
                'date' => $match->getDate() ? $match->getDate()->format('Y-m-d H:i:s') : null,
                'adversaire' => $match->getAdversaire(),
                'butsPour' => $match->getButsPour() ?? 0,
                'butsContre' => $match->getButsContre() ?? 0,
                'resultat' => $match->getResultat(),
                'competition' => $match->getCompetition() ?? 'Premier League',
                'lieu' => $match->getLieu() ?? 'Old Trafford'
            ];
        }

        return $this->json([
            'joueurs' => $joueursData,
            'performances' => $performancesData,
            'matchs' => $matchsData
        ]);
    }
}
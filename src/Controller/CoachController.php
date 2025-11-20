<?php

namespace App\Controller;

use App\Repository\MatchRepository;
use App\Repository\JoueurRepository;
use App\Repository\PerformanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoachController extends AbstractController
{
    #[Route('/coach/dashboard', name: 'coach_dashboard')]
    public function dashboard(
        MatchRepository $matchRepository,
        JoueurRepository $joueurRepository,
        PerformanceRepository $performanceRepository
    ): Response {
        // Récupérer tous les matchs (ou filtrer par date future)
        $matchs = $matchRepository->findAll();
        
        // Récupérer tous les joueurs
        $joueurs = $joueurRepository->findAll();
        
        // Récupérer toutes les performances
        $performances = $performanceRepository->findAll();
        
        // Préparer les données pour le template avec toJson pour éviter les erreurs de sérialisation
        $matchsData = [];
        foreach ($matchs as $match) {
            $dateMatch = $match->getDateMatch();
            $heure = $match->getHeure();
            
            $matchsData[] = [
                'id' => $match->getId(),
                'dateMatch' => $dateMatch ? $dateMatch->format('Y-m-d H:i:s') : null,
                'heure' => $heure ? $heure->format('H:i:s') : null,
                'stade' => $match->getStade(),
                'scoreDomicile' => $match->getScoreDomicile(),
                'scoreExterieur' => $match->getScoreExterieur(),
                'equipeDomicile' => [
                    'nom' => $match->getEquipeDomicile()?->getNom() ?? 'N/A'
                ],
                'equipeExterieur' => [
                    'nom' => $match->getEquipeExterieur()?->getNom() ?? 'N/A'
                ]
            ];
        }
        
        $joueursData = [];
        foreach ($joueurs as $joueur) {
            $joueursData[] = [
                'id' => $joueur->getId(),
                'nom' => $joueur->getNom() ?? '',
                'prenom' => $joueur->getPrenom() ?? '',
                'position' => $joueur->getPosition() ?? '',
                'age' => $joueur->getAge() ?? 0
            ];
        }
        
        $performancesData = [];
        foreach ($performances as $perf) {
            $performancesData[] = [
                'id' => $perf->getId(),
                'matchId' => $perf->getMatch()?->getId(),
                'joueurId' => $perf->getJoueur()?->getId(),
                'buts' => (int)($perf->getButs() ?? 0),
                'passesDecisives' => (int)($perf->getPassesDecisives() ?? 0),
                'noteMatch' => (float)($perf->getNoteMatch() ?? 0),
                'minutesJouees' => (int)($perf->getMinutesJouees() ?? 0),
                'cartonJaune' => (int)($perf->getCartonJaune() ?? 0),
                'cartonRouge' => (int)($perf->getCartonRouge() ?? 0)
            ];
        }
        
        // Debug: vérifier si les données sont bien récupérées
        dump([
            'matchs_count' => count($matchsData),
            'joueurs_count' => count($joueursData),
            'performances_count' => count($performancesData),
            'performances' => $performancesData
        ]);
        
        return $this->render('coach/dashboard.html.twig', [
            'matchs' => $matchsData,
            'joueurs' => $joueursData,
            'performances' => $performancesData,
        ]);
    }
}
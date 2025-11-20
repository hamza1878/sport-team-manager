<?php

namespace App\Controller;

use App\Entity\Performance;
use App\Form\Performance1Type;
use App\Repository\PerformanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/performance')]
final class PerformanceController extends AbstractController
{
    #[Route(name: 'app_performance_index', methods: ['GET'])]
    public function index(PerformanceRepository $performanceRepository): Response
    {
        return $this->render('performance/index.html.twig', [
            'performances' => $performanceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_performance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $performance = new Performance();
        $form = $this->createForm(Performance1Type::class, $performance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($performance);
            $entityManager->flush();

            return $this->redirectToRoute('app_performance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('performance/new.html.twig', [
            'performance' => $performance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_performance_show', methods: ['GET'])]
    public function show(Performance $performance): Response
    {
        return $this->render('performance/show.html.twig', [
            'performance' => $performance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_performance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Performance $performance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Performance1Type::class, $performance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_performance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('performance/edit.html.twig', [
            'performance' => $performance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_performance_delete', methods: ['POST'])]
    public function delete(Request $request, Performance $performance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$performance->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($performance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_performance_index', [], Response::HTTP_SEE_OTHER);
    }
}

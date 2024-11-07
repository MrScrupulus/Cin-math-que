<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmsController extends AbstractController
{
    #[Route('/films', name: 'films_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $films = $entityManager->getRepository(Film::class)->findAll();
        return $this->render('films/index.html.twig', [
            'films' => $films
        ]);
    }

    #[Route('/films/ajouter', name: 'films_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($film);
            $entityManager->flush();

            $this->addFlash('success', 'Film ajouté avec succès');
            return $this->redirectToRoute('films_index');
        }

        return $this->render('films/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/films/modifier/{id}', name: 'films_edit')]
    public function edit(Request $request, Film $film, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Film modifié avec succès');
            return $this->redirectToRoute('films_index');
        }

        return $this->render('films/edit.html.twig', [
            'film' => $film,
            'form' => $form->createView()
        ]);
    }

    #[Route('/films/supprimer/{id}', name: 'films_delete')]
    public function delete(Film $film, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($film);
        $entityManager->flush();

        $this->addFlash('success', 'Film supprimé avec succès');
        return $this->redirectToRoute('films_index');
    }
    #[Route('/films/realisateur/{director}', name: 'films_by_director')]
    public function showByDirector(string $director, EntityManagerInterface $entityManager): Response
    {
        $films = $entityManager->getRepository(Film::class)
            ->findBy(['director' => $director]);

        return $this->render('films/by_director.html.twig', [
            'films' => $films,
            'director' => $director
        ]);
    }

    #[Route('/films/realisateur/{director}/ajouter', name: 'films_add_by_director')]
    public function addByDirector(string $director, Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Film();
        $film->setDirector($director);  // Pré-remplir le réalisateur

        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($film);
            $entityManager->flush();

            $this->addFlash('success', 'Film ajouté avec succès');
            return $this->redirectToRoute('films_by_director', ['director' => $director]);
        }

        return $this->render('films/add.html.twig', [
            'form' => $form->createView(),
            'director' => $director
        ]);
    }
}

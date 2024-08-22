<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/series', name: 'app_serie')]
class SerieController extends AbstractController
{
    #[Route('/list/{page}', name: '_list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'])]
    public function list(SerieRepository $serieRepository, int $page): Response
    {
        $nbByPage = 10;
        $offset = ($page-1) * $nbByPage;

        // requête avec Méthode native du Repository
        // $series = $serieRepository->findAll();

        $criterias = ['status' => 'returning'];

        $nbTotal = $serieRepository->count();

        // requete avec fonctions natives du Respository
/**
        $series = $serieRepository->findBy(
            $criterias,
            ['vote' => 'DESC'],
            $nbByPage,
            $offset
        );
  **/

        //$series = $serieRepository->findAll();

        $series = $serieRepository->findSeriesWithSeasons($nbByPage, $offset);


        // Requete avec QueryBuilder
        //$series = $serieRepository->findBestSeriesWithSpecificGenre();

        // requete avec DQL
//        $series = $serieRepository->getBestSeriesInDQL();

        // requete avec SQL brut
//        $series = $serieRepository->getBestSeriesInRawSQL();

        return $this->render('serie/index.html.twig', [
            'series' => $series,
            'page' => $page,
            'nbPagesMax' => ceil($nbTotal / $nbByPage),
        ]);
    }

    #[Route('/detail/{id}', name:'_detail', requirements: ['id' => '\d+'])]
    public function detail(Serie $serie): Response
    {
        //$serie = $serieRepository->getTheSerie('nostrue dignissimos quae molestiae soluta labore');
        //$serie = $serieRepository->findOneBy(['name' => '']);

        // $serie = $serieRepository->find($id);

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été enregistrée');

            return $this->redirectToRoute('app_serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $em, Serie $serie): Response
    {
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($serie->getCategories() as $cat) {
                $cat->addSeries($serie);
            }

            $em->flush();

            $this->addFlash('success', 'Une série a été modifiée');

            return $this->redirectToRoute('app_serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, EntityManagerInterface $em, Serie $serie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $serie->getId(), $request->get('token'))) {
            $em->remove($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été supprimée');
        } else {
            $this->addFlash('danger', 'Cette opération est illégale');
        }

        return $this->redirectToRoute('app_serie_list');
    }

}

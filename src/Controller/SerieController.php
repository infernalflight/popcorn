<?php

namespace App\Controller;

use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SerieController extends AbstractController
{
    #[Route('/series/{page}', name: 'app_serie_list', requirements: ['page' => '\d+'], defaults: ['page' => 1], methods: ['GET'])]
    public function list(SerieRepository $serieRepository, int $page): Response
    {
        $nbByPage = 10;
        $offset = ($page-1) * $nbByPage;

        // $series = $serieRepository->findAll();

        $criterias = ['status' => 'returning', 'genres' => 'gore'];

        $nbTotal = $serieRepository->count($criterias);

        // requete avec fonctions natives du Respository
        $series = $serieRepository->findBy(
            $criterias,
            ['vote' => 'DESC'],
            $nbByPage,
            $offset
        );


        // Requete avec QueryBuilder
//        $series = $serieRepository->findBestSeriesWithSpecificGenre(['Gore', 'Drama']);

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
}

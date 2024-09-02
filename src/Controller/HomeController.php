<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(HttpClientInterface $httpClient): Response
    {

        //$response = $httpClient->request('GET', 'https://api.api-ninjas.com/v1/chucknorris');
        //$joke = json_decode($response->getContent(), true)['value'];

        return $this->render('home/index.html.twig');
    }
}

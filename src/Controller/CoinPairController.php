<?php

namespace App\Controller;

use App\Repository\CoinPairRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoinPairController extends AbstractController
{
    /**
     * @Route("/coin/pair", name="coin_pair")
     */
    public function index(CoinPairRepository $repository): Response
    {
        $coinpairs = $repository->findAll();

        return $this->render('/create_coinpair/index.html.twig', [
            'message' => 'Coin list',
            'coinpairs' => $coinpairs
        ]);
    }
}

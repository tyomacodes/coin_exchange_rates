<?php

namespace App\Controller;

use App\Entity\CoinPair;
use App\Repository\CoinPairRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    /**
     * @Route("/prices/{id}", name="price")
     */
    public function index(int $id, CoinPairRepository $repository): Response
    {
        $coinpair = $repository->find($id);
        $prices = $coinpair->getPrices();

        return $this->render('price/index.html.twig', [
            'message' => 'Coin prices',
            'prices' => $prices,
            'pair' => $id
        ]);
    }

    /**
     * @Route("/call-command", name="call-command")
     *
     * @param Request $request
     * @param KernelInterface $kernel
     * @param CoinPairRepository $repository
     * @return RedirectResponse
     * @throws Exception
     */
    public function callCommand(Request $request, KernelInterface $kernel, CoinPairRepository $repository): RedirectResponse
    {
        $pairId = $request->query->get('pair');

        $url = '/coin/pair';
        if (isset($pairId)) {
            $pair = $repository->find($pairId);
            $url = '/prices/' . $pairId;
        } else {
            $pair = null;
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $arrayInputData = ['command' => 'Kurs'];
        if ($pair instanceof CoinPair) {
            $arrayInputData['CoinPair'] = $pair->getName();
        }

        $input = new ArrayInput($arrayInputData);
        $application->run($input, null);

        return $this->redirect($url);
    }
}

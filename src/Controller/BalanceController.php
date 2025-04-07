<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BalanceController extends AbstractController
{
    #[Route('/')]
    public function home(): Response
    {
        return $this->render('home.html.twig', [
            'balance' => 0, // TODO: Get balance from storage
        ]);
    }

    #[Route('/balance')]
    public function getCurrentBalance(): Response
    {
        throw new \RuntimeException('Not implemented');
    }

    #[Route('/topUp')]
    public function topUpBalance(): Response
    {
        throw new \RuntimeException('Not implemented');
    }
}
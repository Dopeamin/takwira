<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CpanelController extends AbstractController
{
    /**
     * @Route("/cpanel", name="cpanel")
     */
    public function index(): Response
    {
        return $this->render('cpanel/index.html.twig', [
            'controller_name' => 'CpanelController',
        ]);
    }
}

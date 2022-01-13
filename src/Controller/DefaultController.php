<?php

namespace AcMarche\Duobac\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'duobac_home')]
    public function index(): Response
    {
        return $this->render('@AcMarcheDuobac/default/index.html.twig', [
        ]);
    }

    #[Route(path: '/contact', name: 'duobac_contact')]
    public function contact(): Response
    {
        return $this->render('@AcMarcheDuobac/default/contact.html.twig', []);
    }
}

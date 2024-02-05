<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/duobac')]
#[IsGranted('ROLE_DUOBAC')]
class DuobacController extends AbstractController
{
    public function __construct(
        private readonly DuobacRepository $duobacRepository,
        private readonly SituationFamilialeRepository $situationFamilialeRepository
    ) {
    }

    #[Route(path: '/', name: 'duobac_list')]
    public function index(): Response
    {
        $matricule = $this->getUser()->rdv_matricule;
        $duobacs = $this->duobacRepository->findByMatricule($matricule);
        $situations = $this->situationFamilialeRepository->findByMatricule($matricule);
        $coordonnees = $duobacs[0] ?? null;

        return $this->render(
            '@AcMarcheDuobac/duobac/index.html.twig',
            [
                'duobacs' => $duobacs,
                'situations' => $situations,
                'coordonnees' => $coordonnees,
            ]
        );
    }
}

<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DuobacController
 * @package AcMarche\Duobac\Controller
 * @Route("/duobac")
 * @IsGranted("ROLE_DUOBAC")
 */
class DuobacController extends AbstractController
{
    private DuobacRepository $duobacRepository;
    private SituationFamilialeRepository $situationFamilialeRepository;

    public function __construct(
        DuobacRepository $duobacRepository,
        SituationFamilialeRepository $situationFamilialeRepository
    ) {
        $this->duobacRepository = $duobacRepository;
        $this->situationFamilialeRepository = $situationFamilialeRepository;
    }

    /**
     * @Route("/",name="duobac_list")
     *
     */
    public function index(): Response
    {
        $matricule = $this->getUser()->getRdvMatricule();
        $duobacs = $this->duobacRepository->findOneByMatricule($matricule);
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

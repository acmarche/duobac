<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Manager\DuobacManager;
use AcMarche\Duobac\Manager\SituationManager;
use AcMarche\Duobac\Repository\DuobacRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DuobacController
 * @package AcMarche\Duobac\Controller
 * @Route("/duobac")
 * @IsGranted("ROLE_DUOBAC")
 */
class DuobacController extends AbstractController
{
    /**
     * @var DuobacRepository
     */
    private $duobacRepository;
    /**
     * @var DuobacManager
     */
    private $duobacManager;
    /**
     * @var SituationManager
     */
    private $situationManager;

    public function __construct(
        DuobacRepository $duobacRepository,
        DuobacManager $duobacManager,
        SituationManager $situationManager
    ) {
        $this->duobacRepository = $duobacRepository;
        $this->duobacManager = $duobacManager;
        $this->situationManager = $situationManager;
    }

    /**
     * @Route("/",name="duobac_list")
     *
     */
    public function index()
    {
        $matricule = $this->getUser()->getRdvMatricule();
        $duobacs = $this->duobacManager->getDuobacsByUser($this->getUser());
        $situations = $this->situationManager->getSituationsByMatricule(
            $matricule
        );

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

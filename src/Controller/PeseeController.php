<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Chart\ChartHelper;
use AcMarche\Duobac\Import\MoyenneManager;
use AcMarche\Duobac\Pesee\PeseeUtils;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Repository\PeseeRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use Exception;
use Khill\Lavacharts\Exceptions\InvalidCellCount;
use Khill\Lavacharts\Exceptions\InvalidColumnType;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Exceptions\InvalidRowDefinition;
use Khill\Lavacharts\Exceptions\InvalidRowProperty;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pesee")
 * @IsGranted("ROLE_DUOBAC")
 */
class PeseeController extends AbstractController
{
    private PeseeMoyenneRepository $peseeMoyenneRepository;
    private ChartHelper $chartHelper;
    private DuobacRepository $duobacRepository;
    private SituationFamilialeRepository $situationFamilialeRepository;
    private PeseeRepository $peseeRepository;
    private PeseeUtils $peseeUtils;

    public function __construct(
        PeseeUtils $peseeManager,
        ChartHelper $chartHelper,
        PeseeMoyenneRepository $peseeMoyenneRepository,
        DuobacRepository $duobacRepository,
        SituationFamilialeRepository $situationFamilialeRepository,
        PeseeRepository $peseeRepository,
        PeseeUtils $peseeUtils
    ) {
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
        $this->peseeManager = $peseeManager;
        $this->chartHelper = $chartHelper;
        $this->duobacRepository = $duobacRepository;
        $this->situationFamilialeRepository = $situationFamilialeRepository;
        $this->peseeRepository = $peseeRepository;
        $this->peseeUtils = $peseeUtils;
    }

    /**
     * @Route("/index",name="duobac_pesee_index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $years = [];

        if ($user) {
            $years = $this->situationFamilialeRepository->getAllYearsByMatricule($user);
        }

        return $this->render(
            '@AcMarcheDuobac/pesee/index.html.twig',
            [
                'years' => $years,
            ]
        );
    }

    /**
     * @Route("/all/year",name="duobac_pesee_all")
     */
    public function all(): Response
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();

        $duobacs = $this->duobacRepository->findOneByMatricule($user->getRdvMatricule());

        if (count($duobacs) == 0) {
            $this->addFlash('danger', 'Aucun duobac trouvé');

            return $this->redirectToRoute('duobac_home');
        }

        $years = $this->situationFamilialeRepository->getAllYearsByMatricule($rdvMatricule);

        $data = [];

        foreach ($years as $year) {
            $pesees = $this->peseeRepository->getByMatriculeAndYear($rdvMatricule, $year);
            $totalUser = $this->peseeManager->getTotal($pesees);
            $data[$year]['user'] = $totalUser;

            $charge = $this->situationFamilialeRepository->getChargeByMatriculeAndYear($rdvMatricule, $year);

            $peseesMenages = $this->peseeMoyenneRepository->findByChargeAndYear(
                $charge,
                $year
            );

            $totalMenage = $this->peseeManager->getTotal($peseesMenages);

            $data[$year]['menage'] = $totalMenage;
        }

        $chart = $this->chartHelper->genereratePesee($data, $dataMenage);

        return $this->render(
            '@AcMarcheDuobac/pesee/all.html.twig',
            [
                'years' => $years,
                'chart' => $chart,
            ]
        );
    }

    /**
     * @Route("/annee/{year}", name="duobac_by_year", methods={"GET"})
     */
    public function byYear(int $year): Response
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();
        $pesees = $this->peseeRepository->getByMatriculeAndYear($rdvMatricule, $year);
        $data = $this->peseeUtils->prepare($pesees);
        $totalUser = $this->peseeUtils->getTotal($pesees);

        $situation = $this->situationFamilialeRepository->findByMatriculeAndYear($rdvMatricule, $year, true);

        if (!$situation) {
            $charge = 0;
        } else {
            $charge = $situation->getACharge();
        }

        $peseesMenages = $this->peseeMoyenneRepository->findByChargeAndYear(
            $charge,
            $year
        );
        $totalMenage = $this->peseeUtils->getTotal($peseesMenages);

        $dataMenage = $this->peseeUtils->prepare($peseesMenages);

        $chart = $this->chartHelper->genereratePesee($data, $dataMenage);

        return $this->render(
            '@AcMarcheDuobac/pesee/show.html.twig',
            [
                'year' => $year,
                'pesees' => $pesees,
                'chart' => $chart,
                'totalUser' => $totalUser,
                'totalMenage' => $totalMenage,
                'charge' => $charge,
            ]
        );
    }
}

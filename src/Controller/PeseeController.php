<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Chart\ChartHelper;
use AcMarche\Duobac\Pesee\PeseeUtils;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Repository\PeseeRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use AcMarche\Duobac\Service\ArrayUtils;
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
        ChartHelper                  $chartHelper,
        PeseeMoyenneRepository       $peseeMoyenneRepository,
        DuobacRepository             $duobacRepository,
        SituationFamilialeRepository $situationFamilialeRepository,
        PeseeRepository              $peseeRepository,
        PeseeUtils                   $peseeUtils
    )
    {
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
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

        $duobacs = $this->duobacRepository->findByMatricule($user->getRdvMatricule());

        if (count($duobacs) == 0) {
            $this->addFlash('danger', 'Aucun duobac trouvé');

            return $this->redirectToRoute('duobac_home');
        }

        $years = $this->situationFamilialeRepository->getAllYearsByMatricule($rdvMatricule);
        $peseesMenage = $peseesUser = [];

        $totauxByYearMenage = [];
        $totalUser = $totalMenage = 0;

        foreach ($years as $year) {
            $peseesUser[$year] = $this->peseeRepository->findByDuobacsAndYear($duobacs, $year);
            $charge = $this->situationFamilialeRepository->getChargeByMatriculeAndYear($rdvMatricule, $year);
            $peseesMenage[$year] = $this->peseeMoyenneRepository->findByChargeAndYear(
                $charge,
                $year
            );
            $totauxByYearMenage[$year] = $this->peseeUtils->getTotal($peseesMenage[$year]);
            $totalUser += $this->peseeUtils->getTotal($peseesUser[$year]);
            $totalMenage += $this->peseeUtils->getTotal($peseesMenage[$year]);
        }

        $dataUser = $this->peseeUtils->prepareForAllYears($peseesUser);
        $dataMenage = $this->peseeUtils->prepareForAllYears($peseesMenage);

        $chart = $this->chartHelper->genereratePesee(
            ArrayUtils::resetKeys($dataUser),
            ArrayUtils::resetKeys($dataMenage)
        );

        return $this->render(
            '@AcMarcheDuobac/pesee/all.html.twig',
            [
                'years' => $years,
                'chart' => $chart,
                'dataUser' => $dataUser,
                'dataMenage' => $dataMenage,
                'totauxByYearMenage' => $totauxByYearMenage,
                'totalUser' => $totalUser,
                'totalMenage' => $totalMenage,
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
        $duobacs = $this->duobacRepository->findByMatricule($rdvMatricule);
        $pesees = $this->peseeRepository->findByDuobacsAndYear($duobacs, $year);
        $data = $this->peseeUtils->groupByMonthsForOneYear($pesees);
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
        $dataMenage = $this->peseeUtils->groupByMonthsForOneYear($peseesMenages);

        $chart = $this->chartHelper->genereratePesee($data, $dataMenage);

        return $this->render(
            '@AcMarcheDuobac/pesee/show.html.twig',
            [
                'year' => $year,
                'pesees' => $pesees,
                'chart' => $chart,
                'totalUser' => $totalUser,
                'dataMenage' => $dataMenage,
                'totalMenage' => $totalMenage,
                'charge' => $charge,
            ]
        );
    }
}

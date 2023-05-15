<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Chart\ChartHelper;
use AcMarche\Duobac\Pesee\PeseeUtils;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Repository\PeseeRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use AcMarche\Duobac\Service\ArrayUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/pesee')]
#[IsGranted('ROLE_DUOBAC')]
class PeseeController extends AbstractController
{
    public function __construct(
        private ChartHelper $chartHelper,
        private PeseeMoyenneRepository $peseeMoyenneRepository,
        private DuobacRepository $duobacRepository,
        private SituationFamilialeRepository $situationFamilialeRepository,
        private PeseeRepository $peseeRepository,
        private PeseeUtils $peseeUtils
    ) {
    }

    #[Route(path: '/index', name: 'duobac_pesee_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $years = [];
        if (null !== $user) {
            $years = $this->situationFamilialeRepository->getAllYearsByMatricule($user);
        }

        return $this->render(
            '@AcMarcheDuobac/pesee/index.html.twig',
            [
                'years' => $years,
            ]
        );
    }

    #[Route(path: '/all/year', name: 'duobac_pesee_all')]
    public function all(): Response
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();
        $duobacs = $this->duobacRepository->findByMatricule($user->getRdvMatricule());
        if (0 == \count($duobacs)) {
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

    #[Route(path: '/annee/{year}', name: 'duobac_by_year', methods: ['GET'])]
    public function byYear(int $year): Response
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();
        $duobacs = $this->duobacRepository->findByMatricule($rdvMatricule);
        $pesees = $this->peseeRepository->findByDuobacsAndYear($duobacs, $year);
        $data = $this->peseeUtils->groupByMonthsForOneYear($pesees);
        $totalUser = $this->peseeUtils->getTotal($pesees);
        $situation = $this->situationFamilialeRepository->findByMatriculeAndYear($rdvMatricule, $year, true);
        $charge = $situation ? $situation->getACharge() : 0;
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

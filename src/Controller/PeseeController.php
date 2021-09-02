<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Chart\ChartHelper;
use AcMarche\Duobac\Manager\DuobacFactory;
use AcMarche\Duobac\Manager\MoyenneManager;
use AcMarche\Duobac\Manager\PeseeUtils;
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
    private PeseeUtils $peseeManager;
    private ChartHelper $chartHelper;
    private MoyenneManager $moyenneManager;
    private DuobacRepository $duobacRepository;
    private SituationFamilialeRepository $situationFamilialeRepository;
    private PeseeRepository $peseeRepository;

    public function __construct(
        PeseeUtils $peseeManager,
        ChartHelper $chartHelper,
        PeseeMoyenneRepository $peseeMoyenneRepository,
        DuobacRepository $duobacRepository,
        MoyenneManager $moyenneManager,
        SituationFamilialeRepository $situationFamilialeRepository,
        PeseeRepository $peseeRepository
    ) {
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
        $this->peseeManager = $peseeManager;
        $this->chartHelper = $chartHelper;
        $this->moyenneManager = $moyenneManager;
        $this->duobacRepository = $duobacRepository;
        $this->situationFamilialeRepository = $situationFamilialeRepository;
        $this->peseeRepository = $peseeRepository;
    }

    /**
     * @Route("/index",name="duobac_pesee_index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $years = [];

        if ($user) {
            $years = $this->situationFamilialeRepository->getAllYears($user);
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

        $years = $this->situationFamilialeRepository->getAllYears($user);

        $data = [];

        foreach ($years as $year) {
            $pesees = $this->peseeRepository->getByMatriculeAndYear($rdvMatricule, $year);
            $totalUser = $this->peseeManager->getTotal($pesees);
            $data[$year]['user'] = $totalUser;

            $charge = $this->situationFamilialeRepository->getChargeByMatriculeAndYear($rdvMatricule, $year);

            $peseesMenages = $this->peseeMoyenneRepository->findOneByChargeAndYear(
                $charge,
                $year
            );

            $totalMenage = $this->peseeManager->getTotal($peseesMenages);

            $data[$year]['menage'] = $totalMenage;
        }

        $graph1 = $graph2 = null;

        try {
            $lava = $this->chartHelper->createForAllYears($data);
            $graph1 = $lava->render('ColumnChart', 'ColumnChart', 'column-chart');
            $graph2 = $lava->render('LineChart', 'LineChart', 'line-chart');
        } catch (InvalidCellCount $e) {
        } catch (InvalidColumnType $e) {
        } catch (InvalidLabel $e) {
        } catch (InvalidRowDefinition $e) {
        } catch (InvalidRowProperty $e) {
        }

        return $this->render(
            '@AcMarcheDuobac/pesee/all.html.twig',
            [
                'years' => $years,
                'graph1' => $graph1,
                'graph2' => $graph2,
            ]
        );
    }

    /**
     * @Route("/{year}", name="duobac_annee", methods={"GET"})
     */
    public function annee(int $year): Response
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();

        $pesees = $this->peseeRepository->getByMatriculeAndYear($rdvMatricule, $year);

        $chart = $this->chartHelper->genereratePesee($pesees);

        return $this->render(
            '@AcMarcheDuobac/pesee/show.html.twig',
            [
                'year' => $year,
                'pesees' => $pesees,
                'chart' => $chart,
            ]
        );
    }

    /**
     * Route("/parannee/{year}",name="duobac_annee")
     */
    public function anneeold(int $year): Response
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();

        try {
            $pesees = $this->peseeRepository->getByMatriculeAndYear($rdvMatricule, $year);
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('duobac_home');
        }

        $this->chartHelper->genereratePesee($pesees);

        $total = $this->peseeManager->getTotal($pesees);
        $this->moyenneManager->setMoyennes($pesees);
        $peseesGrouped = $this->peseeManager->groupPeseesByMonth($pesees);

        $charge = $this->situationFamilialeRepository->getChargeByMatriculeAndYear($rdvMatricule, $year);

        $peseesGrouped = $this->peseeManager->setMissingMonths($peseesGrouped, $year, $charge);

        $graph1 = $graph2 = null;

        try {
            $lava = $this->chartHelper->createByYear($peseesGrouped, $year);
            $graph1 = $lava->render('ColumnChart', 'Column'.$year, 'column-chart'.$year);
            $graph2 = $lava->render('LineChart', 'Line'.$year, 'line-chart'.$year);
        } catch (InvalidCellCount $e) {
        } catch (InvalidColumnType $e) {
        } catch (InvalidLabel $e) {
        } catch (InvalidRowDefinition $e) {
        } catch (InvalidRowProperty $e) {
        }

        $chart = $this->chartHelper->create($peseesGrouped, $year);

        return $this->render(
            '@AcMarcheDuobac/pesee/show.html.twig',
            [
                'year' => $year,
                'total' => $total,
                'pesees' => $pesees,
                'graph2' => $graph2,
                'graph1' => $graph1,
            ]
        );
    }
}

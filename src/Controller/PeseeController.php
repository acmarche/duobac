<?php

namespace AcMarche\Duobac\Controller;

use AcMarche\Duobac\Manager\DuobacManager;
use AcMarche\Duobac\Manager\MoyenneManager;
use AcMarche\Duobac\Manager\PeseeManager;
use AcMarche\Duobac\Manager\SituationManager;
use AcMarche\Duobac\Repository\PeseeMoyenneRepository;
use AcMarche\Duobac\Repository\PeseeRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use AcMarche\Duobac\Service\ChartHelper;
use Khill\Lavacharts\Exceptions\InvalidCellCount;
use Khill\Lavacharts\Exceptions\InvalidColumnType;
use Khill\Lavacharts\Exceptions\InvalidLabel;
use Khill\Lavacharts\Exceptions\InvalidRowDefinition;
use Khill\Lavacharts\Exceptions\InvalidRowProperty;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @package AcMarche\Duobac\Controller
 * @Route("/pesee")
 * @IsGranted("ROLE_DUOBAC")
 */
class PeseeController extends AbstractController
{
    /**
     * @var PeseeRepository
     */
    private $peseeRepository;
    /**
     * @var PeseeMoyenneRepository
     */
    private $peseeMoyenneRepository;
    /**
     * @var SituationFamilialeRepository
     */
    private $situationFamilialeRepository;
    /**
     * @var PeseeManager $peseeManager
     */
    private $peseeManager;
    /**
     * @var ChartHelper
     */
    private $chartHelper;
    /**
     * @var DuobacManager
     */
    private $duobacManager;
    /**
     * @var SituationManager
     */
    private $situationManager;
    /**
     * @var MoyenneManager
     */
    private $moyenneManager;

    public function __construct(
        DuobacManager $duobacManager,
        SituationManager $situationManager,
        PeseeRepository $peseeRepository,
        PeseeManager $peseeManager,
        ChartHelper $chartHelper,
        PeseeMoyenneRepository $peseeMoyenneRepository,
        MoyenneManager $moyenneManager,
        SituationFamilialeRepository $situationFamilialeRepository
    ) {
        $this->peseeRepository = $peseeRepository;
        $this->peseeMoyenneRepository = $peseeMoyenneRepository;
        $this->situationFamilialeRepository = $situationFamilialeRepository;
        $this->peseeManager = $peseeManager;
        $this->chartHelper = $chartHelper;
        $this->duobacManager = $duobacManager;
        $this->situationManager = $situationManager;
        $this->moyenneManager = $moyenneManager;
    }

    /**
     * @Route("/all",name="duobac_pesee_all")
     *
     */
    public function all()
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();

        $duobacs = $this->duobacManager->getDuobacsByUser($user);

        if (count($duobacs) == 0) {
            $this->addFlash('danger', 'Aucun duobac trouvé');

            return $this->redirectToRoute('duobac_home');
        }

        $years = $this->situationManager->getAllYears($user);

        $data = [];

        foreach ($years as $year) {
            $pesees = $this->peseeManager->getByMatriculeAndYear($rdvMatricule, $year);
            $totalUser = $this->peseeManager->getTotal($pesees);
            $data[$year]['user'] = $totalUser;

            $charge = $this->situationManager->getChargeByMatriculeAndYear($rdvMatricule, $year);

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
     * @Route("/parannee/{year}",name="duobac_annee")
     *
     */
    public function annee(int $year)
    {
        $user = $this->getUser();
        $rdvMatricule = $user->getRdvMatricule();

        try {
            $pesees = $this->peseeManager->getByMatriculeAndYear($rdvMatricule, $year);
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('duobac_home');
        }

        $total = $this->peseeManager->getTotal($pesees);
        $this->moyenneManager->setMoyennes($pesees);
        $peseesGrouped = $this->peseeManager->groupPeseesByMonth($pesees);

        $charge = $this->situationManager->getChargeByMatriculeAndYear($rdvMatricule, $year);

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

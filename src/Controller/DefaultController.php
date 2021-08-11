<?php

namespace AcMarche\Duobac\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Duobac\Manager\SituationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DefaultController extends AbstractController
{
    private SituationManager $situationManager;

    public function __construct(SituationManager $situationManager)
    {
        $this->situationManager = $situationManager;
    }

    /**
     * @Route("/",name="duobac_home")
     *
     */
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => 100]],
                ],
            ],
        ]);

        return $this->render('@AcMarcheDuobac/default/index.html.twig', [
            'chart' => $chart,
        ]);
    }

    /**
     * @Route("/contact",name="duobac_contact")
     *
     */
    public function contact(): Response
    {
        return $this->render('@AcMarcheDuobac/default/contact.html.twig', []);
    }

    public function menuUser(): Response
    {
        $user = $this->getUser();
        $years = [];

        if ($user) {
            $years = $this->situationManager->getAllYears($user);
        }

        return $this->render(
            '@AcMarcheDuobac/default/_menu_user.html.twig',
            [
                'years' => $years,
            ]
        );
    }
}

<?php

namespace AcMarche\Duobac\Chart;

use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\PeseeMoyenne;
use AcMarche\Duobac\Service\DateUtils;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartHelper
{
    private ChartBuilderInterface $chartBuilder;

    public function __construct(ChartBuilderInterface $chartBuilder)
    {
        $this->chartBuilder = $chartBuilder;
    }

    /**
     * https://www.chartjs.org/docs/latest/general/data-structures.html
     * https://duobac.marche.be/pesee/parannee/2019
     * http://duobac.local/pesee/2020
     * @param array|Pesee[] $pesees
     * @param array|PeseeMoyenne[] $moyennes
     * @return Chart
     */
    public function genereratePesee(array $pesees, array $moyennes): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $labels = DateUtils::getAllMonths();

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Mes pesées',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'minBarLength' => 1,
                    'data' => $pesees,
                ],
                [
                    'label' => 'Pesées moyennes ménages',
                    'backgroundColor' => 'rgb(102, 204, 0)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'minBarLength' => 1,
                    'data' => $moyennes,
                ],
            ],
        ]);

        $chart->setOptions([
            'title' => ['text' => 'Relevés xx', 'display' => false],
            'scales' => [
                'vAxis' => [
                    'title' => ['text' => 'Poids en Kg', 'display' => true],
                ],
                'yAxis' => [
                    'title' => ['text' => 'Poids en Kg', 'display' => true],
                ],
                'yAxes' => [
                    ['ticks' => ['min' => 0]],
                ],

            ],
        ]);

        return $chart;
    }
}

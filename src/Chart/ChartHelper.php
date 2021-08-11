<?php

namespace AcMarche\Duobac\Chart;

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
     * @param array|\AcMarche\Duobac\Entity\Pesee[] $pesees
     * @return \Symfony\UX\Chartjs\Model\Chart
     */
    public function genereratePesee(array $pesees): Chart
    {
        dump($pesees);
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $labels = DateUtils::getAllMonths();
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45, 0, 0, 0, 0, 0],
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

        return $chart;
    }
}

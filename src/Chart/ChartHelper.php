<?php

namespace AcMarche\Duobac\Chart;

use AcMarche\Duobac\Service\ArrayUtils;
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
     * @param array|\AcMarche\Duobac\Entity\Pesee[] $pesees
     * @return Chart
     */
    public function genereratePesee(array $pesees): Chart
    {
        $data = ArrayUtils::initArraMonths();
        foreach ($pesees as $pesee){
            dump($pesee->getDatePesee()->format('m-Y'), $pesee->getPoids());
            $data[$pesee->getDatePesee()->format('n')] += $pesee->getPoids();
        }
        $data = ArrayUtils::resetKeys($data);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $labels = DateUtils::getAllMonths();
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data,
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

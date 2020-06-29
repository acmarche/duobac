<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 16/11/18
 * Time: 20:27
 */

namespace AcMarche\Duobac\Service;

use AcMarche\Duobac\Entity\PeseeInterface;
use Khill\Lavacharts\Lavacharts;

class ChartHelper
{
    public function __construct()
    {
    }

    public static function getInstance(): Lavacharts
    {
        return new Lavacharts();
    }

    /**
     * @param array $data
     * @return Lavacharts
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowProperty
     */
    public function createForAllYears(array $data)
    {
        $lavacharts = self::getInstance();
        $stocksTable = $lavacharts->DataTable();

        $stocksTable->addStringColumn('Année')
            ->addNumberColumn('Vous')
            ->addNumberColumn('Moyenne ménage');

        foreach ($data as $year => $row) {
            $rowData = [$year, $row['user'], $row['menage']];
            $stocksTable->addRow($rowData);
        }

        $years = count($data) + 1;

        $lavacharts->LineChart(
            'LineChart',
            $stocksTable,
            [
                'title' => 'Pesées '.$years.' ans',
                'axisTitlesPosition' => 'string',
                'bar' => [],        //bar Options i.e 'groupWidth'=> '95%'
                'hAxis' => [],        //HorizontalAxis Options
                'isStacked' => true,
                'pointSize' => 12,
                'vAxis' => ['title' => 'Poids en Kg',],        //VerticalAxis Options
            ]
        );

        $lavacharts->ColumnChart(
            'ColumnChart',
            $stocksTable,
            [
                'title' => 'Pesées '.$years.' ans',
                'vAxis' => ['title' => 'Poids en Kg',],
                'titleTextStyle' => [
                    'color' => '#eb6b2c',
                    'fontSize' => 14,
                ],
            ]
        );

        return $lavacharts;
    }

    /**
     * @param iterable|PeseeInterface[] $pesees
     * @param int $year
     * @return Lavacharts
     * @throws \Khill\Lavacharts\Exceptions\InvalidCellCount
     * @throws \Khill\Lavacharts\Exceptions\InvalidColumnType
     * @throws \Khill\Lavacharts\Exceptions\InvalidLabel
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowDefinition
     * @throws \Khill\Lavacharts\Exceptions\InvalidRowProperty
     */
    public function createByYear(iterable $pesees, int $year)
    {
        $lava = self::getInstance();
        $stocksTable = $lava->DataTable();

        $stocksTable->addStringColumn('Mois')
            ->addNumberColumn('Vous')
            ->addNumberColumn('Moyenne ménage');

        foreach ($pesees as $numMois => $data) {
            $mois = DateUtils::getTitleMonth($numMois);
            $rowData = [$mois, $data['poids'], $data['menage']];
            $stocksTable->addRow($rowData);
        }

        $lava->LineChart(
            'Line'.$year,
            $stocksTable,
            [
                'title' => 'Relevés '.$year,
                'axisTitlesPosition' => 'string',
                'bar' => [],        //bar Options i.e 'groupWidth'=> '95%'
                'hAxis' => [],        //HorizontalAxis Options
                'isStacked' => true,
                'pointSize' => 12,
                'vAxis' => ['title' => 'Poids en Kg',],        //VerticalAxis Options
            ]
        );

        $lava->ColumnChart(
            'Column'.$year,
            $stocksTable,
            [
                'title' => 'Relevés '.$year,
                'vAxis' => ['title' => 'Poids en Kg',],
                'titleTextStyle' => [
                    'color' => '#eb6b2c',
                    'fontSize' => 14,
                ],
            ]
        );


        return $lava;
    }
}

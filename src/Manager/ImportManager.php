<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/10/18
 * Time: 16:13
 */

namespace AcMarche\Duobac\Manager;

use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\SituationFamiliale;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class ImportManager
{
    private string $format = 'd/m/Y';
    private SituationFamilialeRepository $situationFamilialeRepository;
    private DuobacRepository $duobacRepository;

    public function __construct(
        DuobacRepository $duobacRepository,
        SituationFamilialeRepository $situationFamilialeRepository
    ) {
        $this->situationFamilialeRepository = $situationFamilialeRepository;
        $this->duobacRepository = $duobacRepository;
    }

    public function updateSituationFamiliale(string $matricule, string $puce, int $year, int $aCharge): void
    {
        if (($situationFamiliale = $this->situationFamilialeRepository->findOneBy(
                ['rdv_matricule' => $matricule, 'annee' => $year]
            )) === null) {
            $situationFamiliale = new SituationFamiliale($matricule, $puce, $year, $aCharge);
            $this->situationFamilialeRepository->persist($situationFamiliale);
        }
        $situationFamiliale->setACharge($aCharge);
        $this->situationFamilialeRepository->flush();
    }

    public function treatment(array $data, int $year): void
    {
        $matricule = $data[0];

        if ($this->skip($matricule)) {
            return;
        }

        $nom = $data[1];
        $prenom = $data[2];
        $codePostal = $data[3];
        $codeRue = (int)($data[4]);
        $rue = $data[5];
        $adresseNumero = $data[6];
        $adresseIndice = $data[7];
        $adresseBoite = $data[8];
        $codeRedevable = (int)$data[9];
        $codeClass = (int)$data[10];
        $aCharge = (int)($data[11]);
        $puce = $data[12];
        $numContainer = $data[13];
        $purDateDebut = $data[14];
        $purDateFin = $data[15];
        $codeTarif = $data[16];
        $codeCapacite = (int)($data[17]);
        $codeClef = (int)($data[18]);
        $codeDechet = (int)($data[19]);

        if (($duobac = $this->duobacRepository->findOneByMatriculeAndPuce($matricule, $puce)) === null) {
            $duobac = new Duobac($matricule, $puce);
            $this->duobacRepository->persist($duobac);
        }

        $duobac->setRdvNom(utf8_encode($nom));
        $duobac->setRdvPrenom1(utf8_encode($prenom));
        $duobac->setLocCodePost($codePostal);
        $duobac->setRueCodeRue($codeRue);
        $duobac->setRueLib1lg(utf8_encode($rue));
        $duobac->setAdrNumero($adresseNumero);
        $duobac->setAdrIndice($adresseIndice);
        $duobac->setAdrBoite(utf8_encode($adresseBoite));
        $duobac->setRdvCodRedevable($codeRedevable);
        $duobac->setRdvCodClasse($codeClass);
        $duobac->setPucNoConteneur(utf8_encode($numContainer));
        if ($purDateDebut) {
            $duobac->setPurDateDebut(DateTime::createFromFormat($this->format, $purDateDebut));
        }
        if ($purDateFin) {
            $duobac->setPurDateFin(DateTime::createFromFormat($this->format, $purDateFin));
        }
        $duobac->setPurCodTarification($codeTarif);
        $duobac->setPucCodCapacite($codeCapacite);
        $duobac->setPurCodClef($codeClef);
        $duobac->setPucCodDechet($codeDechet);

        $this->duobacRepository->flush();

        $this->updateSituationFamiliale($matricule, $puce, $year, $aCharge);
        $i = 20;
        $max = count($data);

        if ($max > 20) {
            while ($i < $max) {
                $date = $data[$i];
                $i += 1;
                $pesee = $data[$i];
                if ($pesee != null && $date != null) {
                    $date = DateTime::createFromFormat($this->format, $date);
                    $pesee = preg_replace('#,#', '.', $pesee);
                    $this->insertReleve($puce, $date, $pesee, $aCharge);
                }
                $i += 1;
            }
        }
        $this->duobacRepository->flush();
    }

    function getLines($file): iterable
    {
        $handle = fopen($file, 'r');
        try {
            while (($line = fgetcsv($handle, 1000, "|")) !== false) {
                yield $line;
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param DateTime|DateTimeImmutable $date
     */
    public function insertReleve(string $puce, DateTimeInterface $date, float $poid, int $acharge): void
    {
        $pesee = new Pesee($puce, $date, $poid, $acharge);
        $this->duobacRepository->persist($pesee);
    }

    public function skip($matricule): bool
    {
        return !(preg_match("#\\d+#", $matricule) && strlen($matricule) > 2);
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 30/10/18
 * Time: 16:13.
 */

namespace AcMarche\Duobac\Import;

use AcMarche\Duobac\Entity\Duobac;
use AcMarche\Duobac\Entity\Pesee;
use AcMarche\Duobac\Entity\SituationFamiliale;
use AcMarche\Duobac\Repository\DuobacRepository;
use AcMarche\Duobac\Repository\SituationFamilialeRepository;
use DateTime;

class ImportManager
{
    private string $format = 'd/m/Y';

    public function __construct(
        private readonly DuobacRepository $duobacRepository,
        private readonly SituationFamilialeRepository $situationFamilialeRepository
    ) {
    }

    public function read(string $fileName):void
    {
        $spl = new \SplFileObject($fileName);
        $spl->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_AHEAD);
        $spl->setCsvControl('|');
        foreach ($spl as $key => $row) {
            if ($key === 0) {
                $header = $row[0];
                continue;
            }
        }
    }

    public function updateSituationFamiliale(string $matricule, string $puce, int $year, int $aCharge): void
    {
        if (($situationFamiliale = $this->situationFamilialeRepository->findOneBy(
                ['rdv_matricule' => $matricule, 'annee' => $year]
            )) === null) {
            $situationFamiliale = new SituationFamiliale($matricule, $puce, $year, $aCharge);
            $this->situationFamilialeRepository->persist($situationFamiliale);
        }
        $situationFamiliale->a_charge = $aCharge;
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

        $duobac->rdv_nom = $nom;
        $duobac->rdv_prenom_1 = $prenom;
        $duobac->loc_code_post = $codePostal;
        $duobac->rue_code_rue = $codeRue;
        $duobac->rue_lib_1lg = $rue;
        $duobac->adr_numero = $adresseNumero;
        $duobac->adr_indice = $adresseIndice;
        $duobac->adr_boite = $adresseBoite;
        $duobac->rdv_cod_redevable = $codeRedevable;
        $duobac->rdv_cod_classe = $codeClass;
        $duobac->puc_no_conteneur = $numContainer;
        if ($purDateDebut) {
            $duobac->pur_date_debut = DateTime::createFromFormat($this->format, $purDateDebut);
        }
        if ($purDateFin) {
            $duobac->pur_date_fin = DateTime::createFromFormat($this->format, $purDateFin);
        }
        $duobac->pur_cod_tarification = $codeTarif;
        $duobac->puc_cod_capacite = $codeCapacite;
        $duobac->pur_cod_clef = $codeClef;
        $duobac->puc_cod_dechet = $codeDechet;

        $this->duobacRepository->flush();

        $this->updateSituationFamiliale($matricule, $puce, $year, $aCharge);
        $i = 20;
        $max = \count($data);

        if ($max > 20) {
            while ($i < $max) {
                $date = $data[$i];
                ++$i;
                $pesee = $data[$i];
                if (null != $pesee && null != $date) {
                    $date = DateTime::createFromFormat($this->format, $date);
                    $pesee = preg_replace('#,#', '.', $pesee);
                    $this->insertReleve($puce, $date, $pesee, $aCharge);
                }
                ++$i;
            }
        }
        $this->duobacRepository->flush();
    }

    public function getLines($file): iterable
    {
        $handle = fopen($file, 'r');
        try {
            while (($line = fgetcsv($handle, 1000, '|')) !== false) {
                yield $line;
            }
        } finally {
            fclose($handle);
        }
    }

    public function insertReleve(string $puce, \DateTime|\DateTimeImmutable $date, float $poid, int $acharge): void
    {
        $pesee = new Pesee($puce, $date, $poid, $acharge);
        $this->duobacRepository->persist($pesee);
    }

    public function skip($matricule): bool
    {
        return !(preg_match('#\\d+#', $matricule) && \strlen($matricule) > 2);
    }
}

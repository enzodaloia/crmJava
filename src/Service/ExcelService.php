<?php

namespace App\Service;

use App\Entity\DiplomePrepare;
use App\Entity\Etablissement;
use App\Entity\GenreEtablissement;
use App\Entity\NiveauEtude;
use App\Entity\OrigineLead;
use App\Entity\Type;
use App\Entity\TypeEtablissement;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;

class ExcelService
{
    private $_entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    public function exportProspectToExcel($prospects): Response
    {
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 20,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0000FF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $filename = 'export_des_prospects.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Prospects');

        $row = 1;
        $sheet->setCellValue('A'.$row, 'Type');
        $sheet->setCellValue('B'.$row, 'Origine du lead');
        $sheet->setCellValue('C'.$row, 'Mois');
        $sheet->setCellValue('D'.$row, 'Année (du contact)');
        $sheet->setCellValue('E'.$row, 'Civilité');
        $sheet->setCellValue('F'.$row, 'Nom');
        $sheet->setCellValue('G'.$row, 'Prénom');
        $sheet->setCellValue('H'.$row, 'Téléphone');
        $sheet->setCellValue('I'.$row, 'Email');
        $sheet->setCellValue('J'.$row, 'Niveau actuel');
        $sheet->setCellValue('K'.$row, 'Diplôme préparé');
        $sheet->setCellValue('L'.$row, 'Spécialité');
        $sheet->setCellValue('M'.$row, 'Établissement');
        $sheet->setCellValue('N'.$row, 'Autre Établissement');
        $sheet->setCellValue('O'.$row, 'Code postal');
        $sheet->setCellValue('P'.$row, 'Ville');
        $sheet->setCellValue('Q'.$row, 'Type d\'Établissement');
        $sheet->setCellValue('R'.$row, 'Année de recrutement');
        $sheet->setCellValue('S'.$row, 'Entrée en');
        $sheet->setCellValue('T'.$row, 'Commentaires');
        $sheet->setCellValue('U'.$row, 'Type de contact');

        foreach (range('A', 'U') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(35);
        }
        $sheet->getStyle('A1:U1')->applyFromArray($headerStyleArray);
        
        $row = 2;
        foreach($prospects as $d){
            if($d->getTypeSalon()){
                $sheet->setCellValue('A'.$row, $d->getTypeSalon()->getLibelle());
            }
            if($d->getOrigineLead()){
                $sheet->setCellValue('B'.$row, $d->getOrigineLead()->getLibelle());
            }
            if($d->getDateContact()){
                $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, null, null, 'MMMM');
                $sheet->setCellValue('C'.$row, $formatter->format($d->getDateContact()));
                $sheet->setCellValue('D'.$row, $d->getDateContact()->format('Y'));
            }
            $sheet->setCellValue('E'.$row, $d->getCivilite());
            $sheet->setCellValue('F'.$row, $d->getNom());
            $sheet->setCellValue('G'.$row, $d->getPrenom());
            $sheet->setCellValue('H'.$row, $d->getTelephone());
            $sheet->setCellValue('I'.$row, $d->getEmail());
            if($d->getNiveauActuel()){
                $sheet->setCellValue('J'.$row, $d->getNiveauActuel()->getLibelle());
            }
            if($d->getDiplomePrepare()){
                $sheet->setCellValue('K'.$row, $d->getDiplomePrepare()->getLibelle());
            }
            $sheet->setCellValue('L'.$row, $d->getSpecialite());
            if($d->getEtablissement()){
                $sheet->setCellValue('M'.$row, $d->getEtablissement()->getLibelle());
                $sheet->setCellValue('O'.$row, $d->getEtablissement()->getCodePostal());
                $sheet->setCellValue('P'.$row, $d->getEtablissement()->getVille());
                $sheet->setCellValue('Q'.$row, $d->getEtablissement()->getTypeEtablissement()->getLibelle());
            }
            $sheet->setCellValue('R'.$row, $d->getDateRecrutement()->format('Y'));
            $entreEnChoices = [
                0 => 'Bachelor 1',
                1 => 'Bachelor 2',
                2 => 'Ingé 1',
                3 => 'Ingé 2',
                4 => 'Ingé 3',
            ];
            $entreEnValue = $d->getEntreEn();
            $sheet->setCellValue('S'.$row, isset($entreEnChoices[$entreEnValue]) ? $entreEnChoices[$entreEnValue] : '');
            $sheet->setCellValue('T'.$row, $d->getCommentaire());
            $sheet->setCellValue('U'.$row, $d->getTypeContact());

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'phpExcel');
        $writer->save($temp_file);

        return new Response(
            file_get_contents($temp_file),
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
<?php

namespace App\Controller\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;


class adminController extends EasyAdminController
{
   /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * AdminController constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return StreamedResponse
     * @throws \Exception
     */
    public function exportExcelAction()
    {
        $entity = $this->request->get('entity');

        if (! $entity) {
            throw new \Exception('Error');
        }

        $sortDirection = $this->request->query->get('sortDirection');
        if (empty($sortDirection) || !in_array(strtoupper($sortDirection), ['ASC', 'DESC'])) {
            $sortDirection = 'DESC';
        }

        $queryBuilder = $this->createListQueryBuilder(
            $this->entity['class'],
            $sortDirection,
            $this->request->query->get('sortField'),
            $this->entity['list']['dql_filter']
        );

        $entities = new ArrayCollection($queryBuilder->getQuery()->getArrayResult());

        if (! count($entities)) {
            throw new \Exception("No data to export");
        }

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;

        $headers = array_keys((array) $entities[0]);

        // write headers
        foreach ($headers as $i => $header) {
            $sheet->setCellValueByColumnAndRow(++$i, $row, $this->translator->trans($this->humanize($header)));
        }

        // set header style
        $styleArray = [
            'font'  => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ]];

        $spreadsheet->getActiveSheet()
            ->getStyle(Coordinate::stringFromColumnIndex(1) .'1:'. Coordinate::stringFromColumnIndex(count($headers)) .'1')
            ->applyFromArray($styleArray)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('066885')
        ;

        // write values
        foreach ($entities as $entry) {
            $row++;
            $values = array_values($entry);

            foreach ($values as $i => $value) {
                $sheet->setCellValueByColumnAndRow(++$i, $row, $value);
            }
        }

        // autosize all columns
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $spreadsheet->setActiveSheetIndex($spreadsheet->getIndex($worksheet));

            $sheet = $spreadsheet->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }

        $sheet->setTitle('Export - '. $entity);

        // Create and force download the file
        $streamedResponse = new StreamedResponse();

        $streamedResponse->setCallback(function () use ($spreadsheet) {
            // $spreadsheet = //create you spreadsheet here;
            $writer =  new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $streamedResponse->setStatusCode(200);
        $streamedResponse->headers->set('Content-Type', 'application/vnd.ms-excel');
        $streamedResponse->headers->set('Content-Disposition', 'attachment; filename="Export.xlsx"');

        return $streamedResponse->send();
    }
	
    /**
     * @param string $value
     * @return string
     */
    private function humanize(string $value): string
    {
        return ucfirst(strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $value))));
    }

}

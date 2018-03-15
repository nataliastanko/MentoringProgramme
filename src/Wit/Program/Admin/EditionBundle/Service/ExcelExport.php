<?php

namespace Wit\Program\Admin\EditionBundle\Service;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Doctrine\ORM\EntityManager;
use Liuggio\ExcelBundle\Factory as PhpExcel;
use Wit\Program\Admin\EditionBundle\Entity\Edition;

/**
 * @link https://github.com/liuggio/ExcelBundle
 * @link https://github.com/PHPOffice/PHPExcel/tree/develop/Examples
 */
class ExcelExport
{
    /**
     * @var PhpExcel
     */
    private $phpexcel;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param PhpExcel        $phpexcel        PHPExcel service version 1.x for symfony ~2.4
     * @param EntityManager   $em
     */
    public function __construct(PhpExcel $phpexcel, Translator $translator, EntityManager $em
    ) {
        $this->phpexcel = $phpexcel;
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * Generale excel file.
     * Prepare mentees list from given edition.
     *
     * The list of the types are:
     *
     * 'Excel5'
     * 'Excel2007'
     * 'Excel2003XML'
     * 'OOCalc'
     * 'SYLK'
     * 'Gnumeric'
     * 'HTML'
     * 'CSV'
     *
     * @param  Edition $edition
     * @return string filename path
     */
    public function exportEditionMenteesList(Edition $edition) {
        /*
         * ask the service for an Excel5
         * @var \PHPExcel
         */
        $phpExcelObject = $this->phpexcel->createPHPExcelObject();

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // $phpExcelObject = $this->setExcelDocumentProperties($phpExcelObject);

        // set sheet's title
        $phpExcelObject->getActiveSheet()->setTitle(
            $edition->getName().' edition mentees list'
        );

        // set doc's properties
        $phpExcelObject->getProperties()->setCreator('Tech Leaders')
            ->setLastModifiedBy('Tech Leaders')
            ->setTitle('Tech Leaders '.$edition->getName().' edition mentees list')
            ->setSubject('Mentees list')
            ->setDescription('Tech Leaders '.$edition->getName().' edition mentees list')
            ->setKeywords('export techleaders edition mentees list')
        ;

        $persons = $this->em->getRepository('WitProgramAdminEditionBundle:Person')
            ->getFromEdition($edition->getId());

        $personsCount = count($persons);

        // write headers
        $phpExcelObject->getActiveSheet()
            ->setCellValue('A1', $this->translator->trans('edition'))
            ->setCellValue('B1', $this->translator->trans('person.name'))
            ->setCellValue('C1', $this->translator->trans('person.lastName'))
            ->setCellValue('D1', $this->translator->trans('submission.date'))
            ->setCellValue('E1', $this->translator->trans('email'))
            ->setCellValue('F1', $this->translator->trans('mentee.isAccepted'))
            ->setCellValue('G1', $this->translator->trans('mentor.self'))
            ->setCellValue('H1', $this->translator->trans('mentee.isChosen'))
        ;

        // for every person
        $currentRow = 2;

        // collect persons events
        foreach ($persons as $person) {

            $phpExcelObject->getActiveSheet()
                // edition
                ->setCellValue('A'.$currentRow, $person->getEdition()->getName())
                // person surname
                ->setCellValue('B'.$currentRow, $person->getLastName())
                // person name
                ->setCellValue('C'.$currentRow, $person->getName())
                // submission date
                ->setCellValue('D'.$currentRow, $person->getCreatedAt()->format('Y-m-d H:i:s'))
                // person email
                ->setCellValue('E'.$currentRow, $person->getEmail())
                // person isAccepted
                ->setCellValue(
                    'F'.$currentRow,
                    $person->getIsAccepted() ? $this->translator->trans('yes') : $this->translator->trans('no')
                )
                // person mentor
                ->setCellValue('G'.$currentRow, $person->getMentor()->getFullName())
                // person isChosen
                ->setCellValue(
                    'H'.$currentRow,
                    $person->getIsChosen() ? $this->translator->trans('yes') : $this->translator->trans('no')
                )
            ;
            // next row
            ++$currentRow;
        }

        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')
            ->setAutoSize(true)
            // ->setWidth('20')
            ;

        // create the writer
        $writer = $this->phpexcel->createWriter($phpExcelObject, 'Excel5');

        // create the response
        /**
         * @var StreamedResponse
         */
        $response = $this->phpexcel->createStreamedResponse($writer);

        return $response;
    }

    /**
     * Generale excel file.
     *
     * The list of the types are:
     *
     * 'Excel5'
     * 'Excel2007'
     * 'Excel2003XML'
     * 'OOCalc'
     * 'SYLK'
     * 'Gnumeric'
     * 'HTML'
     * 'CSV'
     *
     * @param  Edition $edition
     * @return string filename path
     */
    public function exportEditionChosen(Edition $edition) {
        /*
         * ask the service for an Excel5
         * @var \PHPExcel
         */
        $phpExcelObject = $this->phpexcel->createPHPExcelObject();

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // $phpExcelObject = $this->setExcelDocumentProperties($phpExcelObject);

        // set sheet's title
        $phpExcelObject->getActiveSheet()->setTitle(
            $edition->getName().' edition - Chosen mentees'
        );

        // set doc's properties
        $phpExcelObject->getProperties()->setCreator('Tech Leaders')
            ->setLastModifiedBy('Tech Leaders')
            ->setTitle('Tech Leaders '.$edition->getName().' edition - Chosen mentees')
            ->setSubject('Chosen mentees')
            ->setDescription('Tech Leaders '.$edition->getName().' edition - Chosen mentees')
            ->setKeywords('export techleaders edition results announcement chosen mentees')
        ;

        // chosen mentees
        $persons = $this->em->getRepository('WitProgramAdminEditionBundle:Person')
            ->getFromEdition($edition->getId(), true);

        $personsCount = count($persons);

        // write headers
        $phpExcelObject->getActiveSheet()
            ->setCellValue('A1', $this->translator->trans('edition'))
            ->setCellValue('B1', $this->translator->trans('mentee.self'))
            ->setCellValue('C1', $this->translator->trans('mentee.email'))
            ->setCellValue('D1', $this->translator->trans('mentor.self'))
            ->setCellValue('E1', $this->translator->trans('mentor.email'))
            ->setCellValue('F1', $this->translator->trans('mentee.originalMentorChoice'))
        ;

        // for every person
        $currentRow = 2;

        // collect persons events
        foreach ($persons as $person) {

            $phpExcelObject->getActiveSheet()
                // edition
                ->setCellValue('A'.$currentRow, $person->getEdition()->getName())
                // mentee's name
                ->setCellValue('B'.$currentRow, $person->getFullName())
                // mentee's email
                ->setCellValue('C'.$currentRow, $person->getEmail())
                // mentee's mentor
                ->setCellValue('D'.$currentRow, $person->getMentor()->getFullName())
                // mentee's mentor's email
                ->setCellValue('E'.$currentRow, $person->getMentor()->getEmail())
                // originalMentorChoice
                ->setCellValue('F'.$currentRow, $person->getOriginalMentorChoice())
            ;
            // next row
            ++$currentRow;
        }

        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

        // create the writer
        $writer = $this->phpexcel->createWriter($phpExcelObject, 'Excel5');

        // create the response
        /**
         * @var StreamedResponse
         */
        $response = $this->phpexcel->createStreamedResponse($writer);

        return $response;
    }

    /**
     * Get day type to excel.
     *
     * @param array     $notWorkingDays
     * @param \DateTime $currentDay
     *
     * @return string P - pracujący, S - święto lub niedziela, W - inny wolny (np sobota)
     */
    public function getDayType($notWorkingDays, \DateTime $currentDay)
    {
        if (isset($notWorkingDays[$currentDay->format('d-m-Y')])) {
            $notWorkingDay = $notWorkingDays[$currentDay->format('d-m-Y')];

            if (isset($notWorkingDay['isHoliday']) && $notWorkingDay['isHoliday']) {
                $type = 'S';
            } elseif ($this->getDatesComparison()->isSunday($currentDay->format('j-n-Y'))) {
                $type = 'S';
            } elseif (isset($notWorkingDay['isDayOff']) && $notWorkingDay['isDayOff']) {
                $type = 'W';
            } else {
                $type = 'P';
            }
        } else {
            $type = 'P';
        }

        return $type;
    }

    /**
     * Fill cells with empty values.
     *
     * @param [PHPExcel $phpExcelObject
     * @param int       $currentRow     current row
     * @param int       $asciInt        asci letter to start
     * @param int       $j              another column for event to start
     *
     * @return PHPExcel
     */
    private function fillWithEmptyValues($phpExcelObject, $currentRow, $asciInt, $j)
    {
        for ($asci = $asciInt, $i = $j; $i < $this->maxEventCount; ++$i) {
            $phpExcelObject->getActiveSheet()
                // event start
                ->setCellValue(chr($asci++).$currentRow, '0:00')
                // event end
                ->setCellValue(chr($asci++).$currentRow, '0:00')
                // event zone
                ->setCellValue(chr($asci++).$currentRow,
                    $this->translator->trans('export.cdnoptima.excel.event.zone.none')
                )
                // event section
                ->setCellValue(chr($asci++).$currentRow, $this->translator->trans('export.cdnoptima.excel.event.section.none'))
            ;

            // event section node address
            $phpExcelObject->getActiveSheet()->setCellValueExplicit(
                chr($asci++).$currentRow,
                '0',
                \PHPExcel_Cell_DataType::TYPE_STRING
            );

            $phpExcelObject->getActiveSheet()
                // event project
                ->setCellValue(chr($asci++).$currentRow, $this->translator->trans('export.cdnoptima.excel.event.project.none'))
            ;

            // event project node address
            $phpExcelObject->getActiveSheet()->setCellValueExplicit(
                chr($asci++).$currentRow,
                '  1',
                \PHPExcel_Cell_DataType::TYPE_STRING
            );
        }

        return $phpExcelObject;
    }
}

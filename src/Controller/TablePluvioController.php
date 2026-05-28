<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pluviometrie;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/table/pluvio')]
class TablePluvioController extends AbstractController
{
    #[Route('/', name: 'app_table_pluvio')]
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $pluviometries = $em->getRepository(Pluviometrie::class)->findAll();
        usort($pluviometries, fn($a, $b) => $b->getId() <=> $a->getId());

        $paginatedPluviometries = $paginator->paginate(
            $pluviometries,
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('table_pluvio/index.html.twig', [
            'paginatedPluviometries' => $paginatedPluviometries,
            'pluviometries' => $pluviometries,
        ]);
    }

    #[Route('/export', name: 'app_table_pluvio_export')]
    public function exportExcel(EntityManagerInterface $em): Response
    {
        $pluviometries = $em->getRepository(Pluviometrie::class)->findAll();
        usort($pluviometries, fn($a, $b) => $b->getId() <=> $a->getId());

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Numéro de mesure');
        $sheet->setCellValue('B1', 'Pluviométrie (mm)');
        $sheet->setCellValue('C1', 'Horodatage');

        $row = 2;
        foreach ($pluviometries as $pluvio) {
            $sheet->setCellValue('A' . $row, $row - 1);
            $sheet->setCellValue('B' . $row, $pluvio->getPluvioHeure());
            $sheet->setCellValue('C' . $row, $pluvio->getHorodatage()->format('Y-m-d H:i:s'));
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'pluviometrie_' . date('Y-m-d_H-i-s') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}


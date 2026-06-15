<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cuve;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/table/niveau')]
class TableNiveauController extends AbstractController
{
    #[Route('/', name: 'app_table_niveau')]
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $cuves = $em->getRepository(Cuve::class)->findAll();
        usort($cuves, fn($a, $b) => $b->getId() <=> $a->getId());

        $paginatedCuves = $paginator->paginate(
            $cuves,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('table_niveau/index.html.twig', [
            'paginatedCuves' => $paginatedCuves,
            'cuves' => $cuves,
        ]);
    }

    #[Route('/export', name: 'app_table_niveau_export')]
    public function exportExcel(EntityManagerInterface $em): Response
    {
        $cuves = $em->getRepository(Cuve::class)->findAll();
        usort($cuves, fn($a, $b) => $b->getId() <=> $a->getId());

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Numéro de mesure');
        $sheet->setCellValue('B1', 'Niveau (cm)');
        $sheet->setCellValue('C1', 'Horodatage');

        $row = 2;
        foreach ($cuves as $cuve) {
            $sheet->setCellValue('A' . $row, $row - 1);
            $sheet->setCellValue('B' . $row, $cuve->getNiveauCm());
            $sheet->setCellValue('C' . $row, $cuve->getHorodatage()->format('Y-m-d H:i:s'));
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'niveaux_cuve_' . date('Y-m-d_H-i-s') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}




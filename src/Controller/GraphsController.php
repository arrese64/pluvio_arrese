<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cuve;
use App\Entity\Pluviometrie;
use Doctrine\ORM\EntityManagerInterface;

class GraphsController extends AbstractController
{
    #[Route('/graphs', name: 'app_graphs')]
    public function index(EntityManagerInterface $em): Response
    {
        $cuves = $em->getRepository(Cuve::class)->findAll();
        $pluviometries = $em->getRepository(Pluviometrie::class)->findAll();

        return $this->render('graphs/index.html.twig', [
            'cuves' => $cuves,
            'pluviometries' => $pluviometries,
        ]);
    }
}




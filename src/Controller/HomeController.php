<?php

namespace App\Controller;

use App\Controller\HomeController;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name:'app_home')]
    // pour afficher les [5] derniers Prod en ordre descendant [DESC] sur la Home
    public function index(ProduitRepository $repo): Response
    {
        $dernierProduits = $repo->findBy([], ["dateEnregistrement" => "DESC"], 5);
        // dd($dernierProduits);
        return $this->render('home/index.html.twig', [ 
            'produits' => $dernierProduits
        ]);
    }
}

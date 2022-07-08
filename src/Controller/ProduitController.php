<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Controller\ProduitController;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
// pour afficher detail produit pour le client

#[Route('/produit/{id<\d+>}', name:"produit_show")]
public function show($id, ProduitRepository $repo){

    $produit =$repo->find($id);

    return $this->render("produit/show.html.twig",[
        'produit'=>$produit
    ]);
}

// pour afficher TOUS LES produits pour le client

#[Route('/produits', name:"produits_all")]
public function all(ProduitRepository $repoPro, CategorieRepository $repoCat){

    $produits = $repoPro->findAll();
    $categories = $repoCat->findAll();

    return $this->render("produit/all.html.twig",[
        'produits'=>$produits,
        'categories' => $categories
    ]);
}

// pour afficher les produits par categorie

#[Route('/categorie/{id<\d+>}', name:"produits_categorie")]
public function categorieProduits($id, CategorieRepository $repo){
    // Recup la categorie sur laquelle on a cliqué pour accéder au produit lié
    $categorie = $repo->find($id);
    // recup liste de tts les categories pour les Aff dans la liste sur la page
    $categories = $repo->findAll();

    return $this->render("produit/all.html.twig",[
        //Recup les prod de la categorie sur laquelle on a cliqué grace à la RELATION
        'produits' => $categorie->getProduits(),
        'categories' => $categories
    ]);
}


}

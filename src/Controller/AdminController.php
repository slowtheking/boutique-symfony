<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Form\CategorieType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// création d'un préfix pour toutes les routes en dessous [liens]  ex: /admin/ajout-prod dans le Nav. 
// & admin_ajout_produit pour le nom [name:] des routes dans le code
#[Route("/admin", name:"admin_")]

class AdminController extends AbstractController
{

// ========= Pour AJOUTER un NEW Prod

#[Route("/ajout-produit", name:"ajout_produit")]

public function ajout(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

            //photo slug renomme l'image
            $file = $form->get('photoForm')->getData();
            $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('photo_produits'), $fileName);
            }catch(fileException $e){
                // gerer les exeptions d'upload image
            }

            $produit->setphoto($fileName);
            
            $produit->setDateEnregistrement(new DateTime("now"));
            // $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($produit);
            $manager->flush();

            return $this->redirectToRoute("admin_gestion_produits");

        }
    
        return $this->render('admin/formulaire.html.twig', [
            'formProduit' => $form->createView()
        ]);
    }

// ========= Pour GESTION Prod

#[Route("/gestion-produits", name:"gestion_produits")]
    public function gestionProduits(ProduitRepository $repo)
    {
        $produits = $repo->findAll();

        return $this->render("admin/gestion-produits.html.twig", [
            'produits' => $produits
        ]);

    }

// ========= Pour DETAILS Prod    

#[Route("/details-produit-{id<\d+>}", name:"details_produit")]
   public function detailsProduit($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id);

        return $this->render("admin/details-produit.html.twig", [
            'produit' => $produit
        ]);
    }

// ========= Pour UPDATE Prod 

#[Route("/update-produit-{id<\d+>}", name:"update_produit")]

    public function update($id, ProduitRepository $repo, Request $request, SluggerInterface $slugger)
    {
        $produit = $repo->find($id);

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()) {  

            if ($form->get('photoForm')->getData()){
                //photo slug renomme l'image
            $file = $form->get('photoForm')->getData();
            $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('photo_produits'), $fileName);
            }catch(fileException $e){
                // gerer les exeption d'upload image
            }

            $produit->setphoto($fileName);
            }

            $repo->add($produit, 1);
            return $this->redirectToRoute("admin_gestion_produits");
        }

        return $this->render("admin/formulaire.html.twig", [
            'formProduit' => $form->createView()
        ]);
    }

// ========= Pour DELETE Prod 

#[Route("/delete-produit-{id<\d+>}", name:"delete_produit")]

        public function delete($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id); 
        $repo->remove($produit, 1); 
        // On utilise ici la fonction remove |-> ProduitRepository ligne 33 -> remove(Produit $entity, bool $flush = false)
        // on passe le bolean $flush à true [1]

        return $this->redirectToRoute("admin_gestion_produits");
    }

// ========= Pour AJOUTER une CATEGORIE

#[Route("/categorie-ajout", name:"ajout_categorie")]

    public function ajoutCategorie(Request $request, CategorieRepository $repo)
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $repo->add($categorie, 1);

            return $this->redirectToRoute("app_home");
        }

        return $this->render("admin/formCategorie.html.twig", [
            "formCategorie" => $form->createView()
        ]);

}



}







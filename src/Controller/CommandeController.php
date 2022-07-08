<?php

namespace App\Controller;

use DateTime;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandeDetailRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/passer-ma-commande', name: 'passer_commande')]
    public function passerCommande(SessionInterface $session, 
                                    ProduitRepository $repoPro, 
                                    CommandeRepository $repoCom, 
                                    CommandeDetailRepository $repoDet, 
                                    EntityManagerInterface $manager): Response

      // FONCTION                              
    {
            // on crée un Objet commande pour remplir les informations
            $commande = new Commande();

            $panier = $session->get('panier', []);  // Recup  PANIER
            // dd($panier); //verification

            // Recup de l'USER - si de user connecté -> ne peux pas passer commande
            $user = $this->getUser();

            if(!$user)
                {
                $this->addFlash("error", "veuillez vous connecter ou vous inscrire !");
                return $this->redirectToRoute("app_login");
                }
        /*
             - pour chaque ligne de mon tableau panier dans la session, 
             je recupere le produit qui correspond à l'"id" qui est en clé et la "quantité" en valeur.

             - dans le tableau dataPanier je rajoute à chaque tour de boucle un nouveau tableau 
             qui contient une clé "produit" avec comme valeur le produit récuperé, et 
             une autre "quantite" qui contint la quantite du produit en question

             - puis à chaque tour de boucle je calcule le prix total du produit (prix du produit * quantité) 
             et je l'ajoute à ma variable $total 
        */
            // Si Panier est VIDE
            if (empty($panier)) 
            {
                $this->addFlash("error", "veuillez vous connecter ou vous inscrire !");
                return $this->redirectToRoute("produit_all");
                
            }
                $dataPanier = [];
                $total = 0;

            foreach ( $panier as $id => $quantite )
                {
                $produit = $repoPro->find($id);
                // j'initialise un tableau vide dans lequel il y aura le Prod et sa Quantité
                $dataPanier[] = 
                [
                    "produit" => $produit,
                    "quantite" => $quantite,
                    "sousTotal" => $produit->getPrix() * $quantite,
                ];
                // La variable - $total recup les sous-total et additionne
                $total += $produit->getPrix() * $quantite;

                };
                    // dd($dataPanier);  //verification

                $commande->setUser($user)
                        ->setDateDeCommande(new DateTime ("now"))
                        ->setMontant($total);

                $repoCom->add($commande); // On persiste la commande sans l'envoyer FLUSH

                // On parcours le tableau - $dataPanier[] - dans lequel son stockés les commandes dans des tableaux
                
                foreach ( $dataPanier as $key  => $value ){

                    $commandeDetail = new CommandeDetail();

                    $produit = $value["produit"];
                    $quantite = $value["quantite"];
                    $sousTotal = $value["sousTotal"];


                    $commandeDetail->setCommande($commande)
                                    ->setProduit($produit)
                                    ->setQuantite($quantite)
                                    ->setPrix($sousTotal);
                    
                    $repoDet->add($commandeDetail);  // On persiste la commandeDetail sans l'envoyer FLUSH
                }
               
       $manager->flush(); // On Envoye le Panier FLUSH avec la commande $manager
       
       $session->remove("panier"); // On retire le Panier

       $this->addFlash("success", "BRAVO joyeux CONsuméristes Dépensez PLUS ENCORE c'est SOLDES!");
       return $this->redirectToRoute("app_home");



    }





}

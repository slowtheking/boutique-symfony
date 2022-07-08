<?php

namespace App\Controller;

use LogicException;
use App\Form\AdminType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

#[Route(path: '/passer-en-admin_{id<\d+>}', name: 'passer_en_admin')]
public function passerAdmin( UserRepository $repo, Request $request, $id)
{
    //variable '$secret'
$secret ="123456";

$form = $this->createForm(AdminType::class);
$form->handleRequest($request);
// On recup l'User dont l'ID est passé dans l'url du navigateur
$user = $repo->find($id);

if(!$user){
    $this->addFlash("error", "Aucun utilisateur avec cette id : $id");
    return $this->redirectToRoute("app_home");
}

if($form->isSubmitted() && $form->isValid()){

    // Si la saisie dans le champs "secret"du form correspond au MdP stocké dans la variable '$secret'
    if ($form->get('secret')->getData() == $secret){
        $user->setRoles(["ROLE_ADMIN"]);
    }
    else{
    $this->addFlash("error", "Vous n'avez pas les droits pour cette action, contactez l'administrateur");
    return $this->redirectToRoute("app_home");  
    }
    // En passant par la metode ADD du Repository - l'objet sera Persisté & envoyé en bdD grâce au Param 1[TRUE]
    $repo->add($user, 1);
    $this->addFlash("success", "Vous êtes désormais Amin, veuillez vous reconnecter pour profiter de vos privilèges");
    return $this->redirectToRoute("app_home");
    }

    return $this->render("security/passerEnAdmin.html.twig",[
        "user" => $user,
        "formAdmin" => $form->createView()
    ]);
}




}

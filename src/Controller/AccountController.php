<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Vérifier si un utilisateur est connecté
        if (!$user) {
            // Gérer le cas où aucun utilisateur n'est connecté
            throw $this->createNotFoundException('Aucun utilisateur connecté.');
        }

        // Passer l'utilisateur au template
        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }
}

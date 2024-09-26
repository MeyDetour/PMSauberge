<?php

namespace App\Controller;

use App\Repository\SandwichRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SandwichRepository $sandwichRepository): Response
    {

        return $this->json($sandwichRepository->findAll(),200,[],["groups" => "sandwich"]);
    }
}

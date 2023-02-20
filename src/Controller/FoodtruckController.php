<?php

namespace App\Controller;

use App\Entity\Foodtruck;
use App\Repository\FoodtruckRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodtruckController extends AbstractController
{
    /**
     * @Route("/foodtruck", name="foodtruck_index", methods={"GET"})
     */
    public function index(FoodtruckRepository $foodtruckRepository): Response
    {
        return $this->render('foodtruck/index.html.twig', [
            'foodtrucks' => $foodtruckRepository->findAll(),
        ]);
    }
}

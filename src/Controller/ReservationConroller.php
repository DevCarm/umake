<?php
namespace App\Controller;

use App\Entity\Foodtruck;
use App\Entity\Parking;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/reservation/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request, Foodtruck $foodtruck, Parking $parking): Response
    {
        $reservation = new Reservation();
        $reservation->setFoodtruck($foodtruck);
        $reservation->setParking($parking);

        // Validation du nombre de places disponibles pour chaque jour
        if ($parking->getNbrPlaces() > 6 && date('w') == 5) {
            $this->addFlash('danger', 'Il n\'y a que 6 places disponibles le vendredi');

            return $this->render('reservation/new.html.twig', [
                'reservation' => $reservation,
            ]);
        }

        if ($parking->getNbrPlaces() > 7 && date('w') != 5) {
            $this->addFlash('danger', 'Il n\'y a que 7 places disponibles les autres jours');

            return $this->render('reservation/new.html.twig', [
                'reservation' => $reservation,
            ]);
        }

        // Validation de la date
        if ($reservation->getDate() > new \DateTime('now')) {
            $this->addFlash('danger', 'Vous ne pouvez pas réserver pour une date passée');

            return $this->render('reservation/new.html.twig', [
                'reservation' => $reservation,
            ]);
        }

        // Validation de la fréquence de réservation
        $reservations = $this->getDoctrine()
            ->getRepository(Reservation::class)
            ->findBy(['foodtruck' => $foodtruck]);

        foreach($reservations as $reservation) {
            if ($reservation->getDate() > new \DateTime('now')) {
                $this->addFlash('danger', 'Vous ne pouvez pas réserver plus d\'une fois par semaine');

                return $this->render('reservation/new.html.twig', [
                    'reservation' => $reservation,
                ]);
            }
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }
}
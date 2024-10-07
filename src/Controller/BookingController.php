<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class BookingController extends AbstractController
{
    #[Route('/bookings', name: 'app_bookings')]
    public function gets(BookingRepository $bookingRepository): Response
    {
        return $this->json($bookingRepository->findAll(), 200, [], ['groups' => ['entire_booking']]);
    }


    #[Route('/booking/new', name: 'new_booking')]
    public function new(Request $request, EntityManagerInterface $manager,SerializerInterface $serializer): Response
    {
        $booking = $serializer->deserialize($request->getContent(), Booking::class, 'json');
        $booking->setCreatedAt(new \DateTimeImmutable());

        dd($booking);


        return $this->json($booking, 201, [], ['groups' => ['entire_booking']]);
    }


}

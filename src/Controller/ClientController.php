<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\BookingRepository;
use App\Repository\ClientRepository;
use App\Service\GlobalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ClientController extends AbstractController
{
    #[Route('/clients', name: 'app_clients', methods: "get")]
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->json($clientRepository->findAll(), 200, [], ["groups" => "clients"]);
    }

    #[Route('/client/{id}', name: 'getClient', methods: "get")]
    public function getClient(Client $client): Response
    {
        return $this->json($client, 200, [], ["groups" => "client"]);
    }

    #[Route('/edit/client/{id}', name: 'edit_client', methods: "put")]
    public function editClient(Request $request, GlobalService $globalService, SerializerInterface $serializer, Client $client, EntityManagerInterface $manager): Response
    {
        $clientEdited = $serializer->deserialize($request->getContent(), Client::class, "json");
        if (!$globalService->isValidString($clientEdited->getFirstName())
            or !$globalService->isValidString($clientEdited->getLastName())
        ) {
            return $this->json(["message" => "You must provide a first name,last name and birth date for each client. (field : clients [], accepted : {firstName,lastName,birthDate} )"], 406);
        }
        $client->setFirstName($clientEdited->getFirstName());
        $client->setLastName($clientEdited->getLastName());
        $manager->persist($client);
        $manager->flush();

        return $this->json($client, 200, [], ["groups" => "clients"]);
    }
/*
    #[Route('/merge/client/{id}/into/{clientId}', name: 'merge_client', methods: "patch")]
    public function mergeClient(ClientRepository $clientRepository, Client $client, $clientId, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {

        $clientToKeep = $clientRepository->find($clientId);
        if (!$clientToKeep) {
            return $this->json(["message" => "Client not found)"], 406);
        }
        foreach ($client->getBookings() as $booking) {
            $client->setInvitedBy(null);

            $booking->removeClient($client);
            $booking->addClient($clientToKeep);
            $manager->persist($booking);
        }
        foreach ($client->getBookingsAuthor() as $booking) {
            $booking->setMainClient($clientToKeep);
            $manager->persist($booking);
        }
        $manager->remove($client);
        $manager->flush();

        return $this->json($clientToKeep, 200, [], ["groups" => "clients"]);
    }*/

    #[Route('/remove/client/{id}/from/booking/{bookingId}', name: 'remove_client', methods: "patch")]
    public function removeClient(BookingRepository $bookingRepository, GlobalService $globalService, Client $client, $bookingId, EntityManagerInterface $manager): Response
    {

        $booking = $bookingRepository->find($bookingId);
        if ($client == $booking->getMainClient()) {
            return $this->json(["message" => "You cannot remove the author of booking"], 401);
        }

        if (!in_array($client, $booking->getClients()->toArray())) {
            return $this->json(["message" => "This client is not in list of client"], 401);
        }
        if ($globalService->isBookingPassed($booking)) {
            return $this->json(["message" => "Booking is passed you cannot do anything"], 401);
        }

        $booking->removeClient($client);
        $manager->persist($booking);
        if (count($client->getBookings()) == 0 && $client->getBookings() == null) {
            $manager->remove($client);
        }
        $booking->removeBed($booking->getBeds()->last());

        $manager->flush();

        return $this->json($booking, 200, [], ["groups" => "entireBooking"]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Service\GlobalService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class RoomController extends AbstractController
{
    #[Route('/rooms', name: 'app_rooms', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findBy([], ['name' => 'ASC']);
        return $this->json($rooms, 200, [], ['groups' => ['rooms']]);
    }

    #[Route('/rooms/names', name: 'app_rooms_name', methods: ['GET'])]
    public function roomsName(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findBy([], ['name' => 'ASC']);
        return $this->json($rooms, 200, [], ['groups' => ['rooms_names']]);
    }

    #[Route('/room/{id}', name: 'get_room', methods: ['GET'])]
    public function getRoom(Room $room): Response
    {
        return $this->json($room, 200, [], ['groups' => ['rooms_and_bed']]);
    }

    #[Route('/rooms/beds', name: 'get_rooms_and_bed_free_at_this_date', methods: ['GET'])]
    public function getRoomsWithBedFreeAtThisDate(RoomRepository $roomRepository, Request $request): Response
    {

        $desiredDate = $request->query->get('date');
        if (!$desiredDate || !strtotime($desiredDate)) {
            return $this->json(['error' => 'Invalid date format'], 400);
        }

        // Convertir la chaîne de date en objet DateTime
        $desiredDate = new \DateTime($desiredDate);


        $rooms = $roomRepository->findBy([], ['name' => 'ASC']);
        $filteredRooms = array_filter($rooms, function ($room) use ($desiredDate) {
            $availableBeds = array_filter($room->getBeds(), fn($bed) => $this->isBedFreeAtThisDate($bed, $desiredDate));
            return !empty($availableBeds); // Garde la chambre si elle a des lits disponibles
        });
        return $this->json($filteredRooms, 200, [], ['groups' => ['rooms']]);
    }

    private function isBedFreeAtThisDate($bed, $date)
    {
    var_dump("analyse bed ".$bed->getId());
        foreach ($bed->getBookings() as $booking) {
            var_dump($booking->getId(),$booking->getStartDate() <= $date && $date <= $booking->getEndDate());
              if ($booking->getStartDate() <= $date && $date <= $booking->getEndDate()) {
                return false;
            }
        }
        return true;
    }

    #[Route('/room/new', name: 'new_room', methods: ['POST'])]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, GlobalService $globalService): Response
    {
        $room = $serializer->deserialize($request->getContent(), Room::class, 'json');

        if (!$globalService->isValidBool($room->hasLocker())) {
            return $this->json(["message" => "Has the room a locker ? (field : hasLocker, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasPrivateShowerroom())) {
            return $this->json(["message" => "Has the room a private Showerroom ? (field : hasPrivateShowerroom, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->isPrivate())) {
            return $this->json(["message" => "Is the room private ? (field : private, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasTable())) {
            return $this->json(["message" => "Has the a table ? (field : hasTable, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasBalcony())) {
            return $this->json(["message" => "Has the room a balcony ? (field : hasBalcony, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasWashtub())) {
            return $this->json(["message" => "Has the room a washtub ? (field : hasWashtub, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasBin())) {
            return $this->json(["message" => "has the room a bin? (field : hasBin, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasWardrobe())) {
            return $this->json(["message" => "Has the room a wardrobe ? (field : hasWardobe, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidString($room->getName())) {
            return $this->json(["message" => "Please enter name of room ? (field : name, accepted : string)"], 406, [], ['groups' => 'rooms']);
        }

        try {
            $entityManager->persist($room);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(["message" => "This name already existe"], 406, [], ['groups' => 'rooms']);
        }

        return $this->json($room, 200, [], ['groups' => 'rooms']);
    }

    #[Route('/room/edit/{id}', name: 'edit_room', methods: ['PUT'])]
    public function edit(Request $request, SerializerInterface $serializer, Room $room, RoomRepository $roomRepository, GlobalService $globalService, EntityManagerInterface $entityManager): Response
    {

        $room2 = $serializer->deserialize($request->getContent(), Room::class, 'json');

        if (!$globalService->isValidBool($room->hasLocker())) {
            return $this->json(["message" => "Has the room a locker ? (field : hasLocker, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasPrivateShowerroom())) {
            return $this->json(["message" => "Has the room a private Showerroom ? (field : hasPrivateShowerroom, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->isPrivate())) {
            return $this->json(["message" => "Is the room private ? (field : private, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasTable())) {
            return $this->json(["message" => "Has the a table ? (field : hasTable, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasBalcony())) {
            return $this->json(["message" => "Has the room a balcony ? (field : hasBalcony, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasWashtub())) {
            return $this->json(["message" => "Has the room a washtub ? (field : hasWashtub, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasBin())) {
            return $this->json(["message" => "has the room a bin? (field : hasBin, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidBool($room->hasWardrobe())) {
            return $this->json(["message" => "Has the room a wardrobe ? (field : hasWardobe, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!$globalService->isValidString($room->getName())) {
            return $this->json(["message" => "Please enter name of room ? (field : name, accepted : string)"], 406, [], ['groups' => 'rooms']);
        }
        $room->setName($room2->getName());
        $room->setPrivate($room2->isPrivate());
        $room->setHasLocker($room2->hasLocker());
        $room->setHasPrivateShowerroom($room2->hasPrivateShowerroom());
        $room->setHasTable($room2->hasTable());
        $room->setHasBalcony($room2->hasBalcony());
        $room->setHasWashtub($room2->hasWashtub());
        $room->setHasBin($room2->hasBin());
        $room->setHasWardrobe($room2->hasWardrobe());

        try {
            $entityManager->persist($room);
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(["message" => "This name already existe"], 400);
        }

        return $this->json([$room], 200, [], ['groups' => 'rooms']);
    }

    #[Route('/room/remove/{id}', name: 'remove_room', methods: ['DELETE'])]
    public function remove(Request $request, SerializerInterface $serializer, Room $room, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {
        if (count($room->getBeds()) != 0) {
            return $this->json(["messsage" => "Room has beds associated"]);
        }
        $entityManager->remove($room);
        $entityManager->flush();
        return $this->json(["messsage" => "ok"], 200, [], ['groups' => 'rooms']);
    }
}

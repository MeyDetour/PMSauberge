<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
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
        return $this->json($rooms, 200, [], ['groups' => ['rooms_and_bed']]);
    }

    #[Route('/room/new', name: 'new_room', methods: ['POST'])]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $room = $serializer->deserialize($request->getContent(), Room::class, 'json');
        if (!is_bool($room->hasLocker())) {
            return $this->json(["message" => "Has the room a locker ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasPrivateShowerroom())) {
            return $this->json(["message" => "Has the room a Showerroom ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->isPrivate())) {
            return $this->json(["message" => "Is the room private ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasTable())) {
            return $this->json(["message" => "Has the a table ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasBalcony())) {
            return $this->json(["message" => "Has the room a balcony ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasWashtub())) {
            return $this->json(["message" => "Has the room a washtub ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasBin())) {
            return $this->json(["message" => "has the room a bin?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasWardrobe())) {
            return $this->json(["message" => "Has the room a wardrobe ?"], 406, [], ['groups' => 'rooms']);
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
    public function edit(Request $request, SerializerInterface $serializer, Room $room, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {

        $room2 = $serializer->deserialize($request->getContent(), Room::class, 'json');

        if (!is_bool($room2->hasLocker())) {
            return $this->json(["message" => "Has the room a locker ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room2->hasPrivateShowerroom())) {
            return $this->json(["message" => "Has the room a Showerroom ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room2->isPrivate())) {
            return $this->json(["message" => "Is the room private ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasTable())) {
            return $this->json(["message" => "Has the a table ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasBalcony())) {
            return $this->json(["message" => "Has the room a balcony ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasWashtub())) {
            return $this->json(["message" => "Has the room a washtub ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasBin())) {
            return $this->json(["message" => "has the room a bin?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($room->hasWardrobe())) {
            return $this->json(["message" => "Has the room a wardrobe ?"], 406, [], ['groups' => 'rooms']);
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
            return $this->json(["message" => "This name already existe"], 406, [], ['groups' => 'rooms']);
        }

        return $this->json([$room], 200, [], ['groups' => 'rooms']);
    }

    #[Route('/room/remove/{id}', name: 'remove_room', methods: ['DELETE'])]
    public function remove(Request $request, SerializerInterface $serializer, Room $room, RoomRepository $roomRepository, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($room);
        $entityManager->flush();
        return $this->json(["messsage" => "ok"], 200, [], ['groups' => 'rooms']);
    }
}

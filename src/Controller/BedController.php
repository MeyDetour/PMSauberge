<?php

namespace App\Controller;

use App\Entity\Bed;
use App\Entity\Room;
use App\Repository\BedRepository;
use App\Repository\RoomRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class BedController extends AbstractController
{
    private array $stateValueAccepted = ["blocked", "cleaned", "inspected", "notCleaned"];
    private array $bedFormValues = ["topBed", "bottomBed", "singleBed"];

    #[Route('/bed/get/{id}', name: 'get_bed', methods: 'GET', priority: 0)]
    public function getBed(Bed $bed): Response
    {
        return $this->json($bed, 200, [], ["groups" => ['bed', 'rooms']]);
    }

    #[Route('/bed/new', name: 'new_bed', methods: "post", priority: 1)]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, RoomRepository $roomRepository): Response
    {
        $bed = $serializer->deserialize($request->getContent(), Bed::class, 'json');

        if (!is_bool($bed->isSittingApart())) {
            return $this->json(["message" => "Is the bed sitting apart ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($bed->isDoubleBed())) {
            return $this->json(["message" => "Is the bed a double bed ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($bed->hasLamp())) {
            return $this->json(["message" => "Is there bedlight?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($bed->hasLittleStorage())) {
            return $this->json(["message" => "Is there little storage?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($bed->hasShelf())) {
            return $this->json(["message" => "Is there shelf storage?"], 406, [], ['groups' => 'rooms']);
        }
        if (!in_array($bed->getState(), $this->stateValueAccepted)) {
            return $this->json(["message" => "Not accepted value given for bed's state"], 406, [], ['groups' => 'rooms']);
        }
        if (!in_array($bed->getBedShape(), $this->bedFormValues)) {
            return $this->json(["message" => "Not accepted value given for bed's shape"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_int($bed->getNumber())) {
            return $this->json(["message" => "Not accepted value given for bed's state"], 406, [], ['groups' => 'rooms']);
        }

        $data = json_decode($request->getContent(), true);
        $roomId = $data['room'] ?? null;

        if (!$roomId) {
            return $this->json(["message" => "Room ID is required"], 400);
        }

        $room = $roomRepository->find($roomId);

        if (!$room) {
            return $this->json(["message" => "Room not found"], 404);
        }

        // Associer la chambre au lit
        $bed->setRoom($room);
        $bed->setIsOccupied(false);
        try {
            $manager->persist($bed);
            $manager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(["message" => "This number of bed already existe"], 406, [], ['groups' => 'rooms']);
        }

        return $this->json($bed, 201, [], ['groups' => ['bed']]);
    }

    #[Route('/bed/edit/{id}', name: 'edit_bed', methods: "put",)]
    public function edit(Bed $bed, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, RoomRepository $roomRepository): Response
    {
        $editedBed = $serializer->deserialize($request->getContent(), Bed::class, 'json');

        if (!is_bool($editedBed->isSittingApart())) {
            return $this->json(["message" => "Is the bed sitting apart ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($editedBed->isDoubleBed())) {
            return $this->json(["message" => "Is the bed a double bed ?"], 406, [], ['groups' => 'rooms']);
        }
        if (!in_array($bed->getBedShape(), $this->bedFormValues)) {
            return $this->json(["message" => "Not accepted value given for bed's shape"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($bed->hasLamp())) {
            return $this->json(["message" => "Is there bedlight?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($bed->hasLittleStorage())) {
            return $this->json(["message" => "Is there little storage?"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_bool($bed->hasShelf())) {
            return $this->json(["message" => "Is there shelf storage?"], 406, [], ['groups' => 'rooms']);
        }
        if (!in_array($editedBed->getState(), $this->stateValueAccepted)) {
            return $this->json(["message" => "Not accepted value given for bed's state"], 406, [], ['groups' => 'rooms']);
        }
        if (!is_int($editedBed->getNumber())) {
            return $this->json(["message" => "Not accepted value given for bed's state"], 406, [], ['groups' => 'rooms']);
        }
        $bed->setBedShape($editedBed->getBedShape());
        $bed->setDoubleBed($editedBed->isDoubleBed());
        $bed->setHasLamp($editedBed->hasLamp());
        $bed->setHasLittleStorage($editedBed->hasLittleStorage());
        $bed->setHasShelf($editedBed->hasShelf());
        $bed->setState($editedBed->getState());
        $bed->setSittingApart($editedBed->isSittingApart());
        $bed->setNumber($editedBed->getNumber());

        #associate room
        $data = json_decode($request->getContent(), true);
        $roomId = $data['room'] ?? null;

        if (!$roomId) {
            return $this->json(["message" => "Room ID is required"], 400);
        }

        $room = $roomRepository->find($roomId);

        if (!$room) {
            return $this->json(["message" => "Room not found"], 404);
        }
        $bed->setRoom($room);

        try {
            $manager->persist($bed);
            $manager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(["message" => "This number of bed already existe"], 406, [], ['groups' => 'rooms']);
        }
        return $this->json($bed, 200, [], ['groups' => ['bed', 'rooms']]);

    }

    #[Route('/bed/edit/status/{id}', name: 'edit_status_bed', methods: "patch",)]
    public function editStatus(Bed $bed, Request $request, EntityManagerInterface $manager, RoomRepository $roomRepository): Response
    {
        $content = $request->toArray();
        $state = $content['status'];
        if (!$state || !in_array($state, $this->stateValueAccepted)) {
            return $this->json(["message" => "Invalid state given"], 404);

        }
        $bed->setState($state);
        $manager->persist($bed);
        $manager->flush();
        return $this->json($bed, 200, [], ['groups' => ['bed', 'rooms']]);

    }   #[Route('/bed/{id}/change/occupation', name: 'change_occupied', methods: "patch",)]
    public function changeOccupied(Bed $bed, Request $request, EntityManagerInterface $manager, RoomRepository $roomRepository): Response
    {

        $bed->setOccupied(!$bed->isOccupied());
        $manager->persist($bed);
        $manager->flush();
        return $this->json(['message'=>"ok"], 200);

    }

    #[Route('/bed/clean/{id}', name: 'clean_bed', methods: "patch",)]
    public function cleanBed(Bed $bed, EntityManagerInterface $manager, RoomRepository $roomRepository): Response
    {

        $bed->setState("cleaned");
        $bed->setCleanedBy($this->getUser());
        $manager->persist($bed);
        $manager->flush();
        return $this->json(["message" => "ok"], 200);

    }

    #[Route('/bed/inspect/{id}', name: 'inspect_bed', methods: "patch",)]
    public function inspect(Bed $bed, EntityManagerInterface $manager, RoomRepository $roomRepository): Response
    {
        if ($bed->getState() == "inspected") {
            return $this->json(["message" => "Bed is already cleaned"], 404);
        }
        if ($bed->getState() != "cleaned") {
            return $this->json(["message" => "Bed is not cleaned"], 404);
        }
        $bed->setState("inspected");
        $bed->setInspectedBy($this->getUser());
        $manager->persist($bed);
        $manager->flush();
        return $this->json(["message" => "ok"], 200);

    }

    #[Route('/bed/remove/{id}', name: 'remove_bed', methods: "DELETE",)]
    public function remove(Bed $bed, EntityManagerInterface $manager): Response
    {
        $manager->remove($bed);
        $manager->flush();
        return $this->json(["message" => "ok"], 200);
    }

}

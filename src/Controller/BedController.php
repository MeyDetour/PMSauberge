<?php

namespace App\Controller;

use App\Entity\Bed;
use App\Entity\Room;
use App\Repository\BedRepository;
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
class BedController extends AbstractController
{
    private array $stateValueAccepted = ["blocked", "cleaned", "inspected", "notCleaned", "deleted"];
    private array $bedFormValues = ["topBed", "bottomBed", "singleBed"];


    #[Route('/bed/get/{id}', name: 'get_bed', methods: 'GET', priority: 0)]
    public function getBed(Bed $bed, GlobalService $globalService): Response
    {
        $bookings = [];
        if ($bed->getState() == "deleted") {
            return $this->json(["message" => "Bed is deleted"]);
        }
        foreach ($bed->getBookings() as $booking) {
            if (!$globalService->isBookingPassed($booking)) {
                $bookings[] = $booking;
            }
        }
        $data = [
            'bed' => $bed,
            'bookings' => $bookings,
        ];
        return $this->json($data, 200, [], ["groups" => ['bed', 'rooms']]);
    }

    #[Route('/bed/new', name: 'new_bed', methods: "post", priority: 1)]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, RoomRepository $roomRepository,GlobalService $globalService): Response
    {
        $bed = $serializer->deserialize($request->getContent(), Bed::class, 'json');

        if ( !$globalService->isValidBool($bed->isSittingApart()) ) {
            return $this->json(["message" => "Is the bed sitting apart ? (field : sittingApart, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($bed->isDoubleBed()) ) {
            return $this->json(["message" => "Is the bed a double bed ? (field : doubleBed, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!in_array($bed->getBedShape(), $this->bedFormValues)) {
            return $this->json(["message" => "Not accepted value given for bed's shape (field : bedShape, accepted : topBed,bottomBed,singleBed)"], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($bed->hasLamp()) ) {
            return $this->json(["message" => "Is there bedlight ?  (field : hasLamp, accepted : true,false) "], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($bed->hasLittleStorage()) ) {
            return $this->json(["message" => "Is there little storage ? (field : hasLittleStorage, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($bed->hasShelf())) {
            return $this->json(["message" => "Is there shelf storage ? (field : hasShelf, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
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
        $bed->setOccupied(false);
        $bed->setState("cleaned");
        try {
            $manager->persist($bed);
            $manager->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(["message" => "This number of bed already existe"], 406, [], ['groups' => 'rooms']);
        }

        return $this->json($bed, 201, [], ['groups' => ['bed']]);
    }

    #[Route('/bed/edit/{id}', name: 'edit_bed', methods: "put",)]
    public function edit(Bed $bed, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, RoomRepository $roomRepository , GlobalService $globalService): Response
    {
        if ($bed->getState() == "deleted") {
            return $this->json(["message" => "Bed is deleted"]);
        }

        $editedBed = $serializer->deserialize($request->getContent(), Bed::class, 'json');


        if ( !$globalService->isValidBool($editedBed->isSittingApart()) ) {
            return $this->json(["message" => "Is the bed sitting apart ? (field : sittingApart, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($editedBed->isDoubleBed()) ) {
            return $this->json(["message" => "Is the bed a double bed ? (field : doubleBed, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if (!in_array($editedBed->getBedShape(), $this->bedFormValues)) {
            return $this->json(["message" => "Not accepted value given for bed's shape (field : bedShape, accepted : topBed,bottomBed,singleBed)"], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($editedBed->hasLamp()) ) {
            return $this->json(["message" => "Is there bedlight ?  (field : hasLamp, accepted : true,false) "], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($editedBed->hasLittleStorage()) ) {
            return $this->json(["message" => "Is there little storage ? (field : hasLittleStorage, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }
        if ( !$globalService->isValidBool($editedBed->hasShelf())) {
            return $this->json(["message" => "Is there shelf storage ? (field : hasShelf, accepted : true,false)"], 406, [], ['groups' => 'rooms']);
        }

        $bed->setBedShape($editedBed->getBedShape());
        $bed->setDoubleBed($editedBed->isDoubleBed());
        $bed->setHasLamp($editedBed->hasLamp());
        $bed->setHasLittleStorage($editedBed->hasLittleStorage());
        $bed->setHasShelf($editedBed->hasShelf());
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
    #[Route('/bed/{id}/change/occupation', name: 'change_occupied', methods: "patch",)]
    #[Route('/bed/clean/{id}', name: 'clean_bed', methods: "patch",)]
    #[Route('/bed/inspect/{id}', name: 'inspect_bed', methods: "patch",)]
    #[Route('/bed/remove/{id}', name: 'remove_bed', methods: "DELETE")]
    #[Route('/bed/unremove/{id}', name: 'unremove_bed', methods: "PATCH",)]
    public function editStatus(Bed $bed, Request $request, EntityManagerInterface $manager, RoomRepository $roomRepository): Response
    {

        $route = $request->attributes->get('_route');
        if ($bed->getState() == "deleted" && $route!="unremove_bed") {
            return $this->json(["message" => "Bed is deleted"]);
        }
        switch ($route){
            case "clean_bed" :
                $bed->setState("cleaned");
                $bed->setCleanedBy($this->getUser());
                break;
            case "inspect_bed":
                if ($bed->getState() == "inspected") {
                    return $this->json(["message" => "Bed is already inspected"], 404);
                }
                if ($bed->getState() != "cleaned") {
                    return $this->json(["message" => "Bed is not cleaned"], 404);
                }
                $bed->setState("inspected");
                $bed->setInspectedBy($this->getUser());
                break;
            case "change_occupied":
                $bed->setOccupied(!$bed->isOccupied());
                break;
            case "remove_bed":
                $bed->setState("deleted");
                break;

            case "unremove_bed":
                $bed->setState("cleaned");
                break;
        }
        if (!in_array($bed->getState(), $this->stateValueAccepted)) {
            return $this->json(["message" => "Invalid state given"], 404);
        }
        $manager->persist($bed);
        $manager->flush();
        return $this->json($bed, 200, [], ['groups' => ['bed', 'rooms']]);

    }

    #[Route('/beds/deleted', name: 'beds_deleted', methods: "GET")]
    public function get_beds_deleted(BedRepository $bedRepository): Response
    {
        $bedsData = $bedRepository->findAll();
        $beds = [];
        foreach ($bedsData as $bed) {
            if ($bed->getState() == "deleted") {
                $beds[] = $bed;
            }
        }
        return $this->json($beds, 200, [], ["groups" => ['bed', 'rooms']]);
    }

}

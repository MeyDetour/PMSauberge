<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BedRepository;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use App\Service\GlobalService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public int $price = 50;
    private $globalService;

    public function __construct(GlobalService $globalService)
    {
        $this->globalService = $globalService;
    }


    #[Route('/booking/initaliaze', name: 'initaliaze_form_booking', methods: 'get')]
    public function initialize(BedRepository $bedRepository, RoomRepository $roomRepository): Response
    {

        $isThereBunkBed = count($bedRepository->findBy(['bedShape' => "topBed"])) != 0;
        $isTherePrivate = count($roomRepository->findBy(['isPrivate' => true])) != 0;
        $isTherePrivateShowerroom = count($roomRepository->findBy(['hasPrivateShowerroom' => true])) != 0;
        return $this->json(["isThereBunkBed" => $isThereBunkBed], 200);
    }

    #[Route('/booking/verification/{formName}', name: 'initaliaze_form_booking', methods: 'patch')]
    public function verify(Request $request, BedRepository $bedRepository, RoomRepository $roomRepository, $formName): Response
    {

        $data = json_decode($request->getContent(), true);

        $isBookingForToday = false;
        if (isset($data['mail'])) {
            //Search if as an client account
        }
        if (isset($data['getPrivateRoom'])) {
            //if group size < max size in the private room bed
        }

        if (isset($data['startDate'])) {
            $searchDate = \DateTime::createFromFormat('Y-m-d H:i', $data['startDate']);
            if (!$searchDate) {
                return $this->json([
                    "message" => "Invalid datetime"],
                    406);
            }
            $today = new \DateTime();
            $isBookingForToday = $today->format('Y') == $searchDate->format('Y') && $today->format('d') == $searchDate->format('d') && $today->format('m') == $searchDate->format('m');
        }
        if (isset($data['groupSize'])) {
            //if count all bed places


            $bedsFree = count($bedRepository->findBy(['isOccupied' => false]));

            //VERIFYING THE DAY OF THE ARRIVED, IF TODAY VERIFYING CLEANED BED
            if ($isBookingForToday) {
                $bedsFree = count($bedRepository->findBy(['isOccupied' => false]));
            }
            $bedsFree = $bedsFree + count($bedRepository->findBy(['isDoubleBed' => true]));

            //if count all bed in public room
            if (false) {
                $bedsFree = $bedRepository->countAllBedsInPublicRoom();

            }
            if ($bedsFree == 0) {
                return $this->json([
                    "state" => false,
                    "message" => "There are not enought bed for a group of this size."],
                    200);
            }
            //\ TRY TO GROUP BEDS IN MINIMUM OF ROOM
            //-get number of beds in each room
            //-get the max number and the second max number
            //-pay attention to the mixed room

            $beds = ['some beds in this array..'];

            $containBunkBed = false;


            //Go to the next question

            if ($formName == "groupSize") {
                return $this->json([
                    "state" => true,
                    "message" => "There are enought bed"],
                    200);
            }


        }

        if (isset($data['peoples']) && is_array(['peoples'])) {
            $uniqueSexe = true;
            $isThereAndMajor = false;
            $gender = "M";
            foreach ($data['peoples'] as $index => $people) {
                if ($index == 0) {
                    $gender = $people["gender"];
                }
                if ($people["age"] > 18) {
                    $isThereAndMajor = true;
                }
                if ($people['gender'] != $gender) {
                    $uniqueSexe = false;
                }
            }
            if (!$isThereAndMajor) {
                return $this->json([
                    "state" => false,
                    "message" => "There are no major in this booking. An minor must be accompagnated by an major."],
                    200);
            }
            if ($formName == "peoples") {
                if ($uniqueSexe) {
                    return $this->json([
                        "state" => true,
                        "message" => [$gender, "mixed"]],
                        200);
                }
                return $this->json([
                    "state" => true,
                    "message" => ["mixed"]],
                    200);

            }
        }
        if (isset($data['genderOfRoom'])) {
            $genderOfRoom = $data['genderOfRoom'];

            //if selected F or M but gender is not unique
            if (!$uniqueSexe && ("F" == $genderOfRoom || "R" == $genderOfRoom)) {
                return $this->json([
                    "state" => false,
                    "message" => "you can not access this room"],
                    200);
            }
            //if unique gender but room gender is not the same
            if ($uniqueSexe && $gender != $genderOfRoom) {
                return $this->json([
                    "state" => false,
                    "message" => "you can not access this room"],
                    200);
            }
            if ($formName == "genderOfRoom") {

                return $this->json([
                    "state" => true,
                    "message" => "you can not access this room"],
                    200);
            }
            //propose to separate in dortory
            //\IF ENOUGH GIRL FOR GIRL DORTORY BE LIKE IF 9GIRL AND 8GUY, 9 GIRL CAN ACCES DORTORY
        }

        $isThereBunkBed = count($bedRepository->findBy(['bedShape' => "topBed"])) != 0;
        $isTherePrivate = count($roomRepository->findBy(['isPrivate' => true])) != 0;
        $isTherePrivateShowerroom = count($roomRepository->findBy(['hasPrivateShowerroom' => true])) != 0;
        $isThereMixedRoom = count($roomRepository->findBy(['isPrivate' => true])) != 0;
        return $this->json(["isThereBunkBed" => $isThereBunkBed], 200);
    }




    #[Route('/bookings/get/passed', name: 'app_bookings_passed', methods: "get")]
    #[Route('/bookings/get/waiting', name: 'waiting_booking', methods: 'get')]
    #[Route('/bookings/get/refund', name: 'refund_booking', methods: 'get')]
    #[Route('/bookings/get/done', name: 'done_booking', methods: 'get')]
    #[Route('/bookings/get/progress', name: 'progress_booking', methods: 'get')]
    #[Route('/bookings', name: 'app_bookings',methods: "get")]
    public function getBookingsWithConditions(BookingRepository $bookingRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $route = $request->attributes->get('_route');
        $bookings = [];

        switch ($route) {
            case "app_bookings_passed":
                $bookingsData = $bookingRepository->findAll();
                foreach ($bookingsData as $booking) {
                    if ($this->globalService->isBookingPassed($booking)) {
                        $bookings[] = $booking;
                    }
                }
                break;
            case "waiting_booking":
                $bookings = $bookingRepository->findBy(['advencement' => "waiting"]);
                break;
            case "refund_booking":
                $bookings = $bookingRepository->findBy(['advencement' => "refund"]);
                break;
            case "progress_booking":
                $bookings = $bookingRepository->findBy(['advencement' => "progress"]);
                break;
            case "done_booking":
                $bookings = $bookingRepository->findBy(['advencement' => "done"]);
                break;
            case"app_bookings":
                $bookingsData = $bookingRepository->findAll();
                foreach ($bookingsData as $booking) {
                    if (!$this->globalService->isBookingPassed($booking)) {
                        $bookings[] = $booking;
                    }
                    else {
                        $booking->setAdvencement("done");
                        $manager->persist($booking);
                    }


                }
                $manager->flush();
                break;
        }

        return $this->json($bookings, 200, [], ['groups' => ['entireBooking']]);
    }

    #[Route('/booking/new', name: 'new_booking', methods: 'post')]
    public function new(Request $request, EntityManagerInterface $manager, RoomRepository $roomRepository, BedRepository $bedRepository, SerializerInterface $serializer): Response
    {
        $booking = $serializer->deserialize($request->getContent(), Booking::class, 'json');

        $booking->setCreatedAt(new \DateTimeImmutable());

        if ($booking->getMail() == null) {
            return $this->json(["message" => "Enter an valid email.", 406]);
        }
        if ($booking->getPhoneNumber() == null) {
            return $this->json(["message" => "Enter an valid email.", 406]);
        }
        // end date after start date
        if ($booking->getEndDate() <= $booking->getStartDate()) {
            return $this->json(["message" => "End date must be after start date"], 200);
        } // start date after today)
        else if ($booking->getStartDate() <= new \DateTime()) {
            return $this->json(["message" => "Start date must be after today"], 200);
        } // end date after today
        else if ($booking->getEndDate() <= new \DateTime()) {
            return $this->json(["message" => "End date must be after today"], 200);
        }
        if ($booking->getWantPrivateRoom() != true && $booking->getWantPrivateRoom() != false) {
            return $this->json(["message" => "Client wants private room ?", 406]);
        }


        $isThereMajor = false;
        $today = new Datetime();
        foreach ($booking->getClients() as $client) {
            $age = $today->diff($client->getBirthDate())->y;
            if (18 <= $age) {
                $isThereMajor = True;
            }
        }
        if (!$isThereMajor) {
            return $this->json(["message" => "You must be accompagnated by a major", 406]);
        }

        //isFinished is determined
        $booking->setFinished(false);
        $booking->setPaid(false);
        $booking->setAdvencement("waiting");
        $booking->setPrice((count($booking->getClients()) * 50));
        //price is calculated
        //bed are associated
        $beds = $this->correspondingBeds($roomRepository, $bedRepository, $booking, $booking->getWantPrivateRoom());
        if (count($beds) == 0) {
            return $this->json(["message" => "There is no place for you groupe criters", 406]);
        }
        foreach ($beds as $bed) {
            $booking->addBed($bed);
        }
        $manager->persist($booking);
        $manager->flush();
        return $this->json($booking, 201, [], ['groups' => ['entireBooking']]);
    }

    #[Route('/booking/edit/{id}', name: 'edit_booking')]
    public function edit(Booking $booking, SerializerInterface $serializer, EntityManagerInterface $manager, Request $request): Response
    {
        $bookingEdited = $serializer->deserialize($request->getContent(), Booking::class, 'json');
        if ($bookingEdited->getStartDate() != null) {
            $booking->setStartDate($bookingEdited->getStartDate());
        }
        if ($bookingEdited->getEndDate() != null) {
            $booking->setEndDate($bookingEdited->getEndDate());
        }
        // end date after start date
        if ($booking->getEndDate() <= $booking->getStartDate()) {
            return $this->json(["message" => "End date must be after start date"], 200);
        } // start date after today)
        else if ($booking->getStartDate() <= new \DateTime()) {
            return $this->json(["message" => "Start date must be after today"], 200);
        } // end date after today
        else if ($booking->getEndDate() <= new \DateTime()) {
            return $this->json(["message" => "End date must be after today"], 200);
        }
        if ($booking->getWantPrivateRoom() != true && $booking->getWantPrivateRoom() != false) {
            return $this->json(["message" => "Client wants private room ?", 406]);
        }
        if ($bookingEdited->getPhoneNumber() != null) {

            $booking->setPhoneNumber($bookingEdited->getPhoneNumber());
        }
        if ($bookingEdited->isFinished() != null) {

            $booking->setFinished($bookingEdited->isFinished());
        }
        if ($bookingEdited->isPaid() != null) {
            $booking->setPaid($bookingEdited->isPaid());
        }
        if ($bookingEdited->getAdvencement() != null) {
            $booking->setAdvencement($bookingEdited->getAdvencement());
        }
        $manager->persist($booking);
        $manager->flush();
        return $this->json($booking, 201, [], ['groups' => ['entireBooking']]);

    }

    #[Route('/booking/finish/{id}', name: 'finish_booking', methods: 'patch')]
    public function finishBooking(Booking $booking, EntityManagerInterface $manager): Response
    {

        $booking->setAdvencement("done");
        $booking->setFinished(true);
        $manager->persist($booking);
        $manager->flush();

        return $this->json($booking, 201, [], ['groups' => ['entireBooking']]);

    }

    #[Route('/booking/remove/{id}', name: 'remove_booking')]
    public function remove(Booking $booking, EntityManagerInterface $manager): Response
    {
        //if paid but not start
        if (!$booking->isFinished() && $booking->isPaid()) {
            $booking->setAdvencement("refund");
            $manager->persist($booking);
            $manager->flush();
            return $this->json([
                "state" => false,
                "message" => "Bokking will be refund"],
                200);
        }
        //if finish but not paid
        if (!$booking->isPaid() && $booking->isFinished()) {
            return $this->json([
                "state" => false,
                "message" => "Internal Server Error"],
                200);
        }
        $manager->remove($booking);
        $manager->flush();
        return $this->json([
            "state" => false,
            "message" => "ok"],
            200);
    }

    public function countBedFreeInRoom($room, $startDateOfBooking, $endDateOfBooking): array
    {
        $count = 0;
        $beds = [];
        foreach ($room->getBeds() as $bed) {
            if ($this->globalService->isBedDeleted($bed)) {
                break;
            }
            if (count($bed->getBookings()) == 0) {
                $count++;
                if ($bed->isDoubleBed()) {
                    $count++;
                }
                $beds[] = $bed;
            }
            foreach ($bed->getBookings() as $booking) {
                $startDate = $booking->getEndDate();
                $endDate = $booking->getStartDate();
                if (
                    (clone $endDate->modify('+1 day')) < $startDateOfBooking ||
                    (clone $endDateOfBooking)->modify('+1 day') < $startDate
                ) {
                    $count++;
                    if ($bed->isDoubleBed()) {
                        $count++;
                    }
                    $beds[] = $bed;
                }

            }


        }
        return ['count' => $count, 'beds' => $beds];

    }

    private function correspondingBeds(RoomRepository $roomRepository, BedRepository $bedRepository, Booking $booking, $wantPrivateRoom): array
    {
        $beds = [];
        if (!$wantPrivateRoom) {
            $rooms = $roomRepository->findBy(['isPrivate' => false]);
            $hasOneRoomForThisGroup = false;
            foreach ($rooms as $room) {

                //calculte only if we dont found one
                if (!$hasOneRoomForThisGroup) {
                    $result = $this->countBedFreeInRoom($room, $booking->getStartDate(), $booking->getEndDate());
                    $count = $result['count'];

                    if ($count >= count($booking->getClients())) {
                        $hasOneRoomForThisGroup = true;
                        $beds = array_slice($result['beds'], 0, count($booking->getClients()));
                    }

                }
            }

            //search if we have place for group in different room
            if (!$hasOneRoomForThisGroup) {
                $count = 0;
                foreach ($rooms as $room) {

                    if (!$hasOneRoomForThisGroup) {
                        $result = $this->countBedFreeInRoom($room, $booking->getStartDate(), $booking->getEndDate());
                        if ($count + $result['count'] >= count($booking->getClients())) {
                            $beds[] = array_slice($result['beds'], 0, count($booking->getClients()) - count($count) - 1);
                            $hasOneRoomForThisGroup = true;
                        } else {
                            $beds[] = $result['beds'];
                            $count += $result['count'];
                        }

                    }
                }
            }
        }
        //count max size for private room
        if ($wantPrivateRoom and count($booking->getClients()) <= 5) {

            foreach ($roomRepository->findBy(['isPrivate' => true]) as $room) {

                if (count($beds) == 0 && $room->getBedNumber() == count($booking->getClients())) {
                    $bedsBoolean = [];

                    $bedsfreeinthisroom = [];
                    foreach ($room->getBeds() as $bed) {
                        if ($this->globalService->isBedDeleted($bed)) {
                            break;
                        }
                        $isBedFree = false;
                        foreach ($bed->getBookings() as $booking) {
                            $startDate = $booking->getEndDate();
                            $endDate = $booking->getStartDate();
                            if (
                                (clone $endDate->modify('+1 day')) < $booking->getStartDate() ||
                                (clone $booking->getEndDate())->modify('+1 day') < $startDate
                            ) {
                                $isBedFree = true;
                            }
                        }
                        if (count($bed->getBookings()) == 0) {
                            $isBedFree = true;
                        }
                        $bedsBoolean[] = $isBedFree;
                        if ($isBedFree) {
                            $bedsfreeinthisroom[] = $bed;
                        }
                    }
                    if (!in_array(false, $bedsBoolean)) {
                        $beds = $bedsfreeinthisroom;
                    }
                }
            }
        }

        return $beds;

    }

}

<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Client;
use App\Repository\BedRepository;
use App\Repository\BookingRepository;
use App\Repository\ClientRepository;
use App\Repository\RoomRepository;
use App\Service\GlobalService;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
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
use Symfony\Component\Validator\Constraints\Date;

#[Route('/api')]
class BookingController extends AbstractController
{
    public int $price = 50;
    private $globalService;

    public function __construct(GlobalService $globalService)
    {
        $this->globalService = $globalService;
    }

    #[Route('/booking/{id}', name: 'get_booking', methods: "get")]
    public function getBooking(Booking $booking): Response
    {
        return $this->json($booking, 200, [], ['groups' => ['entireBooking']]);
    }

    #[Route('/bookings/state', name: 'booking_state', methods: 'get')]
    public function getBookingState(BedRepository $bedRepository, RoomRepository $roomRepository, BookingRepository $bookingRepository, EntityManagerInterface $manager): Response
    {

        $totalBedNumber = count($bedRepository->findBy(["isReservable" => true]));
        $totalPrivateRoom = count($roomRepository->findBy(["isPrivate" => true]));

        if ($totalBedNumber == 0) {

            return $this->json([
                "clientsToCome" => 0,
                "clientsDeparture" => 0,
                "globalFillingPercentage" => 0,
                "privateRoomFillingPercentage" => 0,
                "morningFillingPercentage" => 0,
                "nightFillingPercentage" => 0,
            ], 200);
        }


        $today = new DateTime();
        $halfJourney = $today->format("d.m.Y 12:00");
        $todayFirstHour = $today->format("d.m.Y 00:01");
        $todayLastHour = $today->format("d.m.Y 23:59");
        $todayString = $today->format("d.m.Y");


        $bookings = $bookingRepository->findAll();


        $globalFilling = 0;
        $clientToCome = 0;
        $clientDeparture = 0;
        $privateRoomOccupied = 0;
        $fillingInMorning = 0;
        $fillingInNight = 0;

        foreach ($bookings as $booking) {

            // Global filling
            if (
                ($booking->getStartDate() <= $todayString && $todayString <= $booking->getEndDate() && !$booking->isFinished()) ||
                $todayFirstHour < $booking->getEndDate() && $booking->getEndDate() < $halfJourney ||
                $todayFirstHour < $booking->getStartDate() && $booking->getStartDate() < $halfJourney ||
                $halfJourney > $booking->getEndDate() && $booking->getEndDate() > $todayLastHour ||
                $halfJourney > $booking->getStartDate() && $booking->getEndDate() > $todayLastHour
            ) {
                $globalFilling += $booking->getBedsCount();

                //private room
                foreach ($booking->getBeds() as $bed) {
                    if ($bed->getRoom()->isPrivate()) {
                        $privateRoomOccupied++;
                    }
                }
            }

            //fillingInMorning
            if (
                $todayFirstHour < $booking->getEndDate() && $booking->getEndDate() < $halfJourney ||
                $todayFirstHour < $booking->getStartDate() && $booking->getStartDate() < $halfJourney
            ) {
                $fillingInMorning += $booking->getBedsCount();
            }


            //filling in night
            if (
                $halfJourney > $booking->getEndDate() && $booking->getEndDate() > $todayLastHour ||
                $halfJourney > $booking->getStartDate() && $booking->getEndDate() > $todayLastHour
            ) {
                $fillingInNight += $booking->getBedsCount();
            }

            // Client to come
            if ($booking->getStartDate() /*it's datetime*/ == $todayString) {
                $clientToCome += $booking->getClientsNumber();
            }

            // Departure
            if ($booking->getEndDate() /*it's datetime*/ == $todayString) {
                $clientDeparture += $booking->getClientsNumber();
            }


        }

        if ($totalPrivateRoom !== 0) {
            $privateRoomPercentage = round($privateRoomOccupied / $totalPrivateRoom);
        } else {
            $privateRoomPercentage = 0;
        }


        //number of stack of 10 years
        $stacksOf10Years = 4;

        //number of stack of 10 years
        $stackOfYears = 4 * 10;

        //number of stack of 10 years
        $stackOfMonths = 5 * 12;
        $bookingsThisYear = [];
        $bookingsThis10Years = [];
        $bookingsThisMonth = [];

        //get stack of 10 years
        for ($i = 0; $i < $stacksOf10Years; $i++) {
            $year2 = (clone $today)->modify("- " . (10 * $i) . " years");
            $year1 = (clone $today)->modify("- " . (10 * ($i + 1)) . " years");
            $year1Int = intval($year1->format('Y'));
            $year2Int = intval($year2->format('Y'));

            $this10Year = [];
            for ($y = $year1Int; $y <= $year2Int; $y++) {
                $firstDayInYear = new DateTime("$y-01-01");
                $firstDayInYear->modify('first day of January');
                $endDayOfYear = new DateTimeImmutable("$y-01-01");
                $endDayOfYear = $endDayOfYear->modify('first day of December');
                $this10Year  [] = $bookingRepository->countAllBetweenDate($firstDayInYear, $endDayOfYear);
            }
            $bookingsThis10Years[] = [
                "mumber" => $year2Int,
                "data" => $this10Year,
            ];
        }

        //get stack of 1 year
        $listOfMonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        for ($i = 0; $i < $stackOfYears; $i++) {

            $yearStudied = (clone $today)->modify("- $i years");

            $thisMonths = [];
            for ($m = 0; $m < 12; $m++) {
                $firstDayOfMonth = $yearStudied->modify('first day of ' . $listOfMonths[$m]);
                $lastDayOfMonth = $yearStudied->modify('last day of ' . $listOfMonths[$m]);
                $thisMonths  [] = $bookingRepository->countAllBetweenDate($firstDayOfMonth, $lastDayOfMonth);
            }
            $bookingsThis10Years[] = [
                "number" => $yearStudied->format('Y'),
                "data" => $thisMonths,
            ];
        }

// Stack de X mois
        for ($i = 0; $i < $stackOfMonths; $i++) {
            $month = (clone $today)->modify("- $i month");
            $daysInMonth = intval($month->format('t')); // Nombre de jours dans le mois

            $thisMonths = [];
            for ($m = 1; $m <= $daysInMonth; $m++) { // De 1 à nombre de jours du mois
                $firstHourDay = new DateTime($month->format('Y-m') . "-$m 00:00:00");
                $lastHourDay = new DateTime($month->format('Y-m') . "-$m 23:59:59");

                $thisMonths[] = $bookingRepository->countAllBetweenDate($firstHourDay, $lastHourDay);
            }

            $bookingsThis10Years[] = [
                "number" => $i,
                "data" => $thisMonths,
            ];
        }

        return $this->json([
            "clientsToCome" => $clientToCome,
            "clientsDeparture" => $clientDeparture,
            "globalFillingPercentage" => round($globalFilling / $totalBedNumber),
            "privateRoomFillingPercentage" => $privateRoomPercentage,
            "morningFillingPercentage" => round($fillingInMorning / $totalBedNumber),
            "nightFillingPercentage" => round($fillingInNight / $totalBedNumber),
            "bookingsThisYear" => $bookingsThisYear,
            "bookingsThis10Years" => $bookingsThis10Years,
            "bookingsThisMonth" => $bookingsThisMonth
        ], 200);
    }

    #[Route('/bookings/get/{field}', name: 'get_specific_booking', methods: "get")]
    public function getBookingsWithConditions(BookingRepository $bookingRepository, Request $request, EntityManagerInterface $manager, $field): Response
    {
        $today = new DateTime();
        $newOneWeek = (clone $today)->modify('+1 week');
        $lastOneWeek = (clone $today)->modify('-1 week');

        $bookings = $bookingRepository->findAll();
        $bookingsFinal = [];
        switch ($field) {
            case "tocome":
                foreach ($bookings as $booking) {
                    if ($today <= $booking->getStartDate() and $booking->getStartDate() <= $newOneWeek) {
                        $bookingsFinal[] = $booking;
                    }
                }
                break;
            case "today":
                foreach ($bookings as $booking) {
                    if ($booking->getStartDate() <= $today && $today <= $booking->getEndDate() && !$booking->isFinished()) {
                        $bookingsFinal[] = $booking;
                    }
                }
                break;

            case "passed":
                foreach ($bookings as $booking) {
                    if ($lastOneWeek <= $booking->getStartDate() and $booking->getStartDate() <= $today) {
                        $bookingsFinal[] = $booking;
                    }
                }
                break;

            case"all":
                $this->globalService->refreshData($bookingRepository, $manager);
                $bookingsFinal = $bookingRepository->findBy([], ["startDate" => "ASC"]);
                break;
        }

        return $this->json($bookingsFinal, 200, [], ['groups' => ['bookings']]);
    }

    #[Route('/booking/new', name: 'new_booking', methods: 'post')]
    public function new(Request $request, ClientRepository $clientRepository, EntityManagerInterface $manager, RoomRepository $roomRepository, SerializerInterface $serializer): Response
    {
        $booking = $serializer->deserialize($request->getContent(), Booking::class, 'json');

        //verifying fields
        if (!$this->globalService->isValidString($booking->getMail())) {
            return $this->json(["message" => "Enter an valid email. (field : mail, accepted : string)"], 406);
        }
        if (!$this->globalService->isValidString($booking->getPhoneNumber())) {
            return $this->json(["message" => "Enter a valid phone number. (field : phoneNumber, accepted : string)"], 406);
        }
        if ($booking->getStartDate() == null) {
            return $this->json(["message" => "Enter a valid start date. (field : startDate, accepted : d.m.Y H:i )"], 406);
        }
        if ($booking->getEndDate() == null) {
            return $this->json(["message" => "Enter a valid end date. (field : endDate, accepted : d.m.Y H:i )"], 406);
        }
        if ($booking->getMainClient() == null) {
            return $this->json(["message" => "You must provide a main client. (field : mainClient, accepted : {firstName,lastName,birthDate} )"], 406);
        }
        if (!$this->globalService->isValidString($booking->getMainClient()->getFirstName())) {
            return $this->json(["message" => "You must provide a first name for main client. (field : mainClient, accepted : {firstName,lastName,birthDate} )"], 406);
        }
        if (!$this->globalService->isValidString($booking->getMainClient()->getLastName())) {
            return $this->json(["message" => "You must provide a last name for main client. (field : mainClient, accepted : {firstName,lastName,birthDate} )"], 406);
        }
        if ($booking->getMainClient()->getBirthDate() == null) {
            return $this->json(["message" => "You must provide a birthDate for main client. (field : mainClient, accepted : {firstName,lastName,birthDate} )"], 406);
        }

        $clientExist = $clientRepository->findOneBy(["firstName" => $booking->getMainClient()->getFirstName(), "lastName" => $booking->getMainClient()->getLastName(), "birthDate" => $booking->getMainClient()->getBirthDate()]);
        if ($clientExist) {
            $booking->setMainClient($clientExist);
        } else {
            $clientCreated = new Client();
            $clientCreated->setFirstName($booking->getMainClient()->getFirstName());
            $clientCreated->setLastName($booking->getMainClient()->getLastName());
            $clientCreated->setBirthDate($booking->getMainClient()->getBirthDate());
            $booking->setMainClient($clientCreated);
        }
        $message = $this->globalService->isStartDateAndEndDateConform($booking->getStartDate(), $booking->getEndDate());
        if ($message != "ok") {
            return $this->json(["message" => $message,], 406);

        }
        if (!$this->globalService->isValidBool($booking->getWantPrivateRoom())) {
            return $this->json(["message" => "Client wants private room ? (field : wantPrivateRoom, value :true,false)"], 406);
        }


        $today = new Datetime();

        $age = $today->diff($booking->getMainClient()->getBirthDate())->y;
        if (18 > $age) {
            return $this->json(["message" => "Main client must be major ? (field : mainClient, value : {firstName,lastName,birthDate} ",], 406);
        }
        $booking->getMainClient()->setEmail($booking->getMail());
        $manager->persist($booking->getMainClient());
        foreach ($booking->getClients() as $client) {
            $booking->removeClient($client);
            if (!$this->globalService->isValidString($client->getFirstName())
                or !$this->globalService->isValidString($client->getLastName())
                or $client->getBirthDate() == null
            ) {
                return $this->json(["message" => "You must provide a first name,last name and birth date for each client. (field : clients [], accepted : {firstName,lastName,birthDate} )"], 406);
            }
            $clientExist = $clientRepository->findOneBy(["firstName" => $client->getFirstName(), "lastName" => $client->getLastName(), "birthDate" => $client->getBirthDate()]);
            if ($clientExist) {
                $client = $clientExist;
            } else {
                $clientCreated = new Client();
                $clientCreated->setFirstName($client->getFirstName());
                $clientCreated->setLastName($client->getLastName());
                $clientCreated->setBirthDate($client->getBirthDate());
                $client = $clientCreated;
            }
            $manager->persist($client);
            $client->addBooking($booking);
            $booking->addClient($client);
            $client->setInvitedBy($booking->getMainClient());
            $manager->persist($client);
        }


        $today = new \DateTime();
        $isBookingForToday = $today->format('Y') == $booking->getStartDate()->format('Y') && $today->format('d') == $booking->getStartDate()->format('d') && $today->format('m') == $booking->getStartDate()->format('m');

        //VERIFYING THE DAY OF THE ARRIVED, IF TODAY VERIFYING CLEANED BED

        $beds = $this->correspondingBeds($roomRepository, $booking, $booking->getWantPrivateRoom(), $isBookingForToday);

        if (count($beds) == 0) {
            return $this->json(["message" => "There is no place for your group criters",], 406);
        }
        foreach ($beds as $bed) {
            $booking->addBed($bed);
        }

        //price is calculated
        $booking->setPrice((count($booking->getClients()) + 1) * 50);

        $booking->setPaid(false);
        $booking->setAdvencement("waiting");
        $booking->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($booking);

        $manager->flush();
        return $this->json($booking, 201, [], ['groups' => ['entireBooking']]);
    }

    #[Route('/booking/edit/{id}', name: 'edit_booking')]
    public function edit(Booking $booking, SerializerInterface $serializer, EntityManagerInterface $manager, Request $request, RoomRepository $roomRepository): Response
    {
        $bookingEdited = $serializer->deserialize($request->getContent(), Booking::class, 'json');

        if (!$this->globalService->isValidString($bookingEdited->getMail())) {
            return $this->json(["message" => "Enter an valid email. (field : mail, accepted : string)",], 406);
        }
        if (!$this->globalService->isValidString($bookingEdited->getPhoneNumber())) {
            return $this->json(["message" => "Enter a valid phone number. (field : phoneNumber, accepted : string)",], 406);
        }
        if ($bookingEdited->getStartDate() == null) {
            return $this->json(["message" => "Enter a valid start date. (field : startDate, accepted : d.m.Y H:i )"], 406);
        }
        if ($bookingEdited->getEndDate() == null) {
            return $this->json(["message" => "Enter a valid end date. (field : endDate, accepted : d.m.Y H:i )"], 406);
        }
        $message = $this->globalService->isStartDateAndEndDateConform($bookingEdited->getStartDate(), $bookingEdited->getEndDate());
        if ($message != "ok") {
            return $this->json(["message" => $message,], 406);
        }
        if (!$this->globalService->isValidBool($bookingEdited->getWantPrivateRoom())) {
            return $this->json(["message" => "Client wants private room ? (field : wantPrivateRoom, value :true,false)",], 406);
        }

        $needToChangeBeds = false;
        if ($booking->getStartDate() != $bookingEdited->getStartDate() or $booking->getEndDate() != $bookingEdited->getEndDate() or $booking->getWantPrivateRoom() != $bookingEdited->getWantPrivateRoom()) {
            $needToChangeBeds = true;
        }

        $booking->setWantPrivateRoom($bookingEdited->getWantPrivateRoom());
        $booking->setStartDate($bookingEdited->getStartDate());
        $booking->setEndDate($bookingEdited->getEndDate());
        $booking->setPhoneNumber($bookingEdited->getPhoneNumber());
        $booking->setMail($bookingEdited->getMail());

        if ($needToChangeBeds) {
            foreach ($booking->getBeds() as $bed) {
                $booking->removeBed($bed);
                $bed->removeBooking($booking);
                $manager->persist($bed);
            }
            $manager->persist($booking);
            $today = new \DateTime();
            $isBookingForToday = $today->format('Y') == $booking->getStartDate()->format('Y') && $today->format('d') == $booking->getStartDate()->format('d') && $today->format('m') == $booking->getStartDate()->format('m');
            $beds = $this->correspondingBeds($roomRepository, $booking, $booking->getWantPrivateRoom(), $isBookingForToday);

            if (count($beds) == 0) {
                return $this->json(["message" => "There is no place for your group criters"], 406);
            }

            foreach ($beds as $bed) {
                $booking->addBed($bed);
            }
            $booking->setPrice((count($booking->getClients()) + 1) * 50);
        }


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


    private function correspondingBeds(RoomRepository $roomRepository, Booking $booking, $wantPrivateRoom, $isBookingForToday): array
    {
        $beds = [];

        //add 1 to count main client not include in clients array
        $finalCount = count($booking->getClients()) + 1;

        if (!$wantPrivateRoom) {

            //ge tall public rooms
            $rooms = $roomRepository->findBy(['isPrivate' => false]);

            //to stop bouclbouclee if we get all beds we need
            $hasOneRoomForThisGroup = false;

            //for place in same room
            foreach ($rooms as $room) {
                //to not iterate if we already have bed we need
                if ($hasOneRoomForThisGroup) {
                    continue;
                }

                $result = $this->countBedFreeInRoom($room, $booking->getStartDate(), $booking->getEndDate(), $booking->getId(), $isBookingForToday);
                $count = $result['count'];

                if ($count >= $finalCount) {
                    $hasOneRoomForThisGroup = true;
                    //get juste bed we need if lenght of array $beds is 10 but we are group of 5 we get just 5 firsts elements of $beds
                    foreach ($result['beds'] as $bedSelected) {
                        $beds[] = $bedSelected;
                    }
                }

            }
            //search if we have place for group in different room
            if (!$hasOneRoomForThisGroup) {
                $count = 0;
                foreach ($rooms as $room) {
                    //to not iterate if we already have bed we need
                    if ($hasOneRoomForThisGroup) {
                        continue;
                    }
                    $result = $this->countBedFreeInRoom($room, $booking->getStartDate(), $booking->getEndDate(), $booking->getId(), $isBookingForToday);

                    //if with that result we have enought beds we stop and save beds
                    if ($count + $result['count'] >= $finalCount) {
                        $hasOneRoomForThisGroup = true;
                    }

                    // add all beds found in array, we will remove beds in surplus , at the end
                    if ($result['beds'] !== []) {
                        foreach ($result['beds'] as $bedSelected) {
                            $beds[] = $bedSelected;
                        }
                        $count += $result['count'];
                    }


                }
            }

        }
        if ($wantPrivateRoom) {

            foreach ($roomRepository->findBy(['isPrivate' => true]) as $room) {
                if ($room->getBedNumber() != $finalCount) {
                    continue;
                }

                //we want to fill all beds in room , we assert that room has beds and beds number corresponding to clients number
                $bedsBoolean = [];

                $bedsfreeinthisroom = [];
                foreach ($room->getBeds() as $bed) {
                    //we dont need to assert if bed is deleted because getBeds exclude already deleted bed

                    if (count($bed->getBookings()) == 0) {
                        $isBedFree = true;
                    } else {
                        $isBedFree = $this->bedFreeAtThisDate($bed, $booking->getStartDate(), $booking->getEndDate(), $booking->getId());
                    }
                    $bedsBoolean[] = $isBedFree;
                    if ($isBedFree) {
                        $bedsfreeinthisroom[] = $bed;
                    }
                }
                //if a bed of this room is not free we dont add beds
                if (!in_array(false, $bedsBoolean)) {
                    //we add beds to beds array and that stopped this foreach
                    $beds = $bedsfreeinthisroom;
                }

            }
        }

        $bedsToSend = [];
        $countBed = 0;
        foreach ($beds as $bed) {
            if ($countBed >= $finalCount) {
                continue;
            }
            $countBed++;
            if ($bed->isDoubleBed()) {
                $countBed++;
            }

            $bedsToSend[] = $bed;
        }

        if ($countBed != $finalCount) {
            return [];
        }

        return $bedsToSend;

    }

    public function countBedFreeInRoom($room, $startDateOfBooking, $endDateOfBooking, $bookingId, $wantToVerifyIfBedIsCleaned): array
    {
        $count = 0;
        $beds = [];
        foreach ($room->getBeds() as $bed) {
            //we dont need to assert if bed is deleted because getBeds exclude already deleted bed

            //if bed has no bookings bed is free
            if (count($bed->getBookings()) == 0 && $bed->isReservable()) {
                $isBedFree = true;
            } else {
                $isBedFree = $this->bedFreeAtThisDate($bed, $startDateOfBooking, $endDateOfBooking, $bookingId);
            }

            if ($wantToVerifyIfBedIsCleaned) {
                if ($bed->getState() != "inspected") {
                    $isBedFree = false;
                }
            }

            if ($isBedFree) {
                $count++;

                //add 2 place if is double bed
                if ($bed->isDoubleBed()) {
                    $count++;
                }

                $beds[] = $bed;
            }


        }
        return ['count' => $count, 'beds' => $beds];

    }


    public function bedFreeAtThisDate($bed, $startDateOfBooking, $endDateOfBooking, $bookingId)
    {
        if (!$bed->isReservable()) {
            return false;
        }

        foreach ($bed->getBookings() as $booking) {
            if ($booking->getId() == $bookingId) {
                continue;
            }
            if ($this->globalService->isBookingPassed($booking)) {
                continue;
            }

            $startDate = $booking->getStartDate()->modify('-1 day');
            $endDate = clone $booking->getEndDate()->modify('+1 day');

            //we add bed only if this booking end before our booking or begin adter our booking
            if (
                ($startDate <= $startDateOfBooking and $startDateOfBooking <= $endDate) or
                ($startDate <= $endDateOfBooking and $endDateOfBooking <= $endDate) or
                ($startDateOfBooking <= $startDate and $endDate <= $endDateOfBooking)
            ) {
                return false;
            }

        }
        return true;
    }
}

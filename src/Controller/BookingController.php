<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BedRepository;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
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
    #[Route('/bookings', name: 'app_bookings')]
    public function gets(BookingRepository $bookingRepository): Response
    {
        return $this->json($bookingRepository->findAll(), 200, [], ['groups' => ['entire_booking']]);
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
        if(isset($data['mail'])){
            //Search if as an client account
        }
     if(isset($data['getPrivateRoom'])){
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


    #[Route('/booking/new', name: 'new_booking')]
    public function new(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        //accept : startDate,endDate,phoneNumber,mail,
        $booking = $serializer->deserialize($request->getContent(), Booking::class, 'json');

        //CreatedAt is added
        $booking->setCreatedAt(new \DateTimeImmutable());


        if ($booking->getMail() == null) {
            return $this->json(["message" => "Enter an valid email.", 406]);
        }
        if ($booking->getPhoneNumber() == null) {
            return $this->json(["message" => "Enter an valid email.", 406]);
        }
        //isFinished is determined
        $booking->setIsFinished(false);

        //price is calculated
        //bed are associated

        dd($booking);


        return $this->json($booking, 201, [], ['groups' => ['entire_booking']]);
    }


}

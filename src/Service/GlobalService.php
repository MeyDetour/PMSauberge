<?php

namespace App\Service;

use App\Entity\Bed;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;

class GlobalService
{
    public function isBookingPassed(Booking $booking)
    {
        $now = new \DateTime();
        if ($booking->getEndDate() < $now) {
            return true;
        }
        return false;
    }
   public function isBookingInProgress(Booking $booking)
    {
        $now = new \DateTime();
        if ($booking->getStartDate() < $now and $now < $booking->getEndDate() ) {
            return true;
        }
        return false;
    }

    public function isStartDateAndEndDateConform($start,$end){
        // end date after start date
        if ($end <= $start) {
            return "End date must be after start date" ;
        } // start date after today)
        else if ($start <= new \DateTime()) {
            return  "Start date must be after today" ;
        } // end date after today
        else if ($end <= new \DateTime()) {
            return "End date must be after today" ;
        }
        return "ok";
    }


    public function isValidBool($value){
        if ($value === null){
            return false;
        }
        if ($value !== true and $value!==false){

            return false;
        }

        return true;
    }
    public function isValidString($value){
        if ($value == null){
            return false;
        }
        if (trim($value,' ') =="") {
            return false;
        }
        if (is_string($value)){
            return true;
        }
        return false;
    }

    public function refreshData(BookingRepository $bookingRepository,EntityManagerInterface $manager){
        $bookings = $bookingRepository->findBy(['advencement'=>["progress","waiting"]]);
        foreach ( $bookings as $booking){
            if ($this->isBookingPassed($booking)){
                if ($booking->getAdvencement() == "waiting" or $booking->getAdvencement()=="progress"){
                    $booking->setAdvencement("done");
                }
            }
            if ($this->isBookingInProgress($booking)){
                if ($booking->getAdvencement() == "waiting" ){
                    $booking->setAdvencement("progress");
                    foreach ($booking->getBeds() as $bed){
                        $bed->setOccupied(true);
                        $bed->setCurrentBooking($booking);
                        $manager->persist($bed);
                    }
                }
            }

            $manager->persist($booking);
        }
        $manager->flush();
        return;

    }


}
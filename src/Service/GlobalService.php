<?php

namespace App\Service;

use App\Entity\Bed;
use App\Entity\Booking;

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


    public function isValidBool($value){
        if ($value === null){
            var_dump("null");
            return false;
        }
        if ($value !== true and $value!==false){
            return false;
        }
        if (is_int($value)){
            return false;
        }
        if (is_string($value)){
            return false;
        }
        return true;
    }
}
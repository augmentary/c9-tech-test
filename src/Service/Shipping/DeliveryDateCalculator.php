<?php

declare(strict_types=1);

namespace App\Service\Shipping;

use App\Entity\Country;
use App\Entity\ShippingMethod;

class DeliveryDateCalculator
{
    private function getShippingDays(ShippingMethod $method, Country $country): int
    {
        if ($country->getIsoCode() === 'GB') {
            return $method->getShipTimeUk();
        }
        if ($country->isEurope()) {
            return $method->getShipTimeEurope();
        }

        return $method->getShipTimeRestOfWorld();
    }

    private function getShippingDate(\DateTimeImmutable $orderDate): \DateTimeImmutable
    {
        $shipDate = $orderDate->setTime(0, 0);

        $hour = (int) $orderDate->format('G');
        if($hour >= 16) {
            $shipDate = $this->incrementWorkingDays($shipDate, 1);
        }

        return $shipDate;
    }

    private function incrementWorkingDays(\DateTimeImmutable $date, int $days): \DateTimeImmutable
    {
        $currentDay =  (int) $date->format('N');

        //if $days is more than 5 we can just add multiple whole weeks
        $weeksToAdd = (int) floor($days / 5);
        $ret = $date->modify($weeksToAdd.' weeks');

        $daysToAdd = $days % 5;
        //if the remaining daysToAdd cause us to cross a weekend
        $weekendCrossed = ($currentDay + $daysToAdd) > 5;
        if($weekendCrossed) {
            //as we're guaranteed to be dealing with increments smaller than 5 days, we will never cross 2 weekends
            $daysToAdd += 2;
        }

        return $ret->modify($daysToAdd.' days');
    }

    public function getDeliveryDate(
        ShippingMethod $method,
        Country $country,
        \DateTimeImmutable $orderDate
    ): \DateTimeImmutable {
        $days = $this->getShippingDays($method, $country);
        $shipDate = $this->getShippingDate($orderDate);
        return $this->incrementWorkingDays($shipDate, $days);
    }
}

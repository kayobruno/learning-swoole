<?php

namespace App\Contracts;

use DateTimeInterface;

interface MessageFactory
{
    public static function createSignOn(DateTimeInterface $dateTime): string;

    public static function createSignOff(DateTimeInterface $dateTime): string;

    public static function createEchoTest(DateTimeInterface $dateTime): string;
}
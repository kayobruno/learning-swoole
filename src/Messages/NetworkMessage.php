<?php

namespace App\Messages;

use App\Contracts\MessageBuilder;
use App\Contracts\MessageFactory;
use DateTimeInterface;

final class NetworkMessage implements MessageFactory
{
    public static function createSignOn(DateTimeInterface $dateTime = null): string {
        return self::buildMessage(new SignOn(), $dateTime);
    }

    public static function createSignOff(DateTimeInterface $dateTime): string
    {
        return self::buildMessage(new SignOff(), $dateTime);
    }

    public static function createEchoTest(DateTimeInterface $dateTime = null): string
    {
        return self::buildMessage(new EchoTest(), $dateTime);
    }

    private static function buildMessage(MessageBuilder $builder, DateTimeInterface $dateTime = null): string
    {
        $header = $builder->buildHeader();
        $primaryBitmap = $builder->buildPrimaryBitmap();
        $secondaryBitmap = $builder->buildSecondaryBitmap();
        $fields = $builder->buildFields($dateTime);

        $message = $header . $primaryBitmap . $secondaryBitmap . $fields;
        $headerPrefix = self::buildHeaderPrefix($message);

        return $headerPrefix . $message;
    }

    private static function buildHeaderPrefix(string $message): string
    {
        $applicationHeader = chr(0);
        $messageLength = strlen($message) + 2;
        $asciiLength = self::convertLengthHexToBin($messageLength);

        return $applicationHeader . $asciiLength;
    }

    private static function convertLengthHexToBin(int $length): bool|string
    {
        $lengthAsHex = dechex($length);
        if (strlen($lengthAsHex) % 2 !== 0) {
            $lengthAsHex = '0' . $lengthAsHex;
        }

        return hex2bin($lengthAsHex);
    }
}
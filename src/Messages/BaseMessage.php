<?php

namespace App\Messages;

use App\Contracts\MessageBuilder;
use DateTimeInterface;

abstract class BaseMessage implements MessageBuilder
{
    public const NETWORK_MESSAGE_HEADER = 'ISO0234000700800';
    public const NETWORK_MESSAGE_SECONDARY_BITMAP = '0400000000000000';
    public const NETWORK_MESSAGE_RETAILER_DATA = '01650NNNY2010000   ';

    public function buildHeader(): string
    {
        return self::NETWORK_MESSAGE_HEADER;
    }

    public function buildSecondaryBitmap(): string
    {
        return self::NETWORK_MESSAGE_SECONDARY_BITMAP;
    }

    public function getAuditNumber(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Him');
    }

    public function getTransmissionDatetime(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('mdHis');
    }

    public function getSettlementDate(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('md');
    }

    public function getCommonFields(string $informationCode, DateTimeInterface $dateTime): array
    {
        return [
            $this->getAuditNumber($dateTime),
            $this->getTransmissionDatetime($dateTime),
            $this->getSettlementDate($dateTime),
            self::NETWORK_MESSAGE_RETAILER_DATA,
            $informationCode,
        ];
    }
}
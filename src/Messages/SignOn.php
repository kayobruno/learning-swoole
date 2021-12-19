<?php

namespace App\Messages;

use App\Contracts\NetworkInformationCode;
use DateTimeInterface;

class SignOn extends BaseMessage implements NetworkInformationCode
{
    public const NETWORK_PRIMARY_BITMAP = '8222000000010000';
    public const NETWORK_INFORMATION_CODE = '001';

    public function buildPrimaryBitmap(): string
    {
        return self::NETWORK_PRIMARY_BITMAP;
    }

    public function buildFields(DateTimeInterface $dateTime): string
    {
        [
            $auditNumber,
            $transmissionDatetime,
            $settlementDate,
            $retailerData,
            $informationCode
        ] = $this->getCommonFields($this->getInformationCode(), $dateTime);

        return $auditNumber . $transmissionDatetime . $settlementDate . $retailerData . $informationCode;
    }

    public function getInformationCode(): string
    {
        return self::NETWORK_INFORMATION_CODE;
    }
}
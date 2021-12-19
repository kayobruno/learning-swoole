<?php

namespace App\Messages;

use App\Contracts\NetworkInformationCode;
use DateTimeInterface;

class EchoTest extends BaseMessage implements NetworkInformationCode
{
    public const NETWORK_PRIMARY_BITMAP = '8222000000000000';
    public const NETWORK_INFORMATION_CODE = '301';

    public function buildPrimaryBitmap(): string
    {
        return self::NETWORK_PRIMARY_BITMAP;
    }

    public function getInformationCode(): string
    {
        return self::NETWORK_INFORMATION_CODE;
    }

    public function buildFields(DateTimeInterface $dateTime): string
    {
        $auditNumber = $this->getAuditNumber($dateTime);
        $transmissionDatetime = $this->getTransmissionDatetime($dateTime);
        $settlementDate = $this->getSettlementDate($dateTime);
        $informationCode = $this->getInformationCode();

        return $auditNumber . $transmissionDatetime . $settlementDate . $informationCode;
    }
}
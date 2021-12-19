<?php

namespace App\Contracts;

use DateTimeInterface;

interface MessageBuilder
{
    public function buildHeader(): string;
    public function buildPrimaryBitmap(): string;
    public function buildSecondaryBitmap(): string;
    public function buildFields(DateTimeInterface $dateTime): string;
    public function getCommonFields(string $informationCode, DateTimeInterface $dateTime): array;
}
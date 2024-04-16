<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class Sender
{
    public function __construct(
        /**
         * @SerializedName("fullName")
         * @Groups({"full"})
         */
        public readonly FullName $fullName,

        /**
         * @SerializedName("phone")
         * @Groups({"full"})
         */
        public readonly string $phone,
    ) {
    }

    public function getFullName(): FullName
    {
        return $this->fullName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}

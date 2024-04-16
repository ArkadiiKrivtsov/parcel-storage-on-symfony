<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @OA\Schema(
 *     type="object",
 *     schema="FullName"
 * )
 */
final class FullName
{
    public function __construct(
        /**
         * @SerializedName("firstName")
         * @Groups({"full"})
         */
        public readonly string $firstName,

        /**
         * @SerializedName("lastName")
         * @Groups({"full"})
         */
        public readonly string $lastName,

        /**
         * @SerializedName("middleName")
         * @Groups({"full"})
         */
        public readonly string $middleName,

        /**
         * @SerializedName("address")
         * @Groups({"full"})
         */
        public readonly string $address,
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}

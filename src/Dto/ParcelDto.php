<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @OA\Schema
 *
 */
final class ParcelDto
{
    public function __construct(

        /**
         * @SerializedName("sender")
         * @Groups({"full"})
         */
        public readonly Sender $sender,

        /**
         * @SerializedName("receiver")
         * @Groups({"full"})
         */
        public readonly Recipient $receiver,

        /**
         * @SerializedName("dimensions")
         * @Groups({"full"})
         */
        public readonly Dimensions $dimensions,

        /**
         * @SerializedName("estimatedCost")
         * @Groups({"full"})
         */
        public readonly int $estimatedCost,
    ) {
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function getReceiver(): Recipient
    {
        return $this->receiver;
    }

    public function getDimensions(): Dimensions
    {
        return $this->dimensions;
    }

    public function getEstimatedCost(): int
    {
        return $this->estimatedCost;
    }
}

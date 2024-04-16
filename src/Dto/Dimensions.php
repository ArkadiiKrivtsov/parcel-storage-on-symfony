<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class Dimensions
{
    public function __construct(
    /**
     * @SerializedName("weight")
     * @Groups({"full"})
     */
    public readonly int $weight,

    /**
     * @SerializedName("length")
     * @Groups({"full"})
     */
    public readonly int $length,

    /**
     * @SerializedName("height")
     * @Groups({"full"})
     */
    public readonly int $height,

    /**
     * @SerializedName("width")
     * @Groups({"full"})
     */
    public readonly int $width,
    ) {
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}

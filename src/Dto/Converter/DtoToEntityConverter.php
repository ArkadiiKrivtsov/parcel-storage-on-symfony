<?php

declare(strict_types=1);

namespace App\Dto\Converter;

use App\Dto\Dimensions;
use App\Dto\FullName;
use App\Dto\ParcelDto;
use App\Dto\Recipient;
use App\Dto\Sender;
use App\Entity\ParcelEntity;
use Doctrine\ORM\EntityManagerInterface;

class DtoToEntityConverter
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function convertToEntity(ParcelDto $parcelDto): ParcelEntity
    {
        $senderEntity = $this->convertSender($parcelDto->getSender());
        $dimensionsEntity = $this->convertDimensions($parcelDto->getDimensions());
        $recipientEntity = $this->convertRecipient($parcelDto->getReceiver());

        $parcelEntity = new ParcelEntity();
        $parcelEntity->setEstimatedCost($parcelDto->getEstimatedCost());
        $parcelEntity->setReceiver($recipientEntity);
        $parcelEntity->setSender($senderEntity);
        $parcelEntity->setDimensions($dimensionsEntity);

        return $parcelEntity;
    }

    public function convertDimensions(Dimensions $dimensions): \App\Entity\Dimensions
    {
        $length = $dimensions->getLength();
        $width = $dimensions->getWidth();
        $height = $dimensions->getHeight();
        $weight = $dimensions->getWeight();

        $dimensionsEntity = $this->entityManager
            ->getRepository(\App\Entity\Dimensions::class)
            ->findOneBy([
                'weight' => $weight,
                'length' => $length,
                'height' => $height,
                'width' => $width,
            ]);

        if ($dimensionsEntity === null) {
            $dimensionsEntity = new \App\Entity\Dimensions();
            $dimensionsEntity->setWeight($weight);
            $dimensionsEntity->setLength($length);
            $dimensionsEntity->setHeight($height);
            $dimensionsEntity->setWidth($width);

            $this->entityManager->persist($dimensionsEntity);
            $this->entityManager->flush();
            return $dimensionsEntity;
        }

        return $dimensionsEntity;
    }

    public function convertSender(Sender $sender): \App\Entity\Sender
    {
        $fullName = $this->convertFullName($sender->getFullName());
        $phone = $sender->getPhone();

        $senderEntity = $this->entityManager
            ->getRepository(\App\Entity\Sender::class)
            ->findOneBy([
                'fullName' => $fullName,
                'phone' => $phone,
            ]);

        if ($senderEntity === null) {
            $senderEntity = new \App\Entity\Sender();
            $senderEntity->setPhone($phone);
            $senderEntity->setFullName($fullName);

            $this->entityManager->persist($senderEntity);
            $this->entityManager->flush();

            return $senderEntity;
        }
        return $senderEntity;
    }

    public function convertRecipient(Recipient $recipient): \App\Entity\Recipient
    {
        $fullName = $this->convertFullName($recipient->getFullName());
        $phone = $recipient->getPhone();

        $recipientEntity = $this->entityManager
            ->getRepository(\App\Entity\Recipient::class)
            ->findOneBy([
                'fullName' => $fullName,
                'phone' => $phone,
            ]);

        if ($recipientEntity === null) {
            $recipientEntity = new \App\Entity\Recipient();
            $recipientEntity->setPhone($phone);
            $recipientEntity->setFullName($fullName);

            $this->entityManager->persist($recipientEntity);
            $this->entityManager->flush();

            return $recipientEntity;
        }

        return $recipientEntity;
    }

    public function convertFullName(FullName $fullName): \App\Entity\FullName
    {
        $firstName = $fullName->getFirstName();
        $middleName = $fullName->getMiddleName();
        $lastName = $fullName->getLastName();
        $address = $fullName->getAddress();

        $fullNameEntity = $this->entityManager
            ->getRepository(\App\Entity\FullName::class)
            ->findOneBy([
                'firstName' => $firstName,
                'middleName' => $middleName,
                'lastName' => $lastName,
                'address' => $address,
            ]);

        if ($fullNameEntity === null) {
            $fullNameEntity = new \App\Entity\FullName();
            $fullNameEntity->setFirstName($firstName);
            $fullNameEntity->setMiddleName($middleName);
            $fullNameEntity->setLastName($lastName);
            $fullNameEntity->setAddress($address);

            $this->entityManager->persist($fullNameEntity);
            $this->entityManager->flush();

            return $fullNameEntity;
        }

        return $fullNameEntity;
    }
}
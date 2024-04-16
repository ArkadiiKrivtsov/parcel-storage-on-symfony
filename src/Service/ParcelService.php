<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\FullName;
use App\Entity\ParcelEntity;
use App\Entity\Recipient;
use App\Entity\Sender;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ParcelService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createParcel(ParcelEntity $parcel): JsonResponse
    {
        try {
            $this->entityManager->persist($parcel);
            $this->entityManager->flush();

            return new JsonResponse('Successfully saved new parcel with id ' . $parcel->getId());

        } catch (Exception $exception) {
            return new JsonResponse(['Error' => $exception->getMessage()], 500);
        }
    }

    public function getParcelBySenderPhone(string $phone): JsonResponse
    {
        if (empty($phone)) {
            return new JsonResponse(['Error' => 'Query param is empty'], 400);
        }

        try {
            $senderRepository = $this->entityManager->getRepository(Sender::class);
            $sender = $senderRepository->findOneBy(['phone' => $phone]);

            if ($sender == null) {
                return new JsonResponse(['Error' => 'Parcel not found.'], 400);
            }

            $senderId = $sender->getId();
            $parcelRepository = $this->entityManager->getRepository(ParcelEntity::class);
            $query = $parcelRepository
                ->createQueryBuilder('p')
                ->select('p', 's', 'sf', 'r', 'rf', 'd') // Выбираем поля ParcelEntity, Sender, Recipient, Dimensions и FullName
                ->join('p.sender', 's') // Связываем ParcelEntity с Sender
                ->join('s.fullName', 'sf') // Связываем Sender с FullName
                ->join('p.receiver', 'r') // Связываем ParcelEntity с Recipient
                ->join('r.fullName', 'rf') // Связываем Recipient с FullName
                ->join('p.dimensions', 'd') // Связываем ParcelEntity с Dimensions
                ->where('s.id = :senderId') // Фильтруем по Sender ID
                ->setParameter('senderId', $senderId)
                ->getQuery();
            $queryResult = $query->getArrayResult();
            return new JsonResponse([
                'Total parcels with sender phone ' . $phone => count($queryResult),
                'Parcels info' => $queryResult,
            ]);
        } catch (Exception $exception) {
            return new JsonResponse(['Error' => $exception->getMessage()], 500);
        }
    }

    public function getParcelByRecipientFullName(string $receiverName): JsonResponse
    {
        if (empty($receiverName)) {
            return new JsonResponse(['Error' => 'Query param is empty'], 400);
        }

        $fullNameArr = explode(' ', $receiverName);
        if (count($fullNameArr) !== 3) {
            return new JsonResponse(['Error' => 'Some part of name is missing'], 400);
        }

        $lastName = $fullNameArr[0];
        $firstName = $fullNameArr[1];
        $middleName = $fullNameArr[2];

        try {
            $fullNameRepository = $this->entityManager->getRepository(FullName::class);
            $fullName = $fullNameRepository->findOneBy([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'middleName' => $middleName,
            ]);

            $receiverRepository = $this->entityManager->getRepository(Recipient::class);
            $receiver = $receiverRepository->findOneBy(['fullName' => $fullName]);

            if ($receiver == null) {
                return new JsonResponse(['Error' => 'Recipient not found.'], 400);
            }
            $receiverId = $receiver->getId();

            $parcelRepository = $this->entityManager->getRepository(ParcelEntity::class);
            $query = $parcelRepository
                ->createQueryBuilder('p')
                ->select('p', 's', 'sf', 'r', 'rf', 'd') // Выбираем поля ParcelEntity, Sender, Recipient, Dimensions и FullName
                ->join('p.sender', 's') // Связываем ParcelEntity с Sender
                ->join('s.fullName', 'sf') // Связываем Sender с FullName
                ->join('p.receiver', 'r') // Связываем ParcelEntity с Recipient
                ->join('r.fullName', 'rf') // Связываем Recipient с FullName
                ->join('p.dimensions', 'd') // Связываем ParcelEntity с Dimensions
                ->where('r.id = :receiverId') // Фильтруем по Sender ID
                ->setParameter('receiverId', $receiverId)
                ->getQuery();

            $queryResult = $query->getArrayResult();
            return new JsonResponse([
                "Total parcels with receiver name: $receiverName" => count($queryResult),
                'Parcels info' => $queryResult,
            ]);
        } catch (Exception $exception) {
            return new JsonResponse(['Error' => $exception->getMessage()], 500);
        }
    }

    public function deleteParcel(int $parcelId): JsonResponse
    {
        $parcel = $this->entityManager->getRepository(ParcelEntity::class)->find($parcelId);

        if ($parcel) {
            try {
                $this->entityManager->remove($parcel);
                $this->entityManager->flush();
                return new JsonResponse( 'Parcel with id = ' . $parcelId . ' successfully deleted.');

            } catch (Exception $exception) {
                return new JsonResponse(['Error' => $exception->getMessage()], 500);
            }
        }

        return new JsonResponse(['Error' => 'Parcel not found'], 500);
    }
}

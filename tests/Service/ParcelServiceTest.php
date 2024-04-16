<?php

// tests/Service/ParcelServiceTest.php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\FullName;
use App\Entity\ParcelEntity;
use App\Entity\Recipient;
use App\Entity\Sender;
use App\Repository\FullNameRepository;
use App\Repository\ParcelEntityRepository;
use App\Repository\RecipientRepository;
use App\Repository\SenderRepository;
use App\Service\ParcelService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ParcelServiceTest extends TestCase
{
    public function testCreateParcel()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $parcel = $this->createMock(ParcelEntity::class);

        $entityManager->expects($this->once())->method('persist')->with($parcel);

        $entityManager->expects($this->once())->method('flush');

        $response = $parcelService->createParcel($parcel);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode('Successfully saved new parcel with id ' . $parcel->getId()), $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testCreateParcelWithError()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $parcel = $this->createMock(ParcelEntity::class);

        $entityManager->expects($this->once())->method('persist')->with($parcel);

        $entityManager->expects($this->once())
            ->method('flush')
            ->willThrowException(new Exception('Error message'));

        $response = $parcelService->createParcel($parcel);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Error message']), $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testDeleteParcel()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $parcel = new ParcelEntity();
        $parcelRepository = $this->createMock(ParcelEntityRepository::class);

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(ParcelEntity::class)
            ->willReturn($parcelRepository);

        $parcelRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($parcel);

        $entityManager->expects($this->once())->method('remove')
            ->with($parcel);
        $entityManager->expects($this->once())->method('flush');

        $response = $parcelService->deleteParcel(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('"Parcel with id = 1 successfully deleted."', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDeleteParcelWithError()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $parcel = new ParcelEntity();
        $parcelRepository = $this->createMock(ParcelEntityRepository::class);

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(ParcelEntity::class)
            ->willReturn($parcelRepository);

        $parcelRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($parcel);

        $entityManager->expects($this->once())->method('remove')
            ->with($parcel);
        $entityManager->expects($this->once())
            ->method('flush')
            ->willThrowException(new Exception('Error message'));

        $response = $parcelService->deleteParcel(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Error message']), $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testDeleteParcelIfParcelNull()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $parcelId = 1;

        $parcelRepository = $this->createMock(ParcelEntityRepository::class);

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(ParcelEntity::class)
            ->willReturn($parcelRepository);

        $parcelRepository->expects($this->once())
            ->method('find')
            ->with($parcelId)
            ->willReturn(null);

        $response = $parcelService->deleteParcel($parcelId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Parcel not found']), $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testGetParcelBySenderPhoneWithError()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);
        $phone = '123';

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Sender::class)
            ->willThrowException(new Exception('Error message'));

        $response = $parcelService->getParcelBySenderPhone($phone);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Error message']), $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testGetParcelByEmptySenderPhone()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $phone = '';
        $response = $parcelService->getParcelBySenderPhone($phone);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Query param is empty']), $response->getContent());
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testGetParcelByEmptySender()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);
        $phone = '123';

        $senderRepository = $this->createMock(SenderRepository::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Sender::class)
            ->willReturn($senderRepository);

        $sender = null;

        $senderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['phone' => $phone])
            ->willReturn($sender);

        $response = $parcelService->getParcelBySenderPhone($phone);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Parcel not found.']), $response->getContent());
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testGetParcelByValidSenderPhone()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);
        $phone = '123';

        $senderRepository = $this->createMock(SenderRepository::class);
        $parcelRepository = $this->createMock(ParcelEntityRepository::class);

        $entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnCallback(
                function ($arg) use ($senderRepository, $parcelRepository) {
                    if ($arg === Sender::class) {
                        return $senderRepository;
                    }
                    return $parcelRepository;
                });

        $sender = $this->createMock(Sender::class);

        $senderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['phone' => $phone])
            ->willReturn($sender);

        $senderId = 1;
        $sender->expects($this->once())
            ->method('getId')
            ->willReturn($senderId);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $parcelRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('p')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->once())
            ->method('select')
            ->with('p', 's', 'sf', 'r', 'rf', 'd')
            ->willReturnSelf();

        $queryBuilder->expects($this->exactly(5))
            ->method('join')
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('s.id = :senderId')
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('senderId', $senderId)
            ->willReturnSelf();

        $query = $this->createMock(AbstractQuery::class);
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $queryResult = ['parcel'];
        $query->expects($this->once())
            ->method('getArrayResult')
            ->willReturn($queryResult);

        $response = $parcelService->getParcelBySenderPhone($phone);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode([
            'Total parcels with sender phone ' . $phone => count($queryResult),
            'Parcels info' => $queryResult,
        ]), $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetParcelByRecipientFullNameWithError()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);
        $receiverName = 'firstName lastName middleName';

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(FullName::class)
            ->willThrowException(new Exception('Error message'));

        $response = $parcelService->getParcelByRecipientFullName($receiverName);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Error message']), $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testGetParcelByEmptyRecipientFullName()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $fullName = '';
        $response = $parcelService->getParcelByRecipientFullName($fullName);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Query param is empty']), $response->getContent());
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testGetParcelByNotFullNameRecipient()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);
        $fullName = 'firstname lastname';

        $response = $parcelService->getParcelByRecipientFullName($fullName);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['Error' => 'Some part of name is missing']), $response->getContent());
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testGetParcelByRecipientFullName()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $parcelService = new ParcelService($entityManager);

        $fullNameRepository = $this->createMock(FullNameRepository::class);
        $recipientRepository = $this->createMock(RecipientRepository::class);
        $parcelRepository = $this->createMock(ParcelEntityRepository::class);

        $entityManager->expects($this->exactly(3))
            ->method('getRepository')
            ->willReturnCallback(
                function ($arg) use ($fullNameRepository, $recipientRepository, $parcelRepository) {
                    if ($arg === FullName::class) {
                        return $fullNameRepository;
                    } elseif ($arg === Recipient::class) {
                        return $recipientRepository;
                    }
                    return $parcelRepository;
                });

        $receiverName = 'firstName lastName middleName';
        $fullNameArr = explode(' ', $receiverName);
        $lastName = $fullNameArr[0];
        $firstName = $fullNameArr[1];
        $middleName = $fullNameArr[2];

        $fullName = $this->createMock(FullName::class);
        $fullNameRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'middleName' => $middleName,
            ])
            ->willReturn($fullName);

        $receiver = $this->createMock(Recipient::class);
        $recipientRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['fullName' => $fullName])
            ->willReturn($receiver);

        $receiverId = 1;
        $receiver->expects($this->once())
            ->method('getId')
            ->willReturn($receiverId);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $parcelRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('p')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->once())
            ->method('select')
            ->with('p', 's', 'sf', 'r', 'rf', 'd')
            ->willReturnSelf();

        $queryBuilder->expects($this->exactly(5))
            ->method('join')
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('r.id = :receiverId')
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('receiverId', $receiverId)
            ->willReturnSelf();

        $query = $this->createMock(AbstractQuery::class);
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $queryResult = ['parcel'];
        $query->expects($this->once())
            ->method('getArrayResult')
            ->willReturn($queryResult);

        $response = $parcelService->getParcelByRecipientFullName($receiverName);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertSame(json_encode([
            "Total parcels with receiver name: $receiverName" => count($queryResult),
            'Parcels info' => $queryResult,
        ]), $response->getContent());

        $this->assertSame(200, $response->getStatusCode());
    }
}

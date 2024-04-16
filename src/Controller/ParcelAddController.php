<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Converter\DtoToEntityConverter;
use App\Dto\ParcelDto;
use App\Service\ParcelService;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class ParcelAddController extends AbstractController
{
    private ParcelService $parcelService;
    private DtoToEntityConverter $dtoConverter;

    public function __construct(ParcelService $parcelService, DtoToEntityConverter $dtoConverter)
    {
        $this->parcelService = $parcelService;
        $this->dtoConverter = $dtoConverter;
    }

    #[OA\Post(
        requestBody: new OA\RequestBody(
            description: 'Заполните данные тела запроса в соответствии с образцом',
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: ParcelDto::class, groups: ['full'])
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Возвращает сообщение об успехе с id посылки',
        content: new OA\JsonContent(
            type: "string",
            example: "Successfully saved new parcel with id <id>"
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Error: Bad Request',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: "Error", type: "string", example: "Выведет саму ошибку")],
            type: 'object'
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal Server Error',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: "Error", type: "string", example: "Выведет саму ошибку")],
            type: 'object'
        )
    )]
    #[OA\Tag(name: 'Parcel')]
    #[Route('/api/parcel', name: 'api_parcel_add', methods: 'POST')]

    public function __invoke(Request $request): JsonResponse
    {
        $requestData = $request->getContent();
        if ($requestData) {
            $encoders = [new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);

            try {
                $newParcelDto = $serializer->deserialize($requestData, ParcelDto::class, 'json');

                $newParcelEntity = $this->dtoConverter->convertToEntity($newParcelDto);

                return $this->parcelService->createParcel($newParcelEntity);

            } catch (Exception $exception) {
                return $this->json(['Error' => $exception->getMessage()], 400);
            }
        }

        return $this->json(['Error' => 'Empty request'], 400);
    }
}

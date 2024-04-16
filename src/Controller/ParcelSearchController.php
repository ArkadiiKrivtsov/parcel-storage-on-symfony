<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ParcelDto;
use App\Service\ParcelService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParcelSearchController extends AbstractController
{
    private ParcelService $parcelService;

    public function __construct(ParcelService $parcelService)
    {
        $this->parcelService = $parcelService;
    }

    #[OA\Response(
        response: 200,
        description: 'Возвращает общее количество найденных посылок и данные по этим посылкам',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: "Total parcels with receiver name: <q>", example: "Количество"), new OA\Property(property: "Parcels info", ref: new Model(type: ParcelDto::class, groups: ['full']))],
            type: 'object'
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
    #[OA\Parameter(
        name: 'searchType',
        description: 'Поле используется для определения типа поиска. Допустимые значения sender_phone и receiver_fullname',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'q',
        description: 'Поле используется для поиска по заданному значению',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'Parcel')]
    #[Route('/api/parcel', name: 'app_parcel_search', methods: 'GET')]
    public function __invoke(Request $request): JsonResponse
    {
        $searchType = $request->query->get('searchType');
        $q = $request->query->get('q');

        if ($searchType && $q) {
            return match ($searchType) {
                'sender_phone' =>
                    $this->parcelService->getParcelBySenderPhone($q),

                'receiver_fullname' =>
                    $this->parcelService->getParcelByRecipientFullName($q),

                default => $this->json(['Error' => 'Not a valid searchType'], 400),
            };
        }

        return $this->json(['Error' => 'searchType or q is missing'], 400);
    }
}

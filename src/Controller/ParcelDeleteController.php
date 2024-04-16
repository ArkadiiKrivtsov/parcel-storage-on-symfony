<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ParcelService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ParcelDeleteController extends AbstractController
{
    private ParcelService $parcelService;

    public function __construct(ParcelService $parcelService)
    {
        $this->parcelService = $parcelService;
    }

    #[OA\Response(
        response: 200,
        description: 'Возвращает сообщение об успехе с id посылки',
        content: new OA\JsonContent(
            type: "string",
            example: "Parcel with id = <id> successfully deleted."
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
        name: 'id',
        description: 'Передайте id посылки для удаления',
        in: 'path',
        required: true
    )]
    #[OA\Tag(name: 'Parcel')]
    #[Route('/api/parcel/{id}', name: 'app_parcel_delete', methods: 'DELETE')]
    public function __invoke($id = null): JsonResponse
    {
        if (!is_numeric($id)) {
            return $this->json(['Error' => 'Resource ID is missing'], 400);
        }

        return $this->parcelService->deleteParcel((int) $id);
    }

    #[OA\Response(
        response: 400,
        description: 'Маршрут предназначен для вывода ошибки, если не передать {id}',
        content: new OA\JsonContent(
            properties: [new OA\Property(property: "Error", type: "string", example: "Resource ID is missing")],
            type: 'object'
        )
    )]
    #[OA\Tag(name: 'Parcel')]
    #[Route('/api/parcel/all', name: 'app_parcel_delete_no_id', methods: 'DELETE')]
    public function showError(): JsonResponse
    {
        return $this->json(['Error' => 'Resource ID is missing'], 400);
    }
}

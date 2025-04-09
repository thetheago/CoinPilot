<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ValidationErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Factories\TransferInputFactory;
use App\Usecases\TransactUseCase;
use App\Repositories\UserRepository;
use App\Http\Responses\UnknownErrorResponse;
use App\Http\Responses\DomainErrorResponse;
use App\Services\AuthorizeService;
use App\Services\LogTransferService;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Bank API",
    description: "API para transferências bancárias"
)]
class TransferController extends Controller
{
    #[OA\Post(
        path: "/api/transfer",
        summary: "Realiza uma transferência entre usuários",
        tags: ["Transferências"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["payer", "payee", "value"],
            properties: [
                new OA\Property(
                    property: "payer",
                    type: "integer",
                    example: 1,
                    description: "ID do usuário pagador"
                ),
                new OA\Property(
                    property: "payee",
                    type: "integer",
                    example: 2,
                    description: "ID do usuário a receber a transferência"
                ),
                new OA\Property(
                    property: "value",
                    type: "number",
                    format: "float",
                    example: 100.50,
                    description: "Valor da transferência"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Transferência realizada com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "data",
                    type: "array",
                    items: new OA\Items(
                        type: "string",
                        example: "Transferência realizada com sucesso"
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Erro de validação",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "O campo payer é obrigatório."
                )
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: "Transação não autorizada.",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Transação não autorizada."
                )
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Erro interno.",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "message",
                    type: "string",
                    example: "Um erro inesperado ocorreu, tente novamente mais tarde."
                )
            ]
        )
    )]
    public function transfer(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();

            $validator = Validator::make($payload, [
                'payer' => 'required|integer',
                'payee' => 'required|integer',
                'value' => 'required|numeric|min:0.01|decimal:0,2'
            ], [
                'payer.required' => 'O campo payer é obrigatório.',
                'payer.integer' => 'O ID do payer deve ser um número inteiro.',
                'payee.required' => 'O campo payee é obrigatório.',
                'payee.integer' => 'O ID do payee deve ser um número inteiro.',
                'value.required' => 'O campo value é obrigatório.',
                'value.numeric' => 'O campo value deve ser um número.',
                'value.min' => 'O campo value deve ser maior que zero.',
                'value.decimal' => 'O campo value deve ser um número com duas casas decimais.'
            ]);

            if ($validator->fails()) {
                return ValidationErrorResponse::make(validator: $validator);
            }

            $input = TransferInputFactory::createFromRequest(request: $request);

            $useCase = new TransactUseCase(
                userRepository: new UserRepository(),
                authorizeService: new AuthorizeService()
            );
            $useCase->execute(input: $input);

            return SuccessResponse::make(data: ['Transferência realizada com sucesso']);

            // Uma saída mais elegante seria utilizar o event listener do laravel para capturar as exceptions.
        } catch (\DomainException $e) {
            return DomainErrorResponse::make(message: $e->getMessage());
        } catch (\Exception $e) {
            LogTransferService::error($e->getMessage(), ['context' => $e->getTraceAsString()]);
            return UnknownErrorResponse::make(message: "Um erro inesperado ocorreu, tente novamente mais tarde.");
        }
    }
}

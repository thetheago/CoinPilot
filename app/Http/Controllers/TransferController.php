<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ValidationErrorResponse;
use App\Http\Responses\SuccessResponse;

class TransferController extends Controller
{
    public function transfer(Request $request): JsonResponse
    {
        $payload = $request->all();

        $validator = Validator::make($payload, [
            'payer' => 'required|integer|exists:users,id',
            'payee' => 'required|integer|exists:users,id',
            'value' => 'required|numeric|min:0.01|decimal:0,2'
        ], [
            'payer.required' => 'O campo payer é obrigatório.',
            'payer.integer' => 'O ID do payer deve ser um número inteiro.',
            'payer.exists' => 'O payer informado não existe.',
            'payee.required' => 'O campo payee é obrigatório.',
            'payee.integer' => 'O ID do payee deve ser um número inteiro.',
            'payee.exists' => 'O payee informado não existe.',
            'value.required' => 'O campo value é obrigatório.',
            'value.numeric' => 'O campo value deve ser um número.',
            'value.min' => 'O campo value deve ser maior que zero.',
            'value.decimal' => 'O campo value deve ser um número com duas casas decimais.'
        ]);

        if ($validator->fails()) {
            return ValidationErrorResponse::make(validator: $validator);
        }

        // Outra saída seria um throw de alguma exception de negócio ou erro mesmo.
        // Pretendo definir isso em event listener do laravel.
        // (Ou colocar um try catch aqui para não utilizar metodos magicos do framework)
        // $this->transferUseCase->execute($dto);

        return SuccessResponse::make(data: ['deu certo' => true]);
    }
}

<?php

declare(strict_types=1);

namespace App\Factories;

use App\Interface\IInputDTOFactory;
use Illuminate\Http\Request;
use App\Dto\TransferInput;

class TransferInputFactory implements IInputDTOFactory
{
    public static function createFromRequest(Request $request): TransferInput
    {
        $payer = $request->input('payer');
        $payee = $request->input('payee');
        $value = $request->input('value') * 100;

        return new TransferInput(
            payer: $payer,
            payee: $payee,
            value: $value
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Interface;

use Illuminate\Http\Request;

interface IInputDTOFactory
{
    public static function createFromRequest(Request $request);
}

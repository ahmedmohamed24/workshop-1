<?php

namespace App\Traits;

trait JsonResponse
{
    public function response(int $code, string $message, string $data = null)
    {
        return \response()->json(['message' => $message, 'data' => $data ?? []], $code);
    }
}

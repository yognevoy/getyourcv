<?php

namespace App\Http\Controllers;

use App\Http\Requests\RewriteTextRequest;
use App\Services\Ai\Exceptions\AiServiceException;
use App\Services\Ai\Rewrite\RewriteServiceInterface;
use App\Services\Ai\Rewrite\RewriteTarget;
use Illuminate\Http\JsonResponse;

class AiRewriteController extends Controller
{
    public function __invoke(RewriteTextRequest $request, RewriteServiceInterface $ai): JsonResponse
    {
        try {
            $variants = $ai->rewrite(
                $request->validated('text'),
                RewriteTarget::fromValue($request->validated('target')),
            );
        } catch (AiServiceException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        return response()->json(['variants' => $variants]);
    }
}

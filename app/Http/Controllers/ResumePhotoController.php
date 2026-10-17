<?php

namespace App\Http\Controllers;

use App\Actions\Resume\UploadResumePhoto;
use App\Http\Requests\StoreResumePhotoRequest;
use Illuminate\Http\JsonResponse;

class ResumePhotoController extends Controller
{
    public function store(StoreResumePhotoRequest $request, UploadResumePhoto $action): JsonResponse
    {
        $path = $action->execute($request->file('photo'));

        return response()->json(['photo_path' => $path]);
    }
}

<?php

namespace App\Http\Controllers\Preferences;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Repositories\PreferenceRepository;
use App\Http\Requests\PreferenceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PreferenceController extends Controller
{

    /**
     * Authenticated User Instance.
     *
     * @var User
     */
    public ?User $user;

    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * Preference Repository class.
     *
     * @var preferenceRepository
     */
    public $preferenceRepository;

    public function __construct(PreferenceRepository $preferenceRepository)
    {
        $this->middleware('auth:api');
        $this->preferenceRepository = $preferenceRepository;
    }

    public function add(PreferenceRequest $request): JsonResponse
    {
        $value = $request->only(['categories', 'sources', 'authors']);
        $result = $this->preferenceRepository->create($value);
        return $this->responseSuccess($result, 'Preferences created Successfully !');
    }

    public function retrieveAll(): JsonResponse
    {
        try {
            $categories = $this->preferenceRepository->getAll('categories');
            $sources = $this->preferenceRepository->getAll('sources');
            $authors = $this->preferenceRepository->getAll('authors');

            $data = [
                'categories' => $categories,
                'sources' => $sources,
                'authors' => $authors,
            ];

            return $this->responseSuccess($data, 'Preference list fetched successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

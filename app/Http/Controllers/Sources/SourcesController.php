<?php

namespace App\Http\Controllers\Sources;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Repositories\ReferencesRepository;
use App\Model\Sources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SourcesController extends Controller
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
     * Reference Repository class.
     *
     * @var referencesRepository
     */
    public $referencesRepository;

    public function __construct(ReferencesRepository $referencesRepository)
    {
        $this->middleware('auth:api');
        $this->referencesRepository = $referencesRepository;
    }

    public function retrieveAll(): JsonResponse
    {
        try {
            $data = $this->referencesRepository->getAll('Sources');
            return $this->responseSuccess($data, 'Sources List Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

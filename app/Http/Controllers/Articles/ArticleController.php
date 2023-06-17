<?php

namespace App\Http\Controllers\Articles;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Interfaces\ArticleServiceInterface;

class ArticleController extends Controller
{

    private $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->middleware('auth:api');
        $this->articleService = $articleService;

    }


    public function index(Request $request)
    {
        $search = $request->query('search');
        $date = $request->query('date');
        $category = $request->query('category');
        $source = $request->query('source');

        $page = $request->query('page');
        // retrieve articles from service
        $articles = $this->articleService->getArticles($search, $date, $source, $page);
        return response()->json([
            'data' => $articles
        ]);
    }
}

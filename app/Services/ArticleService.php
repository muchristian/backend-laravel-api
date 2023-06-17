<?php

namespace App\Services;

use App\Models\Authors;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Repositories\ReferencesRepository;
use App\Interfaces\ArticleServiceInterface;
use Illuminate\Support\Facades\Auth;

class ArticleService implements ArticleServiceInterface {

    public ?User $user;
    private $newsApiUrl;
    private $nyTimesUrl;
    private $guardianUrl;

    public function __construct(ReferencesRepository $referencesRepository)
    {
        $this->newsApiUrl = "https://newsapi.org/v2/everything";
        $this->nyTimesUrl = "https://api.nytimes.com/svc/search/v2/articlesearch.json";
        $this->guardianUrl = "http://content.guardianapis.com/search";
        $this->referencesRepository = $referencesRepository;
        $this->user = Auth::guard()->user();
    }

    public function getArticles($search, $date, $source, $page)
    {
        $dbArticles = Article::when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        })->when($date, function ($query) use ($date) {
            return $query->whereDate('publishedAt', $date);
        })
        ->when($source, function ($query) use ($source) {
            return $query->where('sourceName', $source);
        })->orderBy('publishedAt', 'DESC')->paginate(30, ['*'], 'page', $page);
        
        if ($dbArticles->count() > 0) {
            return $dbArticles;
        }
        $articlesResult = $this->getArticlesCronJob($search, $date, $source, $page);
        shuffle($articlesResult);
        $perPage = count($articlesResult) > 0 ? count($articlesResult) : 1;
        $currentPage = $page;
        $paginator = new LengthAwarePaginator(
            $articlesResult,
            count($articlesResult),
            $perPage,
            $currentPage
        );
        return $paginator;
    }


    public function getArticlesCronJob($search = null, $date = null, $source=null, $page = 1)
{
    $articles = [];
    $newsApiArticles = $this->getNewsApi($search, $date, $source, $page);
    $guardianApiArticles = $this->getGuardianApi($search, $date, $source, $page);
    $nyTimesApiArticles = $this->getNyTimesApi($search, $date, $source, $page);
    $articles = array_merge($articles, $newsApiArticles, $guardianApiArticles, $nyTimesApiArticles);
    $articlesTransformation = $this->transformResult($articles);
    $articlesResult = $this->saveArticles($articlesTransformation);
    return $articlesResult;
}


    private function getNewsApi($search=null, $date, $source, $page=1)
    {
        if ($source && $source !== 'NewsAPI') {
            return [];
        }
        $apiResponse = Http::get($this->newsApiUrl, [
            'q' => $search,
            'from' => $date,
            'to' => $date,
            'page' => $page,
            'pageSize' => 10,
            'sortBy' => 'publishedAt',
            'apiKey' => env('NEWSAPI_KEY')
        ]);
        $apiData = $apiResponse->json();
        return isset($apiData['articles']) ? $apiData['articles'] : [];
    }

    private function getGuardianApi($search, $date, $source, $page)
    {
        if ($source && $source !== 'TheGuardian') {
            return [];
        }
        $apiResponse = Http::get($this->guardianUrl, [
            'q' => $search,
            'from-date' => $date,
            'to-date' => $date,
            'page' => $page,
            'page-size' => 10,
            'show-fields'=> 'thumbnail',
            'show-tags'=>'contributor',
            'show-fields'=>'thumbnail,trailText',
            'order-by' => 'newest',
            'api-key' => env('GUARDIAN_API_KEY')
        ]);
        
        $apiData = $apiResponse->json();
        return $apiData['response']['results'];
    }

    private function getNyTimesApi($search, $date, $source, $page)
    {
        if ($source && $source !== 'TheNewYorkTimes') {
            return [];
        }
        $apiResponse = Http::get($this->nyTimesUrl, [
            'q' => $search,
            'begin_date' => $date,
            'end_date' => $date,
            'page' => $page,
            'show-fields'=> 'thumbnail',
            'show-tags'=>'contributor',
            'show-fields'=>'body',
            'sort' => 'newest',
            'api-key' => env('NYTIMES_KEY')
        ]);
        
        $apiData = $apiResponse->json();
        return $apiData['response']['docs'];
    }

    private function transformResult(array $articles)
    {
        $transformedObjects = [];

        foreach ($articles as $obj) {
            $transformedObj = [];

    if (isset($obj['source'])) {
        $transformedObj['sourceName'] = $obj['source'];
    } else {
        $transformedObj['sourceName'] = 'The Guardian';
    }

    if (isset($obj['publishedAt'])) {
        $transformedObj['publishedAt'] = $obj['publishedAt'];
    } elseif (isset($obj['pub_date'])) {
        $transformedObj['publishedAt'] = $obj['pub_date'];
    } elseif (isset($obj['webPublicationDate'])) {
        $transformedObj['publishedAt'] = $obj['webPublicationDate'];
    }

    if (isset($obj['title'])) {
        $transformedObj['title'] = $obj['title'];
    } elseif (isset($obj['webTitle'])) {
        $transformedObj['title'] = $obj['webTitle'];
    } elseif (isset($obj['headline']['main'])) {
        $transformedObj['title'] = $obj['headline']['main'];
    }

    if (isset($obj['description'])) {
        $transformedObj['description'] = $obj['description'];
    } elseif (isset($obj['lead_paragraph'])) {
        $transformedObj['description'] = $obj['lead_paragraph'];
    } elseif (isset($obj['fields']['trailText'])) {
        $transformedObj['description'] = $obj['fields']['trailText'];
    }

    if (isset($obj['url'])) {
        $transformedObj['url'] = $obj['url'];
    } elseif (isset($obj['web_url'])) {
        $transformedObj['url'] = $obj['web_url'];
    } elseif (isset($obj['webUrl'])) {
        $transformedObj['url'] = $obj['webUrl'];
    }

    if (isset($obj['urlToImage'])) {
        $transformedObj['thumbnail'] = $obj['urlToImage'];
    } elseif (isset($obj['multimedia']) && count($obj['multimedia']) > 0) {
        $transformedObj['thumbnail'] = 'https://www.nytimes.com/'. $obj['multimedia'][0]['url'];
    } elseif (isset($obj['fields']['thumbnail'])) {
        $transformedObj['thumbnail'] = $obj['fields']['thumbnail'];
    }

    if (isset($obj['author'])) {
        $transformedObj['author'] = $obj['author'];
    } elseif (isset($obj['byline']['person'][0])) {
        $transformedObj['author'] = $obj['byline']['person'][0]['firstname'] . ' ' . $obj['byline']['person'][0]['lastname'];
    } elseif (isset($obj['tags'][0])) {
        $transformedObj['author'] = $obj['tags'][0]['firstName'] . " " . $obj['tags'][0]['lastName'];
    }

    $transformedObjects[] = $transformedObj;
    }

    return $transformedObjects;
    }

    private function saveArticles(array $articles)
    {
        $savedArticles = [];
        foreach ($articles as $articleData) {

        $title = str_replace(' ', '', strtolower($articleData['title']));
        
        $existingArticle = Article::whereRaw('LOWER(REPLACE(title, " ", "")) = ?', [$title])->first();

        if ($existingArticle) {
            continue;
        }

            $article = new Article();
            $article->sourceName = is_array($articleData['sourceName']) ? "NewsAPI" : $articleData['sourceName'];
            $article->publishedAt = $articleData['publishedAt'];
            $article->title = $articleData['title'];
            $article->description = $articleData['description'];
            $article->url = $articleData['url'];
            $article->thumbnail = isset($articleData['thumbnail']) ? $articleData['thumbnail'] : null;
            $article->author = isset($articleData['author']) ? $articleData['author'] : null;
            
            // Save the article to the database
            $article->save();
            if (isset($article->author)) {
            $author = Authors::where('name', $article->author)->first();

                if (!isset($author)) {
                    $this->referencesRepository->create(Authors::class, ['name' => $article->author]);
                }
            }
            $savedArticles[] = $article;
        }
        return $savedArticles;
    }
}


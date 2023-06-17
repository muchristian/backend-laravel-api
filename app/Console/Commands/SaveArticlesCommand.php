<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\ReferencesRepository;
use App\Interfaces\ArticleServiceInterface;
use Illuminate\Support\Facades\Log; 

class SaveArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save articles to the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $referencesRepository;
    private $articleService;

    public function __construct(ReferencesRepository $referencesRepository, ArticleServiceInterface $articleService)
    {
        parent::__construct();
        $this->referencesRepository = $referencesRepository;
        $this->articleService = $articleService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Cron Job Started');
        $articles = $this->articleService->getArticlesCronJob();
        return 0;
        Log::info('Cron Job Ended');
    }
}

<?php

namespace App\Interfaces;

interface ArticleServiceInterface
{
    public function getArticles($search, $date, $source, $page);
}
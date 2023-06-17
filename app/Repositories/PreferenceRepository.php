<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use App\Helpers\UploadHelper;
use App\Interfaces\RepositoryInterface;
use App\Models\PreferenceCategories;
use App\Models\PreferenceAuthors;
use App\Models\PreferenceSources;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class PreferenceRepository implements RepositoryInterface
{
    /**
     * Authenticated User Instance.
     *
     * @var User
     */
    public ?User $user;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->user = Auth::guard()->user();
    }

    public function create(array $value)
    {
        $result = [];
    if ($this->validateInput($value['categories'])) {
            $result['categories'] = $this->createPreferences('category', $value['categories'], PreferenceCategories::class);
    }
    if ($this->validateInput($value['sources'])) {
            $result['sources'] = $this->createPreferences('source', $value['sources'], PreferenceSources::class);
    }
    if ($this->validateInput($value['authors'])) {
        $result['authors'] = $this->createPreferences('author', $value['authors'], PreferenceAuthors::class);
    }
    return $result;
    }
    /**
     * Get All Preferences.
     *
     * @return collections Array of Preference Collection
     */
    public function getAll(string $preference): Collection
    {
        switch ($preference) {
            case 'categories':
                return $this->user->preferenceCategory()->get();
            case 'sources':
                return $this->user->preferenceSource()->get();
            case 'authors':
                return $this->user->PreferenceAuthor()->get();
            default:
                return collect();
        }
    }

    private function validateInput(array $field) {
        return isset($field) && count($field) > 0;
    }

    private function createPreferences(string $field, array $items, string $modelClass)
    {
    $results = [];
    foreach ($items as $item) {
        $preference = $modelClass::where($field, $item)->first();
        if (isset($preference)) {
            if ($preference->is_deleted === 0) {
                $preference->is_deleted = true;
                $preference->save();
                continue;
            }
            $preference->is_deleted = false;
            $results[] = $preference;
            $preference->save();
            continue;
        }
        $model = new $modelClass();
        $model->user_id = $this->user->id;
        $model->$field = $item;
        $model->save();
        $results[] = $model;
    }
    return $results;
    }
}
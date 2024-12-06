<?php

namespace App\Actions;

use Illuminate\Support\Facades\Storage;

class ProductDataService
{
    protected $filePath;

    public function __construct($filePath = 'data.json')
    {
        $this->filePath = $filePath;
    }

    /**
     * Retrieve and return data from the JSON file.
     *
     * @return array
     */
    public function getData(): array
    {
        if (!Storage::exists($this->filePath)) {
            return [];
        }

        $content = Storage::get($this->filePath);
        $json = json_decode($content, true);
        if (!is_array($json)) {
            $json = [];
        }

        // Sort by datetime_submitted descending
        usort($json, function ($a, $b) {
            return strtotime($b['datetime_submitted']) - strtotime($a['datetime_submitted']);
        });

        return $json;
    }

    /**
     * Save the given data array back to the JSON file.
     *
     * @param array $data
     * @return void
     */
    public function saveData(array $data): void
    {
        Storage::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Generate a new ID based on existing data.
     *
     * @param array $data
     * @return int
     */
    public function generateId(array $data): int
    {
        return count($data) > 0 ? max(array_column($data, 'id')) + 1 : 1;
    }
}

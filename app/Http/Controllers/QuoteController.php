<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class QuoteController extends Controller
{
    public function random()
    {
        $quotesBatch = Cache::remember('zen_quotes_batch', 86400, function () {
            try {
                $response = Http::timeout(5)->get('https://zenquotes.io/api/quotes');

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json) && is_array($json)) {
                        return $json;
                    }
                }
            } catch (\Exception $e) {
                // Fallback
            }

            return [];
        });

        if (!empty($quotesBatch) && is_array($quotesBatch)) {
            $pick = $quotesBatch[array_rand($quotesBatch)];
            return response()->json([
                'text' => $pick['q'] ?? '',
                'author' => $pick['a'] ?? 'Unknown',
            ]);
        }

        return response()->json($this->fallbackQuote());
    }

    private function fallbackQuote(): array
    {
        $quotes = [
            ['text' => 'The secret of getting ahead is getting started.', 'author' => 'Mark Twain'],
            ['text' => 'Small steps every day lead to big changes.', 'author' => 'Unknown'],
            ['text' => 'Focus on being productive instead of busy.', 'author' => 'Tim Ferriss'],
            ['text' => 'It does not matter how slowly you go as long as you do not stop.', 'author' => 'Confucius'],
            ['text' => 'The only way to do great work is to love what you do.', 'author' => 'Steve Jobs'],
        ];

        return $quotes[array_rand($quotes)];
    }
}

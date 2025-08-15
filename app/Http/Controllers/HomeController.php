<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function askAI(Request $request)
{
    $request->validate([
        'chat' => 'required|array',
    ]);

    $apiKey = env('OPENAI_API_KEY');

    $contents = [];

    foreach ($request->chat as $message) {
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $message['question']]
            ]
        ];
        if (!empty($message['answer'])) {
            $contents[] = [
                'role' => 'model',
                'parts' => [
                    ['text' => $message['answer']]
                ]
            ];
        }
    }

    $body = [
        'contents' => $contents
    ];

    $response = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Content-Type' => 'application/json',
        'X-goog-api-key' => $apiKey,
    ])->post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
        $body
    );

    if ($response->failed()) {
        return response()->json([
            'error' => 'Something went wrong',
            'details' => $response->json()
        ], 500);
    }

    $data = $response->json();
    $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer received.';

    return response()->json(['answer' => $answer]);
}

    

}

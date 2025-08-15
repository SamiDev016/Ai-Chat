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

//     public function askAI(Request $request)
// {
//     $request->validate([
//         'question' => 'required',
//     ]);

//     $apiKey = env('OPENAI_API_KEY'); // Your Gemini API key

//     $body = [
//         'contents' => [
//             [
//                 'parts' => [
//                     [
//                         'text' => $request->question
//                     ]
//                 ]
//             ]
//         ]
//     ];

//     $response = Http::withOptions([
//         'verify' => false, // Disable SSL verification
//     ])
//     ->withHeaders([
//         'Content-Type' => 'application/json',
//         'X-goog-api-key' => $apiKey,
//     ])->post(
//         'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
//         $body
//     );

//     if ($response->failed()) {
//         return back()->with('error', 'Something went wrong with AI request.');
//     }

//     $data = $response->json();
//     $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer received.';

//     return back()->with('answer', $answer)->withInput();
// }


public function askAI(Request $request)
{
    $request->validate([
        'question' => 'required',
    ]);

    $apiKey = env('OPENAI_API_KEY');

    $body = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $request->question]
                ]
            ]
        ]
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
        return response()->json(['error' => 'Something went wrong'], 500);
    }

    $data = $response->json();
    $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer received.';

    return response()->json(['answer' => $answer]);
}

}

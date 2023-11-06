<?php

namespace App\Http\Controllers;

use App\Models\Response;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function store(Request $request)
    {
        // Validate and store the response data in the database
        // You can access the data using $request->input('questionId') and $request->input('answer')

        // Example storage logic:
        Response::updateOrCreate([
            'question_id' => $request->input('questionId'),
            'session_id' => $request->input('sessionId'),
        ],[
            'question_id' => $request->input('questionId'),
            'session_id' => $request->input('sessionId'),
            'response_text' => $request->input('answer'),
            'survey_id' => $request->input('surveyId'),
        ]);

        return response()->json(['message' => 'Response stored successfully']);
    }
}

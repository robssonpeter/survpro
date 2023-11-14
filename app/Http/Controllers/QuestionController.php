<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function index()
    {
        return Inertia::render('Questions/index', [
            'questions' => Question::all(), // Fetch your questions from the database
        ]);
    }

    public function store(Request $request)
    {
        //return response()->json(['question' => request()->all()]);
        // Validate the request data
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string'
            // Add validation rules for other fields as needed
        ]);

        // Check if 'question_id' is provided in the request
        $question_id = $request->question_id;
        $data = $request->all();
        $data['options'] = json_encode($data['options']);
        if ($request->has('question_id') && $question_id) {
            // Update an existing question
            $question = Question::findOrFail($request->input('question_id'));
            $question->update($data);
        } else {
            // Create a new question
            $question = Question::create($data);
        }

        // Return the updated or created question
        return response()->json(['question' => $question]);
    }

    public function deleteQuestion(Request $request, $id)
    {
        // Find the question by ID
        $question = Question::find($id);

        // Check if the question exists
        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        try {
            // Delete the question
            $question->delete();

            // You might want to update the survey or perform other actions here

            return response()->json(['message' => 'Question deleted successfully']);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during deletion
            return response()->json(['error' => 'Failed to delete question'], 500);
        }
    }
}

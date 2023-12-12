<?php

namespace App\Http\Controllers;

use App\Events\BrodcastSurveyResponses;
use App\Http\Requests\SurveyRequest;
use App\Mail\SurveyEmail;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\SurveySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class SurveysController extends Controller
{
    public function index()
    {
        return Inertia::render('Surveys/index', [
            'surveys' => Survey::orderBy('survey_id', 'desc')->get(), // Fetch your questions from the database
        ]);
    }


    public function create()
    {
        // Logic to show the create survey form
        $question_types = Question::$types;

        return Inertia::render('Surveys/new', compact('question_types'));
    }

    public function modify($slug)
    {
        $survey = Survey::where('hashslug', $slug)->first();
        $questions = Question::where('survey_id', $survey->survey_id)->get();
        $settings = SurveySetting::where('survey_id', $survey->survey_id)->pluck('value', 'key');
        $question_types = Question::$types;
        //dd($questions);
        return Inertia::render('Surveys/new', compact('question_types', 'questions', 'survey', 'settings'));
    }

    public function store(SurveyRequest $request)
    {
        // Validate the request data using the SurveyRequest class
        $validatedData = $request->validated();
        $hashslug = $request->input('hashslug');

        try {
            if(!$request->hashslug){
                // Create a new survey instance and set its attributes
                $survey = new Survey();
                $survey->title = $validatedData['title'];
                $survey->description = $validatedData['description']??'-';
                $survey->due_date = $validatedData['due_date'];
                $survey->status = $request->input('status');
                $survey->hashslug = uniqid();
                $survey->user_id = Auth::user()->user_id;

                // Save the survey in the database
                $survey->save();
                $message = 'Survey created successfully';

                // You can also associate questions with the survey if needed
            }else{
                $validatedData['status'] = $request->input('status');
                $survey = Survey::where('hashslug', $hashslug)->firstOrFail();
                $survey->update($validatedData);
                $message = 'Survey updated successfully';
            }


            // Return a success response
            return response()->json(['message' => $message, 'stored' => $survey], 201);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the save process
            return response()->json(['message' => 'Survey creation failed'], 500);
        }
    }

    public function participate($slug){
        $survey = Survey::where('hashslug', $slug)->with('questions')->first();
        if(request()->pid){
            $participantId = request()->pid;
        }else{
            $participantId = session()->get('participant_id');
        }



        if (!$participantId) {
            // Generate a unique participant ID
            $participantId = uniqid();
            // Create a new cookie with the participant ID
            session()->put('participant_id', $participantId);
        }
        $responses = Response::where('session_id', $participantId)->where('survey_id', $survey->survey_id)->get();
        //$questions = Question::where('survey_id', $survey->survey_id)->get();
        //dd($survey);
        return Inertia::render('Surveys/participate', compact('survey', 'participantId', 'responses'));
    }

    public function submitSurvey(Request $request) {
        $session_id = $request->input('session_id');
        $survey_id = $request->input('survey_id');

        // Update the responses for the given session and survey to mark them as submitted
        Response::where('session_id', $session_id)
            ->where('survey_id', $survey_id)
            ->update(['is_submitted' => 1]);

        return response()->json(['message' => 'Survey submitted successfully'], 200);
    }

    public function sendSurvey(Request $request)
    {
        $emails = $request->input('emails');
        $subject = $request->input('subject');
        $message = $request->input('message');
        $survey_hash = $request->input('survey_hash');

        // Replace [LINK] with the actual survey link
        $surveyLink = route('survey.participate', $survey_hash).'?pid=RECIPIENT_EMAIL';

        foreach ($emails as $email) {
            $hashed = hash('sha256', $email);
            $surveyLink = str_replace('RECIPIENT_EMAIL', $hashed, $surveyLink);
            $surveyLinkWithRecipient = "<a href='".$surveyLink."'>".$surveyLink."</a>";

            // Send the email using the Mailable class
            Mail::to($email)->send(new SurveyEmail($subject, str_replace('[LINK]', $surveyLinkWithRecipient, $message)));
        }

        return response()->json(['message' => 'Survey emails sent successfully']);
    }

    public function show($id)
    {
        // Logic to display the details of a specific survey
    }

    public function edit($id)
    {
        // Logic to show the edit survey form
    }

    public function update(Request $request, $id)
    {
        // Logic to update a specific survey in the database
    }

    public function destroy($id)
    {
        // Logic to delete a specific survey from the database
    }

    public function delete($survey_id)
    {
        $survey = Survey::find($survey_id);
        // Delete the survey from the database
        $survey->delete();

        // You can add additional logic here, such as sending a response or a confirmation message

        return back(); // Redirect back to the surveys list page
    }

    public function responses($survey_id){
        broadcast(new BrodcastSurveyResponses("broadcasting "));
        $questions = Question::where('survey_id', $survey_id)->pluck('question_id');
        return Response::whereIn('question_id', $questions)->get();
    }
}

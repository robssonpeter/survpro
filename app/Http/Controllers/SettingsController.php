<?php

namespace App\Http\Controllers;

use App\Models\SurveySetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function saveSetting(Request $request)
    {
        $request->validate([
            'survey_id' => 'required|exists:surveys,survey_id',
            'key' => 'required|string',
            'value' => 'required',
        ]);
        // Check if a setting already exists for the given survey and key
        $setting = SurveySetting::where('survey_id', $request->input('survey_id'))
            ->where('key', $request->input('key'))
            ->first();

        if ($setting) {
            // Update the existing setting
            $setting->update(['value' => $request->input('value')]);
        } else {
            // Create a new setting
            SurveySetting::create([
                'survey_id' => $request->input('survey_id'),
                'key' => $request->input('key'),
                'value' => $request->input('value'),
            ]);
        }

        return response()->json(['message' => 'Setting saved successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Forecast;
use Illuminate\Http\JsonResponse;

class CashflowController extends Controller
{
    public function index(): JsonResponse
    {
        $latestForecast = Forecast::latest('forecast_date')
            ->first();
            
        if (!$latestForecast) {
            return response()->json([
                'message' => 'No forecast data available'
            ], 404);
        }
        
        return response()->json([
            'forecast_date' => $latestForecast->forecast_date,
            'predicted_inflow' => $latestForecast->predicted_inflow,
            'predicted_outflow' => $latestForecast->predicted_outflow,
            'net_flow' => $latestForecast->net_flow,
            'confidence_score' => $latestForecast->confidence_score,
            'analysis_metadata' => $latestForecast->analysis_metadata
        ]);
    }
}
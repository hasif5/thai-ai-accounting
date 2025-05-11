<?php

namespace App\Console\Commands;

use App\Models\Forecast;
use App\Models\LedgerEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForecastCashflowCommand extends Command
{
    protected $signature = 'forecast:cashflow';
    protected $description = 'Generate weekly cash flow forecasts using linear regression';

    public function handle(): void
    {
        $this->info('Generating cash flow forecast...');

        try {
            // Get past 90 days of net cash flow data
            $startDate = Carbon::now()->subDays(90);
            $dailyNetFlow = LedgerEntry::select(
                DB::raw('DATE(date) as flow_date'),
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as net_amount')
            )
                ->where('date', '>=', $startDate)
                ->groupBy('flow_date')
                ->orderBy('flow_date')
                ->get()
                ->map(fn ($entry) => [
                    'x' => Carbon::parse($entry->flow_date)->diffInDays($startDate),
                    'y' => (float) $entry->net_amount
                ])
                ->values()
                ->all();

            // Calculate linear regression coefficients
            $n = count($dailyNetFlow);
            $sumX = array_sum(array_column($dailyNetFlow, 'x'));
            $sumY = array_sum(array_column($dailyNetFlow, 'y'));
            $sumXY = array_sum(array_map(fn($point) => $point['x'] * $point['y'], $dailyNetFlow));
            $sumXX = array_sum(array_map(fn($point) => $point['x'] * $point['x'], $dailyNetFlow));

            $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
            $intercept = ($sumY - $slope * $sumX) / $n;

            // Calculate R-squared for confidence score
            $yMean = $sumY / $n;
            $ssRes = 0;
            $ssTot = 0;

            foreach ($dailyNetFlow as $point) {
                $predicted = $slope * $point['x'] + $intercept;
                $ssRes += pow($point['y'] - $predicted, 2);
                $ssTot += pow($point['y'] - $yMean, 2);
            }

            $rSquared = 1 - ($ssRes / $ssTot);
            $confidenceScore = max(min($rSquared * 100, 100), 0);

            // Generate 7-day forecast
            $forecast = new Forecast([
                'forecast_date' => Carbon::now(),
                'predicted_inflow' => max(0, $slope * ($n + 7) + $intercept),
                'predicted_outflow' => abs(min(0, $slope * ($n + 7) + $intercept)),
                'net_flow' => $slope * ($n + 7) + $intercept,
                'confidence_score' => $confidenceScore,
                'analysis_metadata' => [
                    'slope' => $slope,
                    'intercept' => $intercept,
                    'r_squared' => $rSquared,
                    'data_points' => $n
                ]
            ]);

            $forecast->save();

            $this->info('Forecast generated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to generate forecast: ' . $e->getMessage());
            $this->error('Failed to generate forecast.');
        }
    }
}
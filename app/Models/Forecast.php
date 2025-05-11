<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'forecast_date',
        'predicted_inflow',
        'predicted_outflow',
        'net_flow',
        'confidence_score',
        'analysis_metadata'
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'predicted_inflow' => 'decimal:2',
        'predicted_outflow' => 'decimal:2',
        'net_flow' => 'decimal:2',
        'confidence_score' => 'decimal:2',
        'analysis_metadata' => 'array'
    ];

    public static function generateForecast(): self
    {
        // This will be implemented in the command
        return new self();
    }
}
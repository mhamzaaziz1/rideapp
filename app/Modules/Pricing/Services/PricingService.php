<?php

namespace Modules\Pricing\Services;

use Modules\Pricing\Models\PricingZoneModel;

class PricingService
{
    /**
     * Default Pricing Configuration (Fallback)
     */
    protected $baseFare = 5.00;
    protected $perMileRate = 1.50;
    protected $perMinuteRate = 0.25;
    protected $minimumFare = 10.00;

    /**
     * Vehicle Type Multipliers
     */
    protected $vehicleMultipliers = [
        'standard' => 1.0,  // Sedan / Hatchback
        'suv'      => 1.5,  // SUV / Minivan
        'luxury'   => 2.0,  // Luxury Sedan
        'van'      => 1.8   // Large Van
    ];

    protected $pricingZoneModel;

    public function __construct()
    {
        $this->pricingZoneModel = new PricingZoneModel();
        // In a real app, we would load active pricing rules from DB here
    }

    /**
     * Calculate Trip Fare
     * 
     * @param float $distanceMiles
     * @param int $durationMinutes (estimated)
     * @param string $vehicleType
     * @param float $surgeMultiplier (default 1.0)
     * @return float
     */
    public function calculateFare(float $distanceMiles, int $durationMinutes, string $vehicleType = 'standard', float $surgeMultiplier = 1.0): float
    {
        $typeMultiplier = $this->vehicleMultipliers[strtolower($vehicleType)] ?? 1.0;

        // Core Calculation
        $distanceCost = $distanceMiles * $this->perMileRate;
        $timeCost = $durationMinutes * $this->perMinuteRate;
        
        $subtotal = ($this->baseFare + $distanceCost + $timeCost) * $typeMultiplier * $surgeMultiplier;

        // Ensure Minimum Fare
        return round(max($subtotal, $this->minimumFare), 2);
    }

    /**
     * Calculate Distance between two points (Haversine Formula)
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in Miles
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 3959; // Miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Estimate Duration based on Distance and Average City Speed
     * 
     * @param float $distanceMiles
     * @return int Minutes
     */
    public function estimateDuration(float $distanceMiles): int
    {
        $averageSpeedMph = 25; // City driving average
        $hours = $distanceMiles / $averageSpeedMph;
        return (int) round($hours * 60);
    }
}

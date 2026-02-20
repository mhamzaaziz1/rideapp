<?php

namespace Modules\Pricing\Controllers;

use App\Controllers\BaseController;
use Modules\Pricing\Models\PricingRuleModel;
use Modules\Pricing\Models\PricingPeakHourModel;

use Modules\Pricing\Models\PricingZoneModel;

class PricingController extends BaseController
{
    public function index()
    {
        $model = new PricingRuleModel();
        $peakModel = new PricingPeakHourModel();
        $zoneModel = new PricingZoneModel();
        
        // Get active tab or default to first one
        $activeTab = $this->request->getGet('tab') ?? 'Sedan';
        
        $rules = $model->findAll();
        
        // Find current rule
        $currentRule = null;
        foreach($rules as $r) {
            if($r['vehicle_type'] == $activeTab) {
                $currentRule = $r;
                break;
            }
        }
        
        // Fallback if tab invalid
        if(!$currentRule && count($rules) > 0) {
            $currentRule = $rules[0];
            $activeTab = $currentRule['vehicle_type'];
        }

        // Get Peak Hours and Zones for this rule
        $peakHours = [];
        $zones = [];
        if($currentRule) {
            $peakHours = $peakModel->where('pricing_rule_id', $currentRule['id'])->findAll();
            $zones = $zoneModel->where('pricing_rule_id', $currentRule['id'])->findAll();
        }

        $data = [
            'title' => 'Pricing Configuration',
            'rules' => $rules,
            'currentRule' => $currentRule, 
            'peakHours' => $peakHours,
            'zones' => $zones,
            'activeTab' => $activeTab
        ];
        return view('Modules\Pricing\Views\pricing\index', $data);
    }

    public function update($id)
    {
        $model = new PricingRuleModel();
        
        $updateData = [
            'base_fare' => $this->request->getPost('base_fare'),
            'distance_rate_per_mile' => $this->request->getPost('distance_rate_per_mile'),
            'time_rate_per_minute' => $this->request->getPost('time_rate_per_minute'),
            'minimum_fare' => $this->request->getPost('minimum_fare'),
        ];
        
        if($model->update($id, $updateData)) {
            return redirect()->back()->with('success', 'Pricing updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update pricing.');
    }

    public function addPeakHour()
    {
        $peakModel = new PricingPeakHourModel();
        
        $data = [
            'pricing_rule_id' => $this->request->getPost('pricing_rule_id'),
            'day_of_week' => $this->request->getPost('day_of_week'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'multiplier' => $this->request->getPost('multiplier'),
        ];
        
        $peakModel->insert($data);
        return redirect()->back()->with('success', 'Peak hour added.');
    }

    public function deletePeakHour($id)
    {
        $peakModel = new PricingPeakHourModel();
        $peakModel->delete($id);
        return redirect()->back()->with('success', 'Peak hour removed.');
    }

    public function addZone()
    {
        $zoneModel = new PricingZoneModel();
        
        $data = [
            'pricing_rule_id' => $this->request->getPost('pricing_rule_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'is_active' => 1
        ];
        
        $zoneModel->insert($data);
        return redirect()->back()->with('success', 'Zone added.');
    }

    public function deleteZone($id)
    {
        $zoneModel = new PricingZoneModel();
        $zoneModel->delete($id);
        return redirect()->back()->with('success', 'Zone removed.');
    }
}

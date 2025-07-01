<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use Illuminate\Http\Request;

class PricingPlanController extends Controller
{
    public function index()
    {
        return PricingPlan::orderBy('order')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'billing_interval' => 'required|string|in:one-time,monthly,yearly',
            'order'            => 'integer',
        ]);

        $plan = PricingPlan::create($data);
        return response()->json($plan, 201);
    }

    public function show(PricingPlan $plan)
    {
        return $plan;
    }

    public function update(Request $request, PricingPlan $plan)
    {
        $data = $request->validate([
            'name'             => 'sometimes|string|max:255',
            'price'            => 'sometimes|numeric|min:0',
            'description'      => 'nullable|string',
            'billing_interval' => 'sometimes|string|in:one-time,monthly,yearly',
            'order'            => 'integer',
        ]);

        $plan->update($data);
        return response()->json($plan);
    }

    public function destroy(PricingPlan $plan)
    {
        $plan->delete();
        return response()->noContent();
    }
}

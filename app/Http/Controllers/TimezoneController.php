<?php

namespace App\Http\Controllers;

use App\Models\Timezone;
use Illuminate\Http\Request;

class TimezoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return inertia('settings/Timezone');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'timezone' => ['required', 'array'],
            'timezone.label' => ['required', 'string', 'max:255'],
            'timezone.name' => ['required', 'string', 'max:255'],
            'timezone.tzCode' => ['required', 'string', 'max:255'],
            'timezone.utc' => ['required', 'string', 'max:255'],
        ]);

        $validated['timezone']['code'] = $validated['timezone']['tzCode'];

        $timezone = Timezone::firstOrCreate([
            'code' => $validated['timezone']['code'],
        ], $validated['timezone']);

        $request->user()->update([
            'timezone_id' => $timezone->id,
        ]);

        return back()->with('success', 'Timezone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

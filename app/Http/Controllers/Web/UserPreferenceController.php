<?php

// app/Http/Controllers/Web/UserPreferenceController.php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreference\StoreUserPreferenceRequest;
use App\Http\Requests\UserPreference\UpdateUserPreferenceRequest;
use App\Services\UserPreference\Interfaces\UserPreferenceServiceInterface;

class UserPreferenceController extends Controller
{
    protected $preferenceService;

    public function __construct(UserPreferenceServiceInterface $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function index()
    {
        $preferences = $this->preferenceService->getUserPreferences();
        return view('user.preferences.index', compact('preferences'));
    }

    public function create()
    {
        return view('user.preferences.create');
    }

    public function store(StoreUserPreferenceRequest $request)
    {
        $preference = $this->preferenceService->createPreference($request->validated());
        return redirect()->route('user.preferences.index')
            ->with('success', 'Preferences saved successfully');
    }

    public function show($id)
    {
        $preference = $this->preferenceService->getPreference($id);
        return view('user.preferences.show', compact('preference'));
    }

    public function edit($id)
    {
        $preference = $this->preferenceService->getPreference($id);
        return view('user.preferences.edit', compact('preference'));
    }

    public function update(UpdateUserPreferenceRequest $request, $id)
    {
        $this->preferenceService->updatePreference($id, $request->validated());
        return redirect()->route('user.preferences.index')
            ->with('success', 'Preferences updated successfully');
    }

    public function destroy($id)
    {
        $this->preferenceService->deletePreference($id);
        return redirect()->route('user.preferences.index')
            ->with('success', 'Preferences deleted successfully');
    }
}

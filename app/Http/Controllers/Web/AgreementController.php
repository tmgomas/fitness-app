<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agreement\StoreAgreementRequest;
use App\Http\Requests\Agreement\UpdateAgreementRequest;
use App\Services\Agreement\Interfaces\AgreementServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgreementController extends Controller
{
    protected $agreementService;

    public function __construct(AgreementServiceInterface $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function index(): View
    {
        $agreements = $this->agreementService->getAllAgreements();
        return view('agreements.index', compact('agreements'));
    }

    public function create(): View
    {
        return view('agreements.create');
    }

    public function store(StoreAgreementRequest $request): RedirectResponse
    {
        try {
            $this->agreementService->createAgreement($request->validated());
            return redirect()
                ->route('agreements.index')
                ->with('success', 'Agreement created successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(string $id): View
    {
        $agreement = $this->agreementService->getAgreement($id);
        return view('agreements.edit', compact('agreement'));
    }
public function show(string $id): View
{
    $agreement = $this->agreementService->getAgreement($id);
    
    // Get the users who have accepted this agreement
    $userAgreements = $agreement->userAgreements()->with('user')->orderBy('accepted_at', 'desc')->get();
    
    return view('agreements.show', compact('agreement', 'userAgreements'));
}
    public function update(UpdateAgreementRequest $request, string $id): RedirectResponse
    {
        try {
            $this->agreementService->updateAgreement($id, $request->validated());
            return redirect()
                ->route('agreements.index')
                ->with('success', 'Agreement updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->agreementService->deleteAgreement($id);
            return redirect()
                ->route('agreements.index')
                ->with('success', 'Agreement deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
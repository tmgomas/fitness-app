<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Services\Agreement\Interfaces\AgreementServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAgreementController extends Controller
{
    protected $agreementService;

    public function __construct(AgreementServiceInterface $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function showAgreement(Agreement $agreement)
    {
        $user = Auth::user();
        $hasAccepted = $this->agreementService->checkUserAgreement($user->id, $agreement->id);
        $acceptedDate = null;
        
        if ($hasAccepted) {
            $userAgreement = $agreement->userAgreements()
                ->where('user_id', $user->id)
                ->first();
            
            if ($userAgreement) {
                $acceptedDate = $userAgreement->accepted_at;
            }
        }
        
        return view('agreements.accept', compact('agreement', 'hasAccepted', 'acceptedDate'));
    }
    
    public function acceptAgreement(Request $request, Agreement $agreement)
    {
        $request->validate([
            'accept' => 'required',
        ]);

        $user = Auth::user();
        
        // Check if user has already accepted
        if ($this->agreementService->checkUserAgreement($user->id, $agreement->id)) {
            return redirect()->route('agreements.show-user', $agreement)
                ->with('info', 'You have already accepted this agreement.');
        }
        
        // Accept the agreement
        $this->agreementService->acceptAgreement(
            $user->id,
            $agreement->id,
            $request->ip(),
            $request->userAgent()
        );
        
        return redirect()->route('agreements.show-user', $agreement)
            ->with('success', 'Agreement accepted successfully.');
    }
    
    public function listUserAgreements()
    {
        $user = Auth::user();
        $userAgreements = $user->userAgreements()
            ->with('agreement')
            ->orderBy('accepted_at', 'desc')
            ->get();
        
        return view('users.agreements', compact('userAgreements'));
    }
}
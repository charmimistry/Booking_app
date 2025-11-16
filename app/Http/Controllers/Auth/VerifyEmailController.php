<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        Log::info('Attempting to verify user ID: ' . $request->route('id'));

        $user = User::findOrFail((int) $request->route('id'));

        Log::info('User found with email: ' . $user->email);

        $providedHash = (string) $request->route('hash');
        $calculatedHash = sha1($user->email);
        Log::info('Provided hash: ' . $providedHash);
        Log::info('Calculated hash: ' . $calculatedHash);

        if (! hash_equals($providedHash, $calculatedHash)) {
            Log::info('Hash mismatch');
            abort(404, 'Invalid verification link');
        }

        Log::info('Hash matches');

        if ($user->hasVerifiedEmail()) {
            Log::info('Already verified');
            return redirect('/login?verified=1');
        }

        Log::info('Not verified, marking...');
        $marked = $user->markEmailAsVerified();
        Log::info('Mark result: ' . ($marked ? 'true' : 'false'));

        if ($marked) {
            Log::info('Firing Verified event');
            event(new Verified($user));
        }

        return redirect('/login?verified=1');
    }
}
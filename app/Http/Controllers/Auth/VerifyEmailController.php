<?php
// Controller - verify user email after clicking link in email
// invokable controller = one method only, like a single-purpose use case

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/* CLASS */
class VerifyEmailController extends Controller
{
    /* PUBLIC METHOD */
    /* __invoke */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // if already verified just redirect - nothing to do
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            /** @var \Illuminate\Contracts\Auth\MustVerifyEmail $user */
            $user = $request->user();

            // fire Verified event - Laravel uses it to trigger notifications
            event(new Verified($user));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}

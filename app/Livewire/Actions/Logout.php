<?php
// Action class - handles logout
// invokable class = called directly as a function with $logout()

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/* CLASS */
class Logout
{
    /* PUBLIC METHOD */
    /* __invoke */
    public function __invoke()
    {
        // logout from web guard - session based auth
        Auth::guard('web')->logout();

        // destroy session and regenerate csrf token - security reset after logout
        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}

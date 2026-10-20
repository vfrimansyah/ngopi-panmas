<?php

namespace App\Controllers\Auth;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Controllers\LoginController as ShieldLogin;

class LoginController extends ShieldLogin
{
    /**
     * Displays the form to login to the site.
     * If user is already logged in, redirect to dashboard.
     *
     * @return RedirectResponse|string
     */
    public function loginView()
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->loginRedirect());
        }

        return parent::loginView();
    }

    /**
     * Attempts to log the user in.
     * Always clear previous auth session before processing new login attempt.
     */
    public function loginAction(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            auth()->logout();
        } else {
            session()->remove('user');
        }

        return parent::loginAction();
    }
}

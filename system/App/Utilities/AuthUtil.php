<?php

namespace Utils;

class AuthUtil
{
    /**
     * Check if the user is logged in, redirect to home if not
     *
     * @return void
     */
    public static function requireLogin(): void
    {
        if (!isset($_SESSION['login'])) :
            HttpUtil::redirect('');
        endif;
    }
}

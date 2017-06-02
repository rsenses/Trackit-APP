<?php
// SESSION
$sessionName = $settings['settings']['session_name'];

ini_set('session.use_strict_mode', true);
// ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);

session_set_cookie_params(
    5 * 60 * 60,
    '/',
    null,
    false,
    false
);

$inactive = session_status() === PHP_SESSION_NONE;

if ($inactive) {
    // Refresh session cookie when "inactive",
    // else PHP won't know we want this to refresh
    if (isset($_COOKIE[$sessionName])) {
        setcookie(
            $sessionName,
            $_COOKIE[$sessionName],
            time() + 5 * 60 * 60,
            '/',
            null,
            false,
            false
        );
    }
}

session_name($sessionName);
session_cache_limiter(false);
if ($inactive) {
    session_start();
}

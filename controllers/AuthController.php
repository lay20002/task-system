<?php
// controllers/AuthController.php

class AuthController extends Controller
{
    public function login()
    {
        session_start();

        $userModel = new User();

        // ✅ Flash error (only shown once on GET)
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        // ✅ Auto-login using remember cookie
        if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
            $user = $userModel->findByRememberToken($_COOKIE['remember_token']);
            if ($user) {
                $_SESSION['user'] = $user;
                header("Location: index.php?c=dashboard&a=index");
                exit;
            }
        }

        // ✅ Handle login submit (POST) using PRG pattern
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            // ----------------------------
            // SESSION-ONLY LOGIN GUARD
            // progressive delay + lock
            // ----------------------------
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

            if (!isset($_SESSION['login_guard'])) {
                $_SESSION['login_guard'] = [];
            }

            if (!isset($_SESSION['login_guard'][$ip])) {
                $_SESSION['login_guard'][$ip] = [
                    'attempts'   => 0,
                    'lock_until' => 0
                ];
            }

            $guard = &$_SESSION['login_guard'][$ip];

            // If locked
            if (!empty($guard['lock_until']) && time() < (int)$guard['lock_until']) {
                $remain = (int)$guard['lock_until'] - time();
                $_SESSION['login_error'] = "Too many failed attempts. Try again in {$remain} seconds.";
                header("Location: index.php?c=auth&a=login");
                exit;
            }

            // ----------------------------
            // LOGIN CHECK
            // ----------------------------
            $user = $userModel->findByUsername($username);

            $ok = false;

            if ($user) {
                // ✅ New secure password hashes
                if (!empty($user['password']) && password_verify($password, $user['password'])) {
                    $ok = true;
                }
                // ✅ Old MD5 passwords (upgrade automatically)
                elseif (!empty($user['password']) && md5($password) === $user['password']) {
                    $ok = true;

                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    if ($newHash !== false) {
                        $userModel->updatePassword((int)$user['id'], $newHash);
                        $user['password'] = $newHash; // keep session consistent
                    }
                }
            }

            // ----------------------------
            // FAIL: delay + increment + lock
            // ----------------------------
            if (!$ok) {
                $attemptsBefore = (int)$guard['attempts'];

                // Progressive delay: 1,2,4,8 (cap 8)
                if ($attemptsBefore > 0) {
                    $delay = min(2 ** ($attemptsBefore - 1), 8);
                    sleep((int)$delay);
                }

                $guard['attempts'] = $attemptsBefore + 1;

                // Lock at 5 attempts for 1 minute
                if ($guard['attempts'] >= 5) {
                    $guard['lock_until'] = time() + 60; // 1 minute
                    $_SESSION['login_error'] = "Too many failed attempts. Locked for 1 minute.";
                } else {
                    // ✅ safer message (don’t reveal user exists)
                    $_SESSION['login_error'] = "Invalid username or password.";
                }

                header("Location: index.php?c=auth&a=login");
                exit;
            }

            // ----------------------------
            // SUCCESS: reset guard + login
            // ----------------------------
            $guard['attempts'] = 0;
            $guard['lock_until'] = 0;

            $_SESSION['user'] = $user;

            // ✅ Remember me cookie
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $userModel->updateRememberToken((int)$user['id'], $token);

                setcookie(
                    'remember_token',
                    $token,
                    [
                        'expires'  => time() + 7 * 24 * 60 * 60,
                        'path'     => '/',
                        'secure'   => false, // set true if using HTTPS
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
            }

            header("Location: index.php?c=dashboard&a=index");
            exit;
        }

        // ✅ Render login page only on GET (NO layout)
        $this->view("auth/login", ["error" => $error], false);
    }

    // ✅ Admin: reset login lock (session-only)
    public function resetLock()
    {
        session_start();

        // only admin can reset
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            header("Location: index.php?c=auth&a=login");
            exit;
        }

        unset($_SESSION['login_guard']);
        unset($_SESSION['login_error']);

        $_SESSION['success_message'] = "Login lock cleared.";
        header("Location: index.php?c=dashboard&a=index");
        exit;
    }

    public function logout()
    {
        session_start();

        // Clear remember token in DB for current user (optional but recommended)
        if (isset($_SESSION['user']['id'])) {
            $userModel = new User();
            $userModel->updateRememberToken((int)$_SESSION['user']['id'], null);
        }

        // Clear cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie(
                'remember_token',
                '',
                [
                    'expires'  => time() - 3600,
                    'path'     => '/',
                    'secure'   => false, // set true if using HTTPS
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        }

        // Optional: clear lock guard on logout
        unset($_SESSION['login_guard']);
        unset($_SESSION['login_error']);

        $_SESSION = [];
        session_destroy();

        header("Location: index.php?c=auth&a=login");
        exit;
    }
}

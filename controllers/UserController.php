<?php

class UserController extends Controller
{
    private function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?c=auth&a=login");
            exit;
        }
    }

    private function requireAdmin(): void
    {
        $this->requireLogin();

        if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            header("Location: index.php?c=dashboard&a=index");
            exit;
        }
    }

    public function list()
    {
        $this->requireAdmin();

        $userModel = new User();
        $users = $userModel->all();

        $this->view("users/list", ["users" => $users]);
    }

    public function create()
    {
        $this->requireAdmin();

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role     = trim($_POST['role'] ?? 'user');

            // 🔒 Validation rules
            if ($username === '' || $password === '') {
                $error = "Username and password are required!";
            } elseif (strlen($username) < 3) {
                $error = "Username must be at least 6 characters.";
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
                $error = "Password must be at least 8 characters and include both letters and numbers.";
            } else {
                $userModel = new User();

                try {
                    $userModel->create($username, $password, $role);
                    header("Location: index.php?c=user&a=list");
                    exit;
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        $this->view("users/add", ["error" => $error]);
    }

    public function delete()
    {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header("Location: index.php?c=user&a=list");
            exit;
        }

        // Prevent deleting yourself
        if (isset($_SESSION['user']['id']) && $id === (int)$_SESSION['user']['id']) {
            header("Location: index.php?c=user&a=list");
            exit;
        }

        try {
            $userModel = new User();
            $userModel->delete($id);
        } catch (Exception $e) {
            $_SESSION['user_delete_error'] = $e->getMessage();
        }

        header("Location: index.php?c=user&a=list");
        exit;
    }
}

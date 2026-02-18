<?php

class TaskController extends Controller
{
    private const STATUSES = ['Pending', 'In Progress', 'Completed'];

    private function auth(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?c=auth&a=login");
            exit;
        }
    }

    private function isAdmin(): bool
    {
        return (($_SESSION['user']['role'] ?? 'user') === 'admin');
    }

    private function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            $_SESSION['success_message'] = "Access denied.";
            header("Location: index.php?c=task&a=list");
            exit;
        }
    }

    private function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    private function requireCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
            die("Invalid CSRF token");
        }
    }

    private function validateStatus(string $status): bool
    {
        return in_array($status, self::STATUSES, true);
    }

    private function validateDate(?string $date): bool
    {
        if ($date === null || $date === '') return false;

        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function isCompletedStatus(?string $status): bool
    {
        return trim((string)$status) === 'Completed';
    }

    public function list()
    {
        $this->auth();

        $taskModel = new Task();

        $this->view("tasks/list", [
            "tasks" => $taskModel->all(),
            "user"  => $_SESSION['user'],
            "csrf"  => $this->csrfToken()
        ]);
    }

    public function add()
    {
        $this->auth();
        $this->requireAdmin(); // ✅ admin only

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $location    = trim($_POST['location'] ?? '');
            $description = trim($_POST['task_description'] ?? '');
            $status      = trim($_POST['status'] ?? 'Pending');
            $due_date    = trim($_POST['due_date'] ?? '');

            if ($location === '') {
                $error = "Location is required.";
            } elseif (!$this->validateStatus($status)) {
                $error = "Invalid status selected.";
            } elseif ($due_date === '') {
                $error = "Due date is required.";
            } elseif (!$this->validateDate($due_date)) {
                $error = "Invalid date format. Use YYYY-MM-DD.";
            } else {
                (new Task())->create($location, $description, $status, $due_date);

                $_SESSION['success_message'] = "Task added successfully!";
                header("Location: index.php?c=task&a=list");
                exit;
            }
        }

        $this->view("tasks/add", [
            "error" => $error,
            "csrf"  => $this->csrfToken()
        ]);
    }

    public function edit()
    {
        $this->auth();
        $this->requireAdmin(); // ✅ admin only

        $taskModel = new Task();
        $error = null;

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['success_message'] = "Task not found.";
            header("Location: index.php?c=task&a=list");
            exit;
        }

        $taskData = $taskModel->find($id);
        if (!$taskData) {
            $_SESSION['success_message'] = "Task not found.";
            header("Location: index.php?c=task&a=list");
            exit;
        }

        // 🔒 Completed tasks cannot be edited (admin included)
        if ($this->isCompletedStatus($taskData['status'] ?? null)) {
            $_SESSION['success_message'] = "This task is completed and cannot be edited.";
            header("Location: index.php?c=task&a=list");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $location    = trim($_POST['location'] ?? '');
            $description = trim($_POST['task_description'] ?? '');
            $status      = trim($_POST['status'] ?? 'Pending');
            $due_date    = trim($_POST['due_date'] ?? '');

            if ($location === '') {
                $error = "Location is required.";
            } elseif (!$this->validateStatus($status)) {
                $error = "Invalid status selected.";
            } elseif ($due_date === '') {
                $error = "Due date is required.";
            } elseif (!$this->validateDate($due_date)) {
                $error = "Invalid date format. Use YYYY-MM-DD.";
            } else {
                $taskModel->update($id, $location, $description, $status, $due_date);

                $_SESSION['success_message'] = "Task updated successfully!";
                header("Location: index.php?c=task&a=list");
                exit;
            }
        }

        $this->view("tasks/edit", [
            "task"  => $taskData,
            "error" => $error,
            "csrf"  => $this->csrfToken()
        ]);
    }

    public function delete()
    {
        $this->auth();
        $this->requireAdmin(); // ✅ admin only

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Invalid request method");
        }
        $this->requireCsrf();

        $taskModel = new Task();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['success_message'] = "Task not found.";
            header("Location: index.php?c=task&a=list");
            exit;
        }

        $taskData = $taskModel->find($id);
        if (!$taskData) {
            $_SESSION['success_message'] = "Task not found.";
            header("Location: index.php?c=task&a=list");
            exit;
        }

        // 🔒 Completed tasks cannot be deleted (admin included)
        if ($this->isCompletedStatus($taskData['status'] ?? null)) {
            $_SESSION['success_message'] = "This task is completed and cannot be deleted.";
            header("Location: index.php?c=task&a=list");
            exit;
        }

        $taskModel->delete($id);

        $_SESSION['success_message'] = "Task deleted successfully!";
        header("Location: index.php?c=task&a=list");
        exit;
    }
}

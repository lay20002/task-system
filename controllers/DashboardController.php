<?php

class DashboardController extends Controller
{
    public function index()
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?c=auth&a=login");
            exit;
        }

        $userModel = new User();
        $users = $userModel->all();

        $taskModel = new Task();
        // If your Task model doesn't have all(), use whatever you already have for listing tasks.
        // We'll try all(), else you can switch to list() method you have.
        $tasks = method_exists($taskModel, 'all') ? $taskModel->all() : [];

        $stats = [
            "users" => is_array($users) ? count($users) : 0,
            "tasks" => is_array($tasks) ? count($tasks) : 0,
        ];

        $this->view("dashboard/index", [
            "stats" => $stats,
            "users" => is_array($users) ? $users : [],
            "tasks" => is_array($tasks) ? $tasks : [],
        ]);
    }
}

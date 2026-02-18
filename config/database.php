<?php
$conn = new mysqli("localhost", "root", "", "task_db");
if ($conn->connect_error) die("Database connection failed");


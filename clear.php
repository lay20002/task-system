<?php
session_start();
unset($_SESSION['login_guard']);
unset($_SESSION['login_error']);
session_regenerate_id(true);
echo "Login lock cleared. <a href='index.php?c=auth&a=login'>Go to Login</a>";

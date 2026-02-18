<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Login Page</title>
<style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  }

  body, html {
    height: 100%;
    background-color: #f0f2f5; /* Slightly softer grey background */
    display: flex;
    justify-content: center;
    align-items: center;
    align-items: flex-start;
padding-top: 80px;
  }

  .login-form {
    width: 100%;
    max-width: 400px;
    background-color: white;
    /* Reduced top padding from 40px to 20px to move everything up */
    padding: 20px 40px 40px 40px; 
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  /* Logo Styling - Pulling the content up */
  .logo-container {
    width: 100%;
    /* margin-bottom reduced to 5px to close the gap to the Username field */
    margin: 0 0 5px 0; 
  }
  .login-logo {
    /* width: 300px; /* Keep the size you prefer */
    /* height: auto;
    display: inline-block; */ 
    width: 300px;        /* BIG logo */
  display: block;
  margin: 0 auto;
  margin-top: -50px;   /* move DOWN a lot */
  margin-bottom: -90px;

  }

  /* Input Styling */
  .login-form input[type="text"],
  .login-form input[type="password"] {
    width: 100%;
    padding: 14px;
    margin: 8px 0;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 16px;
    color: #555;
    outline: none;
  }

  .login-form input:focus {
    border-color: #00aaff;
  }

  /* Remember Me Styling */
  .remember-me {
    display: flex;
    align-items: center;
    margin: 15px 0;
    font-size: 15px;
    color: #444;
  }

  .remember-me input {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    cursor: pointer;
    accent-color: #00aaff; /* Matches the blue theme */
  }

  /* Button Styling */
  .login-form button {
    width: 100%;
    padding: 14px;
    background-color: #00aaff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
  }

  .login-form button:hover {
    background-color: #0088cc;
  }

  .footer {
    margin-top: 20px;
    font-size: 14px;
    color: #888;
    text-align: center;

  }
  .error-alert {
  background-color: #fdecea;
  color: #b71c1c;
  border: 1px solid #f5c6cb;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 14px;
  margin: 12px 0;
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
}

.error-icon {
  font-size: 16px;
}
.brand-title {
  text-align: center;
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 4px;
  color: #0088cc;
  margin: 10px 0 20px 0;
  text-transform: uppercase;
}



  
</style>
</head>
<body>

<div class="login-container">
  <form class="login-form" method="post">
    <div class="logo-container">
      <img src="public/logo.png" alt="Logo"  class="login-logo">
       <h2 class="brand-title">ANXIN-NETWORK</h2>
    </div>

    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
<?php if (!empty($error)): ?>
    <div id="login-error" class="error-alert">
     <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($error) ?>
    </div>
    <script>
        // Hide the alert after 10 seconds (10000 milliseconds)
        setTimeout(function() {
            const alert = document.getElementById('login-error');
            if (alert) {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500); // Remove after fade
            }
        }, 10000);
    </script>
<?php endif; ?>
    <div class="remember-me">
      <input type="checkbox" name="remember" id="remember" checked>
      <label for="remember">Remember Me</label>
    </div>
    <button type="submit">Login</button>

    <div class="footer">
      <p>&copy; Anxin.net <?php echo date('Y');?></p>
    </div>
  </form>
</div>

</body>
</html>
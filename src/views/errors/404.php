<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 - Access Forbidden</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .error-container {
      text-align: center;
      max-width: 500px;
      padding: 2rem;
    }

    .error-code {
      font-size: 8rem;
      font-weight: bold;
      margin-bottom: 1rem;
      opacity: 0.8;
    }

    .error-title {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    .error-message {
      font-size: 1.1rem;
      margin-bottom: 2rem;
      opacity: 0.9;
    }

    .error-actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 500;
      transition: background-color 0.3s;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  </style>
</head>

<body>
  <div class="error-container">
    <div class="error-code">403</div>
    <h1 class="error-title">Access Forbidden</h1>
    <p class="error-message">
      You don't have permission to access this resource.
      Please contact an administrator if you believe this is an error.
    </p>
    <div class="error-actions">
      <a href="/dashboard" class="btn">Go to Dashboard</a>
      <a href="/login" class="btn">Login</a>
    </div>
  </div>
</body>

</html>

<!-- views/errors/404.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Page Not Found</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .error-container {
      text-align: center;
      max-width: 500px;
      padding: 2rem;
    }

    .error-code {
      font-size: 8rem;
      font-weight: bold;
      margin-bottom: 1rem;
      opacity: 0.8;
    }

    .error-title {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    .error-message {
      font-size: 1.1rem;
      margin-bottom: 2rem;
      opacity: 0.9;
    }

    .error-actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 500;
      transition: background-color 0.3s;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  </style>
</head>

<body>
  <div class="error-container">
    <div class="error-code">404</div>
    <h1 class="error-title">Page Not Found</h1>
    <p class="error-message">
      The page you're looking for doesn't exist or has been moved.
      Let's get you back on track!
    </p>
    <div class="error-actions">
      <a href="/" class="btn">Go Home</a>
      <a href="/dashboard" class="btn">Dashboard</a>
    </div>
  </div>
</body>

</html>

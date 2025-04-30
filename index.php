<!-- File: loginForm.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRM Login</title>
    <link rel="stylesheet" href="style/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #ffffff, #dfe6e9);
            margin: 0;
        }
        .header {
            background-color: #1d3557;
            padding: 15px;
            color: white;
            text-align: center;
            font-size: 24px;
        }
        .container {
            width: 400px;
            background-color: white;
            margin: 40px auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1d3557;
        }
        .form-box input[type=email],
        .form-box input[type=password] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-box button {
            width: 100%;
            padding: 10px;
            background-color: #1d3557;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-box button:hover {
            background-color: #274c77;
        }
        .roles {
            margin-bottom: 15px;
        }
        .info-box {
            margin-top: 20px;
            font-size: 14px;
        }
        .info-box p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">WEBDAMN.COM | Home</div>

    <div class="container">
        <h2>Example: Customer Relationship Management (CRM) System</h2>
        <form method="POST" action="login.php" class="form-box">
            <!-- You can show this if validation fails -->
            <!-- <div style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 10px;">Fill all fields.</div> -->

            <input type="email" name="email" placeholder="email" required>
            <input type="password" name="password" placeholder="password" required>

            <div class="roles">
                <label><input type="radio" name="role" value="manager" required> Sales Manager</label>
                <label><input type="radio" name="role" value="employee"> Sales People</label>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="info-box">
            <strong>Sales Manager Login</strong><br>
            <p><strong>Email:</strong> william@webdamn.com</p>
            <p><strong>Password:</strong> 123</p>

            <strong>Sales People Login</strong><br>
            <p><strong>Email:</strong> smith@webdamn.com</p>
            <p><strong>Password:</strong> 123</p>
        </div>
    </div>
</body>
</html>

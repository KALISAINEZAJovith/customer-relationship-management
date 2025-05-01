<!-- File: newLoginForm.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise CRM Portal</title>
    <link rel="stylesheet" href="style/navbar.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        .header-container {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .logo-text {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            width: 40px;
            height: 40px;
        }

        .header1 {
            font-size: 26px;
            font-weight: bold;
            line-height: 1;
        }

        .header {
            font-size: 12px;
            margin-top: 4px;
            padding-left: 25px;
        }

        .container {
            width: 450px;
            background-color: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-box {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-box label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
        }

        .form-box input[type=email],
        .form-box input[type=password] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }

        .form-box input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        .form-box button {
            width: 100%;
            padding: 14px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .form-box button:hover {
            background-color: #3498db;
        }

        .roles {
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
        }

        .roles label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .roles input {
            margin-right: 8px;
        }

        .info-box {
            margin-top: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            font-size: 14px;
            color: #555;
        }

        .error-message {
            color: #e74c3c;
            background-color: #fdecea;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }

        .forgot-password {
            text-align: right;
            margin: 10px 0 20px;
        }

        .forgot-password a {
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo-text">
            <img src="images/panda-logo.png" alt="Panda Logo" class="logo-img">
            <div>
                <div class="header1">PANDA</div>
                <div class="header">CRM SOLUTION</div>
            </div>
        </div>
    </div>
    <div class="container">
        <h2>Account Login</h2>
        
        <div class="error-message" id="errorMessage">
            Please complete all required fields.
        </div>
        
        <form method="POST" action="authenticate.php" class="form-box" id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <div class="forgot-password">
                <a href="resetPassword.php">Forgot password?</a>
            </div>

            <div class="form-group">
                <label>Select Account Type</label>
                <div class="roles">
                    <label><input type="radio" name="role" value="admin"> Administrator</label>
                    <label><input type="radio" name="role" value="manager"> Manager</label>
                    <label><input type="radio" name="role" value="representative"> Representative</label>
                </div>
            </div>

            <button type="submit">Sign In</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const roleSelected = document.querySelector('input[name="role"]:checked');
            
            if (!email || !password || !roleSelected) {
                event.preventDefault();
                document.getElementById('errorMessage').style.display = 'block';
            }
        });
    </script>
</body>
</html>

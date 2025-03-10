<?php
session_start();
include('config.php');

$registration_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminname = $_POST['adminname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Password validation
    if (strlen($password) < 6 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $registration_message = "Password must be at least 6 characters long and contain at least one letter and one number.";
    } else {
        // Check if email already exists
        $check_email_sql = "SELECT * FROM Admin WHERE admin_email = ?";
        $check_stmt = $conn->prepare($check_email_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $registration_message = "Email already exists. Please use a different email.";
        } else {
            // Insert new admin
            $sql = "INSERT INTO Admin (admin_name, admin_email, admin_pwd) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $adminname, $email, $password);

            if ($stmt->execute()) {
                $registration_message = "Registration successful. <a href='admin_login.php'>Login</a>";
                // Optionally redirect to login page after registration
                // header("Location: admin_login.php");
                // exit();
            } else {
                $registration_message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check_stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #3D4E67, #A2A9B7, #879FC4, #56A3A6);
            overflow: hidden;
        }

        .container {
            display: flex;
            width: 90%;
            max-width: 1000px;
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
            z-index: 1;
        }

        .left-side, .right-side {
            flex: 1;
            padding: 40px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .left-side {
            background-color: #A2A9B7;
            text-align: center;
        }

        .left-side .header-text {
            font-size: 3.8em;
            font-family: 'Pacifico', cursive;
            color: #3D4E67;
            margin-bottom: 20px;
        }

        .left-side .slogan {
            font-size: 1.8em;
            color: #3D4E67;
        }

        .right-side {
            background-color: #ffffff;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        }

        .register-container {
            width: 100%;
            max-width: 400px;
        }

        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
            font-family: 'Georgia', cursive;
            color: #3D4E67;
        }

        .register-container form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .register-container form label {
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
            text-align: left;
            width: 100%;
        }

        .register-container form input {
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #879FC4;
            border-radius: 25px;
            font-size: 14px;
            width: 100%;
            transition: border-color 0.3s ease;
        }

        .register-container form input:focus {
            border-color: #3D4E67;
        }

        .register-container form button {
            padding: 12px 20px;
            background-color: #3D4E67;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .register-container form button:hover {
            background-color: #879FC4;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: left;
            width: 100%;
        }

        .login-link {
            margin-top: 15px;
            text-align: center;
            margin-left: 50px;
            font-size: 17px;
            color: #3D4E67;
            text-decoration: none;
            display: inline-block;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        @keyframes float {
            0% {
                transform: translatey(0px);
            }
            50% {
                transform: translatey(-10px);
            }
            100% {
                transform: translatey(0px);
            }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        .password-restrictions {
            margin-bottom: 15px;
            color: #3D4E67;
            font-size: 14px;
            text-align: left;
            width: 100%;
        }
    </style>
    <script>
        function validateForm() {
            var password = document.getElementById('password').value;
            var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;

            if (!passwordRegex.test(password)) {
                document.getElementById('password-error').innerText = 'Password length must be at least 6 and contains at least one number.';
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <div class="left-side">
        <div class="header-text floating">𝙁𝙡𝙮𝙬𝙞𝙩𝙝𝙐𝙨</div>
        <div class="slogan">Your journey, Our wings</div>
    </div>
    <div class="right-side">
        <div class="register-container">
            <h2>ADMIN REGISTRATION</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm()" autocomplete="off">
                <label for="adminname">Admin name:</label>
                <input type="text" id="adminname" name="adminname" required autocomplete="off">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required autocomplete="off">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required autocomplete="off">
                <div id="password-error" class="error-message"></div>
                <button type="submit">Register</button>
            </form>
            <?php if (!empty($registration_message)) echo "<p class='error-message'>$registration_message</p>"; ?>
            <a href="admin_login.php" class="login-link">Already have an account? Login here</a>
        </div>
    </div>
</div>
</body>
</html>
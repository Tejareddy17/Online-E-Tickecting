<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticketing Login & Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome for icons -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef2ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }
        .container {
            width: 400px;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            border: 1px solid #ddd;
            display: none; /* Hide both by default */
        }
        h2 {
            margin-bottom: 30px;
            font-size: 30px;
            color: #3f51b5;
            text-align: center;
            font-weight: 600;
        }
        form input, form select, form button {
            margin-bottom: 18px;
            padding: 12px;
            border: none;
            border-bottom: 2px solid #ddd;
            font-size: 16px;
            background: transparent;
            width: 100%;
            border-radius: 6px;
        }
        form input:focus, form select:focus {
            border-color: #3f51b5;
            outline: none;
        }
        form button {
            background-color: #3f51b5;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            padding: 12px 25px;
            font-size: 18px;
            border-radius: 8px;
            width: 100%;
            text-align: center;
        }
        form button:hover {
            background-color: #303f9f;
        }
        .toggle {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }
        .toggle a {
            color: #3f51b5;
            text-decoration: none;
            cursor: pointer;
        }
        .terms {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-top: 10px;
        }
        .terms input {
            margin-right: 8px;
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- LOGIN SECTION -->
    <div class="container" id="login-container">
        <h2>Login</h2>
        <form id="login-form" action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <div class="password-container">
                <input type="password" id="login-password" name="password" placeholder="Password" required>
                <i class="fa fa-eye" id="login-eye" onclick="togglePassword('login-password', 'login-eye')"></i>
            </div>
            <div class="toggle" style="text-align: right; margin-bottom: 20px;">
                <a href="#">Forgot Password?</a>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="toggle">
            Don't have an account? <a id="toggle-register">Register here</a>
        </div>
    </div>

    <!-- REGISTER SECTION -->
    <div class="container" id="register-container">
        <h2>Create an Account</h2>
        <form id="register-form" action="register.php" method="POST">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="text" id="dob" name="dob" placeholder="Date of Birth" required>
            <input type="number" id="age" name="age" placeholder="Age" required readonly>
            <select name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="tel" name="phone" placeholder="Phone Number" required>
            <div class="password-container">
                <input type="password" id="register-password" name="password" placeholder="Password" required>
                <i class="fa fa-eye" id="register-eye" onclick="togglePassword('register-password', 'register-eye')"></i>
            </div>
            <div class="terms">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="#">Terms and Conditions</a></label>
            </div>
            <button type="submit">Register</button>
        </form>
        <div class="toggle">
            Already have an account? <a id="toggle-login">Login here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Show login by default
        document.getElementById('login-container').style.display = 'block';

        document.getElementById('toggle-register').addEventListener('click', () => {
            document.getElementById('login-container').style.display = 'none';
            document.getElementById('register-container').style.display = 'block';
        });

        document.getElementById('toggle-login').addEventListener('click', () => {
            document.getElementById('login-container').style.display = 'block';
            document.getElementById('register-container').style.display = 'none';
        });

        flatpickr("#dob", {
            dateFormat: "d-m-Y",
            maxDate: "today",
            onChange: function(selectedDates) {
                const dob = selectedDates[0];
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
                    age--;
                }
                document.getElementById('age').value = age;
            }
        });

        function togglePassword(inputId, iconId) {
            const passwordField = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            passwordField.type = passwordField.type === "password" ? "text" : "password";
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        }
    </script>

</body>
</html>

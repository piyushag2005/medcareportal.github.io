<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Portal</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        p {
            font-size: 18px;
            margin-bottom: 40px;
            color: #555;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            text-align: center;
        }

        .role-container {
            display: flex;
            justify-content: space-around;
            width: 100%;
            max-width: 1000px;
            margin-bottom: 40px;
        }

        .card {
            background: #fff;
            padding: 30px;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            width: 280px;
            transition: transform 0.3s ease-in-out;
            text-align: center;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        h2 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 15px;
        }

        p a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            padding: 8px;
            border: 2px solid transparent;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        p a:hover {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        footer {
            position: relative;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 50px;
        }

        footer a {
            color: #007bff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .role-container {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 90%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Welcome to the Medical Portal</h1>
        <p>Select your role to proceed:</p>

        <div class="role-container">
            <div class="card">
                <h2>Doctor</h2>
                <p><a href="doctor_login.php">Login</a> | <a href="doctor_register.php">Register</a></p>
            </div>

            <div class="card">
                <h2>Patient</h2>
                <p><a href="patient_login.php">Login</a> | <a href="patient_register.php">Register</a></p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Medical Portal. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>

</body>
</html>

<?php

session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    /* Find user by email */

    $sql = "SELECT id, first_name, last_name, email, password FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    /* Check if user exists */

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        /* Verify password */

        if (password_verify($password, $user["password"])) {

            /* Create session */

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["first_name"] = $user["first_name"];
            $_SESSION["last_name"] = $user["last_name"];
            $_SESSION["email"] = $user["email"];

            /* Redirect after successful login */

            header("Location: complaint.php");
            exit();

        } else {

            $message = "Invalid email or password.";
            $message_type = "error";

        }

    } else {

        $message = "Invalid email or password.";
        $message_type = "error";

    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - Online Crime Reporting System</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


    <!-- Header -->

    <header class="header">

        <div class="logo">
            <h2>🚔 OCRS</h2>
        </div>

        <div class="header-text">

            <h1>Online Crime Reporting System</h1>

            <p>
                Report incidents securely and track your complaints online.
            </p>

        </div>

    </header>


    <!-- Navigation Bar -->

    <nav class="navbar">

        <a href="index.php">
            Home
        </a>

        <a href="complaint.php">
            Register Complaint
        </a>

        <a href="#">
            Track Complaint
        </a>

        <a href="#">
            About
        </a>

        <a href="#">
            Contact
        </a>


        <div class="nav-buttons">

            <a
                href="login.php"
                class="login-btn"
            >
                Login
            </a>

            <a
                href="register.php"
                class="register-btn"
            >
                Register
            </a>

        </div>

    </nav>


    <!-- Login Section -->

    <main>

        <section class="login-section">

            <div class="login-container">

                <h2>Login</h2>

                <p class="form-description">

                    Login to access your account and manage your complaints.

                </p>


                <!-- Login Message -->

                <?php if (!empty($message)): ?>

                    <p
                        class="<?php echo $message_type; ?>"
                        style="
                            text-align: center;
                            margin-bottom: 20px;
                            font-weight: bold;
                        "
                    >

                        <?php echo htmlspecialchars($message); ?>

                    </p>

                <?php endif; ?>


                <form
                    action="login.php"
                    method="POST"
                >


                    <!-- Email -->

                    <div class="form-group">

                        <label for="email">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email address"
                            required
                        >

                    </div>


                    <!-- Password -->

                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                    </div>


                    <!-- Remember Me -->

                    <div class="login-options">

                        <div class="remember-me">

                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                            >

                            <label for="remember">
                                Remember me
                            </label>

                        </div>


                        <a
                            href="#"
                            class="forgot-password"
                        >
                            Forgot Password?
                        </a>

                    </div>


                    <!-- Login Button -->

                    <button
                        type="submit"
                        class="submit-btn"
                    >
                        Login
                    </button>


                    <!-- Register Link -->

                    <p class="register-text">

                        Don't have an account?

                        <a href="register.php">
                            Register here
                        </a>

                    </p>

                </form>

            </div>

        </section>

    </main>


    <!-- Footer -->

    <footer class="footer">

        <p>
            © 2026 Online Crime Reporting System |
            Final Year Project
        </p>

    </footer>


</body>

</html>

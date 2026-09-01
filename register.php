<?php

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        /* Check whether email already exists */

        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);

        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();

        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $message_type = "error";

        } else {

            /* Hash password securely */

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            /* Insert user into database */

            $sql = "
                INSERT INTO users
                (first_name, last_name, email, phone, address, password)
                VALUES (?, ?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssssss",
                $first_name,
                $last_name,
                $email,
                $phone,
                $address,
                $hashed_password
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can now login.";
                $message_type = "success";

            } else {

                $message = "Registration failed. Please try again.";
                $message_type = "error";

            }

            $stmt->close();
        }

        $check_stmt->close();
    }
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

    <title>Register - Online Crime Reporting System</title>

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

        <a href="index.php">Home</a>

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


    <!-- Registration Section -->

    <main>

        <section class="register-section">

            <div class="register-container">

                <h2>Create Your Account</h2>

                <p class="form-description">

                    Register to submit and track your complaints online.

                </p>


                <!-- Registration Message -->

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
                    method="POST"
                    action="register.php"
                >


                    <div class="form-row">


                        <!-- First Name -->

                        <div class="form-group">

                            <label for="first_name">
                                First Name
                            </label>

                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                placeholder="Enter your first name"
                                required
                            >

                        </div>


                        <!-- Last Name -->

                        <div class="form-group">

                            <label for="last_name">
                                Last Name
                            </label>

                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                placeholder="Enter your last name"
                                required
                            >

                        </div>

                    </div>


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


                    <!-- Phone Number -->

                    <div class="form-group">

                        <label for="phone">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="Enter your phone number"
                            required
                        >

                    </div>


                    <!-- Address -->

                    <div class="form-group">

                        <label for="address">
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="3"
                            placeholder="Enter your address"
                            required
                        ></textarea>

                    </div>


                    <div class="form-row">


                        <!-- Password -->

                        <div class="form-group">

                            <label for="password">
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Create a password"
                                required
                            >

                        </div>


                        <!-- Confirm Password -->

                        <div class="form-group">

                            <label for="confirm_password">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Confirm your password"
                                required
                            >

                        </div>

                    </div>


                    <!-- Terms and Conditions -->

                    <div class="declaration">

                        <input
                            type="checkbox"
                            id="terms"
                            required
                        >

                        <label for="terms">

                            I agree to the Terms and Conditions and confirm
                            that the information provided is correct.

                        </label>

                    </div>


                    <!-- Register Button -->

                    <button
                        type="submit"
                        class="submit-btn"
                    >
                        Create Account
                    </button>


                    <!-- Login Link -->

                    <p class="login-link">

                        Already have an account?

                        <a href="login.php">
                            Login here
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

<?php

session_start();

require_once "config/database.php";

/* Check if user is logged in */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

/* Get logged-in user's information */

$user_id = $_SESSION["user_id"];

$sql = "SELECT first_name, last_name, email, phone
        FROM users
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    session_destroy();

    header("Location: login.php");
    exit();

}

$user = $result->fetch_assoc();

$stmt->close();


/* Submit complaint */ if ($_SERVER["REQUEST_METHOD"] === "POST") { $crime_type = trim($_POST["crime_type"]); $incident_date = $_POST["incident_date"]; $location = trim($_POST["location"]); $description = trim($_POST["description"]); $evidence_filename = null; /* Handle evidence upload */ if ( isset($_FILES["evidence"]) && $_FILES["evidence"]["error"] !== UPLOAD_ERR_NO_FILE ) { if ($_FILES["evidence"]["error"] !== UPLOAD_ERR_OK) { $message = "There was a problem uploading the evidence."; $message_type = "error"; } else { $file = $_FILES["evidence"]; /* Maximum file size: 5 MB */ $max_size = 5 * 1024 * 1024; if ($file["size"] > $max_size) { $message = "Evidence file must be less than 5 MB."; $message_type = "error"; } else { /* Allowed file types */ $allowed_types = [ "image/jpeg", "image/png", "image/webp", "application/pdf" ]; $file_type = mime_content_type($file["tmp_name"]); if (!in_array($file_type, $allowed_types)) { $message = "Only JPG, PNG, WEBP and PDF files are allowed."; $message_type = "error"; } else { /* Generate a safe unique filename */ $extension = strtolower( pathinfo( $file["name"], PATHINFO_EXTENSION ) ); $new_filename = uniqid("evidence_", true) . "." . $extension; $upload_path = "/var/www/html/LinuxProject/uploads/" . $new_filename; /* Move uploaded file */ if (move_uploaded_file( $file["tmp_name"], $upload_path )) { $evidence_filename = $new_filename; } else { $message = "Failed to save the evidence file."; $message_type = "error"; } } } } } /* Insert complaint only if there is no upload error */ if (empty($message)) { $sql = "INSERT INTO complaints (user_id, crime_type, incident_date, location, description, evidence) VALUES (?, ?, ?, ?, ?, ?)"; $stmt = $conn->prepare($sql); $stmt->bind_param( "isssss", $user_id, $crime_type, $incident_date, $location, $description, $evidence_filename ); if ($stmt->execute()) { $message = "Complaint submitted successfully!"; $message_type = "success"; } else { $message = "Failed to submit complaint. Please try again."; $message_type = "error"; } $stmt->close(); } }

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Register Complaint - Online Crime Reporting System
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>


    <!-- Header -->

    <header class="header">

        <div class="logo">
            <h2>🚔 OCRS</h2>
        </div>

        <div class="header-text">

            <h1>
                Online Crime Reporting System
            </h1>

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


    <!-- Complaint Form -->

    <main>

        <section class="complaint-section">

            <div class="complaint-container">

                <h2>
                    Register a Complaint
                </h2>

                <p class="form-description">
                    Please provide accurate information about the incident.
                </p>


                <!-- Message -->

                <?php if (!empty($message)): ?>

                    <p
                        class="<?php echo $message_type; ?>"
                        style="
                            text-align: center;
                            margin-bottom: 20px;
                            font-weight: bold;
                        "
                    >

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </p>

                <?php endif; ?>


                <form
    action="complaint.php"
    method="POST"
    enctype="multipart/form-data"
>


                    <!-- Personal Information -->

                    <h3>
                        Personal Information
                    </h3>


                    <div class="form-row">


                        <!-- Full Name -->

                        <div class="form-group">

                            <label for="name">
                                Full Name
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?php
                                    echo htmlspecialchars(
                                        $user["first_name"] . " " .
                                        $user["last_name"]
                                    );
                                ?>"
                                readonly
                            >

                        </div>


                        <!-- Phone -->

                        <div class="form-group">

                            <label for="phone">
                                Phone Number
                            </label>

                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="<?php
                                    echo htmlspecialchars(
                                        $user["phone"]
                                    );
                                ?>"
                                readonly
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
                            value="<?php
                                echo htmlspecialchars(
                                    $user["email"]
                                );
                            ?>"
                            readonly
                        >

                    </div>


                    <!-- Incident Information -->

                    <h3>
                        Incident Information
                    </h3>


                    <div class="form-row">


                        <!-- Crime Type -->

                        <div class="form-group">

                            <label for="crime_type">
                                Type of Crime
                            </label>

                            <select
                                id="crime_type"
                                name="crime_type"
                                required
                            >

                                <option value="">
                                    Select Crime Type
                                </option>

                                <option value="Theft">
                                    Theft
                                </option>

                                <option value="Fraud">
                                    Fraud
                                </option>

                                <option value="Cyber Crime">
                                    Cyber Crime
                                </option>

                                <option value="Harassment">
                                    Harassment
                                </option>

                                <option value="Other">
                                    Other
                                </option>

                            </select>

                        </div>


                        <!-- Incident Date -->

                        <div class="form-group">

                            <label for="incident_date">
                                Incident Date
                            </label>

                            <input
                                type="date"
                                id="incident_date"
                                name="incident_date"
                                required
                            >

                        </div>

                    </div>


                    <!-- Location -->

                    <div class="form-group">

                        <label for="location">
                            Incident Location
                        </label>

                        <input
                            type="text"
                            id="location"
                            name="location"
                            placeholder="Enter incident location"
                            required
                        >

                    </div>


                    <!-- Description -->

                    <div class="form-group">

                        <label for="description">
                            Complaint Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            placeholder="Describe the incident in detail"
                            required
                        ></textarea>

                    </div>


                    <!-- Evidence -->

                    <h3>
                        Upload Evidence
                    </h3>


                    <div class="form-group">

                        <label for="evidence">
                            Upload Image or Document
                        </label>

                        <input
                            type="file"
                            id="evidence"
                            name="evidence"
                        >

                    </div>


                    <!-- Declaration -->

                    <div class="declaration">

                        <input
                            type="checkbox"
                            id="declaration"
                            required
                        >

                        <label for="declaration">

                            I confirm that the information provided by me is
                            correct to the best of my knowledge.

                        </label>

                    </div>


                    <!-- Submit -->

                    <button
                        type="submit"
                        class="submit-btn"
                    >
                        Submit Complaint
                    </button>

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


<?php
session_start();
include_once("config.php");
define("TITLE", "My Account");
include_once('../header.php');

// ✅ Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userID'];


// ✅ Fetch user and patient information
$user_query = $con->prepare("
    SELECT ua.user_id, ua.username, ua.first_name, ua.last_name, ua.email, ua.phone,
           p.patient_id, p.birthdate, p.gender, p.address
    FROM user_account ua
    LEFT JOIN patient_information p ON ua.user_id = p.user_id
    WHERE ua.user_id = ?
");
$user_query->bind_param("s", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// ✅ Fetch most recent appointment (if patient exists)
$recent_appointment = null;
if (!empty($user['patient_id'])) {
    $appt_query = $con->prepare("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, 
               s.service_category, a.status
        FROM appointments a
        INNER JOIN services s ON a.service_id = s.service_id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
        LIMIT 1
    ");
    $appt_query->bind_param("s", $user['patient_id']);
    $appt_query->execute();
    $appt_result = $appt_query->get_result();
    $recent_appointment = $appt_result->fetch_assoc();
}

// ✅ Debug output to browser console
echo "<script>
console.log('DEBUG: User ID => " . addslashes($user_id) . "');
console.log('DEBUG: Patient ID => " . addslashes($user['patient_id'] ?? 'NULL') . "');
console.log('DEBUG: User data exists => " . (!empty($user) ? 'YES' : 'NO') . "');
console.log('DEBUG: Found appointment => " . (!empty($recent_appointment) ? 'YES' : 'NO') . "');
console.log('DEBUG: Appointment ID => " . addslashes($recent_appointment['appointment_id'] ?? 'NULL') . "');
</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="accountstyle.css">
</head>
<body>

<div class="container">
    <!-- Account action buttons -->
    <div class="account-actions">
        <a href="#edit" class="btn btn-warning" onclick="openEditModal()">Edit Account</a>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>

    <!-- Account Info -->
    <div class="card">
        <h2 class="card-title">Account Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <strong>Username</strong>
                <?= htmlspecialchars($user['username'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>User ID</strong>
                <?= htmlspecialchars($user['user_id'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>Email</strong>
                <?= htmlspecialchars($user['email'] ?? ''); ?>
            </div>
        </div>
    </div>

    <!-- Recent Appointment -->
    <div class="card">
        <h2 class="card-title">Your Recent Appointment</h2>

        <?php if ($recent_appointment): ?>
            <div class="appointment-details">
                <p><strong>Appointment ID:</strong> <?= htmlspecialchars($recent_appointment['appointment_id']); ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($recent_appointment['appointment_date']); ?></p>
                <p><strong>Time:</strong> <?= htmlspecialchars($recent_appointment['appointment_time']); ?></p>
                <p><strong>Service:</strong> <?= htmlspecialchars($recent_appointment['service_category']); ?></p>
                <p><strong>Dentist:</strong> Dr. Michelle Landero</p>

                <?php
                $status = $recent_appointment['status'];
                $statusClass = match($status) {
                    'Pending' => 'status-pending',
                    'Confirmed' => 'status-confirmed',
                    'Cancelled' => 'status-cancelled',
                    'Completed' => 'status-completed',
                    default => 'status-default'
                };
                ?>
                <p class="status-badge <?= $statusClass; ?>"><strong>Status:</strong> <?= htmlspecialchars($status); ?></p>

                <?php
                if ($status == "Pending") {
                    echo "<p><strong>Your appointment has been scheduled. Please wait for confirmation.</strong></p>";
                } elseif ($status == "Confirmed") {
                    echo "<p><strong>Your appointment has been confirmed.</strong></p>";
                } elseif ($status == "Completed") {
                    echo "<p><strong>Your appointment has been completed.</strong></p>";
                } elseif ($status == "Cancelled") {
                    echo "<p><strong>Your appointment has been cancelled.</strong></p>";
                }
                ?>
            </div>

            <div class="action-buttons">
                <a href="cancelAppointment.php?id=<?= $recent_appointment['appointment_id']; ?>" 
                   class="btn btn-danger <?= ($status == 'Cancelled' || $status == 'Completed') ? 'disabled' : ''; ?>"
                   <?= ($status == 'Cancelled' || $status == 'Completed') ? 'onclick="return false;"' : "onclick=\"return confirm('Are you sure you want to cancel?');\""; ?>>
                    Cancel Appointment
                </a>

                <a href="reschedule.php?id=<?= $recent_appointment['appointment_id']; ?>" 
                   class="btn btn-primary <?= ($status == 'Cancelled' || $status == 'Completed') ? 'disabled' : ''; ?>">
                    Reschedule Appointment
                </a>
            </div>

        <?php else: ?>
            <div class="no-appointment">
                <p>You have no recent appointments.</p>
                <a href="../index.php" class="btn btn-primary">Book an Appointment</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Account Modal -->
<div id="editModal" class="edit-modal">
    <div class="edit-modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>EDIT ACCOUNT</h3>
        <form action="updateAccount.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Birthdate:</label>
                    <input type="date" name="birthdate" value="<?= htmlspecialchars($user['birthdate'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="Male" <?= (($user['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?= (($user['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group full-width">
                <label>Address:</label>
                <textarea name="address" required><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn-submit">UPDATE ACCOUNT</button>
        </form>
    </div>
</div>

<script>
function openEditModal() {
    const modal = document.getElementById("editModal");
    modal.style.display = "flex";
    setTimeout(() => modal.classList.add("show"), 10);
}
function closeEditModal() {
    const modal = document.getElementById("editModal");
    modal.classList.remove("show");
    setTimeout(() => modal.style.display = "none", 300);
}
window.onclick = function(event) {
    const modal = document.getElementById("editModal");
    if (event.target === modal) closeEditModal();
};
</script>

<?php include_once('../footer.php'); ?>
</body>
</html>

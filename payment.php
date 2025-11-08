<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect to login page
    header("Location: login.php");
    exit("You must be logged in to view this page.");
}

define("TITLE", "Payment");
include_once('header.php');

$fname = $lname = $birthdate = $age = $email = $gender = $phone = '';
$address = $service_id = $subService = $branch = $date = $time = '';
$price = 500;

$dentist = 'Dr. Michelle Landero';

$timeRanges = [
    'firstBatch' => '8:00AM-9:00AM',
    'secondBatch' => '9:00AM-10:00AM',
    'thirdBatch' => '10:00AM-11:00AM',
    'fourthBatch' => '11:00AM-12:00PM',
    'fifthBatch' => '1:00PM-2:00PM',
    'sixthBatch' => '2:00PM-3:00PM',
    'sevenBatch' => '3:00PM-4:00PM',
    'eightBatch' => '4:00PM-5:00PM',
    'nineBatch' => '5:00PM-6:00PM',
    'tenBatch' => '6:00PM-7:00PM',
    'lastBatch' => '7:00PM-8:00PM'
];

// Calculate age
if (!empty($birthdate) && $birthdate !== 'N/A') {
    $birthDateObj = new DateTime($birthdate);
    $todayObj = new DateTime();
    $age = $todayObj->diff($birthDateObj)->y;
} else {
    $age = 'N/A';
}

// Override with POST values if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $fname = htmlspecialchars($_POST['fname'] ?? $fname);
    $lname = htmlspecialchars($_POST['lname'] ?? $lname);
    $birthdate = htmlspecialchars($_POST['birthdate'] ?? $birthdate);
    $age = htmlspecialchars($_POST['age'] ?? $age);
    $email = htmlspecialchars($_POST['email'] ?? $email);
    $gender = htmlspecialchars($_POST['gender'] ?? $gender);
    $phone = htmlspecialchars($_POST['phone'] ?? $phone);
    $address = htmlspecialchars($_POST['address'] ?? $address);

    $subService = htmlspecialchars($_POST['sub_service'] ?? 'N/A');

    // Map subService to service_id
    switch ($subService) {
        case 'Cleaning':   $service_id = 'S001'; break;
        case 'Checkups':   $service_id = 'S001'; break;
        case 'Extraction': $service_id = 'S001'; break;
        case 'Braces':     $service_id = 'S002'; break;
        default:           $service_id = 'N/A'; break;
    }

    // Map service_id to service_name
    switch ($service_id) {
        case 'S001': $service_name = 'General Dentistry'; break;
        case 'S002': $service_name = 'Orthodontics'; break;
        default:     $service_name = 'Unknown Service'; break;
    }

    $branch = htmlspecialchars($_POST['branch'] ?? 'N/A');
    $date = htmlspecialchars($_POST['date'] ?? 'N/A');
    $time = isset($_POST['time']) && isset($timeRanges[$_POST['time']]) ? $timeRanges[$_POST['time']] : 'N/A';

    // Format branch names
    if (strtolower($branch) === 'comembo') {
        $branch = 'Comembo Branch';
    } elseif (strtolower($branch) === 'taytay') {
        $branch = 'Taytay Rizal Branch';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm & Pay - SmileCare Dental</title>
    <link rel="stylesheet" href="paymentstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<br><br>
<h2>Confirm Your Appointment</h2>

<form id="paymentForm" action="appointmentProcess.php" method="POST" enctype="multipart/form-data">
<div class="container">
    <!-- Appointment Summary -->
    <div class="card">
        <h3>Appointment Summary</h3>

        <div class="detail-item"><strong>Patient:</strong> <?= strtoupper("$fname $lname") ?>
            <input type="hidden" name="fname" value="<?= $fname ?>">
            <input type="hidden" name="lname" value="<?= $lname ?>">
        </div>

        <div class="detail-item"><strong>Age:</strong> <?= $age ?>
            <input type="hidden" name="age" value="<?= $age ?>">
            <input type="hidden" name="birthdate" value="<?= $birthdate ?>">
        </div>

        <div class="detail-item"><strong>Gender:</strong> <?= strtoupper($gender) ?>
            <input type="hidden" name="gender" value="<?= $gender ?>">
        </div>

        <div class="detail-item"><strong>Contact:</strong>
            <div><?= strtoupper($email) ?></div>
            <div><?= strtoupper($phone) ?></div>
            <input type="hidden" name="email" value="<?= $email ?>">
            <input type="hidden" name="phone" value="<?= $phone ?>">
        </div>

        <div class="detail-item"><strong>Address:</strong> <?= strtoupper("$address") ?>
            <input type="hidden" name="street" value="<?= $address ?>">
        </div>

        <div class="detail-item"><strong>Service:</strong> <span class="service-badge"><?= ucwords($service_name) ?></span>
            <input type="hidden" name="service_id" value="<?= $service_id ?>">
        </div>

        <div class="detail-item"><strong>Sub Service:</strong> <span class="service-badge"><?= ucwords($subService) ?></span>
            <input type="hidden" name="subService" value="<?= $subService ?>">
            <input type="hidden" name="subservice_id" value="<?= $subservice_id ?>">
        </div>

        <div class="detail-item"><strong>Dentist:</strong> <?= strtoupper($dentist) ?>
            <input type="hidden" name="dentist" value="<?= $dentist ?>">
        </div>

        <div class="detail-item"><strong>Branch:</strong> <?= strtoupper($branch) ?>
            <input type="hidden" name="branch" value="<?= $branch ?>">
        </div>

        <div class="detail-item"><strong>Date:</strong> <?= date('F j, Y', strtotime($date)) ?>
            <input type="hidden" name="date" value="<?= $date ?>">
        </div>

        <div class="detail-item"><strong>Time Slot:</strong> <?= $time ?>
            <input type="hidden" name="time" value="<?= htmlspecialchars($_POST['time'] ?? '') ?>">
        </div>

        <div class="price-display"><strong>Consulatation Fee:</strong> ₱<?= number_format($price, 2) ?></div>
        <p>This appointment fee will be deducted from the total payment.</p>
    </div>

    <!-- Payment Section -->
    <div class="card">
        <h3>Payment Information</h3>

        <label for="paymentMethod">Payment Method</label>
        <select name="paymentMethod" id="paymentMethod" required>
            <option value="">Select payment method</option>
            <option value="GCash">GCash</option>
            <option value="PayMaya">PayMaya</option>
        </select>

        <!-- GCash Section -->
        <div id="gcashDetails" style="display: none;">
            <label>Our GCash Number</label>
            <input type="text" value="0917 123 4567" disabled>

            <label>Scan to Pay via GCash:</label><br>
            <img src="gcash.jpg" alt="GCash QR Code" style="width: 200px; border: 1px solid #ccc; padding: 5px;">

            <label for="gcashaccName">Account Name:</label>
            <input type="text" name="gcashaccName" id="gcashaccName" placeholder="Your Account Name">

            <label for="gcashNum">GCash Number:</label>
            <input type="text" name="gcashNum" id="gcashNum" placeholder="Your GCash Account Number" maxlength="11" pattern="\d{11}">

            <label for="gcashAmount">Payment Amount You've Sent</label>
            <input type="number" name="gcashAmount" id="gcashAmount" placeholder="Amount Sent" min="500" step="0.01">

            <label for="gcashrefNum">Reference Number</label>
            <input type="text" name="gcashrefNum" id="gcashrefNum" placeholder="Reference No.">

            <label for="proofImage">Upload Receipt</label>
            <input type="file" name="proofImage" id="proofImage">

            <div class="form-group" style="display: flex; align-items: center; margin: 1.5rem 0;">
                <input type="checkbox" id="gcashConfirm" style="margin: 0; padding: 0; width: 40px;" onchange="togglePayButton('gcash')">
                <label for="gcashConfirm" style="line-height: 1.6; margin-left: 6px;">I confirm that the above details are correct and I agree to proceed with the payment.</label>
            </div>

            <button type="submit" class="btn" id="gcashPayBtn" disabled>Pay Now</button>
        </div>

        <!-- PayMaya Section -->
        <div id="mayaDetails" style="display: none;">
            <label>Our PayMaya Number</label>
            <input type="text" value="0915 067 2948" disabled>

            <label>Scan to Pay via PayMaya:</label><br>
            <img src="maya.png" alt="Maya QR Code" style="width: 200px; border: 1px solid #ccc; padding: 5px;">

            <label for="mayaaccName">Account Name:</label>
            <input type="text" name="mayaaccName" id="mayaaccName" placeholder="Your Account Name">

            <label for="mayaNum">PayMaya Number:</label>
            <input type="text" name="mayaNum" id="mayaNum" placeholder="Your PayMaya Account Number">

            <label for="mayaAmount">Payment Amount</label>
            <input type="number" name="mayaAmount" id="mayaAmount" placeholder="Amount Sent" min="500" step="0.01">

            <label for="mayarefNum">Reference Number</label>
            <input type="text" name="mayarefNum" id="mayarefNum" placeholder="Reference No.">

            <label for="proofImageMaya">Upload Receipt</label>
            <input type="file" name="proofImageMaya" id="proofImageMaya">

            <div class="form-group" style="display: flex; align-items: center; margin: 1.5rem 0;">
                <input type="checkbox" id="mayaConfirm" style="margin: 0; padding: 0; width: 40px;" onchange="togglePayButton('maya')">
                <label for="mayaConfirm" style="line-height: 1.6; margin-left: 6px;">I confirm that the above details are correct and I agree to proceed with the payment.</label>
            </div>

            <button type="submit" class="btn" id="mayaPayBtn" disabled>Pay Now</button>
        </div>
    </div>

    <!-- Hidden IDs -->
    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">
    <input type="hidden" name="appointment_id" value="<?= $appointment_id ?? '' ?>">
</div>
</form>

<br><br>
<?php include_once('footer.php'); ?>

<script>
const paymentMethodSelect = document.getElementById('paymentMethod');

const gcashFields = ['gcashaccName', 'gcashNum', 'gcashAmount', 'gcashrefNum', 'proofImage'];
const mayaFields = ['mayaaccName', 'mayaNum', 'mayaAmount', 'mayarefNum', 'proofImageMaya'];

function toggleFields(fields, show) {
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.required = show;
            el.disabled = !show;
        }
    });
}

paymentMethodSelect.addEventListener('change', function () {
    const method = this.value;
    document.getElementById('gcashDetails').style.display = 'none';
    document.getElementById('mayaDetails').style.display = 'none';

    toggleFields(gcashFields, false);
    toggleFields(mayaFields, false);

    document.getElementById('gcashPayBtn').disabled = true;
    document.getElementById('mayaPayBtn').disabled = true;
    document.getElementById('gcashConfirm').checked = false;
    document.getElementById('mayaConfirm').checked = false;

    if (method === 'GCash') {
        document.getElementById('gcashDetails').style.display = 'block';
        toggleFields(gcashFields, true);
    } else if (method === 'PayMaya') {
        document.getElementById('mayaDetails').style.display = 'block';
        toggleFields(mayaFields, true);
    }
});

function togglePayButton(type) {
    const btn = document.getElementById(type + 'PayBtn');
    const confirm = document.getElementById(type + 'Confirm');
    if (btn && confirm) {
        btn.disabled = !confirm.checked;
    }
}
</script>

</body>
</html>

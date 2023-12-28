<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Establish a database connection
include '../include/connection.php';

// Fetch the list of available facilities
$facilitySql = "SELECT * FROM facility";
$facilityResult = $conn->query($facilitySql);

// Fetch the booked time slots for the selected facility
if (isset($_GET['facility'])) {
    $selectedFacility = $_GET['facility'];

    $bookedSlotsSql = "SELECT booking_id, booking_date, start_time, end_time 
                      FROM booking 
                      WHERE facility_id = $selectedFacility";

    $bookedSlotsResult = $conn->query($bookedSlotsSql);
}

$userId = $_SESSION["user_id"];
$userBookedSlotsSql = "SELECT booking_id, f.facility_name, b.booking_date, b.start_time, b.end_time 
                      FROM booking b
                      JOIN facility f ON b.facility_id = f.facility_id
                      WHERE b.user_id = $userId";

$userBookedSlotsResult = $conn->query($userBookedSlotsSql);

// Cancel booking
if (isset($_POST['cancel_booking'])) {
    $bookingId = $_POST['cancel_booking'];

    // Fetch booking details
    $bookingDetailsSql = "SELECT booking_date, start_time FROM booking WHERE booking_id = $bookingId";
    $bookingDetailsResult = $conn->query($bookingDetailsSql);

    if ($bookingDetailsResult->num_rows > 0) {
        $bookingDetails = $bookingDetailsResult->fetch_assoc();
        $bookingDate = $bookingDetails['booking_date'];
        $bookingStartTime = $bookingDetails['start_time'];

        // Check if the booking date and time haven't arrived yet
        $currentDateTime = date("Y-m-d H:i:s");
        $bookingDateTime = $bookingDate . ' ' . $bookingStartTime;

        if ($currentDateTime < $bookingDateTime) {
            // Perform the cancellation
            $cancelBookingSql = "DELETE FROM booking WHERE booking_id = $bookingId";
            if ($conn->query($cancelBookingSql) === TRUE) {
                echo '<script>alert("Booking canceled successfully!"); window.location.href = "facility_booking.php";</script>';
                exit();
            } else {
                echo '<script>alert("Error canceling booking."); window.location.href = "facility_booking.php";</script>';
                exit();
            }
        } else {
            echo '<script>alert("Cannot cancel booking, as the date and time have already passed."); window.location.href = "facility_booking.php";</script>';
            exit();
        }
    } else {
        echo '<script>alert("Booking details not found."); window.location.href = "facility_booking.php";</script>';
        exit();
    }
}
?>

<?php include '../include/header.php'; ?>

<div class="container">
    <?php include '../include/sidebar.php'; ?>

    <main>
        <h2>Facility Booking</h2>
        <?php if ($facilityResult->num_rows > 0): ?>
            <!-- Facility selection form -->
            <form action="facility_booking.php" method="get">
                <label for="facility">Select Facility:</label>
                <select id="facility" name="facility" required>
                    <?php
                    // Store facility results in an array
                    $facilityOptions = [];
                    while ($row = $facilityResult->fetch_assoc()) {
                        $facilityOptions[] = $row;
                    }

                    // Reset the internal pointer
                    reset($facilityOptions);

                    // Display facility options
                    foreach ($facilityOptions as $option) {
                        echo '<option value="' . $option['facility_id'] . '">' . $option['facility_name'] . '</option>';
                    }
                    ?>
                </select>
                <input type="submit" value="View Booked Slots">
            </form>

            <?php if (isset($bookedSlotsResult)): ?>
                <!-- Display other resident booked time slots -->
                <h3>Booked Time Slots:</h3>
                <?php if ($bookedSlotsResult->num_rows > 0): ?>
                    <ul>
                        <?php while ($slot = $bookedSlotsResult->fetch_assoc()): ?>
                            <li>
                                Date: <?php echo $slot['booking_date']; ?>,
                                Time: <?php echo $slot['start_time'] . ' - ' . $slot['end_time']; ?>
                                <?php
                                // Check if the current date and time are before the booked date and time
                                $currentDateTime = date("Y-m-d H:i:s");
                                $bookingDateTime = $slot['booking_date'] . ' ' . $slot['start_time'];

                                if ($currentDateTime < $bookingDateTime): ?>
                                    <form action="facility_booking.php" method="post">
                                        <input type="hidden" name="cancel_booking" value="<?php echo $slot['booking_id']; ?>">
                                        <input type="submit" value="Cancel Booking">
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No booked time slots for the selected facility.</p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($userBookedSlotsResult)): ?>
                <!-- Display user's booked time slots -->
                <h3>Your Booked Time Slots:</h3>
                <?php if ($userBookedSlotsResult->num_rows > 0): ?>
                    <ul>
                        <?php while ($slot = $userBookedSlotsResult->fetch_assoc()): ?>
                            <li>
                                Facility: <?php echo $slot['facility_name']; ?>,
                                Date: <?php echo $slot['booking_date']; ?>,
                                Time: <?php echo $slot['start_time'] . ' - ' . $slot['end_time']; ?>
                                <?php
                                // Check if the current date and time are before the booked date and time
                                $currentDateTime = date("Y-m-d H:i:s");
                                $bookingDateTime = $slot['booking_date'] . ' ' . $slot['start_time'];

                                if ($currentDateTime < $bookingDateTime): ?>
                                    <form action="facility_booking.php" method="post">
                                        <input type="hidden" name="cancel_booking" value="<?php echo $slot['booking_id']; ?>">
                                        <input type="submit" value="Cancel Booking">
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No booked time slots for you.</p>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Facility booking form -->
            <form action="book_facility.php" method="post">
                <label for="facility">Select Facility:</label>
                <select id="facility" name="facility" required>
                    <?php
                    // Display facility options from the stored array
                    foreach ($facilityOptions as $option) {
                        echo '<option value="' . $option['facility_id'] . '">' . $option['facility_name'] . '</option>';
                    }
                    ?>
                </select><br>

                <label for="booking_date">Booking Date:</label>
                <input type="date" id="booking_date" name="booking_date" required><br>

                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required><br>

                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required><br>

                <input type="submit" value="Book Facility">
            </form>
        <?php else: ?>
            <p>No facilities available for booking.</p>
        <?php endif; ?>
    </main>
</div>

<?php include '../include/footer.php'; ?>

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

    $bookedSlotsSql = "SELECT booking_date, start_time, end_time 
                      FROM booking 
                      WHERE facility_id = $selectedFacility";

    $bookedSlotsResult = $conn->query($bookedSlotsSql);
}

// Close the database connection
$conn->close();
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
                <!-- Display booked time slots -->
                <h3>Booked Time Slots:</h3>
                <?php if ($bookedSlotsResult->num_rows > 0): ?>
                    <ul>
                        <?php while ($slot = $bookedSlotsResult->fetch_assoc()): ?>
                            <li>Date: <?php echo $slot['booking_date']; ?>, Time: <?php echo $slot['start_time'] . ' - ' . $slot['end_time']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No booked time slots for the selected facility.</p>
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

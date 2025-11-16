<x-app-layout>
<form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
    @csrf

    <label>Customer Name</label>
    <input type="text" name="customer_name" required>

    <label>Customer Email</label>
    <input type="email" name="customer_email" required>

    <label>Booking Date</label>
    <input type="date" name="booking_date" required>

    <label>Booking Type</label>
    <select name="booking_type" id="booking_type" required>
        <option value="full_day">Full Day</option>
        <option value="half_day">Half Day</option>
        <option value="custom">Custom</option>
    </select>

    <!-- Slot (half day only) -->
    <div id="slotContainer" style="display:none; margin-top:10px;">
        <label>Booking Slot</label>
        <select name="slot">
            <option value="first_half">First Half</option>
            <option value="second_half">Second Half</option>
        </select>
    </div>

    <!-- Custom time (custom only) -->
    <div id="customTimeContainer" style="display:none; margin-top:10px;">
        <label>From Time</label>
        <input type="time" name="from_time">

        <label>To Time</label>
        <input type="time" name="to_time">
    </div>

    <button type="submit" style="margin-top:15px;">Submit Booking</button>
</form>


<script>
    const bookingType = document.getElementById("booking_type");
    const slotContainer = document.getElementById("slotContainer");
    const customTimeContainer = document.getElementById("customTimeContainer");

    function updateVisibility() {
        const type = bookingType.value;

        if (type === "half_day") {
            slotContainer.style.display = "block";
        } else {
            slotContainer.style.display = "none";
        }

        if (type === "custom") {
            customTimeContainer.style.display = "block";
        } else {
            customTimeContainer.style.display = "none";
        }
    }

    // run on change
    bookingType.addEventListener("change", updateVisibility);

    // run on page load
    updateVisibility();
</script>
</x-app-layout>
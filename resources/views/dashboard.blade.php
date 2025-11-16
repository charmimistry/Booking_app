<x-app-layout>


    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                 <h2 class="text-2xl font-bold mb-6">Create Booking</h2>
                    <form id="bookingForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf

                        <div>
                            <label class="block mb-1 text-sm font-medium">Customer Name</label>
                            <input type="text" name="customer_name"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                required>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium">Customer Email</label>
                            <input type="email" name="customer_email"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                required>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium">Booking Date</label>
                            <input type="date" name="booking_date" id="booking_date"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                required>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium">Booking Type</label>
                            <select name="booking_type" id="booking_type"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                required>
                                <option value="full_day">Full Day</option>
                                <option value="half_day">Half Day</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div id="slotContainer" class="col-span-1 md:col-span-2 hidden">
                            <label class="block mb-1 text-sm font-medium">Booking Slot</label>
                            <select name="slot"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="first_half">First Half</option>
                                <option value="second_half">Second Half</option>
                            </select>
                        </div>

                        <div id="customTimeContainer" class="col-span-1 md:col-span-2 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block mb-1 text-sm font-medium">From Time</label>
                                    <input type="time" name="from_time"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium">To Time</label>
                                    <input type="time" name="to_time"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                Submit Booking
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const bookingDateInput = document.getElementById('booking_date');
        const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
        bookingDateInput.setAttribute('min', today);
        document.getElementById("bookingForm").addEventListener("submit", async function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            try {
                let response = await fetch("{{ route('bookings.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        "Accept": "application/json"   
                    },
                    body: formData
                });

                let json = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: "success",
                        title: "Booking Added",
                        text: json.message,
                        timer: 2500,
                        showConfirmButton: false
                    });

                    document.getElementById("bookingForm").reset();
                    updateVisibility();

                } else {
                    if (response.status === 422) {
                        let msg = Object.values(json.errors).join("\n");
                        Swal.fire("Validation Error", msg, "error");
                    } else {
                        Swal.fire("Failed", json.message ?? "Something went wrong", "error");
                    }
                }

            } catch (error) {
                Swal.fire("Error", "Server error, try again later", "error");
            }
        });


        const bookingType = document.getElementById("booking_type");
        const slotContainer = document.getElementById("slotContainer");
        const customTimeContainer = document.getElementById("customTimeContainer");

        function updateVisibility() {
            let type = bookingType.value;

            slotContainer.classList.toggle("hidden", type !== "half_day");
            customTimeContainer.classList.toggle("hidden", type !== "custom");
        }

        bookingType.addEventListener("change", updateVisibility);
        updateVisibility();
    </script>

</x-app-layout>

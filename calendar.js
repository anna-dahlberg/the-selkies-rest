document.addEventListener("DOMContentLoaded", function () {
    const calendar = document.getElementById("calendar");
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth(); // Current month (0-indexed)

    // Fetch booked dates from the server
    fetch("fetch_booked_dates.php")
        .then(response => response.json())
        .then(data => {
            const bookedDates = data.bookedDates; // Array of booked dates
            renderCalendar(year, month, bookedDates);
        })
        .catch(err => console.error("Error fetching booked dates:", err));

    function renderCalendar(year, month, bookedDates) {
        calendar.innerHTML = ""; // Clear the calendar

        const firstDay = new Date(year, month, 1).getDay(); // Day of week for 1st of the month
        const daysInMonth = new Date(year, month + 1, 0).getDate(); // Number of days in the month

        // Add empty cells for days before the first day
        for (let i = 0; i < firstDay; i++) {
            calendar.innerHTML += '<div class="day empty"></div>';
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
            const isBooked = bookedDates.includes(date);
            const dayClass = isBooked ? "day booked" : "day";
            calendar.innerHTML += `<div class="${dayClass}" data-date="${date}">${day}</div>`;
        }
    }
});

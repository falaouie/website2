document.addEventListener('DOMContentLoaded', function () {
  const dateDisplay = document.querySelector('.date-display');
  if (dateDisplay) {
    const days = [
      'Sunday',
      'Monday',
      'Tuesday',
      'Wednesday',
      'Thursday',
      'Friday',
      'Saturday',
    ];
    const now = new Date();
    const dayName = days[now.getDay()];
    const formattedDate = `${String(now.getDate()).padStart(2, '0')}/${String(
      now.getMonth() + 1
    ).padStart(2, '0')}/${now.getFullYear()}`;

    dateDisplay.textContent = `${dayName} ${formattedDate}`;
  }

  const attendanceBtn = document.querySelector('.attendance-btn');
  if (attendanceBtn) {
    attendanceBtn.addEventListener('click', function () {
      // Placeholder for attendance module functionality
      alert('Attendance module clicked');
    });
  }
});

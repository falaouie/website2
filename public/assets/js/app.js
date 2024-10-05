document.addEventListener('DOMContentLoaded', function () {
  const dateDisplay = document.querySelector('.login-date-display');
  if (dateDisplay) {
    const now = new Date();
    const options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    };
    dateDisplay.textContent = now.toLocaleDateString('en-US', options);
  }

  // Inactivity Timer
  let inactivityTimer;
  const inactivityTime = 5 * 60 * 1000; // 5 minutes in milliseconds

  function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(logoutDueToInactivity, inactivityTime);
  }

  function logoutDueToInactivity() {
    fetch('logout.php?reason=inactivity', { method: 'POST' }).then(() => {
      window.location.href = 'login.php';
    });
  }

  // Reset timer on user activity
  ['mousemove', 'keypress', 'click', 'touchstart'].forEach((event) => {
    document.addEventListener(event, resetInactivityTimer, true);
  });

  // Initial setup of timer
  resetInactivityTimer();
});

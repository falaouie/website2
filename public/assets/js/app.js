document.addEventListener('DOMContentLoaded', function () {
  // Inactivity Timer
  let inactivityTimer;
  const inactivityTime = 5 * 60 * 1000; // 5 minutes in milliseconds

  function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(logoutDueToInactivity, inactivityTime);
  }

  function logoutDueToInactivity() {
    fetch('/logout.php?reason=inactivity', { method: 'POST' }).then(() => {
      window.location.href = '/login.php';
    });
  }

  // Reset timer on user activity
  ['mousemove', 'keypress', 'click', 'touchstart'].forEach((event) => {
    document.addEventListener(event, resetInactivityTimer, true);
  });

  // Initial setup of timer
  resetInactivityTimer();
});

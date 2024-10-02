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
});

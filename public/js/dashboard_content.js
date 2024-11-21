document.addEventListener("DOMContentLoaded", function () {
  // Iterate through all modals on the page
  document.querySelectorAll('.modal').forEach(function (modal) {
    // Check if the modal's h5 contains the word "Konfirmasi"
    const heading = modal.querySelector('h5');
    if (heading && heading.textContent.includes('Konfirmasi')) {
      const modalContent = modal.querySelector('.modal-content');

      // Apply styles directly to the modal-content
      if (modalContent) {
        modalContent.style.textAlign = 'center';
      }

      // Apply styles directly to the h5
      if (heading) {
        heading.style.textAlign = 'center';
      }
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // Iterate through all modals on the page
  document.querySelectorAll('.modal').forEach(function (modal) {
    const heading = modal.querySelector('h5');
    if (heading) {
      const modalDialog = modal.querySelector('.modal-dialog');

      // Check if the modal's h5 contains the word "Ubah"
      if (heading.textContent.includes('Ubah')) {
        // Apply styles directly to the modal-dialog
        if (modalDialog) {
          modalDialog.style.maxWidth = '65dvw';
        }
      } else {
        // Apply styles directly to the modal-dialog
        if (modalDialog) {
          modalDialog.style.maxWidth = 'fit-content';
          modalDialog.style.minWidth = '35dvw';
        }
      }
    }
  });
});

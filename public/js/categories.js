document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("category-modal");
  const openBtn = document.getElementById("open-modal-btn");
  const closeBtn = document.getElementById("close-modal-btn");
  const cancelBtn = document.getElementById("cancel-modal-btn");

  function showModal() {
    modal.style.display = "flex";
  }

  function hideModal() {
    modal.style.display = "none";
  }

  openBtn.addEventListener("click", showModal);
  closeBtn.addEventListener("click", hideModal);
  cancelBtn.addEventListener("click", hideModal);

  modal.addEventListener("click", function (e) {
    if (e.target === modal) {
      hideModal();
    }
  });

  // Auto-open modal if there are validation errors
  if (document.querySelector('[data-has-errors="true"]')) {
    showModal();
  }
});

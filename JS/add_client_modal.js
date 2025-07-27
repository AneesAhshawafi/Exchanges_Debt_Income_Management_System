const openBtn = document.getElementById("addClientBtn");
const closeBtn = document.getElementById("closeAddClientBtn");
const modal = document.getElementById("add-client-overlay");

openBtn.addEventListener("click", () => {
  modal.classList.remove("hidden");
});

closeBtn.addEventListener("click", () => {
  modal.classList.add("hidden");
});

window.addEventListener("click", function (e) {
  if (e.target === modal) {
    modal.classList.add("hidden");
  }
});


// استرجاع الحسابات من التخزين المحلي
let users = JSON.parse(localStorage.getItem("users")) || [];

const resetForm = document.getElementById("resetForm");
const emailInput = document.getElementById("email");
const newPasswordInput = document.getElementById("newPassword");
const togglePassword = document.getElementById("togglePassword");

// إظهار/إخفاء كلمة المرور
if (togglePassword) {
  togglePassword.addEventListener("click", () => {
    const type = newPasswordInput.getAttribute("type") === "password" ? "text" : "password";
    newPasswordInput.setAttribute("type", type);
  });
}

// عند إرسال النموذج
resetForm.addEventListener("submit", function (e) {
  e.preventDefault();
  const email = emailInput.value.trim();
  const newPassword = newPasswordInput.value.trim();

  const userIndex = users.findIndex(u => u.email === email);

  if (userIndex === -1) {
    alert("⚠️ هذا البريد غير مسجل");
    return;
  }

  users[userIndex].password = newPassword;
  localStorage.setItem("users", JSON.stringify(users));
  alert("✅ تم تحديث كلمة المرور بنجاح");
  window.location.href = "login.html";
});

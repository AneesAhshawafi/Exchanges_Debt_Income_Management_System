// استرجاع الحسابات من التخزين المحلي
let users = JSON.parse(localStorage.getItem("users")) || [];

const signupForm = document.getElementById("signupForm");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const confirmInput = document.getElementById("confirmPassword");
const togglePassword = document.getElementById("togglePassword");

// إظهار/إخفاء كلمة المرور
if (togglePassword) {
  togglePassword.addEventListener("click", () => {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
  });
}

// عند إرسال نموذج التسجيل
signupForm.addEventListener("submit", function (e) {
  e.preventDefault();
  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();
  const confirm = confirmInput.value.trim();

  if (password !== confirm) {
    alert("❌ كلمتا المرور غير متطابقتين");
    return;
  }

  const exists = users.some(user => user.email === email);
  if (exists) {
    alert("⚠️ هذا البريد مسجل مسبقًا");
    return;
  }

  const newUser = { email, password };
  users.push(newUser);
  localStorage.setItem("users", JSON.stringify(users));
  alert("✅ تم إنشاء الحساب بنجاح");
  window.location.href = "login.html";
});

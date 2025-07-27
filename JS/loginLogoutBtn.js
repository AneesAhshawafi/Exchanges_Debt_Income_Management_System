const translations = {
    ar: {
      
        "login-btn": "تسجيل الدخول",
        "logout-btn": "تسجيل الخروج"

    }

  
};
document.addEventListener("DOMContentLoaded", () => {
  const loginLink = document.getElementById("login-link");
  const loginLi = document.getElementById("login-li");
  const user = JSON.parse(localStorage.getItem("loggedInUser"));

  if (user && loginLi) {
    // إزالة زر تسجيل الدخول
    if (loginLink) loginLink.remove();

    // التحقق من وجود زر تسجيل الخروج مسبقًا
    if (!document.getElementById("logout-link")) {
      const lang =  "ar";

      const logoutLink = document.createElement("a");
      logoutLink.id = "logout-link";
      logoutLink.setAttribute("data-lang", "logout-btn");
      logoutLink.classList.add("login-buttn");
      logoutLink.href = "#";
      // تعيين النص بناءً على الترجمة
      logoutLink.textContent = translations[lang]["logout-btn"];

      // حدث تسجيل الخروج
      logoutLink.addEventListener("click", () => {
        localStorage.removeItem("loggedInUser");
        window.location.href = "index.html";
      });

      loginLi.insertBefore(logoutLink, loginLi.firstChild);
    }
  }
});

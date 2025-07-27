// Wait for the DOM (HTML) to be fully loaded before executing the function
document.addEventListener("DOMContentLoaded", () => {

  // Retrieve the currently logged-in user from localStorage
  const user = JSON.parse(localStorage.getItem("loggedInUser"));
  const lang = localStorage.getItem("preferredLanguage") || "ar";
  // If no user is found (not logged in)
  if (!user) {
    // Show an alert message indicating restricted access
    if (lang === "ar") {
      alert("🚫لا يمكن الوصول لهذه الصفحة بدون تسجيل الدخول ");

    } else {

      alert("🚫 You cannot access this page without logging in.");
    }

    // Redirect the user to the login page
    window.location.href = "login.html";
  }
});



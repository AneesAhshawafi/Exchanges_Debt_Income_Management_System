// Load users from localStorage or initialize as an empty array if none exist
const users = JSON.parse(localStorage.getItem("users")) || [];

// Get references to the login form and input fields
const loginForm = document.getElementById("loginForm");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const lang = localStorage.getItem("preferredLanguage") || "ar";

// Check if the login form exists before attaching the submit handler
if (loginForm) {
    // Handle the form submission
    loginForm.addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent the default form submission behavior which is reloading the page

        // Get trimmed values (remove spaces from the start and the end) from email and password inputs
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        // If either email or password is empty, show an alert and stop
        if (!email) {
            if (lang === "ar") {
                alert("يجب إدخال البريد الإلكتروني"); // Arabic: Please enter both email and password
                return;
            } else {
                alert("Email is required");
                return;
            }
        }
        if (!password) {
            if (lang === "ar") {
                alert("يجب إدخال كلمة المرور "); // Arabic: Please enter both email and password
                return;
            } else {
                alert("Password is Required");
                return;
            }
        }

        // Search for a matching user with the entered email and password
        const user = users.find(u => u.email === email && u.password === password);

        // If a matching user is found, login successful
        if (user) {
            if (lang === "ar") {
                alert("✅ تم تسجيل الدخول بنجاح"); // Arabic: Login successful

            } else {
                alert("Login Successful ✅ ");
            }
            // Store the logged-in user in localStorage
            localStorage.setItem("loggedInUser", JSON.stringify(user));
            // Redirect to the homepage
            window.location.href = "index.html";
        } else {
            // No match found — show error alert
            if (lang === "ar") {

                alert("❌ البريد الإلكتروني أو كلمة المرور غير صحيحة"); // Arabic: Incorrect email or password
            } else {
                alert("Email or Password is no correct ❌");
            }
        }
    });
}

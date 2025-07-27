//// File: /JS/darkMode.js
//
//(function () {
//  // Get the dark mode toggle checkbox element
//  const toggle = document.getElementById("darkModeToggle");
//
//  // Detect if the user's system prefers dark mode
//  const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)").matches;
//
//  // Get the user's previously selected theme from localStorage (if any)
//  const userTheme = localStorage.getItem("theme");
//
//  // Reference to the root element (<html>) to apply class
//  const root = document.documentElement;
//
//  // Function to enable dark mode
//  const enableDark = () => {
//      root.classList.add("dark-mode");           // Add dark-mode class to <html>
//      localStorage.setItem("theme", "dark");     // Save preference in localStorage
//      toggle.checked = true;                     // Check the toggle switch
//    };
//
//  // Function to enable light mode
//  const enableLight = () => {
//    root.classList.remove("dark-mode");        // Remove dark-mode class from <html>
//    localStorage.setItem("theme", "light");    // Save preference in localStorage
//    toggle.checked = false;                    // Uncheck the toggle switch
//  };
//
//  // Decide which theme to apply on page load
//  const setTheme = () => {
//    if (userTheme === "dark") enableDark();                // Use stored dark mode
//    else if (userTheme === "light") enableLight();         // Use stored light mode
//    else if (prefersDarkScheme) enableDark();      // Use system preference if no user setting
//    else enableLight();                                    // Default to light mode
//  };
//
//  // Set theme when the script loads
//  setTheme();
//
//  // Listen for changes on the toggle checkbox
//  toggle.addEventListener("change", () => {
//    if (toggle.checked) enableDark();   // If checkbox is checked, enable dark mode
//    else enableLight();                 // If unchecked, enable light mode
//  });
//})();

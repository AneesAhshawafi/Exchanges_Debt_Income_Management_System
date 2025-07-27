const menu = document.getElementById("menu");
const fa_bar = document.getElementById("fa-bar");

// فتح/إغلاق القائمة
fa_bar.addEventListener("click", function (e) {
  e.stopPropagation(); // حتى لا يغلق عند الضغط على الزر نفسه
  menu.classList.toggle("active");
});

// إغلاق عند الضغط خارج القائمة
document.addEventListener("click", function (e) {
  if (!menu.contains(e.target) && e.target !== fa_bar) {
    menu.classList.remove("active");
  }
});

//const toggle = document.getElementById("darkModeToggle");
//const body = document.body;
//
//// حمّل الوضع المحفوظ من التخزين المحلي
//const savedTheme = localStorage.getItem("theme");
//if (savedTheme === "dark") {
//  body.classList.add("dark-mode");
//  toggle.checked = true;
//}
//
//toggle.addEventListener("change", () => {
//  if (toggle.checked) {
//    body.classList.add("dark-mode");
//    localStorage.setItem("theme", "dark");
//  } else {
//    body.classList.remove("dark-mode");
//    localStorage.setItem("theme", "light");
//  }
//});
//
//
//

(function () {
  "use strict";
  var STORAGE_KEY = "hazleton-lang";

  function getLang() {
    try {
      var stored = localStorage.getItem(STORAGE_KEY);
      if (stored === "en" || stored === "ru") return stored;
    } catch (e) {}
    return "en";
  }

  function applyLang(lang) {
    var root = document.documentElement;
    root.setAttribute("data-lang", lang);
    root.setAttribute("lang", lang);
    document.querySelectorAll("[data-lang-btn]").forEach(function (btn) {
      btn.classList.toggle("active", btn.getAttribute("data-lang-btn") === lang);
    });
    try {
      localStorage.setItem(STORAGE_KEY, lang);
    } catch (e) {}
  }

  function init() {
    applyLang(getLang());
    document.querySelectorAll("[data-lang-btn]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        applyLang(btn.getAttribute("data-lang-btn"));
      });
    });
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.querySelector(".main-nav");
    if (toggle && nav) {
      toggle.addEventListener("click", function () {
        nav.classList.toggle("open");
      });
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

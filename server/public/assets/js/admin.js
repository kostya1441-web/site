(function () {
  "use strict";

  function reindex(container) {
    var rows = container.querySelectorAll(":scope > .repeater-row");
    rows.forEach(function (row, i) {
      row.querySelectorAll("[name]").forEach(function (el) {
        el.name = el.name.replace(/\[(items|buttons|groups)\]\[\d+\]/, "[$1][" + i + "]");
      });
    });
  }

  document.addEventListener("click", function (e) {
    var addBtn = e.target.closest("[data-add-row]");
    if (addBtn) {
      e.preventDefault();
      var containerId = addBtn.getAttribute("data-add-row");
      var container = document.getElementById(containerId);
      var tpl = document.getElementById(containerId + "-template");
      if (container && tpl) {
        var clone = tpl.content.cloneNode(true);
        container.appendChild(clone);
        reindex(container);
      }
    }
    var rmBtn = e.target.closest(".remove-row");
    if (rmBtn) {
      e.preventDefault();
      var row = rmBtn.closest(".repeater-row");
      var parent = row.parentElement;
      row.remove();
      reindex(parent);
    }
  });

  // Live preview of an image file input next to its existing value.
  document.addEventListener("change", function (e) {
    if (e.target.matches('input[type="file"][data-preview]')) {
      var previewId = e.target.getAttribute("data-preview");
      var img = document.getElementById(previewId);
      var file = e.target.files && e.target.files[0];
      if (img && file) {
        img.src = URL.createObjectURL(file);
      }
    }
  });
})();

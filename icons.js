(() => {
  "use strict";
  const renderIcons = () => {
    if (!window.lucide || typeof window.lucide.createIcons !== "function") return;
    document.querySelectorAll(".trust-icon").forEach((container, index) => {
      if (container.querySelector("svg")) return;
      const label = (container.parentElement?.innerText || "").toLowerCase();
      const icon = label.includes("100% madagascar") ? "map-pinned"
        : label.includes("responsable") ? "hand-heart"
        : label.includes("accompagnement") ? "compass"
        : label.includes("sur mesure") ? "route"
        : ["map-pinned", "hand-heart", "compass", "route"][index % 4];
      container.replaceChildren(Object.assign(document.createElement("i"), {
        className: "icon-lucide",
        dataset: { lucide: icon }
      }));
    });
    document.querySelectorAll("[data-lucide]").forEach((node) => {
      if (!node.hasAttribute("aria-hidden") && !node.getAttribute("aria-label")) {
        node.setAttribute("aria-hidden", "true");
      }
    });
    window.lucide.createIcons({
      attrs: { class: "icon lucide-icon", "stroke-width": 1.7 }
    });
  };
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", renderIcons, { once: true });
  } else {
    renderIcons();
  }
  window.addEventListener("load", renderIcons, { once: true });
})();
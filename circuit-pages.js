/* Published detailed circuit pages are shared by both MIARADI signature sliders. */
(() => {
  const apiUrl = "backend/public/api/circuit-pages.php";
  const detailUrl = (slug) => "backend/public/circuit-detail.php?slug=" + encodeURIComponent(slug);
  const imageUrl = (value) => {
    const path = String(value || "").trim();
    if (!path) return "";
    if (/^(https?:)?\/\//i.test(path) || path.startsWith("/")) return path;
    if (path.startsWith("assets/")) return path;
    if (path.startsWith("uploads/")) return "backend/public/" + path;
    return path;
  };
  const text = (tag, className, value) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    node.textContent = value || "";
    return node;
  };
  const getLanguage = () => document.documentElement.lang.toLowerCase().startsWith("en") ? "en" : "fr";

  function addHomeSlides(pages) {
    const slider = document.querySelector(".circuit-feature-slider");
    const track = slider?.querySelector(".circuit-feature-track");
    const dotsWrap = slider?.querySelector(".circuit-feature-dots");
    if (!slider || !track || !dotsWrap) return;
    const lang = getLanguage();
    pages.forEach((page) => {
      if (track.querySelector('[data-dynamic-slug="' + CSS.escape(page.slug) + '"]')) return;
      const title = page["title_" + lang] || page.title_fr;
      const summary = page["summary_" + lang] || page.summary_fr;
      const slide = document.createElement("article");
      slide.className = "nord-circuit-feature circuit-feature-slide";
      slide.dataset.dynamicSlug = page.slug;
      slide.setAttribute("aria-hidden", "true");
      const copy = document.createElement("div");
      copy.append(text("p", "section-kicker", lang === "en" ? "NEW SIGNATURE JOURNEY" : "NOUVEAU VOYAGE SIGNATURE"));
      copy.append(text("h3", "", title));
      copy.append(text("p", "", summary));
      const link = document.createElement("a");
      link.className = "button button-outline-light";
      link.href = detailUrl(page.slug);
      link.append(document.createTextNode(lang === "en" ? "Discover this journey " : "Découvrir le circuit "));
      const arrow = text("span", "", "↗"); arrow.setAttribute("aria-hidden", "true"); link.append(arrow);
      slide.append(copy, link);
      track.append(slide);
      const dot = document.createElement("button");
      dot.type = "button";
      dot.dataset.circuitSlide = String(track.querySelectorAll(".circuit-feature-slide").length - 1);
      dot.setAttribute("aria-label", (lang === "en" ? "Show " : "Afficher ") + title);
      dot.setAttribute("aria-pressed", "false");
      dotsWrap.append(dot);
    });
  }

  function addAboutSlides(pages) {
    const slider = document.querySelector("[data-circuit-slider]");
    const dotsWrap = slider?.querySelector(".signature-slider-dots");
    if (!slider || !dotsWrap) return;
    const lang = getLanguage();
    const controls = slider.querySelector(".signature-slider-controls");
    pages.forEach((page) => {
      if (slider.querySelector('[data-dynamic-slug="' + CSS.escape(page.slug) + '"]')) return;
      const title = page["title_" + lang] || page.title_fr;
      const summary = page["summary_" + lang] || page.summary_fr;
      const slide = document.createElement("article");
      slide.className = "signature-slide";
      slide.dataset.slide = "";
      slide.dataset.dynamicSlug = page.slug;
      slide.setAttribute("aria-hidden", "true");
      const image = document.createElement("img");
      image.className = "signature-slide-image";
      image.src = imageUrl(page.hero_image) || "assets/images/circuits/majunga.jpg";
      image.alt = title;
      image.loading = "lazy";
      image.decoding = "async";
      const shade = text("div", "signature-slide-shade", "");
      const content = text("div", "signature-slide-content", "");
      content.append(text("p", "section-kicker", lang === "en" ? "SIGNATURE JOURNEY · MADAGASCAR" : "VOYAGE SIGNATURE · MADAGASCAR"));
      content.append(text("h2", "", title));
      content.append(text("p", "", summary));
      const link = document.createElement("a");
      link.className = "button button-outline-light";
      link.href = detailUrl(page.slug);
      link.append(document.createTextNode(lang === "en" ? "Discover this journey " : "Découvrir le circuit "));
      const arrow = text("span", "", "↗"); arrow.setAttribute("aria-hidden", "true"); link.append(arrow);
      content.append(link);
      slide.append(image, shade, content);
      slider.insertBefore(slide, controls);
      const dot = document.createElement("button");
      dot.type = "button";
      dot.dataset.slideTo = String(slider.querySelectorAll("[data-slide]").length - 1);
      dot.setAttribute("aria-label", (lang === "en" ? "Show " : "Afficher ") + title);
      dotsWrap.append(dot);
    });
  }

  async function init() {
    try {
      const response = await fetch(apiUrl, { headers: { Accept: "application/json" }, cache: "no-store" });
      if (!response.ok) return;
      const payload = await response.json();
      if (!Array.isArray(payload.data) || !payload.data.length) return;
      addHomeSlides(payload.data);
      addAboutSlides(payload.data);
      document.dispatchEvent(new CustomEvent("miaradi:circuit-pages-loaded", { detail: { count: payload.data.length } }));
    } catch (error) {
      console.warn("Les pages détaillées des circuits ne sont pas disponibles.", error);
    }
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init, { once: true });
  else init();
})();

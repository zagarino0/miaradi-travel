(() => {
  "use strict";

  const API_BASE = "backend/public/api/";
  const LANGUAGE = (document.documentElement.lang || "fr").toLowerCase() === "en" ? "en" : "fr";
  const $ = (id) => document.getElementById(id);
  const params = new URLSearchParams(window.location.search);
  const requestedCircuit = (params.get("circuit") || "").trim();

  const image = $("gallery-stage-image");
  const video = $("gallery-stage-video");
  const stage = $("gallery-stage");
  const fallback = $("gallery-unavailable");
  const title = $("gallery-stage-title");
  const description = $("gallery-stage-description");
  const counter = $("gallery-page-counter");
  const thumbs = $("gallery-page-thumbnails");
  const suggestionsGrid = $("gallery-suggestions-grid");
  const prevButton = $("gallery-page-prev");
  const nextButton = $("gallery-page-next");
  const suggestionsPrev = $("gallery-suggestions-prev");
  const suggestionsNext = $("gallery-suggestions-next");

  let circuits = [];
  let activeCircuit = null;
  let allMedia = [];
  let activeIndex = 0;
  let frame = null;
  let autoplayTimer = null;
  let isManuallyPaused = false;
  const toggleButton = $("gallery-page-toggle");
  const AUTOPLAY_DELAY = 3000;

  const text = (value, backup = "") => String(value == null || value === "" ? backup : value);
  const normalize = (value) => text(value).normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().trim();
  const isVideo = (item) => item.type === "video" || /\.(mp4|webm|ogg)(?:[?#].*)?$/i.test(text(item.url));
  const rootUrl = (value) => {
    if (!value) return "";
    try { return new URL(value, document.baseURI).href; } catch (_) { return ""; }
  };

  async function getJson(url) {
    const response = await fetch(url, { headers: { Accept: "application/json" }, credentials: "same-origin", cache: "no-store" });
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) {
      throw new Error(payload.error?.message || "Impossible de charger les données de la galerie.");
    }
    if (payload.error) throw new Error(payload.error.message || "Une erreur est survenue.");
    return payload;
  }

  function clearCurrentMedia() {
    if (video) {
      video.pause();
      video.hidden = true;
      video.onerror = null;
      video.removeAttribute("src");
      video.load();
    }
    if (frame) {
      frame.remove();
      frame = null;
    }
    image.hidden = true;
    image.onerror = null;
    image.removeAttribute("src");
  }

  function showUnavailable(message) {
    clearCurrentMedia();
    fallback.hidden = false;
    fallback.querySelector("strong").textContent = message || (allMedia[activeIndex] && isVideo(allMedia[activeIndex])
      ? "Vidéo momentanément indisponible"
      : "Photo momentanément indisponible");
  }

  function videoEmbedUrl(rawUrl) {
    try {
      const url = new URL(rawUrl);
      const host = url.hostname.replace(/^www\./, "").toLowerCase();
      if (host === "youtu.be") {
        const id = url.pathname.split("/").filter(Boolean)[0];
        return id ? "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(id) + "?autoplay=1&mute=1&playsinline=1&rel=0" : "";
      }
      if (host === "youtube.com" || host === "m.youtube.com" || host === "youtube-nocookie.com") {
        let id = url.searchParams.get("v");
        if (!id && /^\/(embed|shorts|live)\//.test(url.pathname)) id = url.pathname.split("/")[2];
        return id ? "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(id) + "?autoplay=1&mute=1&playsinline=1&rel=0" : "";
      }
      if (host === "vimeo.com" || host === "player.vimeo.com") {
        const match = url.pathname.match(/(?:video\/)?(\d+)/);
        return match ? "https://player.vimeo.com/video/" + match[1] + "?autoplay=1&muted=1&playsinline=1" : "";
      }
    } catch (_) {}
    return "";
  }

  function renderMediaContent(item) {
    clearCurrentMedia();
    fallback.hidden = true;

    if (isVideo(item)) {
      const mediaUrl = rootUrl(item.url);
      if (/\.(mp4|webm|ogg)(?:[?#].*)?$/i.test(text(item.url))) {
        video.hidden = false;
        video.autoplay = true;
        video.muted = true;
        video.defaultMuted = true;
        video.playsInline = true;
        video.setAttribute("playsinline", "");
        video.setAttribute("autoplay", "");
        video.setAttribute("muted", "");
        video.onerror = () => showUnavailable("Vidéo momentanément indisponible");
        video.src = mediaUrl;
        video.load();
        const playAttempt = video.play();
        if (playAttempt && typeof playAttempt.catch === "function") {
          playAttempt.catch(() => {
            // Le navigateur peut bloquer la lecture automatique selon ses réglages.
            // La vidéo reste disponible avec les contrôles pour lancer la lecture manuellement.
          });
        }
        return;
      }

      const embed = videoEmbedUrl(text(item.url));
      if (embed) {
        frame = document.createElement("iframe");
        frame.className = "gallery-page-stage-embed";
        frame.src = embed;
        frame.title = text(item.title, "Vidéo MIARADI-TRAVEL");
        frame.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
        frame.allowFullscreen = true;
        frame.loading = "eager";
        frame.referrerPolicy = "strict-origin-when-cross-origin";
        frame.addEventListener("error", () => showUnavailable("Vidéo momentanément indisponible"), { once: true });
        stage.insertBefore(frame, fallback);
        return;
      }

      showUnavailable("Lien vidéo indisponible");
      return;
    }

    image.hidden = false;
    image.alt = text(item.alt, text(item.title));
    image.onerror = () => showUnavailable("Photo momentanément indisponible");
    image.src = rootUrl(item.url);
  }

  function updateCounter() {
    counter.textContent = allMedia.length
      ? String(activeIndex + 1).padStart(2, "0") + " / " + String(allMedia.length).padStart(2, "0")
      : "00 / 00";
  }

  function showMedia(nextIndex) {
    if (!allMedia.length) {
      activeIndex = 0;
      title.textContent = "Aucun média publié pour ce circuit";
      description.textContent = "Les photos et vidéos de ce circuit apparaîtront ici dès leur publication dans l’administration.";
      updateCounter();
      prevButton.disabled = true;
      nextButton.disabled = true;
      thumbs.replaceChildren();
      showUnavailable("Galerie en cours de préparation");
      return;
    }

    activeIndex = (nextIndex + allMedia.length) % allMedia.length;
    const item = allMedia[activeIndex];
    renderMediaContent(item);

    title.textContent = text(item.title, isVideo(item) ? "Vidéo du circuit" : "Photo du circuit");
    description.textContent = [item.circuit && item.circuit.title, text(item.description, activeCircuit && activeCircuit.summary || "Découvrez ce paysage de Madagascar.")].filter(Boolean).join(" · ");
    updateCounter();

    thumbs.querySelectorAll("[data-index]").forEach((button) => {
      const selected = Number(button.dataset.index) === activeIndex;
      button.classList.toggle("is-active", selected);
      button.setAttribute("aria-pressed", String(selected));
    });

    prevButton.disabled = allMedia.length < 2;
    nextButton.disabled = allMedia.length < 2;
    scheduleAutoAdvance();
  }

  function stopAutoAdvance() {
    if (autoplayTimer !== null) {
      window.clearTimeout(autoplayTimer);
      autoplayTimer = null;
    }
  }

  function scheduleAutoAdvance() {
    stopAutoAdvance();
    if (isManuallyPaused || allMedia.length < 2 || document.hidden ||
        window.matchMedia("(prefers-reduced-motion: reduce)").matches ||
        stage.matches(":hover") || stage.contains(document.activeElement)) return;

    // Les vidéos restent sous le contrôle du visiteur : seul le diaporama photo avance seul.
    if (isVideo(allMedia[activeIndex])) return;

    autoplayTimer = window.setTimeout(() => {
      autoplayTimer = null;
      showMedia(activeIndex + 1);
    }, AUTOPLAY_DELAY);
  }

  function renderThumbnails() {
    thumbs.replaceChildren();
    allMedia.forEach((item, index) => {
      const button = document.createElement("button");
      button.type = "button";
      button.className = "gallery-page-thumb" + (isVideo(item) ? " is-video" : "");
      button.dataset.index = String(index);
      button.setAttribute("aria-pressed", "false");
      button.setAttribute("aria-label", text(item.title, "Média") + " — " + text(item.description, ""));
      button.addEventListener("click", () => showMedia(index));

      if (isVideo(item)) {
        const posterUrl = rootUrl(item.thumbnail);
        if (posterUrl) {
          const poster = document.createElement("img");
          poster.alt = "";
          poster.loading = "lazy";
          poster.src = posterUrl;
          poster.onerror = () => {
            poster.remove();
            button.classList.add("is-unavailable");
          };
          button.append(poster);
        } else {
          button.classList.add("is-unavailable");
          const label = document.createElement("span");
          label.className = "gallery-page-thumb-label";
          label.textContent = "▶ Vidéo";
          button.append(label);
        }
      } else {
        const thumb = document.createElement("img");
        thumb.alt = "";
        thumb.loading = "lazy";
        thumb.src = rootUrl(item.thumbnail || item.url);
        thumb.onerror = () => {
          thumb.remove();
          button.classList.add("is-unavailable");
          const label = document.createElement("span");
          label.className = "gallery-page-thumb-label";
          label.textContent = "Photo indisponible";
          button.append(label);
        };
        button.append(thumb);
      }

      const caption = document.createElement("span");
      caption.className = "gallery-page-thumb-label";
      caption.textContent = text(item.title, isVideo(item) ? "Vidéo" : "Photo");
      button.append(caption);
      thumbs.append(button);
    });
  }

  function circuitMatches(circuit, requested) {
    const needle = normalize(requested);
    return normalize(circuit.id) === needle ||
      normalize(circuit.slug) === needle ||
      normalize(circuit.title) === needle;
  }

  function chooseCircuit(list) {
    if (!list.length) return null;
    if (requestedCircuit) {
      const found = list.find((circuit) => circuitMatches(circuit, requestedCircuit));
      if (found) return found;
    }
    return list[0];
  }

  function setCircuitHeader(circuit, media) {
    activeCircuit = circuit;
    $("gallery-page-title").textContent = circuit.title;
    $("gallery-region").textContent = text(circuit.region, "MADAGASCAR · INSPIRATIONS").toUpperCase();
    $("gallery-region-short").textContent = text(circuit.region, "Madagascar");
    $("gallery-theme").textContent = text(circuit.theme, "Découverte");
    $("gallery-page-description").textContent = text(circuit.summary, "Découvrez Madagascar à travers nos inspirations de voyage.");
    $("gallery-media-count").textContent = media.length + " média" + (media.length > 1 ? "s" : "");
    $("gallery-page-thumbs-count").textContent = String(media.length).padStart(2, "0") + " MÉDIAS";
    $("gallery-page-quote").href = "index.html?circuit=" + encodeURIComponent(circuit.slug || circuit.id) + "#contact";
    document.title = circuit.title + " — Galerie MIARADI-TRAVEL";
  }

  function renderSuggestions(suggestions, mediaByCircuit) {
    suggestionsGrid.replaceChildren();
    if (!suggestions.length) {
      const note = document.createElement("p");
      note.className = "gallery-suggestions-empty";
      note.textContent = "D’autres circuits seront bientôt disponibles.";
      suggestionsGrid.append(note);
      return;
    }

    suggestions.forEach((circuit) => {
      const media = mediaByCircuit.get(String(circuit.id)) || [];
      const cover = media.find((item) => item.type !== "video" && item.url) || null;
      const link = document.createElement("a");
      link.className = "gallery-suggestion-card";
      link.href = "galerie.html?circuit=" + encodeURIComponent(circuit.slug || circuit.id);

      const visual = document.createElement("div");
      visual.className = "gallery-suggestion-image";
      if (cover) {
        const img = document.createElement("img");
        img.src = rootUrl(cover.thumbnail || cover.url);
        img.alt = text(cover.alt, text(cover.title, circuit.title));
        img.loading = "lazy";
        img.onerror = () => {
          img.remove();
          visual.classList.add("is-image-unavailable");
        };
        visual.append(img);
      } else if (circuit.image) {
        const img = document.createElement("img");
        img.src = rootUrl(circuit.image);
        img.alt = circuit.title;
        img.loading = "lazy";
        img.onerror = () => {
          img.remove();
          visual.classList.add("is-image-unavailable");
        };
        visual.append(img);
      } else {
        visual.classList.add("is-image-unavailable");
      }

      const badge = document.createElement("span");
      badge.textContent = text(circuit.region, "Madagascar");
      visual.append(badge);

      const copy = document.createElement("div");
      copy.className = "gallery-suggestion-copy";
      const heading = document.createElement("h3");
      heading.textContent = circuit.title;
      const summary = document.createElement("p");
      summary.textContent = text(circuit.summary, "Découvrez ce circuit à Madagascar.");
      const cta = document.createElement("strong");
      cta.innerHTML = 'Découvrir la galerie <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg>';
      copy.append(heading, summary, cta);
      link.append(visual, copy);
      suggestionsGrid.append(link);
    });

    // Carrousel infini : avance vers la gauche toutes les 3 secondes,
    // avec une copie des cartes pour éviter le retour visuel au début.
    if (suggestionsGrid._autoSlideTimer) {
      window.clearInterval(suggestionsGrid._autoSlideTimer);
      suggestionsGrid._autoSlideTimer = null;
    }
    suggestionsGrid.querySelectorAll("[data-carousel-clone]").forEach((node) => node.remove());
    const visibleCount = window.matchMedia("(min-width: 1101px)").matches ? 4
      : window.matchMedia("(min-width: 701px)").matches ? 3 : 1;
    const originalCards = Array.from(suggestionsGrid.querySelectorAll(".gallery-suggestion-card"));
    const reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    suggestionsGrid._carouselLoopWidth = 0;
    suggestionsGrid._carouselVisibleCount = visibleCount;
    if (originalCards.length > visibleCount) {
      originalCards.forEach((card) => {
        const clone = card.cloneNode(true);
        clone.dataset.carouselClone = "true";
        clone.setAttribute("aria-hidden", "true");
        clone.querySelectorAll("a, button, [tabindex]").forEach((item) => item.setAttribute("tabindex", "-1"));
        suggestionsGrid.append(clone);
      });
      const firstClone = suggestionsGrid.querySelector("[data-carousel-clone]");
      suggestionsGrid._carouselLoopWidth = firstClone.offsetLeft - originalCards[0].offsetLeft;
    }
    if (!reduceMotion && suggestionsGrid._carouselLoopWidth > 0) {
      let paused = false;
      const advance = () => {
        if (paused || !suggestionsGrid._carouselLoopWidth) return;
        const first = suggestionsGrid.querySelector(".gallery-suggestion-card");
        const styles = getComputedStyle(suggestionsGrid);
        const gap = parseFloat(styles.columnGap || styles.gap) || 0;
        const step = first.getBoundingClientRect().width + gap;
        const loopWidth = suggestionsGrid._carouselLoopWidth;
        const target = suggestionsGrid.scrollLeft + step;
        suggestionsGrid.scrollTo({ left: target, behavior: "smooth" });
        if (target >= loopWidth - 2) {
          window.setTimeout(() => {
            if (suggestionsGrid.scrollLeft >= loopWidth - 2) {
              suggestionsGrid.scrollLeft -= loopWidth;
            }
          }, 550);
        }
      };
      suggestionsGrid._autoSlideTimer = window.setInterval(advance, 3000);
      suggestionsGrid.addEventListener("mouseenter", () => { paused = true; });
      suggestionsGrid.addEventListener("mouseleave", () => { paused = false; });
      suggestionsGrid.addEventListener("focusin", () => { paused = true; });
      suggestionsGrid.addEventListener("focusout", () => { paused = false; });
      suggestionsGrid.addEventListener("touchstart", () => { paused = true; }, { passive: true });
      suggestionsGrid.addEventListener("touchend", () => { paused = false; }, { passive: true });
    }
  }

  async function init() {
    try {
      const circuitPayload = await getJson(API_BASE + "circuits.php?lang=" + encodeURIComponent(LANGUAGE));
      circuits = Array.isArray(circuitPayload.data) ? circuitPayload.data : [];
      if (!circuits.length) throw new Error("Aucun circuit publié n’est disponible pour le moment.");

      $("gallery-page-title").textContent = "Laissez-vous inspirer";
      $("gallery-page-description").textContent = "Une sélection de photographies de Madagascar, des paysages côtiers aux hauts plateaux, réunies dans un diaporama.";
      $("gallery-region").textContent = "MADAGASCAR EN IMAGES";
      $("gallery-region-short").textContent = "Toutes les régions";
      $("gallery-theme").textContent = "Toutes les thématiques";
      document.title = "Madagascar en images — MIARADI-TRAVEL";

      let pages = [];
      try {
        const pagesPayload = await getJson(API_BASE + "circuit-pages.php?lang=" + encodeURIComponent(LANGUAGE));
        pages = Array.isArray(pagesPayload.data) ? pagesPayload.data : [];
      } catch (_) {
        // Les photos de la galerie restent disponibles même si les pages d'itinéraire ne répondent pas.
      }

      const mediaByCircuit = new Map();
      const globalSeen = new Set();
      allMedia = [];

      for (const circuit of circuits) {
        const circuitPage = pages.find((page) =>
          String(page.id) === String(circuit.id) ||
          normalize(page.slug) === normalize(circuit.slug) ||
          normalize(page.title_fr) === normalize(circuit.title)
        );
        const circuitMedia = [];
        const localSeen = new Set();

        const addPhoto = (photo, source, fallbackTitle) => {
          if (!photo || !photo.url || isVideo(photo)) return;
          const url = text(photo.url).trim();
          const key = rootUrl(url) || url;
          if (!url || localSeen.has(key) || globalSeen.has(key)) return;
          localSeen.add(key);
          globalSeen.add(key);
          circuitMedia.push({
            id: text(photo.id, "circuit-" + circuit.id + "-photo-" + circuitMedia.length),
            circuit: { id: circuit.id, slug: circuit.slug, title: circuit.title },
            type: "image",
            source,
            title: text(photo.title, text(photo.alt, fallbackTitle || circuit.title)),
            description: text(photo.description, text(photo.alt, text(circuit.summary, circuit.title))),
            alt: text(photo.alt, text(photo.title, circuit.title)),
            url,
            thumbnail: text(photo.thumbnail, url),
            isHero: source === "circuit-cover"
          });
        };

        // La couverture ouvre chaque série, suivie des photos de l'itinéraire.
        if (circuit.image) addPhoto({ url: circuit.image, title: circuit.title, alt: circuit.title, description: circuit.summary }, "circuit-cover", circuit.title);
        if (circuitPage && Array.isArray(circuitPage.gallery)) {
          circuitPage.gallery.forEach((photo, index) => {
            addPhoto(photo, index === 0 && !circuit.image ? "circuit-cover" : "itinerary", circuit.title);
          });
        }

        try {
          const mediaPayload = await getJson(API_BASE + "gallery.php?circuit_id=" + encodeURIComponent(circuit.id) + "&lang=" + encodeURIComponent(LANGUAGE));
          const galleryMedia = Array.isArray(mediaPayload.data) ? mediaPayload.data : [];
          galleryMedia.forEach((photo) => addPhoto(photo, "gallery", circuit.title));
        } catch (_) {
          // On conserve les photos déjà chargées pour ce circuit.
        }

        mediaByCircuit.set(String(circuit.id), circuitMedia);
        allMedia.push(...circuitMedia);
      }

      // Une URL ?circuit=... doit ouvrir la galerie du circuit demandé.
      // Sans paramètre, on conserve la galerie globale de tous les circuits.
      const selectedCircuit = requestedCircuit ? chooseCircuit(circuits) : null;
      if (selectedCircuit) {
        activeCircuit = selectedCircuit;
        allMedia = mediaByCircuit.get(String(selectedCircuit.id)) || [];
        setCircuitHeader(selectedCircuit, allMedia);
      } else {
        $("gallery-media-count").textContent = allMedia.length + " photo" + (allMedia.length > 1 ? "s" : "");
        $("gallery-page-thumbs-count").textContent = String(allMedia.length).padStart(2, "0") + " PHOTOS";
        $("gallery-page-quote").href = "index.html#contact";
        activeCircuit = allMedia.length && allMedia[0].circuit
          ? circuits.find((circuit) => String(circuit.id) === String(allMedia[0].circuit.id)) || circuits[0]
          : circuits[0];
      }

      renderThumbnails();
      showMedia(0);

      const suggestions = circuits.filter((circuit) =>
        (mediaByCircuit.get(String(circuit.id)) || []).length > 0 &&
        (!selectedCircuit || String(circuit.id) !== String(selectedCircuit.id))
      );
      renderSuggestions(suggestions, mediaByCircuit);
      requestAnimationFrame(updateSuggestionsControls);

      if (!allMedia.length) {
        const status = $("gallery-page-load-status");
        if (status) {
          status.hidden = false;
          status.textContent = "Aucune photo publiée n’a été trouvée. Ajoutez des photos aux circuits ou à la galerie depuis l’administration.";
        }
      }
    } catch (error) {
      $("gallery-page-title").textContent = "Galerie momentanément indisponible";
      $("gallery-page-description").textContent = error.message || "Impossible de charger les photos de Madagascar.";
      $("gallery-region").textContent = "MIARADI-TRAVEL";
      $("gallery-region-short").textContent = "Madagascar";
      $("gallery-media-count").textContent = "0 photo";
      $("gallery-page-thumbs-count").textContent = "00 PHOTOS";
      title.textContent = "La galerie n’a pas pu être chargée";
      description.textContent = "Vérifiez que l’API PHP et la base de données sont accessibles.";
      thumbs.replaceChildren();
      suggestionsGrid.replaceChildren();
      updateSuggestionsControls();
      showUnavailable("Galerie momentanément indisponible");
      prevButton.disabled = true;
      nextButton.disabled = true;
      const status = $("gallery-page-load-status");
      if (status) {
        status.hidden = false;
        status.textContent = error.message || "Erreur de chargement.";
      }
    }
  }

  function updateSuggestionsControls() {
    if (!suggestionsGrid || !suggestionsPrev || !suggestionsNext) return;
    const maxScroll = Math.max(0, suggestionsGrid.scrollWidth - suggestionsGrid.clientWidth);
    suggestionsPrev.disabled = suggestionsGrid.scrollLeft <= 2;
    suggestionsNext.disabled = suggestionsGrid.scrollLeft >= maxScroll - 2;
    const hasOverflow = maxScroll > 2;
    suggestionsPrev.hidden = !hasOverflow;
    suggestionsNext.hidden = !hasOverflow;
  }

  function scrollSuggestions(direction) {
    if (!suggestionsGrid) return;
    const card = suggestionsGrid.querySelector(".gallery-suggestion-card");
    const styles = getComputedStyle(suggestionsGrid);
    const gap = parseFloat(styles.columnGap || styles.gap) || 22;
    const distance = card ? card.getBoundingClientRect().width + gap : suggestionsGrid.clientWidth * 0.8;
    suggestionsGrid.scrollBy({ left: direction * distance, behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth" });
  }

  suggestionsPrev?.addEventListener("click", () => scrollSuggestions(-1));
  suggestionsNext?.addEventListener("click", () => scrollSuggestions(1));
  suggestionsGrid?.addEventListener("scroll", updateSuggestionsControls, { passive: true });
  window.addEventListener("resize", updateSuggestionsControls);
  if (typeof ResizeObserver !== "undefined" && suggestionsGrid) {
    new ResizeObserver(updateSuggestionsControls).observe(suggestionsGrid);
  }

  toggleButton?.addEventListener("click", () => {
    isManuallyPaused = !isManuallyPaused;
    if (toggleButton) {
      toggleButton.innerHTML = isManuallyPaused
        ? '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 11 7-11 7Z"/></svg><span>Reprendre</span>'
        : '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5v14M15 5v14"/></svg><span>Pause</span>';
      toggleButton.setAttribute("aria-pressed", String(isManuallyPaused));
      toggleButton.setAttribute("aria-label", isManuallyPaused ? "Reprendre le diaporama" : "Mettre le diaporama en pause");
    }
    if (isManuallyPaused) stopAutoAdvance();
    else scheduleAutoAdvance();
  });

  prevButton.addEventListener("click", () => showMedia(activeIndex - 1));
  nextButton.addEventListener("click", () => showMedia(activeIndex + 1));

  // Pause pendant l’interaction et reprise douce lorsque le visiteur quitte la zone.
  stage.addEventListener("mouseenter", stopAutoAdvance);
  stage.addEventListener("mouseleave", scheduleAutoAdvance);
  stage.addEventListener("focusin", stopAutoAdvance);
  stage.addEventListener("focusout", () => window.setTimeout(scheduleAutoAdvance, 0));
  document.addEventListener("visibilitychange", scheduleAutoAdvance);
  window.matchMedia("(prefers-reduced-motion: reduce)").addEventListener?.("change", scheduleAutoAdvance);
  document.addEventListener("keydown", (event) => {
    if (event.altKey || event.ctrlKey || event.metaKey || /INPUT|TEXTAREA|SELECT/.test(event.target.tagName)) return;
    if (event.key === "ArrowLeft") showMedia(activeIndex - 1);
    if (event.key === "ArrowRight") showMedia(activeIndex + 1);
    if (event.key === "Escape") window.location.href = "index.html#circuits";
  });

  init();
})();
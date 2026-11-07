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

  let circuits = [];
  let activeCircuit = null;
  let allMedia = [];
  let activeIndex = 0;
  let frame = null;
  let loadGeneration = 0;

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
        return id ? "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(id) : "";
      }
      if (host === "youtube.com" || host === "m.youtube.com" || host === "youtube-nocookie.com") {
        let id = url.searchParams.get("v");
        if (!id && /^\/(embed|shorts|live)\//.test(url.pathname)) id = url.pathname.split("/")[2];
        return id ? "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(id) : "";
      }
      if (host === "vimeo.com" || host === "player.vimeo.com") {
        const match = url.pathname.match(/(?:video\/)?(\d+)/);
        return match ? "https://player.vimeo.com/video/" + match[1] : "";
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
        video.onerror = () => showUnavailable("Vidéo momentanément indisponible");
        video.src = mediaUrl;
        video.load();
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
    description.textContent = text(item.description, activeCircuit.summary || "Découvrez ce paysage de Madagascar.");
    updateCounter();

    thumbs.querySelectorAll("[data-index]").forEach((button) => {
      const selected = Number(button.dataset.index) === activeIndex;
      button.classList.toggle("is-active", selected);
      button.setAttribute("aria-pressed", String(selected));
    });

    prevButton.disabled = allMedia.length < 2;
    nextButton.disabled = allMedia.length < 2;
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
    $("gallery-region-short").textContent = [circuit.region, circuit.theme].filter(Boolean).join(" · ");
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
      cta.textContent = "Découvrir la galerie ↗";
      copy.append(heading, summary, cta);
      link.append(visual, copy);
      suggestionsGrid.append(link);
    });
  }

  async function init() {
    try {
      const circuitPayload = await getJson(API_BASE + "circuits.php?lang=" + encodeURIComponent(LANGUAGE));
      circuits = Array.isArray(circuitPayload.data) ? circuitPayload.data : [];
      if (!circuits.length) throw new Error("Aucun circuit publié n’est disponible pour le moment.");

      const requestedMatch = requestedCircuit
        ? circuits.find((circuit) => circuitMatches(circuit, requestedCircuit))
        : null;

      if (requestedCircuit && !requestedMatch) {
        const notice = $("gallery-page-load-status");
        if (notice) {
          notice.hidden = false;
          notice.textContent = "Ce circuit n’est pas publié dans le catalogue. Choisissez l’un des circuits disponibles ci-dessous.";
        }
        $("gallery-page-title").textContent = "Circuit indisponible";
        $("gallery-page-description").textContent = "La galerie est reliée au catalogue publié. Le circuit demandé doit être publié et posséder une entrée correspondante dans l’administration.";
        $("gallery-region").textContent = "MIARADI-TRAVEL";
        $("gallery-region-short").textContent = "Catalogue des circuits";
        $("gallery-media-count").textContent = "0 média";
        $("gallery-page-thumbs-count").textContent = "00 MÉDIAS";
        title.textContent = "Choisissez un circuit publié";
        description.textContent = "Les médias seront chargés automatiquement depuis la base de données.";
        showUnavailable("Aucune galerie pour ce circuit");
        prevButton.disabled = true;
        nextButton.disabled = true;
        renderSuggestions(circuits.slice(0, 3), new Map());
        return;
      }

      activeCircuit = requestedMatch || chooseCircuit(circuits);

      const mediaPayload = await getJson(API_BASE + "gallery.php?circuit_id=" + encodeURIComponent(activeCircuit.id) + "&lang=" + encodeURIComponent(LANGUAGE));
      allMedia = Array.isArray(mediaPayload.data) ? mediaPayload.data : [];
      setCircuitHeader(activeCircuit, allMedia);
      renderThumbnails();
      showMedia(0);

      const suggestions = circuits.filter((circuit) => String(circuit.id) !== String(activeCircuit.id)).slice(0, 3);
      const mediaResults = await Promise.all(suggestions.map(async (circuit) => {
        try {
          const payload = await getJson(API_BASE + "gallery.php?circuit_id=" + encodeURIComponent(circuit.id) + "&lang=" + encodeURIComponent(LANGUAGE));
          return [String(circuit.id), Array.isArray(payload.data) ? payload.data : []];
        } catch (_) {
          return [String(circuit.id), []];
        }
      }));
      renderSuggestions(suggestions, new Map(mediaResults));
    } catch (error) {
      $("gallery-page-title").textContent = "Galerie momentanément indisponible";
      $("gallery-page-description").textContent = error.message || "Impossible de charger les circuits et les médias.";
      $("gallery-region").textContent = "MIARADI-TRAVEL";
      $("gallery-region-short").textContent = "Madagascar";
      $("gallery-media-count").textContent = "0 média";
      $("gallery-page-thumbs-count").textContent = "00 MÉDIAS";
      title.textContent = "La galerie n’a pas pu être chargée";
      description.textContent = "Vérifiez que l’API PHP et la base de données sont accessibles.";
      thumbs.replaceChildren();
      suggestionsGrid.replaceChildren();
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

  prevButton.addEventListener("click", () => showMedia(activeIndex - 1));
  nextButton.addEventListener("click", () => showMedia(activeIndex + 1));
  document.addEventListener("keydown", (event) => {
    if (event.altKey || event.ctrlKey || event.metaKey || /INPUT|TEXTAREA|SELECT/.test(event.target.tagName)) return;
    if (event.key === "ArrowLeft") showMedia(activeIndex - 1);
    if (event.key === "ArrowRight") showMedia(activeIndex + 1);
    if (event.key === "Escape") window.location.href = "index.html#circuits";
  });

  init();
})();
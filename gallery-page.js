(() => {
  const circuits = {
    "L'Ouest sauvage": {
      title: "L'Ouest sauvage", region: "OUEST · MORONDAVA", shortRegion: "Morondava · Ouest",
      description: "Baobabs, pistes rouges et couchers de soleil autour de Morondava.",
      photos: [
        {file:"Walking_the_Avenue_of_the_Baobabs.jpg",title:"L'allée des baobabs",description:"Les silhouettes emblématiques des baobabs au cœur de l'Ouest malgache."},
        {file:"Avenue_of_the_Baobabs_Madagascar.jpg",title:"Baobabs de Madagascar",description:"Une lumière chaude révèle les formes singulières de ces arbres majestueux."},
        {file:"Baobab_allee_Morondava_Madagascar.jpg",title:"Les paysages de Morondava",description:"Un décor ouvert sur les pistes rouges et les horizons de l'Ouest."}
      ]
    },
    "L'appel du lagon": {
      title:"L'appel du lagon",region:"NORD · NOSY BE",shortRegion:"Nosy Be · Nord",
      description:"Plages, eaux turquoise et îles du Nord malgache.",
      photos:[
        {file:"Beach_at_Nosy_Be.jpg",title:"Le lagon de Nosy Be",description:"Une plage lumineuse bordée par les eaux du canal du Mozambique."},
        {file:"Nosy_Be_Madagascar.jpg",title:"Le littoral de Nosy Be",description:"Des paysages côtiers qui invitent à ralentir et à explorer les îles."},
        {file:"Nosy_Iranja_Madagascar.jpg",title:"L'île de Nosy Iranja",description:"Un paysage insulaire entre sable clair, mer et végétation tropicale."}
      ]
    },
    "Routes & rencontres": {
      title:"Les paysages de l'Isalo",region:"SUD · ISALO",shortRegion:"Isalo · Sud",
      description:"Massifs rocheux, canyons et grands espaces du Sud.",
      photos:[
        {file:"Isalo_landscape.jpg",title:"Les reliefs de l'Isalo",description:"Les formes sculptées du massif donnent au paysage un caractère minéral."},
        {file:"Isalo-madagascar.jpg",title:"Les grands espaces du Sud",description:"Un panorama de roches, de vallées et d'horizons ouverts."},
        {file:"Isalo_National_Park_Madagascar.jpg",title:"Canyons de l'Isalo",description:"Une invitation à parcourir les sentiers et les paysages du parc."}
      ]
    },
    "Les Tsingy de Bemaraha": {
      title:"Les Tsingy de Bemaraha",region:"OUEST · MENABE",shortRegion:"Bemaraha · Ouest",
      description:"Un paysage calcaire spectaculaire classé au patrimoine mondial.",
      photos:[
        {file:"Tsingy_de_Bemaraha%2C_Madagascar.jpg",title:"Les Tsingy de Bemaraha",description:"Un relief calcaire spectaculaire façonné par le temps."},
        {file:"Tsingy_de_Bemaraha.jpg",title:"Formations calcaires des Tsingy",description:"Des aiguilles de pierre composent un paysage rare et impressionnant."},
        {file:"Grand_Tsingy_Bemaraha_Madagascar.jpg",title:"Aiguilles rocheuses du Bemaraha",description:"Une vue sur les formations minérales caractéristiques du parc."}
      ]
    },
    "La forêt d'Andasibe": {
      title:"La forêt d'Andasibe",region:"EST · ANDASIBE",shortRegion:"Andasibe · Est",
      description:"Forêt humide, biodiversité endémique et observation des indris.",
      photos:[
        {file:"Indri_Andasibe.JPG",title:"Indri dans la forêt d'Andasibe",description:"Une rencontre avec l'un des lémuriens emblématiques des forêts de l'Est."},
        {file:"Andasibe-Mantadia_National_Park.jpg",title:"La forêt humide d'Andasibe",description:"Une végétation dense et vivante, refuge d'une biodiversité exceptionnelle."},
        {file:"Indri_indri_Andasibe.jpg",title:"Le lémurien indri",description:"L'indri, espèce endémique, est célèbre pour ses appels puissants."}
      ]
    },
    "Sainte-Marie, l'île nature": {
      title:"Sainte-Marie, l'île nature",region:"EST · SAINTE-MARIE",shortRegion:"Sainte-Marie · Est",
      description:"Plages paisibles et vie insulaire sur la côte Est.",
      photos:[
        {file:"Nosy_Boraha_Sainte_Marie_Madagascar.jpg",title:"Le littoral de Sainte-Marie",description:"Une côte insulaire à découvrir au fil des villages et des plages."},
        {file:"Sainte_Marie_Madagascar_beach.jpg",title:"Plage de Sainte-Marie",description:"Un paysage de bord de mer pour prendre le temps de voyager."},
        {file:"Madagascar_Sainte-Marie_island.jpg",title:"Une île à explorer",description:"La douceur d'un itinéraire entre nature, villages et océan."}
      ]
    },
    "Les Hautes Terres et Antsirabe": {
      title:"Les Hautes Terres et Antsirabe",region:"HAUTES TERRES · ANTSIRABE",shortRegion:"Antsirabe · Hautes Terres",
      description:"Villages, artisanat et paysages des Hautes Terres.",
      photos:[
        {file:"Antsirabe_Madagascar_landscape.jpg",title:"Les paysages d'Antsirabe",description:"Des collines et des campagnes façonnent les paysages du centre."},
        {file:"Antsirabe_Madagascar.jpg",title:"À la découverte d'Antsirabe",description:"Une étape culturelle au cœur des Hautes Terres malgaches."},
        {file:"Madagascar_Highlands_landscape.jpg",title:"Les Hautes Terres",description:"Des paysages vallonnés, des villages et des scènes de vie locale."}
      ]
    },
    "La Route nationale 7": {
      title:"La Route nationale 7",region:"RN7 · DES HAUTES TERRES AU SUD",shortRegion:"RN7 · Centre et Sud",
      description:"Un itinéraire du centre vers le Sud, entre Hautes Terres et paysages de l'Isalo.",
      photos:[
        {file:"Isalo-madagascar.jpg",title:"Les paysages de l'Isalo",description:"Une étape marquante sur la route vers les paysages du Sud."},
        {file:"Antsirabe_Madagascar_landscape.jpg",title:"Les Hautes Terres",description:"Des routes qui traversent collines, villages et terres cultivées."},
        {file:"Madagascar_RN7.jpg",title:"Sur la Route nationale 7",description:"Un voyage par étapes entre le centre de Madagascar et le Sud."}
      ]
    }
  };
  const params = new URLSearchParams(window.location.search);
  const requestedKey = params.get("circuit") || "L'Ouest sauvage";
  const selectedKey = Object.prototype.hasOwnProperty.call(circuits, requestedKey) ? requestedKey : "L'Ouest sauvage";
  const activeCircuit = circuits[selectedKey];
  const allMedia = activeCircuit.photos.map((item) => ({...item, type:item.type || (/\.(mp4|webm|ogg)(?:[?#].*)?$/i.test(item.file) ? "video" : "image")}));
  const $ = (id) => document.getElementById(id);
  const image = $("gallery-stage-image"), video = $("gallery-stage-video"), fallback = $("gallery-unavailable");
  const title = $("gallery-stage-title"), description = $("gallery-stage-description"), counter = $("gallery-page-counter");
  const thumbs = $("gallery-page-thumbnails");
  let activeIndex = 0;
  const urlFor = (file, width=1800) => /^(https?:)?\/\//i.test(file) || file.startsWith("/") || file.startsWith("../") ? file : "https://commons.wikimedia.org/wiki/Special:FilePath/" + file + "?width=" + width;
  const isVideo = (item) => item.type === "video" || /\.(mp4|webm|ogg)(?:[?#].*)?$/i.test(item.file);
  $("gallery-page-title").textContent = activeCircuit.title;
  $("gallery-region").textContent = activeCircuit.region;
  $("gallery-region-short").textContent = activeCircuit.shortRegion;
  $("gallery-page-description").textContent = activeCircuit.description;
  $("gallery-media-count").textContent = allMedia.length + " média" + (allMedia.length > 1 ? "s" : "");
  $("gallery-page-thumbs-count").textContent = String(allMedia.length).padStart(2,"0") + " MÉDIAS";
  $("gallery-page-quote").href = "index.html?circuit=" + encodeURIComponent(selectedKey) + "#contact";
  document.title = activeCircuit.title + " — Galerie MIARADI-TRAVEL";

  function unavailable() {
    if (video) { video.pause(); video.hidden = true; video.removeAttribute("src"); video.load(); }
    image.hidden = true;
    fallback.hidden = false;
    fallback.querySelector("strong").textContent = isVideo(allMedia[activeIndex]) ? "Vidéo momentanément indisponible" : "Photo momentanément indisponible";
  }
  function showMedia(nextIndex) {
    activeIndex = (nextIndex + allMedia.length) % allMedia.length;
    const item = allMedia[activeIndex];
    fallback.hidden = true;
    image.hidden = true;
    image.onerror = null;
    if (video) { video.pause(); video.hidden = true; video.onerror = null; video.removeAttribute("src"); video.load(); }
    if (isVideo(item)) {
      video.hidden = false;
      video.onerror = unavailable;
      video.src = item.file;
      video.load();
    } else {
      image.hidden = false;
      image.alt = item.title;
      image.onerror = unavailable;
      image.src = urlFor(item.file);
    }
    title.textContent = item.title;
    description.textContent = item.description || activeCircuit.description;
    counter.textContent = String(activeIndex+1).padStart(2,"0") + " / " + String(allMedia.length).padStart(2,"0");
    thumbs.querySelectorAll("[data-index]").forEach((button) => {
      const selected = Number(button.dataset.index) === activeIndex;
      button.classList.toggle("is-active", selected);
      button.setAttribute("aria-pressed", String(selected));
    });
    $("gallery-page-prev").disabled = allMedia.length < 2;
    $("gallery-page-next").disabled = allMedia.length < 2;
  }
  allMedia.forEach((item,index) => {
    const button = document.createElement("button");
    button.type = "button"; button.className = "gallery-page-thumb" + (isVideo(item) ? " is-video" : "");
    button.dataset.index = String(index); button.setAttribute("aria-pressed","false");
    button.setAttribute("aria-label",item.title + " — " + (item.description || activeCircuit.description));
    if (!isVideo(item)) {
      const thumb = document.createElement("img"); thumb.alt=""; thumb.loading="lazy"; thumb.src=urlFor(item.file,480);
      thumb.onerror=()=>{button.classList.add("is-unavailable");button.innerHTML="Photo indisponible";button.disabled=true;};
      button.append(thumb);
    } else {
      const label=document.createElement("span"); label.className="gallery-page-thumb-label"; label.textContent="▶ Vidéo"; button.append(label);
    }
    const caption=document.createElement("span"); caption.className="gallery-page-thumb-label"; caption.textContent=item.title; button.append(caption);
    button.addEventListener("click",()=>showMedia(index)); thumbs.append(button);
  });
  $("gallery-page-prev").addEventListener("click",()=>showMedia(activeIndex-1));
  $("gallery-page-next").addEventListener("click",()=>showMedia(activeIndex+1));
  document.addEventListener("keydown",(event)=>{if(event.key==="ArrowLeft")showMedia(activeIndex-1);if(event.key==="ArrowRight")showMedia(activeIndex+1);});
  showMedia(0);

  const suggestions = Object.entries(circuits).filter(([key])=>key!==selectedKey).slice(0,3);
  const suggestionsGrid = $("gallery-suggestions-grid");
  suggestions.forEach(([key,circuit])=>{
    const link=document.createElement("a"); link.className="gallery-suggestion-card"; link.href="galerie.html?circuit="+encodeURIComponent(key);
    const visual=document.createElement("div"); visual.className="gallery-suggestion-image";
    const img=document.createElement("img"); img.src=urlFor(circuit.photos[0].file,900); img.alt=circuit.photos[0].title; img.loading="lazy";
    img.onerror=()=>{img.hidden=true;visual.classList.add("is-image-unavailable");};
    const badge=document.createElement("span"); badge.textContent=circuit.region;
    visual.append(img,badge);
    const copy=document.createElement("div"); copy.className="gallery-suggestion-copy";
    const h3=document.createElement("h3"); h3.textContent=circuit.title;
    const p=document.createElement("p"); p.textContent=circuit.description;
    const cta=document.createElement("strong"); cta.textContent="Découvrir la galerie ↗";
    copy.append(h3,p,cta); link.append(visual,copy); suggestionsGrid.append(link);
  });
})();
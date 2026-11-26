(() => {
  const grid = document.getElementById('catalogue-grid');
  const count = document.getElementById('catalogue-count');
  const regionFilter = document.getElementById('catalogue-region');
  const themeFilter = document.getElementById('catalogue-theme');
  const durationFilter = document.getElementById('catalogue-duration');
  const mapCount = document.getElementById('catalogue-map-count');
  const mapElement = document.getElementById('catalogue-map');
  let circuits = [];
  let map = null;
  let markerLayer = null;
  // Repères géographiques par région, pas par grande zone approximative.
  // Les valeurs correspondent à des centres régionaux/lieux connus de Madagascar.
  // Repères de destination : les grands secteurs pointent vers leur ville/région de référence.
  // Les coordonnées sont des repères cartographiques, pas des limites administratives.
  const regionLocations = [
    {keys:['nord – nord-ouest','nord-nord-ouest','nord-ouest','fleuve sofia','boeny','mahajanga','majunga'],name:'Nord-Ouest · Boeny',lat:-15.7167,lng:46.3167},
    {keys:['ouest','menabe','morondava','tsiribihina','bemaraha','melaky','maintirano'],name:'Ouest · Menabe',lat:-20.2833,lng:44.2833},
    {keys:['est','atsinanana','toamasina','tamatave','sainte-marie'],name:'Est · Atsinanana',lat:-18.1492,lng:49.4023},
    {keys:['hautes terres','haute terre','analamanga','antananarivo','vakinankaratra','antsirabe','itas','mianinarivo'],name:'Hautes Terres · Analamanga',lat:-18.8792,lng:47.5079},
    {keys:['nord','diana','antsiranana','diego suarez','sava','sambava','antalaha','vohemar'],name:'Nord · Diana',lat:-12.2765,lng:49.2917},
    {keys:['sud','atsimo-andrefana','toliara','tuléar','tulear'],name:'Sud-Ouest · Atsimo-Andrefana',lat:-23.3500,lng:43.6667},
    {keys:['androy','ambovombe'],name:'Sud · Androy',lat:-25.1784,lng:46.0870},
    {keys:['anosy','tolagnaro','fort-dauphin'],name:'Sud-Est · Anosy',lat:-25.0319,lng:46.9987},
    {keys:['atsimo-atsinanana','farafangana'],name:'Sud-Est · Atsimo-Atsinanana',lat:-22.8167,lng:47.8333},
    {keys:['haute matsiatra','fianarantsoa','ihorombe','ranohira','isalo'],name:'Centre-Sud · Haute Matsiatra',lat:-21.4527,lng:47.0857},
    {keys:['alaotra','ambatondrazaka'],name:'Centre-Est · Alaotra-Mangoro',lat:-17.8333,lng:48.4167},
    {keys:['betsiboka','maevatanana'],name:'Centre-Nord · Betsiboka',lat:-16.9500,lng:46.8333},
    {keys:['nosy be','nosy-be','hell-ville'],name:'Nosy Be',lat:-13.4000,lng:48.2667},
    {keys:['madagascar'],name:'Madagascar · itinéraire national',lat:-19.1,lng:46.7}
  ];
  const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const normal = value => String(value || '').trim();
  const durationLabel = duration => {
    const min = Number(duration?.min || 0), max = Number(duration?.max || 0);
    if (min && max && min !== max) return min + '–' + max + ' jours';
    if (min || max) return (min || max) + ' jours';
    return 'Sur mesure';
  };
  const category = value => normal(value).toLocaleLowerCase('fr');
  const icon = name => {
    const paths = {pin:'<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',theme:'<path d="M4 5h16l-2 14H2L4 5Z"/><path d="M7 9h10M6 13h10"/>',calendar:'<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 9h18"/>',arrow:'<path d="M5 12h14m-7-7 7 7-7 7"/>'};
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'+paths[name]+'</svg>';
  };
  function fillFilter(select, values, label) {
    const old = select.value;
    select.replaceChildren(new Option(label, 'all'));
    values.sort((a,b)=>a.localeCompare(b,'fr')).forEach(value => select.add(new Option(value, value)));
    if ([...select.options].some(option=>option.value===old)) select.value=old;
  }
  function initMap() {
    if (!mapElement || !window.L || map) return;
    map = L.map(mapElement, {
      scrollWheelZoom:false,
      zoomControl:true,
      zoomSnap:0.5,
      maxBounds:[[-26.5,42.5],[-10.5,52.5]],
      maxBoundsViscosity:0.9
    }).setView([-18.8,48.8], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 12,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    markerLayer = L.layerGroup().addTo(map);
    window.setTimeout(() => map.invalidateSize(), 120);
  }
  function locationFor(region) {
    const text = category(region).normalize('NFD').replace(/[\\u0300-\\u036f]/g, '').replace(/[_–—]/g, '-');
    // Matcher du plus spécifique au plus générique évite qu'un "nord-ouest"
    // soit confondu avec une région du Nord ou qu'une région inconnue soit
    // silencieusement placée au centre de l'île.
    const sorted = [...regionLocations].sort((a, b) =>
      Math.max(...b.keys.map(key => key.length)) - Math.max(...a.keys.map(key => key.length))
    );
    const match = sorted.find(item => item.keys.some(key =>
      text.includes(key.normalize('NFD').replace(/[\\u0300-\\u036f]/g, ''))
    ));
    return match || null;
  }
  function renderMap(filteredCircuits) {
    if (!mapElement || !window.L) return;
    initMap();
    if (!map || !markerLayer) return;
    markerLayer.clearLayers();
    const active = filteredCircuits || circuits;
    const byRegion = new Map();
    active.forEach(c => {
      const region = normal(c.region) || 'Madagascar';
      if (!byRegion.has(region)) byRegion.set(region, []);
      byRegion.get(region).push(c);
    });
    if (mapCount) mapCount.textContent = byRegion.size + (byRegion.size > 1 ? ' régions' : ' région');
    const unmappedRegions = [];
    byRegion.forEach((items, region) => {
      const loc = locationFor(region);
      if (!loc) {
        unmappedRegions.push(region);
        return;
      }
      const marker = L.circleMarker([loc.lat,loc.lng], {
        radius: Math.min(12, 6 + Math.sqrt(items.length) * 2),
        color:'#fff8f5', weight:2, fillColor:'#922d20', fillOpacity:.95
      }).addTo(markerLayer);
      marker.bindTooltip(region + ' · ' + items.length + (items.length > 1 ? ' circuits' : ' circuit'));
      marker.bindPopup('<strong>' + escapeHtml(region) + '</strong><br>' + items.length + (items.length > 1 ? ' circuits disponibles' : ' circuit disponible') + '<br><button type="button" class="map-filter-button" data-region="' + escapeHtml(region) + '">Voir cette région</button>');
      marker.on('popupopen', event => {
        const button = event.popup.getElement()?.querySelector('.map-filter-button');
        if (button) button.addEventListener('click', () => {
          const exactOption = [...regionFilter.options].find(option => option.value === region);
          if (exactOption) {
            regionFilter.value = region;
            render();
            document.getElementById('catalogue-grid')?.scrollIntoView({behavior:'smooth',block:'start'});
          } else {
            const location = locationFor(region);
            const related = [...regionFilter.options].find(option => category(option.value).includes(category(location.name).split(' / ')[0]));
            if (related) { regionFilter.value = related.value; render(); }
          }
          map.closePopup();
        }, {once:true});
      });
    });
    if (unmappedRegions.length) {
      console.warn('[MIARADI map] Coordonnées manquantes pour les régions :', unmappedRegions);
    }
    // Garder la vue centrée sur Madagascar : fitBounds sur les seuls marqueurs
    // décalait la carte vers la côte africaine ou coupait l'île selon les filtres.
    map.setView([-18.8,48.8], 6, {animate:false});
  }
  function render() {
    const region = regionFilter.value, theme = themeFilter.value, duration = durationFilter.value;
    const filtered = circuits.filter(c => {
      if(region!=='all' && normal(c.region)!==region) return false;
      if(theme!=='all' && normal(c.theme)!==theme) return false;
      const days = Number(c.duration?.max || c.duration?.min || 0);
      if(duration==='short' && (!days || days>5)) return false;
      if(duration==='medium' && (days<6 || days>10)) return false;
      if(duration==='long' && days<11) return false;
      return true;
    });
    count.textContent = filtered.length + (filtered.length>1 ? ' circuits disponibles' : ' circuit disponible');
    renderMap(filtered);
    if(!filtered.length){grid.innerHTML='<p class="catalogue-empty">Aucun circuit ne correspond à ces filtres. Essayez une autre sélection.</p>';renderMap(filtered);return;}
    grid.innerHTML = filtered.map(c => {
      const slug = normal(c.slug), title = normal(c.title) || 'Circuit à découvrir';
      const image = normal(c.image);
      const detailUrl = 'backend/public/circuit-detail.php?' + (slug ? 'slug='+encodeURIComponent(slug) : 'id='+encodeURIComponent(c.id));
      const galleryUrl = 'galerie.html?circuit='+encodeURIComponent(slug || c.id);
      const region = normal(c.region) || 'Madagascar';
      const theme = normal(c.theme) || 'Découverte';
      return '<article class="catalogue-card"><a class="catalogue-card-image" href="'+galleryUrl+'" aria-label="Voir les photos de '+escapeHtml(title)+'">'+(image?'<img src="'+escapeHtml(image)+'" alt="'+escapeHtml(title)+'" loading="lazy" decoding="async" onerror="this.classList.add(\'image-unavailable\')">':'')+'<span class="catalogue-card-region">'+escapeHtml(region)+'</span></a><div class="catalogue-card-body"><h2>'+escapeHtml(title)+'</h2><p>'+escapeHtml(normal(c.summary) || 'Un itinéraire pour découvrir les paysages, la culture et les rencontres de Madagascar.')+'</p><div class="catalogue-facts"><span>'+icon('pin')+escapeHtml(region)+'</span><span>'+icon('theme')+escapeHtml(theme)+'</span><span>'+icon('calendar')+escapeHtml(durationLabel(c.duration))+'</span></div><div class="catalogue-card-actions"><a href="'+galleryUrl+'">Voir les photos ↗</a><a class="catalogue-detail" href="'+detailUrl+'">Découvrir le circuit '+icon('arrow')+'</a></div></div></article>';
    }).join('');
    if(window.lucide) window.lucide.createIcons();
  }
  [regionFilter,themeFilter,durationFilter].forEach(select=>select.addEventListener('change',render));
  fetch('backend/public/api/circuits.php?lang=fr',{headers:{Accept:'application/json'},cache:'no-store'})
    .then(response=>{if(!response.ok)throw new Error('Le catalogue est momentanément indisponible.');return response.json();})
    .then(payload=>{
      if(!Array.isArray(payload.data))throw new Error(payload.error?.message || 'Impossible de charger les circuits.');
      circuits=payload.data;
      fillFilter(regionFilter,[...new Set(circuits.map(c=>normal(c.region)).filter(Boolean))],'Toutes les régions');
      fillFilter(themeFilter,[...new Set(circuits.map(c=>normal(c.theme)).filter(Boolean))],'Toutes les thématiques');
      render();
      renderMap(circuits);
    })
    .catch(error=>{count.textContent='Catalogue indisponible';grid.innerHTML='<p class="catalogue-error">'+escapeHtml(error.message)+' Réessayez dans quelques instants.</p>';renderMap([]);});
})();
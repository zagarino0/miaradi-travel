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
  // Les circuits sont regroupés dans neuf grandes zones de voyage MIARADI.
  // Les 23 régions administratives et les alias des destinations sont normalisés
  // avant le classement : le nom d'une région ne dépend jamais d'un simple mot
  // générique comme « nord », « est » ou « ouest ».
  const zones = [
    {name:'NORD', lat:-12.2765, lng:49.2917, aliases:['nord','diana','antsiranana','diego suarez','diego-suarez']},
    {name:'NORD-EST', lat:-14.2667, lng:50.1667, aliases:['nord-est','nord est','analanjirofo','sava','sambava','antalaha','vohemar','andapa','fenoarivo atsinanana','mananara']},
    {name:'NORD-OUEST', lat:-15.7167, lng:46.3167, aliases:['nord-ouest','nord ouest','nord-oest','nord oest','boeny','mahajanga','majunga','sofia','antsohihy','betsiboka','maevatanana','fleuve sofia']},
    {name:'OUEST', lat:-20.2833, lng:44.2833, aliases:['ouest','menabe','morondava','melaky','maintirano','tsiribihina','bemaraha','tsingy de bemaraha','bekopaka']},
    {name:'EST', lat:-18.1492, lng:49.4023, aliases:['est','atsinanana','toamasina','tamatave','alaotra-mangoro','alaotra mangoro','ambatondrazaka','moramanga','ranomafana']},
    {name:'SUD', lat:-25.1784, lng:46.0870, aliases:['sud','androy','ambovombe','ihorombe','ihosy','ranohira','isalo']},
    {name:'SUD-OUEST', lat:-23.3500, lng:43.6667, aliases:['sud-ouest','sud ouest','atsimo-andrefana','toliara','tulear','tulear','tuléar']},
    {name:'SUD-EST', lat:-25.0319, lng:46.9987, aliases:['sud-est','sud est','atsimo-atsinanana','farafangana','vatovavy','mananjary','fitovinany','manakara','anosy','tolagnaro','fort-dauphin','fort dauphin']},
    {name:'HAUTE-TERRE', lat:-18.8792, lng:47.5079, aliases:['haute-terre','haute terre','hautes-terres','hautes terres','haute terres','analamanga','antananarivo','tananarive','vakinankaratra','antsirabe','itasy','mianinarivo','bongolava','tsiroanomandidy','haute matsiatra','fianarantsoa','amoron i mania','amoron mania','ambositra','andringitra','ambalavao']}
  ];
  const nationalLocation = {name:'MADAGASCAR', lat:-19.1, lng:46.7};
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
    }).setView([-18.8,48.2], 5.5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 12,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    markerLayer = L.layerGroup().addTo(map);
    window.setTimeout(() => map.invalidateSize(), 120);
  }
  function normalizeRegion(value) {
    return String(value || '')
      .toLocaleLowerCase('fr')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[’']/g, ' ')
      .replace(/[_–—]/g, '-')
      .replace(/\s*-\s*/g, '-')
      .replace(/[^a-z0-9-]+/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();
  }
  function zoneForRegion(region) {
    const key = normalizeRegion(region);
    if (!key) return null;

    // Reconnaître d'abord les neuf zones elles-mêmes.
    const directZone = zones.find(zone => normalizeRegion(zone.name) === key);
    if (directZone) return directZone.name;

    // Accepter explicitement les alias géographiques, en évitant les mots génériques
    // (nord/est/sud/ouest) comme seule correspondance lorsqu'ils font partie d'un titre.
    const matchesAny = aliases => aliases.some(alias => {
      const candidate = normalizeRegion(alias);
      return key === candidate || key.includes(candidate);
    });

    if (matchesAny(['nord – nord-ouest','nord-nord-ouest','nord-ouest','nord ouest','nord-oest','nord oest','boeny','mahajanga','majunga','sofia','antsohihy','betsiboka','maevatanana','fleuve sofia','nord-ouest de madagascar'])) return 'NORD-OUEST';
    if (matchesAny(['sud-ouest','sud ouest','atsimo-andrefana','toliara','tulear','tuléar'])) return 'SUD-OUEST';
    if (matchesAny(['sud-est','sud est','atsimo-atsinanana','farafangana','vatovavy','mananjary','fitovinany','manakara','anosy','tolagnaro','fort-dauphin','fort dauphin'])) return 'SUD-EST';
    if (matchesAny(['nord-est','nord est','analanjirofo','sava','sambava','antalaha','vohemar','andapa','fenoarivo atsinanana','mananara'])) return 'NORD-EST';
    if (matchesAny(['haute-terre','haute terre','hautes-terres','hautes terres','haute terres','analamanga','antananarivo','tananarive','vakinankaratra','antsirabe','itasy','mianinarivo','bongolava','tsiroanomandidy','haute matsiatra','fianarantsoa','amoron i mania','amoron mania','ambositra','andringitra','ambalavao'])) return 'HAUTE-TERRE';
    if (matchesAny(['atsinanana','toamasina','tamatave','alaotra-mangoro','alaotra mangoro','ambatondrazaka','moramanga','ranomafana'])) return 'EST';
    if (matchesAny(['menabe','morondava','melaky','maintirano','tsiribihina','bemaraha','tsingy de bemaraha','bekopaka'])) return 'OUEST';
    if (matchesAny(['androy','ambovombe','ihorombe','ihosy','ranohira','isalo'])) return 'SUD';
    if (matchesAny(['diana','antsiranana','diego suarez','diego-suarez'])) return 'NORD';
    if (matchesAny(['madagascar','itineraire national','circuit national'])) return 'MADAGASCAR';
    return null;
  }
  function locationFor(region) {
    const zoneName = zoneForRegion(region);
    if (zoneName === 'MADAGASCAR') return nationalLocation;
    return zones.find(zone => zone.name === zoneName) || null;
  }
  function renderMap(filteredCircuits) {
    if (!mapElement || !window.L) return;
    initMap();
    if (!map || !markerLayer) return;
    markerLayer.clearLayers();

    const active = filteredCircuits || circuits;
    const byZone = new Map();
    active.forEach(c => {
      const sourceRegion = normal(c.region);
      const routeText = [c.title, c.summary, sourceRegion].filter(Boolean).join(' ');
      let resolvedZones = [];
      if (sourceRegion && zoneForRegion(sourceRegion) !== 'MADAGASCAR') {
        const primaryZone = zoneForRegion(sourceRegion);
        if (primaryZone) resolvedZones = [primaryZone];
      }
      // Les circuits nationaux ou sans région peuvent apparaître dans plusieurs zones
      // si leur titre/description nomme des destinations correspondantes.
      if (!resolvedZones.length) {
        // Pour une détection à partir du titre/résumé, ignorer les mots-cardinaux
        // génériques (« nord », « sud », « est », « ouest ») : ils sur-déclenchent
        // plusieurs marqueurs pour un circuit national.
        const genericAliases = new Set(['nord','sud','est','ouest']);
        const textValue = normalizeRegion(routeText);
        resolvedZones = zones
          .filter(zone => zone.name !== 'MADAGASCAR' && zone.aliases.some(alias => {
            const a = normalizeRegion(alias);
            return a.length > 2 && !genericAliases.has(a) && textValue.includes(a);
          }))
          .map(zone => zone.name);
      }
      if (!resolvedZones.length && (!sourceRegion || zoneForRegion(sourceRegion) === 'MADAGASCAR')) {
        // Circuit couvrant Madagascar entier : repère national dédié.
        resolvedZones = ['MADAGASCAR'];
      }
      resolvedZones.forEach(zoneName => {
        if (!byZone.has(zoneName)) byZone.set(zoneName, []);
        byZone.get(zoneName).push({circuit:c, sourceRegion:sourceRegion || '(région non renseignée)'});
      });
    });

    if (mapCount) {
      const zoneCount = [...byZone.keys()].filter(name => name !== 'MADAGASCAR').length;
      mapCount.textContent = zoneCount + (zoneCount > 1 ? ' zones' : ' zone');
    }

    const unmappedRegions = [];
    const sortedZones = [...byZone.keys()].sort((a,b) => {
      if (a === 'MADAGASCAR') return 1;
      if (b === 'MADAGASCAR') return -1;
      return a.localeCompare(b,'fr');
    });
    sortedZones.forEach(zoneName => {
      const items = byZone.get(zoneName);
      const loc = zoneName === 'MADAGASCAR' ? nationalLocation : zones.find(zone => zone.name === zoneName);
      if (!loc) {
        unmappedRegions.push(...items.map(item => item.sourceRegion));
        return;
      }
      const marker = L.circleMarker([loc.lat,loc.lng], {
        radius: Math.min(12, 6 + Math.sqrt(items.length) * 2),
        color:'#fff8f5', weight:2, fillColor:'#922d20', fillOpacity:.95
      }).addTo(markerLayer);
      const label = zoneName === 'MADAGASCAR' ? 'Madagascar · itinéraires nationaux' : zoneName;
      marker.bindTooltip(label + ' · ' + items.length + (items.length > 1 ? ' circuits' : ' circuit'));
      marker.bindPopup('<strong>' + escapeHtml(label) + '</strong><br>' + items.length + (items.length > 1 ? ' circuits disponibles' : ' circuit disponible') + '<br><button type="button" class="map-filter-button" data-region="' + escapeHtml(zoneName) + '">Voir cette zone</button>');
      marker.on('popupopen', event => {
        const button = event.popup.getElement()?.querySelector('.map-filter-button');
        if (button) button.addEventListener('click', () => {
          if (zoneName === 'MADAGASCAR') {
            regionFilter.value = 'all';
            render();
          } else {
            const option = [...regionFilter.options].find(item => item.value === zoneName);
            if (option) {
              regionFilter.value = zoneName;
              render();
              document.getElementById('catalogue-grid')?.scrollIntoView({behavior:'smooth',block:'start'});
            }
          }
          map.closePopup();
        }, {once:true});
      });
    });
    if (unmappedRegions.length) {
      console.warn('[MIARADI map] Régions non classées dans les 9 zones :', [...new Set(unmappedRegions)]);
    }
    // Vue stable sur l'île entière, indépendamment des filtres actifs.
    map.setView([-18.8,46.9], 5.3, {animate:false});
  }
  function render() {
    const region = regionFilter.value, theme = themeFilter.value, duration = durationFilter.value;
    const filtered = circuits.filter(c => {
      if(region!=='all' && zoneForRegion(normal(c.region) || 'Madagascar')!==region) return false;
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
      fillFilter(regionFilter,zones.map(zone=>zone.name),'Toutes les zones');
      fillFilter(themeFilter,[...new Set(circuits.map(c=>normal(c.theme)).filter(Boolean))],'Toutes les thématiques');
      render();
      renderMap(circuits);
    })
    .catch(error=>{count.textContent='Catalogue indisponible';grid.innerHTML='<p class="catalogue-error">'+escapeHtml(error.message)+' Réessayez dans quelques instants.</p>';renderMap([]);});
})();
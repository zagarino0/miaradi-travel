(() => {
  const grid = document.getElementById('catalogue-grid');
  const count = document.getElementById('catalogue-count');
  const regionFilter = document.getElementById('catalogue-region');
  const themeFilter = document.getElementById('catalogue-theme');
  const durationFilter = document.getElementById('catalogue-duration');
  let circuits = [];
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
    if(!filtered.length){grid.innerHTML='<p class="catalogue-empty">Aucun circuit ne correspond à ces filtres. Essayez une autre sélection.</p>';return;}
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
    })
    .catch(error=>{count.textContent='Catalogue indisponible';grid.innerHTML='<p class="catalogue-error">'+escapeHtml(error.message)+' Réessayez dans quelques instants.</p>';});
})();
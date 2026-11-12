(() => {
  const footer = document.querySelector('.site-footer');
  const bottom = footer?.querySelector('.footer-bottom');
  if (!footer || !bottom) return;
  fetch('backend/public/api/footer-partners.php', { headers: { Accept: 'application/json' } })
    .then(response => { if (!response.ok) throw new Error('Partners unavailable'); return response.json(); })
    .then(payload => {
      const partners = Array.isArray(payload.data) ? payload.data : [];
      if (!partners.length) return;
      const section = document.createElement('section');
      section.className = 'footer-partners';
      section.setAttribute('aria-label', 'Nos partenaires');
      const title = document.createElement('h2');
      title.textContent = 'Nos partenaires';
      section.appendChild(title);
      const list = document.createElement('div');
      list.className = 'footer-partners-list';
      partners.forEach(partner => {
        const item = partner.url ? document.createElement('a') : document.createElement('div');
        item.className = 'footer-partner';
        if (partner.url) {
          item.href = partner.url;
          item.target = '_blank';
          item.rel = 'noopener noreferrer';
          item.setAttribute('aria-label', partner.name + ' — site partenaire (nouvel onglet)');
        }
        const logo = document.createElement('img');
        logo.src = partner.logo;
        logo.alt = partner.alt || partner.name;
        logo.loading = 'lazy';
        logo.decoding = 'async';
        item.appendChild(logo);
        const name = document.createElement('span');
        name.className = 'footer-partner-name';
        name.textContent = partner.name || '';
        item.appendChild(name);
        list.appendChild(item);
      });
      section.appendChild(list);
      footer.insertBefore(section, bottom);
    })
    .catch(() => { /* The footer remains usable when the partner API is not configured. */ });
})();

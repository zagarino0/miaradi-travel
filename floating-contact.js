(() => {
  "use strict";

  // Renseigner ici le numéro WhatsApp officiel de MIARADI-TRAVEL au format international,
  // chiffres uniquement (ex. indicatif pays + numéro, sans +, espaces ni tirets).
  // Aucun numéro n'est préconfiguré afin de ne pas inventer les coordonnées de l'agence.
  const WHATSAPP_NUMBER = "261345740491";
  const MESSAGE = "Bonjour MIARADI-TRAVEL, je souhaite obtenir des informations sur vos voyages à Madagascar.";

  const styles = `
    .miaradi-floating-contact {
      position: fixed;
      z-index: 1200;
      right: max(22px, env(safe-area-inset-right));
      bottom: max(22px, env(safe-area-inset-bottom));
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 11px;
      min-height: 54px;
      padding: 0 20px;
      border: 1px solid rgba(255,255,255,.2);
      border-radius: 999px;
      background: #922D20;
      color: #fff;
      box-shadow: 0 10px 28px rgba(33,29,26,.22);
      font: 700 13px/1.2 "DM Sans", Arial, sans-serif;
      text-decoration: none;
      transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .miaradi-floating-contact:hover {
      transform: translateY(-3px);
      background: #742319;
      box-shadow: 0 14px 32px rgba(33,29,26,.28);
    }
    .miaradi-floating-contact:focus-visible {
      outline: 3px solid #211d1a;
      outline-offset: 4px;
    }
    .miaradi-floating-contact svg { width: 22px; height: 22px; flex: 0 0 22px; }
    @media (max-width: 520px) {
      .miaradi-floating-contact {
        right: max(14px, env(safe-area-inset-right));
        bottom: max(14px, env(safe-area-inset-bottom));
        min-height: 50px;
        padding: 0 16px;
        font-size: 12px;
      }
    }
    @media (prefers-reduced-motion: reduce) {
      .miaradi-floating-contact { transition: none; }
      .miaradi-floating-contact:hover { transform: none; }
    }
  `;

  const installButton = () => {
    if (document.querySelector(".miaradi-floating-contact")) return;

    const style = document.createElement("style");
    style.id = "miaradi-floating-contact-styles";
    style.textContent = styles;
    document.head.append(style);

    const isConfigured = /^\d{8,15}$/.test(WHATSAPP_NUMBER);
    const onHomePage = /(?:^|\/)index\.html$/.test(window.location.pathname) ||
      window.location.pathname.endsWith("/");
    const contactUrl = onHomePage ? "#contact" : "index.html#contact";
    const button = document.createElement("a");
    button.className = "miaradi-floating-contact";
    button.href = isConfigured
      ? "https://wa.me/" + WHATSAPP_NUMBER + "?text=" + encodeURIComponent(MESSAGE)
      : contactUrl;
    button.setAttribute("aria-label", isConfigured
      ? "Contacter MIARADI-TRAVEL sur WhatsApp"
      : "Contacter MIARADI-TRAVEL via le formulaire de contact");
    button.innerHTML = `
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path fill="currentColor" d="M12.04 2a9.84 9.84 0 0 0-8.43 14.92L2.2 22l5.24-1.37A9.93 9.93 0 0 0 12.04 22h.01A10 10 0 0 0 12.04 2Zm0 18.18a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.11.81.83-3.03-.2-.31a8.17 8.17 0 1 1 6.96 3.85Zm4.49-6.12c-.25-.13-1.47-.73-1.7-.81-.23-.08-.4-.13-.57.13-.17.25-.65.81-.8.98-.15.17-.3.19-.55.06-.25-.13-1.05-.39-2-.1-.74-.67-1.23-1.5-1.37-1.75-.14-.25-.02-.39.11-.52.11-.11.25-.3.37-.45.12-.15.16-.25.25-.42.08-.17.04-.32-.02-.45-.06-.13-.57-1.37-.78-1.87-.2-.48-.41-.42-.57-.43h-.49c-.17 0-.45.06-.68.32-.23.25-.89.87-.89 2.12s.91 2.46 1.04 2.63c.13.17 1.79 2.73 4.34 3.83.61.26 1.09.42 1.47.53.62.2 1.18.17 1.62.1.5-.07 1.47-.6 1.68-1.19.21-.58.21-1.08.15-1.18-.06-.1-.23-.16-.48-.29Z"/>
      </svg>
      <span>Contactez-nous</span>
    `;
    document.body.append(button);
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", installButton, { once: true });
  } else {
    installButton();
  }
})();
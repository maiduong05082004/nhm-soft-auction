document.addEventListener('DOMContentLoaded', function () {
  const openBtn = document.querySelector('[data-open-mobile-menu]');
  const overlay = document.getElementById('mobileMenuOverlay');
  const panel = document.getElementById('mobileMenuPanel');
  const closeSelectors = panel.querySelectorAll('#closeMobileMenu');

  function open() {
    overlay.classList.remove('hidden');
    panel.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }
  function close() {
    overlay.classList.add('hidden');
    panel.classList.add('hidden');
    document.body.style.overflow = '';
  }

  openBtn && openBtn.addEventListener('click', open);
  overlay && overlay.addEventListener('click', close);
  closeSelectors.forEach(btn => btn.addEventListener('click', close));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
  //popupsearch header
  const openBtnPopupSearch = document.getElementById("openPopup");
  const closeBtn = document.getElementById("closePopup");
  const closeBtn2 = document.getElementById("closeBtn");
  const overlayPopupSearch = document.getElementById("popupOverlay");

  openBtnPopupSearch.addEventListener("click", () => {
    overlayPopupSearch.classList.remove("hidden");
    overlayPopupSearch.classList.add("flex");
  });

  function closePopup() {
    overlayPopupSearch.classList.add("hidden");
    overlayPopupSearch.classList.remove("flex");
  }

  closeBtn.addEventListener("click", closePopup);
  closeBtn2.addEventListener("click", closePopup);

  overlayPopupSearch.addEventListener("click", (e) => {
    if (e.target === overlayPopupSearch) {
      closePopup();
    }
  });

});

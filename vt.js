// vt.js — Dirección de transición entre secciones
(function () {
  const order = ["estancia1.php", "estancia2.php", "estadia.php", "delfin.php", "conocete.php"];

  // Obtiene basename del href (sin query ni hash)
  const getBase = (url) => {
    try {
      const u = new URL(url, window.location.href);
      return u.pathname.split("/").pop() || "estancia1.php";
    } catch {
      return String(url || "").split("/").pop() || "estancia1.php";
    }
  };

  const current = getBase(location.href);
  const currentIdx = order.indexOf(current);

  // Guardado temprano del dir en sessionStorage y en dataset del body (para la nueva página)
  function setDir(dir) {
    sessionStorage.setItem("vt-dir", dir);
    document.body.dataset.dir = dir;
  }

  // Restaura dir en la carga (por si no se usó startViewTransition)
  window.addEventListener("DOMContentLoaded", () => {
    const saved = sessionStorage.getItem("vt-dir");
    if (saved) {
      document.body.dataset.dir = saved;
      // No limpiar aquí aún; dejar que la nueva página lo lea antes de pintar.
      // Se limpia al finalizar la transición o tras un pequeño timeout.
      setTimeout(() => sessionStorage.removeItem("vt-dir"), 350);
    }
  });

  // Delegación: enlaces del sidebar
  document.addEventListener("click", (ev) => {
    const a = ev.target.closest('.navlist a, header .actions a');
    if (!a) return;

    const targetBase = getBase(a.getAttribute("href"));
    const targetIdx = order.indexOf(targetBase);
    if (targetIdx === -1 || currentIdx === -1) {
      setDir("same");
      return; // navegación normal
    }

    let dir = "same";
    if (targetIdx > currentIdx) dir = "next";
    else if (targetIdx < currentIdx) dir = "prev";

    if (!document.startViewTransition) {
      setDir(dir);
      return; // deja que navegue normal con fallback
    }

    ev.preventDefault();
    setDir(dir);
    document.startViewTransition(() => {
      window.location.href = a.href;
    });
  });
})();

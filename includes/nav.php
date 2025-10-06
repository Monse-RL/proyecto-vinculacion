<?php
/* nav.php — Topbar + Sidebar + Loader + Dirección VT
   Usa $active para resaltar: estancia1, estancia2, estadia, delfin, conocete
*/
?>
<header class="topbar">
  <div class="wrap">
    <div class="brand"><span class="dot"></span> Vinculación</div>
    <div class="title">Gestor de Documentos</div>
    <div class="actions">
      <a class="link-btn" href="estancia1.php">Inicio</a>
    </div>
  </div>
</header>

<!-- Loader Overlay -->
<div id="page-loader" aria-hidden="true">
  <div class="loader-box" role="status" aria-live="polite">
    <div class="spinner" aria-hidden="true"></div>
    <div class="loader-text">Cargando…</div>
  </div>
</div>

<div class="layout">
  <aside class="sidebar">
    <h2>Secciones</h2>
    <nav>
      <ul class="navlist">
        <li><a class="<?php echo ($active==='estancia1'?'active':'');?>" href="estancia1.php">📁 Estancia 1</a></li>
        <li><a class="<?php echo ($active==='estancia2'?'active':'');?>" href="estancia2.php">📁 Estancia 2</a></li>
        <li><a class="<?php echo ($active==='estadia'  ?'active':'');?>" href="estadia.php">📁 Estadia</a></li>
        <li><a class="<?php echo ($active==='delfin'   ?'active':'');?>" href="delfin.php">📁 Delfín</a></li>
        <li><a class="<?php echo ($active==='conocete' ?'active':'');?>" href="conocete.php">📁 Conócete</a></li>
      </ul>
    </nav>
  </aside>

  <script>
    // ====== Dirección para View Transitions ======
    window.CURRENT_SECTION = <?php echo json_encode($active ?? null); ?>;
    const ORDER = ["estancia1","estancia2","estadia","delfin","conocete"];

    const mapHrefToSection = (href) => {
      try{
        const url = new URL(href, window.location.href);
        const file = url.pathname.split("/").pop().split("?")[0].toLowerCase();
        if (file.endsWith(".php")) return file.replace(".php","");
        if (file === "" || file === "index") return "estancia1";
        return file;
      }catch(e){ return null; }
    };

    // Aplica la dirección guardada al cargar
    (function applyDirectionFromStorage(){
      const dir = sessionStorage.getItem("vt-dir") || "neutral";
      document.documentElement.setAttribute("data-vt-dir", dir);
      sessionStorage.removeItem("vt-dir");
    })();

    // ====== Loader helpers ======
    const loader = document.getElementById('page-loader');
    const showLoader = () => loader && loader.classList.add('show');
    const hideLoader = () => loader && loader.classList.remove('show');

    // Muestra loader si la carga se retrasa (por si hay caché rápida no parpadea)
    let delayedTimer;
    const delayedShow = () => {
      clearTimeout(delayedTimer);
      delayedTimer = setTimeout(showLoader, 120); // 120ms evita flash innecesario
    };

    // Oculta loader si vuelve a la página por historial (bfcache)
    window.addEventListener('pageshow', (e)=>{ if (e.persisted) hideLoader(); });

    // Intercepta clicks en sidebar y botón Inicio
    document.addEventListener("click", (ev) => {
      const a = ev.target.closest(".navlist a, .actions a");
      if (!a) return;

      // Solo enlaces same-origin y navegación real
      const targetUrl = new URL(a.href, window.location.href);
      if (targetUrl.origin !== window.location.origin) return;

      const current = window.CURRENT_SECTION;
      const target  = mapHrefToSection(a.getAttribute("href"));
      if (current && target){
        const ci = ORDER.indexOf(current), ti = ORDER.indexOf(target);
        if (ci !== -1 && ti !== -1 && ci !== ti) {
          sessionStorage.setItem("vt-dir", (ti > ci) ? "forward" : "backward");
        } else {
          sessionStorage.setItem("vt-dir","neutral");
        }
      }

      // Muestra loader (con retardo suave)
      delayedShow();
    }, {capture:true});

    // Muestra loader al enviar formularios que cambian de página
    document.addEventListener("submit", (ev)=>{
      const form = ev.target;
      // Evita loader en submits AJAX (si los hubiera) o con target distinto
      if (form.target && form.target !== "_self") return;
      delayedShow();
    }, {capture:true});

    // Si la nueva página tarda, el loader queda visible; en la carga de la siguiente
    // página se reemplaza por el wipe y el overlay desaparece naturalmente.
  </script>
  <!-- Importante: aquí NO se cierra .layout; cada página incluye <main class="page"> y lo cierra con </main></div> -->

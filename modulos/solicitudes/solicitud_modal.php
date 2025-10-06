<?php
/* Modal para generar solicitud .DOCX en Estancia I / II / Estadía
   - Requiere: generar_solicitud_docx.php
   - Usa $SECCION definido en cada página (estancia1, estancia2, estadia)
*/
?>
<div class="modal-overlay" id="solicitud-overlay" hidden>
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="solicitud-title">
    <div class="modal-header">
      <h2 id="solicitud-title">Generar solicitud (.DOCX)</h2>
      <button type="button" class="modal-close" id="solicitud-close" aria-label="Cerrar">✕</button>
    </div>

    <form class="modal-body" method="POST" action="generar_solicitud_docx.php">
      <input type="hidden" name="seccion_actual" value="<?php echo htmlspecialchars($SECCION ?? ''); ?>">

      <fieldset class="grid">
        <legend>Solicitud de</legend>
        <label class="check"><input type="radio" name="solicitud_de" value="Estancia I" required><span>Estancia I</span></label>
        <label class="check"><input type="radio" name="solicitud_de" value="Estancia II"><span>Estancia II</span></label>
        <label class="check"><input type="radio" name="solicitud_de" value="Estadía"><span>Estadía</span></label>
      </fieldset>

      <div class="grid">
        <label>Periodo de Estancia/Estadía
          <input type="text" name="periodo" placeholder="Ej. Mayo–Agosto 2025" required>
        </label>
        <label class="check" style="align-self:end; margin-top: 22px;">
          <input type="checkbox" name="recurse" value="Sí"><span>¿Recurse?</span>
        </label>
      </div>

      <h3 class="sec">Datos del alumno</h3>
      <div class="grid">
        <label>Nombre completo<input type="text" name="alumno_nombre" required></label>
        <label>Sexo<input type="text" name="alumno_sexo" placeholder="F / M / Otro" required></label>
        <label>Cuatrimestre terminado<input type="text" name="cuatrimestre" placeholder="Ej. 6°" required></label>
        <label>Matrícula<input type="text" name="matricula" required></label>
        <label>Carrera<input type="text" name="carrera" required></label>
        <label>Teléfono<input type="tel" name="tel_alumno" required></label>
        <label>Correo electrónico<input type="email" name="mail_alumno" required></label>
      </div>

      <h3 class="sec">Datos para carta de presentación</h3>
      <div class="grid">
        <label>Nombre de la organización<input type="text" name="org_nombre" required></label>
        <label>Cargo y nombre del destinatario<input type="text" name="destinatario" placeholder="Ing. Juan Pérez – Director" required></label>
        <label>Teléfono de la organización<input type="tel" name="org_tel" required></label>
        <label>Correo del destinatario<input type="email" name="dest_mail" required></label>
        <label>Nombre del asesor académico<input type="text" name="asesor_academico" required></label>
      </div>

      <label>Observaciones
        <textarea name="observaciones" rows="3" placeholder="Opcional"></textarea>
      </label>

      <div class="modal-actions">
        <button type="button" class="btn ghost" id="solicitud-cancel">Cancelar</button>
        <button type="submit" class="btn">Generar y guardar</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function(){
    const btn = document.getElementById('btn-solicitud');
    const ovl = document.getElementById('solicitud-overlay');
    const closeBtns = [document.getElementById('solicitud-close'), document.getElementById('solicitud-cancel')];
    if(!btn || !ovl) return;

    const seccionActual = (<?php echo json_encode($SECCION ?? ''); ?> || '').toLowerCase();
    const map = { estancia1:'Estancia I', estancia2:'Estancia II', estadia:'Estadía' };

    btn.addEventListener('click', (e)=>{
      e.preventDefault();
      const val = map[seccionActual];
      if (val){
        const r = ovl.querySelector(`input[type="radio"][name="solicitud_de"][value="${val}"]`);
        if (r) r.checked = true;
      }
      ovl.hidden = false;
      setTimeout(()=>ovl.classList.add('show'), 10);
    });

    closeBtns.forEach(el => el?.addEventListener('click', ()=>{
      ovl.classList.remove('show');
      setTimeout(()=> ovl.hidden = true, 180);
    }));

    ovl.addEventListener('click', (e)=>{
      if (e.target === ovl){
        ovl.classList.remove('show');
        setTimeout(()=> ovl.hidden = true, 180);
      }
    });

    document.addEventListener('keydown', (e)=>{
      if (e.key === 'Escape' && !ovl.hidden){
        ovl.classList.remove('show');
        setTimeout(()=> ovl.hidden = true, 180);
      }
    });
  })();
</script>



//---Non affichage des transfert---
const SHOW_TRANSFERS = false;

// --- script.js (Pro) AM1+AM2+AM3 (UI simplifiée) ---
const API_BASE = 'api/';

// State
let rooms = [
  'Chambre 1','Chambre 2','Chambre 3','Chambre 4','Chambre 5',
  'Chambre 6','Chambre 7','Chambre 8','Chambre 9','Chambre 10'
];
let current = new Date();
let mode = 'month';
let reservations = []; // loaded from API

// DOM
const plannerHeader = document.getElementById('plannerHeader');
const plannerBody = document.getElementById('plannerBody');
const periodLabel = document.getElementById('periodLabel');
const viewMode = document.getElementById('viewMode');
const plannerTable = document.getElementById('plannerTable');
const ganttContainer = document.getElementById('ganttContainer');
document.getElementById('prevBtn').onclick = ()=>shift(-1);
document.getElementById('nextBtn').onclick = ()=>shift(+1);
viewMode.onchange = (e)=>{ mode = e.target.value; render(); };

// Export CSV for current visible period
document.getElementById('exportCsv').onclick = ()=>{
  const [start, end] = daterangeForMode();
  const startStr = start.toISOString().slice(0,10);
  const endStr = end.toISOString().slice(0,10);
  const qs = new URLSearchParams({ start: startStr, end: endStr });
  window.location.href = API_BASE+'export_csv.php?'+qs.toString();
};

// Helpers
const weekdayNames = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];
function cellKey(y,m,d){return `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`}
function addDays(dateStr, n){
  const dt = new Date(dateStr);
  dt.setDate(dt.getDate()+n);
  return dt.toISOString().slice(0,10);
}
function occupancyToTotal(s){
  if (!s) return null;
  const parts = String(s).replace(/\s+/g,'').split('+').filter(Boolean);
  if (!parts.length) return null;
  let total = 0;
  for (const p of parts){
    const n = parseInt(p,10);
    if (Number.isNaN(n)) return null;
    total += n;
  }
  return total;
}

// Colors
function colorForReservation(id){
  const n = parseInt(id, 10);
  const hue = isNaN(n) ? 200 : (n * 47) % 360;
  const sat = 65;
  const light = 45;
  return `hsl(${hue} ${sat}% ${light}%)`;
}
function setReadableText(el){
  el.style.color = '#fff';
  el.style.textShadow = '0 1px 2px #0007';
  el.style.boxShadow = 'inset 0 0 0 1px #0005';
}

// Table helpers
function createTD(content, cls=''){ const td = document.createElement('td'); if (cls) td.className = cls; td.textContent = content; return td; }
function createCell(room, Y, M, d) {
  const td = document.createElement('td');
  const key = cellKey(Y, M, d);
  const dayRes = reservations.filter(
    r => r.room === room && r.date_start <= key && addDays(r.date_start, r.nights) > key
  );
  const div = document.createElement('div');
  div.className = 'cell' + (dayRes.length ? ' booked' : '');

  if (dayRes.length) {
    const r = dayRes[0];
    const demandeBadge = (r.chambre_demande == 1) ? " ⭐" : "";
    const status = r.status || 'reservation';
    const isBlock = (status !== 'reservation');

    if (isBlock) {
      div.classList.add('blocked');
    }

    // 👥 Occupation (2+2 ou nb personnes)
    const peopleBadge =
      r.occupancy && r.occupancy.trim() ? `👥 ${r.occupancy}` : `👥 ${r.count}`;

    // 🚐 Transferts
   const transferIcons = SHOW_TRANSFERS
     ? (r.transfer_arrivee === 'oui' ? '🚐A ' : '') +
    (r.transfer_depart === 'oui' ? '🚐D' : '')
    : '';
    // 🍽️ Repas
    const mealsText =
      (r.breakfast_count > 0 ? `☕x${r.breakfast_count} ` : '') +
      (r.halfboard_count > 0 ? `🍽️x${r.halfboard_count} ` : '') +
      (r.fullboard_count > 0 ? `🍱x${r.fullboard_count} ` : '');

    // 💶 Numéro de facture
    const invoiceHtml =
      r.invoice && r.invoice.trim() !== ''
        ? `<div class="invoice-badge">Fact. ${r.invoice}</div>`
        : '';

    // Contenu de la cellule
    if (isBlock) {
      const icon = (status === 'maintenance') ? '🛠️' : '🔒';
      const label = (status === 'maintenance') ? 'MAINTENANCE' : 'OPTION';
      const title = r.name && r.name.trim() !== '' ? `${label} — ${r.name}${demandeBadge}` : `${label}${demandeBadge}`;
      const reason = (r.block_reason && r.block_reason.trim() !== '') ? r.block_reason : '';
      div.innerHTML = `
        ${r.notes && r.notes.trim() !== '' ? '<span class="note-dot"></span>' : ''}
        <div class="title">${icon} ${title}</div>
        ${reason ? `<div class="line"><span class="badge">${reason}</span></div>` : ''}
      `;
    } else {
      div.innerHTML = `
        ${r.notes && r.notes.trim() !== '' ? '<span class="note-dot"></span>' : ''}
        <div class="title">${r.name || '(Sans nom)'}${demandeBadge}</div>
        ${invoiceHtml}
        <div class="line">
          <span class="badge">${peopleBadge}</span>
          ${mealsText}
          ${transferIcons}
        </div>
      `;
    }

    // Ouvre la modale au clic
    div.onclick = () => openModal(r);

    // Drag & drop
    div.draggable = true;
    div.ondragstart = ev => ev.dataTransfer.setData('resId', r.id);

    // Couleur + contraste du texte
    div.style.background = colorForReservation(r.id);
    setReadableText(div);
  } else {
    // Case libre → clic = création de résa
    div.innerHTML = `<div class="muted">Libre</div>`;
    div.onclick = () =>
      openModal({
        room,
        date_start: key,
        nights: 1,
        name: '',
        phone: '',
        count: 0,
        occupancy: '',
        breakfast: 'non',
        halfboard: 'non',
        fullboard: 'non',
        transfer_arrivee: 'non',
        transfer_depart: 'non',
        flight: '',
        invoice: '',
        notes: '',
        chambre_demande: 0,
        status: 'reservation',
        block_reason: '',
      });
  }

  // Zone de drop pour déplacer une résa
  div.ondragover = ev => ev.preventDefault();
  div.ondrop = async ev => {
    ev.preventDefault();
    const id = ev.dataTransfer.getData('resId');
    if (!id) return;
    const payload = { id: id, room: room, date_start: key };
    const res = await fetch(API_BASE + 'update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (!res.ok || data.error) {
      alert(data.error || 'Erreur API');
      return;
    }
    await loadReservations();
    renderGridOrGantt();
  };

  td.appendChild(div);
  return td;
}


// Meals counter (AM2)
function countMealsForDay(dateStr){
  let breakfasts = 0, halfboards = 0, fullboards = 0;
  reservations.forEach(r=>{
    if ((r.status || 'reservation') !== 'reservation') return;
    for(let i=0;i<r.nights;i++){
      const day = addDays(r.date_start, i);
      if(day === dateStr){
        breakfasts += (r.breakfast_count||0);
        halfboards += (r.halfboard_count||0);
        fullboards += (r.fullboard_count||0);
      }
    }
  });
  return { breakfasts, halfboards, fullboards };
}

function renderMonth(){
  const Y = current.getFullYear();
  const M = current.getMonth();
  periodLabel.textContent = current.toLocaleString('fr-FR', { month:'long', year:'numeric' });

  plannerHeader.innerHTML = '';
  const th0 = document.createElement('th'); th0.textContent = 'Chambre';
  plannerHeader.appendChild(th0);

  const days = new Date(Y, M+1, 0).getDate();
  for (let d=1; d<=days; d++){
    const dt = new Date(Y,M,d);
    const dateStr = dt.toISOString().slice(0,10);
    const th = document.createElement('th');
    th.textContent = `${weekdayNames[dt.getDay()]} ${d}/${M+1}`;
    // Colorer les week-ends
    if (dt.getDay() === 0 || dt.getDay() === 6) {
        th.classList.add("weekend");
    }

    th.style.cursor = 'pointer';
    th.title = 'Clique pour voir les totaux repas';
    th.onclick = ()=>{
      const {breakfasts, halfboards, fullboards} = countMealsForDay(dateStr);
      alert(`${dateStr}\n☕ Petits-déj : ${breakfasts}\n🍽️ Demi-pension : ${halfboards}\n🍱 Pension complète : ${fullboards}`);
    };
    plannerHeader.appendChild(th);
  }
  plannerBody.innerHTML='';
  rooms.forEach(room=>{
    const tr = document.createElement('tr');
    tr.appendChild(createTD(room, 'room'));
    for (let d=1; d<=days; d++) tr.appendChild(createCell(room,Y,M,d));
    plannerBody.appendChild(tr);
  });
}
function renderYear() {
  const Y = current.getFullYear();
  periodLabel.textContent = `Année ${Y}`;

  plannerHeader.innerHTML = '';
  const th0 = document.createElement('th');
  th0.textContent = 'Chambre';
  plannerHeader.appendChild(th0);

  const allDates = [];

  // Construire toutes les dates de l'année
  for (let M = 0; M < 12; M++) {
    const daysInMonth = new Date(Y, M + 1, 0).getDate();
    for (let d = 1; d <= daysInMonth; d++) {
      const dt = new Date(Y, M, d);
      const dateStr = dt.toISOString().slice(0, 10);
      allDates.push(dt);

      const th = document.createElement('th');
      th.textContent = `${weekdayNames[dt.getDay()]} ${d}/${M + 1}`;

      // Colorer les week-ends
      if (dt.getDay() === 0 || dt.getDay() === 6) {
        th.classList.add('weekend');
      }

      th.style.cursor = 'pointer';
      th.title = 'Clique pour voir les totaux repas';
      th.onclick = () => {
        const { breakfasts, halfboards, fullboards } = countMealsForDay(dateStr);
        alert(
          `${dateStr}\n` +
            `☕ Petits-déj : ${breakfasts}\n` +
            `🍽️ Demi-pension : ${halfboards}\n` +
            `🍱 Pension complète : ${fullboards}`
        );
      };

      plannerHeader.appendChild(th);
    }
  }

  plannerBody.innerHTML = '';
  rooms.forEach(room => {
    const tr = document.createElement('tr');
    tr.appendChild(createTD(room, 'room'));
    allDates.forEach(dt => {
      tr.appendChild(createCell(room, dt.getFullYear(), dt.getMonth(), dt.getDate()));
    });
    plannerBody.appendChild(tr);
  });
}

function renderWeek(){
  const base = new Date(current);
  const wd = base.getDay();
  const monday = new Date(base);
  monday.setDate(base.getDate() - ((wd+6)%7));
  const days = [...Array(7)].map((_,i)=> new Date(monday.getFullYear(), monday.getMonth(), monday.getDate()+i));
  periodLabel.textContent = `Semaine du ${days[0].toLocaleDateString('fr-FR')} au ${days[6].toLocaleDateString('fr-FR')}`;

  plannerHeader.innerHTML = '';
  const th0 = document.createElement('th'); th0.textContent = 'Chambre';
  plannerHeader.appendChild(th0);

  days.forEach(dt => {
    const dateStr = dt.toISOString().slice(0,10);
    const th = document.createElement('th');
    th.textContent = `${weekdayNames[dt.getDay()]} ${dt.getDate()}/${dt.getMonth()+1}`;
    th.style.cursor = 'pointer';
    th.title = 'Clique pour voir les totaux repas';
    th.onclick = ()=>{
      const {breakfasts, halfboards, fullboards} = countMealsForDay(dateStr);
      alert(`${dateStr}\n☕ Petits-déj : ${breakfasts}\n🍽️ Demi-pension : ${halfboards}\n🍱 Pension complète : ${fullboards}`);
    };
    plannerHeader.appendChild(th);
  });

  plannerBody.innerHTML='';
  rooms.forEach(room=>{
    const tr = document.createElement('tr');
    tr.appendChild(createTD(room, 'room'));
    days.forEach(dt => tr.appendChild(createCell(room, dt.getFullYear(), dt.getMonth(), dt.getDate())));
    plannerBody.appendChild(tr);
  });
}

function daterangeForMode() {
  if (mode === 'week') {
    const base = new Date(current);
    const monday = new Date(base);
    monday.setDate(base.getDate() - ((base.getDay() + 6) % 7));
    const sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);
    return [monday, sunday];
  } else if (mode === 'year') {
    const Y = current.getFullYear();
    const start = new Date(Y, 0, 1);
    const end = new Date(Y, 11, 31);
    return [start, end];
  } else {
    // mois (et gantt qui se base sur un mois)
    const Y = current.getFullYear(),
      M = current.getMonth();
    const start = new Date(Y, M, 1);
    const end = new Date(Y, M + 1, 0);
    return [start, end];
  }
}


function renderGantt(){
  const [start, end] = daterangeForMode();
  const days = Math.floor((end-start)/(24*3600*1000))+1;
  periodLabel.textContent = `${start.toLocaleDateString('fr-FR')} → ${end.toLocaleDateString('fr-FR')} (${days} j)`;
  plannerTable.hidden = true;
  ganttContainer.hidden = false;
  ganttContainer.innerHTML = '';

  rooms.forEach(room=>{
    const wrap = document.createElement('div');
    wrap.className = 'room';
    wrap.innerHTML = `<div class="title">${room}</div><div class="axis"></div><div class="track"></div>`;
    const axis = wrap.querySelector('.axis');
    const track = wrap.querySelector('.track');
    const ticks = [];
    for (let i=0;i<days;i++){
      const d = new Date(start); d.setDate(start.getDate()+i);
      if (d.getDate()===1 || i===0) ticks.push(d.toLocaleDateString('fr-FR'));
    }
    axis.textContent = ticks.join('  ·  ');

    const roomRes = reservations.filter(r=> r.room===room);
    roomRes.forEach(r=>{
      const rStart = new Date(r.date_start);
      const rEnd = new Date(rStart); rEnd.setDate(rEnd.getDate()+r.nights);
      const iStart = new Date(Math.max(rStart, start));
      const iEnd = new Date(Math.min(rEnd, new Date(end.getFullYear(), end.getMonth(), end.getDate()+1)));
      if (iEnd <= iStart) return;
      const totalMs = (end - start) + 24*3600*1000;
      const leftPct = ((iStart - start) / totalMs) * 100;
      const widthPct = ((iEnd - iStart) / totalMs) * 100;
      const bar = document.createElement('div');
      bar.className = 'bar';
      bar.style.left = leftPct+'%';
      bar.style.width = Math.max(1,widthPct)+'%';

      // Couleur + texte
      bar.style.background = colorForReservation(r.id);
      bar.style.borderColor = '#0005';
      bar.style.color = '#fff';
      bar.style.textShadow = '0 1px 2px #0007';

      const peopleText = (r.occupancy && r.occupancy.trim()) ? r.occupancy : r.count;
      const tIcons =
        (r.transfer_arrivee === 'oui' ? ' 🚐A' : '') +
        (r.transfer_depart  === 'oui' ? ' 🚐D' : '');
      bar.title = `${r.name||'(Sans nom)'} • ${r.nights} nuit(s) • #${r.id}`;
      bar.textContent = `${r.name||'(Sans nom)'} • 👥${peopleText}`
        + (r.breakfast === 'oui' ? ' ☕' : '')
        + (r.halfboard === 'oui' ? ' 🍽️' : '')
        + tIcons;
      bar.onclick = ()=> openModal(r);
      track.appendChild(bar);
    });

    ganttContainer.appendChild(wrap);
  });
}

// Modal
const modal = document.getElementById('resModal');
const form = document.getElementById('resForm');
const delBtn = document.getElementById('deleteBtn');
const modalCloseBtn = document.getElementById('modalClose');
const statusField = document.getElementById('resStatus');
const dateStartField = document.getElementById('resDateStart');
const nightsField = document.getElementById('resNights');
const staySummaryEl = document.getElementById('resStaySummary');
const endPreviewEl = document.getElementById('resDateEndPreview');
const transferSection = document.getElementById('transferSection');
modalCloseBtn.onclick = ()=> modal.hidden = true;
document.getElementById('cancelBtn').onclick = ()=> modal.hidden = true;

function formatDateFr(dateStr){
  if (!dateStr) return '—';
  const [y,m,d] = String(dateStr).split('-').map(Number);
  if (!y || !m || !d) return '—';
  const dt = new Date(y, m - 1, d);
  return dt.toLocaleDateString('fr-FR', { weekday:'short', day:'2-digit', month:'short', year:'numeric' });
}

function updateStayPreview(){
  const start = dateStartField.value;
  const nights = Math.max(1, parseInt(nightsField.value || '1', 10) || 1);
  const end = start ? addDays(start, nights) : '';
  staySummaryEl.textContent = start ? `${nights} nuit${nights > 1 ? 's' : ''}` : '—';
  endPreviewEl.textContent = end ? formatDateFr(end) : '—';
}

function updateModalLayout(){
  const status = statusField.value || 'reservation';
  const isReservation = status === 'reservation';
  document.querySelectorAll('.reservation-only').forEach(el => {
    el.hidden = !isReservation;
  });
  document.querySelectorAll('.block-only').forEach(el => {
    el.hidden = isReservation;
  });
  if (transferSection){
    transferSection.style.display = (SHOW_TRANSFERS && isReservation) ? '' : 'none';
  }
  modal.dataset.mode = isReservation ? 'reservation' : 'block';
}

statusField.addEventListener('change', updateModalLayout);
dateStartField.addEventListener('input', updateStayPreview);
nightsField.addEventListener('input', updateStayPreview);

// Keyboard shortcuts: ESC to cancel/close, ENTER to save (except in textarea)
document.addEventListener('keydown', (e)=>{
  const modalEl = document.getElementById('resModal');
  if (modalEl && !modalEl.hidden){
    if (e.key === 'Escape'){
      e.preventDefault();
      // Close without saving
      modal.hidden = true;
    } else if (e.key === 'Enter'){
      const tag = (document.activeElement && document.activeElement.tagName) ? document.activeElement.tagName.toUpperCase() : '';
      // Avoid submitting when typing in a textarea (allows new lines)
      if (tag !== 'TEXTAREA'){
        e.preventDefault();
        // Save changes
        form.requestSubmit();
      }
    }
  }
});

modal.addEventListener('click', (e)=>{
  if (e.target === modal) modal.hidden = true;
});

// ✅ Version sans lien automatique entre occupancy et count
form.onsubmit = async (e)=>{
  e.preventDefault();
  const fd = new FormData(form);
  const payload = Object.fromEntries(fd.entries());
  payload.chambre_demande = document.getElementById('resChambreDemande').checked ? 1 : 0;

  // On laisse le user gérer :
  // - count = nb de personnes (numérique)
  // - occupancy = détail couchage (texte libre)
  payload.count = parseInt(payload.count || '0', 10);
  payload.occupancy = (payload.occupancy || '').trim();

  payload.nights = parseInt(payload.nights || '1', 10);

  payload.breakfast_count = parseInt(document.getElementById('resBreakfastCount').value||'0',10) || 0;
  payload.halfboard_count = parseInt(document.getElementById('resHalfboardCount').value||'0',10) || 0;
  payload.fullboard_count = parseInt(document.getElementById('resFullboardCount').value||'0',10) || 0;

  payload.status = (payload.status || 'reservation');
  if (payload.status !== 'reservation') {
    payload.count = 0;
    payload.occupancy = '';
    payload.breakfast_count = 0;
    payload.halfboard_count = 0;
    payload.fullboard_count = 0;
    payload.transfer_arrivee = 'non';
    payload.transfer_depart = 'non';
    payload.flight = '';
  }

  const isUpdate = !!payload.id;
  const endpoint = isUpdate ? 'update.php' : 'add.php';
  const res = await fetch(API_BASE+endpoint, {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  });
  const data = await res.json();
  if (!res.ok || data.error){
    alert(data.error||'Erreur API');
    return;
  }
  modal.hidden = true;
  await loadReservations();
  renderGridOrGantt();
};

delBtn.onclick = async ()=>{
  const id = document.getElementById('resId').value;
  if (!id) return;
  if (!confirm('Supprimer la réservation ?')) return;
  const res = await fetch(API_BASE+'delete.php?id='+encodeURIComponent(id), {method:'DELETE'});
  const data = await res.json();
  if (!res.ok || data.error){ alert(data.error||'Erreur API'); return; }
  modal.hidden = true;
  await loadReservations();
  renderGridOrGantt();
};

function shift(step) {
  if (mode === 'month') {
    current.setMonth(current.getMonth() + step);
  } else if (mode === 'week') {
    current.setDate(current.getDate() + step * 7);
  } else if (mode === 'year') {
    current.setFullYear(current.getFullYear() + step);
  } else {
    // gantt ou fallback → on bouge par mois
    current.setMonth(current.getMonth() + step);
  }
  render();
}

// Renderers
async function loadReservations(){
  // UI simplifiée : on récupère tout
  const res = await fetch(API_BASE+'list.php');
  const data = await res.json();
  if (!res.ok) throw new Error(data.error||'Erreur API');
  reservations = (data.items||[]).map(x=>({...x, id: String(x.id)}));
}

async function render(){
  await loadReservations().catch(e=>{
    console.error(e);
    alert('Impossible de charger les réservations (API). Vérifie la base SQL et les fichiers PHP.');
  });
  renderGridOrGantt();
}
function renderGridOrGantt() {
  const isGantt = (mode === 'gantt');

  plannerTable.hidden = isGantt;
  ganttContainer.hidden = !isGantt;

  // Safety: some CSS rules can visually override [hidden] depending on browser/style priority.
  plannerTable.style.display = isGantt ? 'none' : '';
  ganttContainer.style.display = isGantt ? '' : 'none';

  if (isGantt) {
    renderGantt();
    return;
  }

  if (mode === 'week') {
    renderWeek();
  } else if (mode === 'year') {
    renderYear();
  } else {
    // défaut : mois
    renderMonth();
  }
}


// open modal for create or edit
function openModal(r){
  document.getElementById('modalTitle').textContent = r.id ? `Réservation #${r.id}` : 'Nouvelle réservation';
  document.getElementById('resId').value = r.id || '';
  document.getElementById('resRoom').value = r.room || 'Chambre 1';
  document.getElementById('resDateStart').value = r.date_start || new Date().toISOString().slice(0,10);
  document.getElementById('resNights').value = r.nights || 1;
  document.getElementById('resName').value = r.name || '';
  document.getElementById('resPhone').value = r.phone || '';
  document.getElementById('resCount').value = r.count || 0;
  document.getElementById('resOccupancy').value = r.occupancy || '';
  document.getElementById('resBreakfastCount').value = r.breakfast_count || 0;
  document.getElementById('resHalfboardCount').value = r.halfboard_count || 0;
  document.getElementById('resFullboardCount').value = r.fullboard_count || 0;
  document.getElementById('resTransferArrivee').value = r.transfer_arrivee || 'non';
  document.getElementById('resTransferDepart').value  = r.transfer_depart  || 'non';
  document.getElementById('resFlight').value = r.flight || '';
  document.getElementById('resInvoice').value = r.invoice || '';
  document.getElementById('resNotes').value = r.notes || '';
  document.getElementById('resChambreDemande').checked = (String(r.chambre_demande||'0') === '1');
  document.getElementById('resStatus').value = r.status || 'reservation';
  document.getElementById('resBlockReason').value = r.block_reason || '';
  document.getElementById('deleteBtn').hidden = !r.id;
  updateModalLayout();
  updateStayPreview();
  modal.hidden = false;
  setTimeout(()=> document.getElementById('resRoom').focus(), 0);
}

// init
render();

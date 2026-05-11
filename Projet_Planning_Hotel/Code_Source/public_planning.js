
(() => {
  const rooms = Array.from({length:10}, (_,i)=>`Chambre ${i+1}`);
  const monthNames = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];

  const table = document.getElementById('pubTable');
  const label = document.getElementById('pubLabel');
  const prev = document.getElementById('pubPrev');
  const next = document.getElementById('pubNext');

  if (!table || !label || !prev || !next) return;

  let cur = new Date();
  cur.setDate(1);

  function yyyymm(d){
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    return `${y}-${m}`;
  }

  function daysInMonth(d){
    return new Date(d.getFullYear(), d.getMonth()+1, 0).getDate();
  }

  function fmtLabel(d){
    return `${monthNames[d.getMonth()]} ${d.getFullYear()}`;
  }

  function buildEmptyGrid(d){
    const dim = daysInMonth(d);
    const head = ['Chambre', ...Array.from({length:dim}, (_,i)=>String(i+1))];
    const grid = {};
    rooms.forEach(r => {
      grid[r] = Array.from({length:dim}, ()=>true); // true=dispo
    });
    return {head, grid};
  }

  async function load(d){
    label.textContent = fmtLabel(d);
    table.innerHTML = '';
    const ym = yyyymm(d);
    const dim = daysInMonth(d);

    const {head, grid} = buildEmptyGrid(d);

    // fetch bookings (unavailable ranges)
    let data = [];
    try{
      const res = await fetch(`api/public_availability.php?month=${encodeURIComponent(ym)}`, {cache:'no-store'});
      data = await res.json();
      if (!Array.isArray(data)) data = [];
    }catch(e){
      data = [];
    }

    // mark unavailable
    for (const b of data){
      const room = b.room;
      if (!grid[room]) continue;

      const start = new Date(b.date_start + 'T00:00:00');
      const end = new Date(b.date_end + 'T00:00:00'); // exclusive end
      const monthStart = new Date(d.getFullYear(), d.getMonth(), 1);
      const monthEnd = new Date(d.getFullYear(), d.getMonth(), dim+1);

      const s = start < monthStart ? monthStart : start;
      const e = end > monthEnd ? monthEnd : end;

      for (let dt = new Date(s); dt < e; dt.setDate(dt.getDate()+1)){
        const day = dt.getDate();
        if (day>=1 && day<=dim) grid[room][day-1] = false;
      }
    }

    // render
    const thead = document.createElement('thead');
    const trh = document.createElement('tr');
    head.forEach((h, idx) => {
      const th = document.createElement('th');
      th.textContent = h;
      if (idx === 0) th.style.minWidth = '140px';
      trh.appendChild(th);
    });
    thead.appendChild(trh);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    rooms.forEach(room => {
      const tr = document.createElement('tr');
      const td0 = document.createElement('td');
      td0.textContent = room;
      tr.appendChild(td0);

      for (let i=0;i<dim;i++){
        const td = document.createElement('td');
        const ok = grid[room][i];
        td.className = ok ? 'cell-ok' : 'cell-no';
        td.title = ok ? 'Libre' : 'Occupé';
        td.textContent = ok ? '✓' : '×';
        tr.appendChild(td);
      }
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
  }

  prev.addEventListener('click', () => {
    cur.setMonth(cur.getMonth()-1);
    load(cur);
  });
  next.addEventListener('click', () => {
    cur.setMonth(cur.getMonth()+1);
    load(cur);
  });

  load(cur);
})();

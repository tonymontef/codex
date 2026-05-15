(async function(){
  const qs=id=>document.getElementById(id);
  const list=qs('bertin-list'); if(!list) return;
  const settings=BertinApp.settings||{}; qs('b-title').textContent=settings.app_title||'Nos Réalisations'; qs('b-sub').textContent=settings.subtitle||'';
  const projects=await fetch(BertinApp.rest+'/projects').then(r=>r.json());
  const waves=[...new Set(projects.map(p=>p.wave).filter(Boolean))]; const types=[...new Set(projects.map(p=>p.building_type).filter(Boolean))];
  waves.forEach(v=>qs('bertin-wave').insertAdjacentHTML('beforeend',`<option>${v}</option>`));
  types.forEach(v=>qs('bertin-type').insertAdjacentHTML('beforeend',`<option>${v}</option>`));
  let filtered=projects; let markers=[]; let map=null;
  if(settings.enable_map){ map=L.map('bertin-map').setView([46.6,2.2],6); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);} else {qs('bertin-map').style.display='none';}

  function passes(p){
    const q=qs('bertin-search').value.toLowerCase(); const w=qs('bertin-wave').value; const t=qs('bertin-type').value; const withPhoto=qs('bertin-photo').checked;
    const hay=[p.client_name,p.address,p.city,p.postal_code,p.building_type,p.description,p.wave].join(' ').toLowerCase();
    return (!q||hay.includes(q)) && (!w||p.wave===w) && (!t||p.building_type===t) && (!withPhoto||Number(p.images_count)>0);
  }
  async function openDetail(id){ const p=await fetch(BertinApp.rest+'/project/'+id).then(r=>r.json()); const html=`<h3>${p.client_name}</h3><p>${p.building_type||''}</p><p>${p.address||''} ${p.postal_code||''}</p><p>${p.description||''}</p>`; qs('bertin-detail').innerHTML=html; qs('bertin-modal').hidden=false; }
  function render(){ filtered=projects.filter(passes); list.innerHTML=''; if(map){markers.forEach(m=>map.removeLayer(m)); markers=[];}
    filtered.forEach(p=>{const d=document.createElement('div'); d.className='bertin-card'; d.innerHTML=`<strong>${p.client_name}</strong><div>${p.address||''} ${p.postal_code||''}</div><div>${p.building_type||''} <span class="tag">${p.wave||''}</span> ${Number(p.images_count)>0?'📷':''}</div><button class="bertin-btn">${settings.main_button_text||'Voir détail'}</button>`; d.querySelector('button').onclick=()=>openDetail(p.id); list.appendChild(d);
      if(map&&p.latitude&&p.longitude){const m=L.marker([parseFloat(p.latitude),parseFloat(p.longitude)]).addTo(map).bindPopup(p.client_name); m.on('click',()=>openDetail(p.id)); markers.push(m);} });
    qs('bertin-count').textContent=`${filtered.length} résultat(s)`;
  }
  ['bertin-search','bertin-wave','bertin-type','bertin-photo'].forEach(id=>qs(id).addEventListener('input',render));
  qs('bertin-close').onclick=()=>qs('bertin-modal').hidden=true; render();
})();

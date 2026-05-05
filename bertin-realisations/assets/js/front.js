(async function(){
 const list=document.getElementById('bertin-list'); if(!list)return;
 const search=document.getElementById('bertin-search'); const cnt=document.getElementById('bertin-count');
 const projects=await fetch(BertinApp.rest+'/projects').then(r=>r.json()); let filtered=projects;
 let map=L.map('bertin-map').setView([46.6,2.2],6); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
 const markers=[];
 function render(){list.innerHTML=''; markers.forEach(m=>map.removeLayer(m)); markers.length=0;
 filtered.forEach(p=>{const d=document.createElement('div'); d.className='bertin-card'; d.innerHTML=`<strong>${p.client_name}</strong><br>${p.address} ${p.postal_code}<br>${p.building_type}`; list.appendChild(d);
 if(p.latitude&&p.longitude){const m=L.marker([parseFloat(p.latitude),parseFloat(p.longitude)]).addTo(map); m.bindPopup(p.client_name); markers.push(m);} });
 cnt.textContent=`${filtered.length} résultat(s)`; }
 search.addEventListener('input',()=>{const q=search.value.toLowerCase(); filtered=projects.filter(p=>[p.client_name,p.address,p.postal_code,p.building_type,p.wave].join(' ').toLowerCase().includes(q)); render();});
 render();
})();

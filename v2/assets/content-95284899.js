import{q as v,r as a,a2 as c,a3 as r}from"./index-90eb49f0.js";const P=v("content",()=>{const h=a([]),s=a([]),p=a([]),f=a([]),i=a([]);async function d(){let e="error";return await c.post(""+r.fetchNews,{},{headers:{"Content-Type":"application/json"}}).then(t=>{var n,o;console.log((n=t==null?void 0:t.headers)==null?void 0:n["content-type"]),((o=t==null?void 0:t.headers)==null?void 0:o["content-type"])!=="text/html; charset=utf-8"?h.value=t==null?void 0:t.data:e="error"}).catch(()=>{e="error"}),e}async function y(){let e="error";return await c.post(""+r.fetchContacts,{},{headers:{"Content-Type":"application/json"}}).then(t=>{var n,o;console.log((n=t==null?void 0:t.headers)==null?void 0:n["content-type"]),((o=t==null?void 0:t.headers)==null?void 0:o["content-type"])!=="text/html; charset=utf-8"?f.value=t==null?void 0:t.data:e="error"}).catch(()=>{e="error"}),e}async function C(){let e="error";return await c.post(""+r.fetchPartners,{},{headers:{"Content-Type":"application/json"}}).then(t=>{var n;((n=t==null?void 0:t.headers)==null?void 0:n["content-type"])!=="text/html; charset=utf-8"?s.value=t==null?void 0:t.data:e="error"}).catch(()=>{e="error"}),e}async function w(){let e="error";return await c.post(""+r.fetchClients,{},{headers:{"Content-Type":"application/json"}}).then(t=>{var n;((n=t==null?void 0:t.headers)==null?void 0:n["content-type"])!=="text/html; charset=utf-8"?p.value=t==null?void 0:t.data:e="error"}).catch(()=>{e="error"}),e}async function m(){try{const e=await c.get(""+r.fetchContent,{headers:{"Content-Type":"application/json"}});if(e.status===200&&e.data)i.value=e.data;else throw new Error("Некорректный ответ сервера")}catch(e){console.error("Ошибка при запросе к API:",e)}}function j(e){const t=e.join("/");return Object.values(i.value).find(n=>n.Partition===t)}return{newsList:h,partnersList:s,clientsList:p,documentsList:i,contacts:f,fetchContacts:y,fetchNews:d,fetchPartners:C,fetchClients:w,fetchDocuments:m,getDocumentByPartition:j}});export{P as u};

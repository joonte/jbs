import{s as E,r as a,a3 as n,a4 as o}from"./index-400feb76.js";const T=E("EDesks",()=>{const r=a(null),k=a(null),i=a(null);async function h(){let e="error";return await n.post(""+o.fetchEDesks).then(t=>{r.value=t==null?void 0:t.data}).catch(()=>{e="error"}),e}async function d(e){let t="error";return await n.get(""+o.fetchEDeskMessages+`?EdeskID=${e}`).then(s=>{k.value=s==null?void 0:s.data,t="success"}).catch(()=>{t="error"}),t}function f(e){let t=new FormData;for(let s in e)t.append(s,e[s]);return t}async function D(e){let t="error",s=f(e);return e!=null&&e.TicketMessageFile&&(e==null||e.TicketMessageFile.forEach(c=>{s.append("TicketMessageFile[]",c)})),await n.post(""+o.sendTicketMessage,s).then(c=>{t="success"}).catch(()=>{t="error"}),t}async function u(){let e="error";return await n.post(""+o.fetchTicketGroups).then(t=>{t!=null&&t.data&&(i.value=t.data,e="success")}).catch(()=>{e="error"}),e}async function g(e){let t="error",s=f(e);return e!=null&&e.TicketMessageFile&&(e==null||e.TicketMessageFile.forEach(c=>{s.append("TicketMessageFile[]",c)})),await n.post(""+o.ticketEdit,s).then(c=>{(c==null?void 0:c.data.Status)==="Ok"&&(i.value=c.data,t="success")}).catch(()=>{t="error"}),t}return{eDesksList:r,eDeskMessages:k,eDeskGroups:i,fetchEDesks:h,fetchEDeskMessages:d,sendTicketMessage:D,fetchTicketGroups:u,ticketEdit:g}});export{T as u};

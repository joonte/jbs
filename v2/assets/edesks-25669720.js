import{q as E,r as a,a2 as n,a3 as o}from"./index-00a0bf0d.js";const T=E("EDesks",()=>{const r=a(null),k=a(null),i=a(null);async function h(){let e="error";return await n.post(""+o.fetchEDesks).then(t=>{r.value=t==null?void 0:t.data}).catch(()=>{e="error"}),e}async function d(e){let t="error";return await n.get(""+o.fetchEDeskMessages+`?EdeskID=${e}`).then(c=>{k.value=c==null?void 0:c.data,t="success"}).catch(()=>{t="error"}),t}function f(e){let t=new FormData;for(let c in e)t.append(c,e[c]);return t}async function D(e){let t="error",c=f(e);return e!=null&&e.TicketMessageFile&&(e==null||e.TicketMessageFile.forEach(s=>{c.append("TicketMessageFile[]",s)})),await n.post(""+o.sendTicketMessage,c).then(s=>{t="success"}).catch(()=>{t="error"}),t}async function u(){let e="error";return await n.post(""+o.fetchTicketGroups).then(t=>{t!=null&&t.data&&(i.value=t.data,e="success")}).catch(()=>{e="error"}),e}async function g(e){let t="error",c=f(e);return e!=null&&e.TicketMessageFile&&(e==null||e.TicketMessageFile.forEach(s=>{c.append("TicketMessageFile[]",s)})),await n.post(""+o.ticketEdit,c).then(s=>{(s==null?void 0:s.data.Status)==="Ok"&&(i.value=s.data,t="success")}).catch(()=>{t="error"}),t}return{eDesksList:r,eDeskMessages:k,eDeskGroups:i,fetchEDesks:h,fetchEDeskMessages:d,sendTicketMessage:D,fetchTicketGroups:u,ticketEdit:g}});export{T as u};

import{o as l,c as d,F as D,x as A,n as N,a as m,t as h,k,p as x,l as y,_ as j,u as b,S as F,r as P,e as L,a7 as O,g as B}from"./index-3af25d4c.js";import{_ as R}from"./ButtonDefault-b95c906d.js";import{u as V}from"./invoices-5c27f2f4.js";import{u as W}from"./postings-cc82980a.js";const z=n=>(x("data-v-d2a59930"),n=n(),y(),n),E={class:"list-part"},q=z(()=>m("div",{class:"list-item__title"},"Выбрать платёжную систему",-1)),G={key:0,class:"list-row grid-net"},H=["onClick"],J=["src"],K={class:"list-item__image-title"},Q={class:"modal__error"};function T(n,p,v,e,I,S){return l(),d("div",E,[q,e.filterActiveSystems?(l(),d("div",G,[(l(!0),d(D,null,A(e.filterActiveSystems,t=>{var c;return l(),d(D,null,[t!=null&&t.IsActive?(l(),d("div",{key:0,class:N(["list-item",{"list-item_active":((c=e.paymentSystem)==null?void 0:c.id)===(t==null?void 0:t.ID)}]),onClick:_=>{e.paymentSystem={id:t==null?void 0:t.ID,type:t==null?void 0:t.Source},e.selectPaymentSystem()}},[m("img",{class:"list-item__image",src:t==null?void 0:t.Image},null,8,J),m("div",K,h(t==null?void 0:t.Description),1)],10,H)):k("",!0)],64)}),256))])):k("",!0),m("div",Q,h(e.errorMessage),1)])}const U={components:{ButtonDefault:R},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(n,{emit:p}){const v=b(),e=V(),I=W(),S=F("emitter"),t=P(null);L(),O();const c=B(()=>v.ConfigList),_=P(null),M=B(()=>{var a;const o=c.value,r=(a=n.data)==null?void 0:a.ContractPaymentSystems;if(o!=null&&o.PaymentSystems&&r){const u=new Set(r);return Object.keys(o.PaymentSystems).filter(s=>u.has(s)).map(s=>{const i=o.PaymentSystems[s];return String(i==null?void 0:i.IsActive)==="1"?i.Collations:null}).filter(s=>s!==null).flat().slice().sort((s,i)=>{const g=parseInt(s.SortID,10),C=parseInt(i.SortID,10);return isNaN(g)?1:isNaN(C)?-1:g-C})}else return null});function w(){var o,r;_.value!==null&&e.InvoiceMake({ContractID:(o=n.data)==null?void 0:o.ItemID,PaymentSystemID:(r=_.value)==null?void 0:r.type}).then(a=>{if(a.status==="success"&&a.data&&a.data.InvoiceID){p("modalClose");const u=a.data.InvoiceID;I.InvoiceDocument({InvoiceID:u}).then(f=>{S.emit("open-modal",{component:"InvoiceDocumentBasket",data:{html:f,ID:u}})})}else p("modalClose")})}return{getConfig:c,errorMessage:t,filterActiveSystems:M,selectPaymentSystem:w,paymentSystem:_}}},et=j(U,[["render",T],["__scopeId","data-v-d2a59930"]]);export{et as default};

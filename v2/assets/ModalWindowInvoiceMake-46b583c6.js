import{o as n,c as a,F as d,x as f,n as b,a as u,t as g,k as D,p as x,l as A,_ as w,u as B,S as j,r as C,e as F,a7 as L,g as h}from"./index-5f8fd0e9.js";import{_ as N}from"./ButtonDefault-458eab69.js";import{u as O}from"./invoices-9cebe57c.js";import{u as R}from"./postings-2abc5e3f.js";const V=c=>(x("data-v-238d17ab"),c=c(),A(),c),W={class:"list-part"},z=V(()=>u("div",{class:"list-item__title"},"Выбрать платёжную систему",-1)),E={key:0,class:"list-row grid-net"},q=["onClick"],G=["src"],H={class:"list-item__image-title"},J={class:"modal__error"};function K(c,_,m,s,v,I){return n(),a("div",W,[z,s.filterActiveSystems?(n(),a("div",E,[(n(!0),a(d,null,f(s.filterActiveSystems,p=>(n(),a(d,null,[(n(!0),a(d,null,f(p,e=>{var r;return n(),a(d,null,[e!=null&&e.IsActive?(n(),a("div",{key:0,class:b(["list-item",{"list-item_active":((r=s.paymentSystem)==null?void 0:r.id)===(e==null?void 0:e.ID)}]),onClick:y=>{s.paymentSystem={id:e==null?void 0:e.ID,type:e==null?void 0:e.Source},s.selectPaymentSystem()}},[u("img",{class:"list-item__image",src:e==null?void 0:e.Image},null,8,G),u("div",H,g(e==null?void 0:e.Description),1)],10,q)):D("",!0)],64)}),256))],64))),256))])):D("",!0),u("div",J,g(s.errorMessage),1)])}const Q={components:{ButtonDefault:N},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(c,{emit:_}){const m=B(),s=O(),v=R(),I=j("emitter"),p=C(null);F(),L();const e=h(()=>m.ConfigList),r=C(null),y=h(()=>{const o=e.value;return o!=null&&o.PaymentSystems?Object.keys(o.PaymentSystems).map(l=>{var i;const t=o.PaymentSystems[l];return String(t==null?void 0:t.IsActive)==="1"?(i=t.Collations)==null?void 0:i.slice().sort((M,P)=>parseInt(M.SortID)-parseInt(P.SortID)):null}).filter(l=>l!==null):null});function k(){var o,l;r.value!==null&&s.InvoiceMake({ContractID:(o=c.data)==null?void 0:o.ItemID,PaymentSystemID:(l=r.value)==null?void 0:l.type}).then(t=>{if(t.status==="success"&&t.data&&t.data.InvoiceID){_("modalClose");const i=t.data.InvoiceID;v.InvoiceDocument({InvoiceID:i}).then(S=>{I.emit("open-modal",{component:"InvoiceDocument",data:{html:S,ID:i}})})}else _("modalClose")})}return{getConfig:e,errorMessage:p,filterActiveSystems:y,selectPaymentSystem:k,paymentSystem:r}}},Z=w(Q,[["render",K],["__scopeId","data-v-238d17ab"]]);export{Z as default};

import{o as _,c as v,F as B,x as j,n as F,a as S,t as M,k as N,p as L,l as O,_ as R,u as V,S as W,r as w,e as z,a7 as E,g as A}from"./index-6f846e0d.js";import{_ as J}from"./ButtonDefault-40efcd87.js";import{u as T}from"./invoices-41375e35.js";import{u as $}from"./postings-24d27e64.js";const q=t=>(L("data-v-12eb2681"),t=t(),O(),t),G={class:"list-part"},H=q(()=>S("div",{class:"list-item__title"},"Выбрать платёжную систему",-1)),K={key:0,class:"list-row grid-net"},Q=["onClick"],U=["src"],X={class:"list-item__image-title"},Y={class:"modal__error"};function Z(t,l,P,a,D,C){return _(),v("div",G,[H,a.filterActiveSystems?(_(),v("div",K,[(_(!0),v(B,null,j(a.filterActiveSystems,e=>{var p;return _(),v(B,null,[e!=null&&e.IsActive?(_(),v("div",{key:0,class:F(["list-item",{"list-item_active":((p=a.paymentSystem)==null?void 0:p.id)===(e==null?void 0:e.ID)}]),onClick:g=>{a.paymentSystem={id:e==null?void 0:e.ID,type:e==null?void 0:e.Source},a.selectPaymentSystem()}},[S("img",{class:"list-item__image",src:e==null?void 0:e.Image},null,8,U),S("div",X,M(e==null?void 0:e.Description),1)],10,Q)):N("",!0)],64)}),256))])):N("",!0),S("div",Y,M(a.errorMessage),1)])}const ee={components:{ButtonDefault:J},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(t,{emit:l}){const P=V(),a=T(),D=$(),C=W("emitter"),e=w(null),p=z();E();const g=A(()=>P.ConfigList),s=w(null),b=A(()=>{var d;const c=g.value,r=(d=t.data)==null?void 0:d.ContractPaymentSystems;if(c!=null&&c.PaymentSystems&&r){const f=new Set(r);return Object.keys(c.PaymentSystems).filter(o=>f.has(o)).map(o=>{const n=c.PaymentSystems[o];return String(n==null?void 0:n.IsActive)==="1"?n.Collations:null}).filter(o=>o!==null).flat().slice().sort((o,n)=>{const u=parseInt(o.SortID,10),I=parseInt(n.SortID,10);return isNaN(u)?1:isNaN(I)?-1:u-I})}else return null});function x(){var c,r,d,f,h,k,o,n,u,I;s.value!==null&&(((c=t.data)==null?void 0:c.ProfileID)===null?((r=s.value)==null?void 0:r.type)==="Natural"||((d=s.value)==null?void 0:d.type)==="Juridical"||((f=s.value)==null?void 0:f.type)==="Individual"?(p.push(`/ProfileMake?ContractNew=${(h=t.data)==null?void 0:h.ItemID}&TemplateID=${(k=s.value)==null?void 0:k.type}`),l("modalClose")):a.InvoiceMake({ContractID:(o=t.data)==null?void 0:o.ItemID,PaymentSystemID:(n=s.value)==null?void 0:n.type}).then(i=>{if(i.status==="success"&&i.data&&i.data.InvoiceID){l("modalClose");const m=i.data.InvoiceID;D.InvoiceDocument({InvoiceID:m}).then(y=>{C.emit("open-modal",{component:"InvoiceDocumentBasket",data:{html:y,ID:m}})})}else l("modalClose")}):a.InvoiceMake({ContractID:(u=t.data)==null?void 0:u.ItemID,PaymentSystemID:(I=s.value)==null?void 0:I.type}).then(i=>{if(i.status==="success"&&i.data&&i.data.InvoiceID){l("modalClose");const m=i.data.InvoiceID;D.InvoiceDocument({InvoiceID:m}).then(y=>{C.emit("open-modal",{component:"InvoiceDocumentBasket",data:{html:y,ID:m}})})}else l("modalClose")}))}return{getConfig:g,errorMessage:e,filterActiveSystems:b,selectPaymentSystem:x,paymentSystem:s}}},ie=R(ee,[["render",Z],["__scopeId","data-v-12eb2681"]]);export{ie as default};
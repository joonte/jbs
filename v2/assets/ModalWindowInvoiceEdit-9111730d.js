import{o as l,c,F as y,x as A,n as O,a as v,t as B,k as M,b as T,p as V,l as F,_ as U,u as W,r as S,S as z,g as j,h as K,O as q}from"./index-5e313302.js";import{_ as N}from"./ButtonDefault-7c216fc6.js";import{u as G}from"./invoices-5e70ed98.js";const H=n=>(V("data-v-0060ce78"),n=n(),F(),n),J={class:"list-part"},Q=H(()=>v("div",{class:"list-item__title"},"Платежные системы",-1)),R={key:0,class:"list-row grid-net"},X=["onClick"],Y=["src"],Z={class:"list-item__image-title"},$={class:"modal__error"};function ee(n,i,f,t,d,g){const m=N;return l(),c("div",J,[Q,t.filterActiveSystems?(l(),c("div",R,[(l(!0),c(y,null,A(t.filterActiveSystems,s=>(l(),c(y,null,[(l(!0),c(y,null,A(s,e=>{var u;return l(),c(y,null,[e!=null&&e.IsActive?(l(),c("div",{key:0,class:O(["list-item",{"list-item_active":((u=t.paymentSystem)==null?void 0:u.id)===(e==null?void 0:e.ID)}]),onClick:p=>t.paymentSystem={id:e==null?void 0:e.ID,type:e==null?void 0:e.Source}},[v("img",{class:"list-item__image",src:e==null?void 0:e.Image},null,8,Y),v("div",Z,B(e==null?void 0:e.Description),1)],10,X)):M("",!0)],64)}),256))],64))),256))])):M("",!0),v("div",$,B(t.errorMessage),1),T(m,{label:"Выбрать","is-loading":t.isLoading,onClick:i[0]||(i[0]=s=>t.switchPaymentSystem())},null,8,["is-loading"])])}const te={components:{ButtonDefault:N},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(n,{emit:i}){const f=W(),t=G(),d=S(!1),g=S(null),m=z("emitter"),s=j(()=>f.ConfigList),e=S(null),u=j(()=>{var a,r;return(a=s.value)!=null&&a.PaymentSystems?Object.keys((r=s.value)==null?void 0:r.PaymentSystems).map(o=>{var _,C,h,k,D,E,P,b,w,L,x;return String((h=(C=(_=s.value)==null?void 0:_.PaymentSystems)==null?void 0:C[o])==null?void 0:h.IsActive)==="1"&&((b=(E=(D=(k=s.value)==null?void 0:k.PaymentSystems)==null?void 0:D[o])==null?void 0:E.ContractsTypes)==null?void 0:b[(P=n.data)==null?void 0:P.TypeID])==="1"?(x=(L=(w=s.value)==null?void 0:w.PaymentSystems)==null?void 0:L[o])==null?void 0:x.Collations:null}).filter(o=>o!==null):null});function p(){var a,r,o;e.value!==null&&(d.value=!0,t.InvoiceEdit({InvoiceID:(a=n.data)==null?void 0:a.ItemID,PaymentSystemID:(r=e.value)==null?void 0:r.type,Summ:(o=n.data)==null?void 0:o.Summ}).then(_=>{_==="SUCCESS"&&(i("modalClose"),m.emit("updateInvoicesTable")),d.value=!1}))}const I=a=>{a.key==="Enter"&&p()};return K(()=>{document.addEventListener("keyup",I)}),q(()=>{document.removeEventListener("keyup",I)}),{getConfig:s,errorMessage:g,filterActiveSystems:u,switchPaymentSystem:p,isLoading:d,paymentSystem:e}}},ae=U(te,[["render",ee],["__scopeId","data-v-0060ce78"]]);export{ae as default};

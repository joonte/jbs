import{o as x,c as I,a as s,t as h,Z as P,af as g,n as S,b as k,p as D,l as O,_ as L,r,S as N,i as y,O as B}from"./index-6f846e0d.js";import{u as M}from"./globalActions-d40367d6.js";import{_ as U}from"./ButtonDefault-40efcd87.js";const u=a=>(D("data-v-a42ac468"),a=a(),O(),a),V={class:"modal__body"},G={class:"list-item__title"},j={class:"list-item__pass"},W=u(()=>s("div",{class:"list-item__prepassword"},"Использовать сгенерированный пароль:",-1)),z={class:"modal__input-row"},A=u(()=>s("div",{class:"modal__input-label"},"Новый пароль",-1)),K={class:"list-form_col list-form_col--xl"},T={class:"search-field form-field"},Z={class:"modal__input-row"},q=u(()=>s("div",{class:"modal__input-label"},"Подтверждение пароля",-1)),F={class:"list-form_col list-form_col--xl"},H={class:"search-field form-field"};function J(a,o,d,e,n,_){var l;const i=U;return x(),I("div",V,[s("div",G,"Смена пароля, "+h((l=d.data)==null?void 0:l.Login),1),s("div",j,[W,s("div",{class:"list-item__password",onClick:o[0]||(o[0]=(...t)=>e.useGeneratedPassword&&e.useGeneratedPassword(...t))},h(e.generatedPassword.Password),1)]),s("div",z,[A,s("div",K,[s("div",T,[P(s("input",{class:S({"error-input":e.isError}),"onUpdate:modelValue":o[1]||(o[1]=t=>e.Password=t),placeholder:"",name:"password",autocomplete:"off",type:"text"},null,2),[[g,e.Password]])])])]),s("div",Z,[q,s("div",F,[s("div",H,[P(s("input",{class:S({"error-input":e.isError}),"onUpdate:modelValue":o[2]||(o[2]=t=>e.PasswordNew=t),placeholder:"",name:"password",autocomplete:"off",type:"text"},null,2),[[g,e.PasswordNew]])])])]),k(i,{class:"modal__button",label:"Сменить пароль","is-loading":e.isLoading,onClick:o[3]||(o[3]=t=>e.changePassword())},null,8,["is-loading"])])}const Q={props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(a,{emit:o}){const d=r(""),e=r(""),n=r(""),_=r(!1),i=M(),l=r(!1),t=N("emitter");function m(){var c,w;d.value===e.value&&d.value.length>0&&e.value.length>0?(l.value=!0,i.OrderChangePassword({ServiceOrderID:(c=a.data)==null?void 0:c.OrderID,ServiceID:(w=a.data)==null?void 0:w.ServiceID,Password:d.value,_Password:e.value}).then(E=>{var f,p;let{result:b,error:R}=E;b==="SUCCESS"&&((f=a.data)!=null&&f.emitEvent&&t.emit((p=a.data)==null?void 0:p.emitEvent),o("modalClose"),t.emit("open-modal",{component:"SuccessChange",data:{newPassword:d.value}})),l.value=!1})):_.value=!0}function C(){d.value=n.value.Password,e.value=n.value.Password}const v=c=>{c.key==="Enter"&&m()};return y(()=>{document.addEventListener("keyup",v)}),y(async()=>{n.value=await i.fetchPassword()}),B(()=>{document.removeEventListener("keyup",v)}),{Password:d,isError:_,PasswordNew:e,isLoading:l,changePassword:m,generatedPassword:n,useGeneratedPassword:C}}},ee=L(Q,[["render",J],["__scopeId","data-v-a42ac468"]]);export{ee as default};
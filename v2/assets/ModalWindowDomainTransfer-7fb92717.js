import{_ as T}from"./ButtonDefault-458eab69.js";import{_ as M}from"./BasicInput-4035fd95.js";import{_ as O}from"./BlockSelectContract-adea085a.js";import{_ as w,S as B,r as c,g as u,i as x,O as U,o as m,c as p,a as n,b as v,F as j,x as F,t as h,k as L,p as W,l as $}from"./index-5f8fd0e9.js";import{u as R}from"./domain-afe1e9d6.js";import"./component-62d13c49.js";import"./contracts-00b6c53b.js";import"./multiselect-482845f2.js";const S=d=>(W("data-v-d9a1cbc4"),d=d(),$(),d),Y={class:"modal__body"},Z={class:"modal__domain-select"},H=S(()=>n("div",{class:"list-item__title"},"Перенос домена",-1)),K=S(()=>n("div",{class:"list-item__secondary-title"},"Домены в зонах ru/su/рф переносятся без дополнительных условий, во всех остальных зонах переносятся с оплатой продления на год. Ниже приведены цены на продление Вашего доменного имени.",-1)),P={class:"modal__info"},q={class:"modal__lower-block"},z={class:"modal__domain-list"},G={key:0,class:"domain__grid"},J={class:"domain__item domain__item_inactive"},Q={class:"domain__info-wrapper"},X={class:"domain__info-wrapper-vertical"},ee={class:"domain__info-wrapper"},oe={class:"domain__info-title"},ae={class:"domain__package-name"},ne={class:"domain__info-wrapper"},se={class:"domain__price"},te={__name:"ModalWindowDomainTransfer",props:["data"],emits:["modalClose"],setup(d,{emit:g}){const _=R(),N=B("emitter"),l=c(""),r=c(""),f=c(""),D=c(!1),i=u(()=>_==null?void 0:_.domainSchemes),C=u(()=>{var a;return(a=Object.keys(i.value).map(e=>i.value[e]).find(e=>{var t,s;return e.Name===((s=(t=l.value)==null?void 0:t.split("."))==null?void 0:s[1])}))==null?void 0:a.ID}),V=u(()=>{var a;return(a=Object.keys(i.value).map(e=>i.value[e]).find(e=>{var t,s;return e.Name===((s=(t=l.value)==null?void 0:t.split("."))==null?void 0:s[1])}))==null?void 0:a.DaysAfterTransfer}),A=u(()=>{var a;return(a=Object.keys(i.value).map(e=>i.value[e]).find(e=>{var t,s;return e.Name===((s=(t=l.value)==null?void 0:t.split("."))==null?void 0:s[1])}))==null?void 0:a.DaysBeforeTransfer});function y(){g("modalClose")}function k(){D.value=!0,_.CheckDomainTransfer({ContractID:r.value||null,DomainName:l.value||null,StepID:1}).then(({result:a})=>{a==="SUCCESS"?_.DomainTransfer({ContractID:r.value||null,DomainName:l.value.split(".")[0]||null,AuthInfo:f.value||"",DomainSchemeID:C.value||null,DomainZone:l.value.split(".")[1],DaysBeforeTransfer:A.value||null,DaysAfterTransfer:V.value||null}).then(()=>{N.emit("updateDomainsList"),y()}):D.value=!1})}const b=a=>{a.key==="Enter"&&k()};return x(()=>{document.addEventListener("keyup",b)}),U(()=>{document.removeEventListener("keyup",b)}),(a,e)=>{var I;const t=O,s=M,E=T;return m(),p("div",Y,[n("div",Z,[H,K,n("div",P,[v(t,{modelValue:r.value,"onUpdate:modelValue":e[0]||(e[0]=o=>r.value=o),onButtonClicked:e[1]||(e[1]=o=>y())},null,8,["modelValue"]),v(s,{label:"Доменное имя",placeholder:"yourDomain.ru",necessarily:!0,modelValue:l.value,"onUpdate:modelValue":e[2]||(e[2]=o=>l.value=o)},null,8,["modelValue"]),v(s,{label:"Ключ переноса домена (AuthInfo)",placeholder:"",modelValue:f.value,"onUpdate:modelValue":e[3]||(e[3]=o=>f.value=o)},null,8,["modelValue"])])]),n("div",q,[n("div",z,[i.value?(m(),p("div",G,[(m(!0),p(j,null,F(i.value,o=>(m(),p("div",J,[n("div",Q,[n("div",X,[n("div",ee,[n("div",oe,"."+h(o==null?void 0:o.Name),1)]),n("div",ae,h(o==null?void 0:o.PackageID),1)])]),n("div",ne,[n("div",se,h(o==null?void 0:o.CostTransfer)+" ₽",1)])]))),256))])):L("",!0)])]),v(E,{class:"domain__transfer-item",label:"Перенести","is-loading":D.value,disabled:((I=l.value)==null?void 0:I.length)<=0,onClick:e[4]||(e[4]=o=>k())},null,8,["is-loading","disabled"])])}}},pe=w(te,[["__scopeId","data-v-d9a1cbc4"]]);export{pe as default};

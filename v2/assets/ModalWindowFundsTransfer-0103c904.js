import{_ as g}from"./ButtonDefault-b95c906d.js";import{_ as B}from"./BasicInput-ee6974cd.js";import{_ as I,r as d,e as T,S as y,g as w,i as F,O as M,o as x,c as D,b as i,p as E,l as L,a as f}from"./index-3af25d4c.js";import{u as U}from"./contracts-4f21f7b6.js";import{_ as v}from"./BlockSelectContract-bbfe1748.js";import"./component-17343301.js";import"./multiselect-eb4b0170.js";const b=l=>(E("data-v-001e6765"),l=l(),L(),l),$={class:"modal__body"},N=b(()=>f("div",{class:"list-item__title"},"Перевод средств между счетами",-1)),O=b(()=>f("div",{class:"modal__input-row"},null,-1)),W={__name:"ModalWindowFundsTransfer",props:["data"],emits:["modalClose","buttonClicked"],setup(l,{emit:C}){const n=U(),s=d(null),a=d(null),u=d(null),_=d(!1);T(),y("emitter");const V=w(()=>n==null?void 0:n.contractsList);function k(t){const e=Object.values(V.value).find(m=>m.ID===t);e&&(u.value=e.Balance)}function r(){C("modalClose")}function c(){_.value=!0;const t={FromContractID:s.value,ToContractID:a.value,Summ:u.value};n.FundsTransfer(t).then(e=>{e.status==="success"&&r(),_.value=!1})}const p=t=>{t.key==="Enter"&&c()};return F(()=>{document.addEventListener("keyup",p),n.fetchContracts()}),M(()=>{document.removeEventListener("keyup",p)}),(t,e)=>{const m=B,S=g;return x(),D("div",$,[N,O,i(v,{title:"Откуда",modelValue:s.value,"onUpdate:modelValue":[e[0]||(e[0]=o=>s.value=o),k],isBalance:!0,onButtonClicked:e[1]||(e[1]=o=>r())},null,8,["modelValue"]),i(v,{title:"Куда",modelValue:a.value,"onUpdate:modelValue":e[2]||(e[2]=o=>a.value=o),onButtonClicked:e[3]||(e[3]=o=>r())},null,8,["modelValue"]),i(m,{label:"Сумма",name:"Summ",placeholder:"199.99",modelValue:u.value,"onUpdate:modelValue":e[4]||(e[4]=o=>u.value=o)},null,8,["modelValue"]),i(S,{class:"modal__button",label:"Сохранить",disabled:s.value===null||a.value===null,"is-loading":_.value,onClick:e[5]||(e[5]=o=>c())},null,8,["disabled","is-loading"])])}}},H=I(W,[["__scopeId","data-v-001e6765"]]);export{H as default};

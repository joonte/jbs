import{o as c,c as m,b as i,p,l as u,a as f,_ as v,r as _,S as b,i as g,O as S}from"./index-5f8fd0e9.js";import{u as y}from"./globalActions-2cf5b597.js";import{_ as E}from"./ButtonDefault-458eab69.js";import{_ as k}from"./BasicInput-4035fd95.js";import"./component-62d13c49.js";const h=e=>(p("data-v-89ebc149"),e=e(),u(),e),x={class:"modal__body"},C=h(()=>f("div",{class:"list-item__title"},"Передача заказа",-1));function I(e,o,n,t,s,r){const l=k,a=E;return c(),m("div",x,[C,i(l,{label:"Почтовый адрес",name:"mailIndex",placeholder:"Email",modelValue:t.email,"onUpdate:modelValue":o[0]||(o[0]=d=>t.email=d)},null,8,["modelValue"]),i(a,{class:"modal__button",label:"Передать","is-loading":t.isLoading,onClick:o[1]||(o[1]=d=>t.transfer())},null,8,["is-loading"])])}const O={props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(e,{emit:o}){const n=_(""),t=y(),s=_(!1);b("emitter");function r(){n.value.length>3&&(s.value=!0,t.OrdersTransfer({...e.data,Email:n.value}).then(({result:a,error:d})=>{a==="SUCCESS"&&o("modalClose"),s.value=!1}))}const l=a=>{a.key==="Enter"&&r()};return g(()=>{document.addEventListener("keyup",l)}),S(()=>{document.removeEventListener("keyup",l)}),{email:n,isLoading:s,transfer:r}}},T=v(O,[["render",I],["__scopeId","data-v-89ebc149"]]);export{T as default};

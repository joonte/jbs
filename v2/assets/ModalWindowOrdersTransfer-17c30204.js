import{o as c,c as m,b as d,p,l as f,a as u,_ as v,r as _,R as g}from"./index-1c309346.js";import{u as b}from"./globalActions-8efdfe11.js";import{_ as S}from"./ButtonDefault-cd183bf8.js";import{_ as x}from"./BasicInput-33bf464f.js";import"./component2-139d1cad.js";import"./component-a2c8255f.js";const C=e=>(p("data-v-69ef55f2"),e=e(),f(),e),I={class:"modal__body"},V=C(()=>u("div",{class:"list-item__title"},"Передача заказа",-1));function h(e,o,t,a,s,l){const n=x,r=S;return c(),m("div",I,[V,d(n,{label:"Почтовый адрес",name:"mailIndex",placeholder:"Email",modelValue:a.email,"onUpdate:modelValue":o[0]||(o[0]=i=>a.email=i)},null,8,["modelValue"]),d(r,{class:"modal__button",label:"Передать","is-loading":a.isLoading,onClick:o[1]||(o[1]=i=>a.transfer())},null,8,["is-loading"])])}const y={props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(e,{emit:o}){const t=_(""),a=b(),s=_(!1);g("emitter");function l(){t.value.length>3&&(s.value=!0,a.OrdersTransfer({...e.data,Email:t.value}).then(({result:n,error:r})=>{console.log(n),n==="SUCCESS"&&o("modalClose"),s.value=!1}))}return{email:t,isLoading:s,transfer:l}}},j=v(y,[["render",h],["__scopeId","data-v-69ef55f2"]]);export{j as default};

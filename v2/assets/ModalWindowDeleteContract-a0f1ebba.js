import{o as u,c as f,b as r,p as v,m as C,a as i,_ as I,U as b,r as S,i as y,P as D}from"./index-400feb76.js";import{I as _}from"./IconHelpCircle-04615a6e.js";import{u as h}from"./globalActions-9f4dbd2c.js";import{_ as g}from"./ButtonDefault-7609cc9e.js";const k=e=>(v("data-v-a78b18e2"),e=e(),C(),e),E={class:"modal__body"},w=k(()=>i("div",{class:"modal__info"},[i("div",{class:"modal__alert-title"},"Вы действительно хотите удалить договор?")],-1));function x(e,a,n,t,s,l){const o=_,c=g;return u(),f("div",E,[r(o,{class:"modal__alert-icon"}),w,r(c,{class:"modal__alert-button",onClick:a[0]||(a[0]=p=>t.deleteContract()),label:"УДАЛИТЬ","is-loading":t.isLoading},null,8,["is-loading"])])}const B={components:{IconHelpCircle:_},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(e,{emit:a}){const n=h();b("emitter");const t=S(!1);function s(){var o;t.value=!0,n.deleteItem({TableID:"Contracts",RowsIDs:(o=e.data)==null?void 0:o.contractID}).then(({result:c,error:p})=>{var d;c==="SUCCESS"&&n.deleteItem({TableID:"Profiles",RowsIDs:(d=e.data)==null?void 0:d.id}).then(({result:m,error:L})=>{m==="SUCCESS"&&a("modalClose")}),t.value=!1})}const l=o=>{o.key==="Enter"&&s()};return y(()=>{document.addEventListener("keyup",l)}),D(()=>{document.removeEventListener("keyup",l)}),{deleteContract:s,isLoading:t}}},N=I(B,[["render",x],["__scopeId","data-v-a78b18e2"]]);export{N as default};

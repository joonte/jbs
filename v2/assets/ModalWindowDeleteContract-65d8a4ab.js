import{o as u,c as f,b as r,p as v,l as C,a as i,_ as I,S,r as b,i as y,O as D}from"./index-b5b5ce3d.js";import{I as _}from"./IconHelpCircle-4f0b5717.js";import{u as h}from"./globalActions-6936f67f.js";import{_ as g}from"./ButtonDefault-0006f72a.js";const k=e=>(v("data-v-a78b18e2"),e=e(),C(),e),E={class:"modal__body"},w=k(()=>i("div",{class:"modal__info"},[i("div",{class:"modal__alert-title"},"Вы действительно хотите удалить договор?")],-1));function x(e,a,n,t,s,l){const o=_,c=g;return u(),f("div",E,[r(o,{class:"modal__alert-icon"}),w,r(c,{class:"modal__alert-button",onClick:a[0]||(a[0]=p=>t.deleteContract()),label:"УДАЛИТЬ","is-loading":t.isLoading},null,8,["is-loading"])])}const B={components:{IconHelpCircle:_},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(e,{emit:a}){const n=h();S("emitter");const t=b(!1);function s(){var o;t.value=!0,n.deleteItem({TableID:"Contracts",RowsIDs:(o=e.data)==null?void 0:o.contractID}).then(({result:c,error:p})=>{var d;c==="SUCCESS"&&n.deleteItem({TableID:"Profiles",RowsIDs:(d=e.data)==null?void 0:d.id}).then(({result:m,error:L})=>{m==="SUCCESS"&&a("modalClose")}),t.value=!1})}const l=o=>{o.key==="Enter"&&s()};return y(()=>{document.addEventListener("keyup",l)}),D(()=>{document.removeEventListener("keyup",l)}),{deleteContract:s,isLoading:t}}},N=I(B,[["render",x],["__scopeId","data-v-a78b18e2"]]);export{N as default};

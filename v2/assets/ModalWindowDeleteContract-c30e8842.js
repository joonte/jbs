import{o as m,c as f,b as r,p as u,l as C,a as _,_ as I,S,r as b}from"./index-145d9cde.js";import{I as i}from"./IconHelpCircle-f7346a2b.js";import{u as v}from"./globalActions-de64efb8.js";import{_ as D}from"./ButtonDefault-6a3a5e1e.js";const g=e=>(u("data-v-0fce5d5b"),e=e(),C(),e),h={class:"modal__body"},w=g(()=>_("div",{class:"modal__info"},[_("div",{class:"modal__alert-title"},"Вы действительно хотите удалить договор?")],-1));function x(e,o,a,t,c,s){const l=i,d=D;return m(),f("div",h,[r(l,{class:"modal__alert-icon"}),w,r(d,{class:"modal__alert-button",onClick:o[0]||(o[0]=n=>t.deleteContract()),label:"УДАЛИТЬ","is-loading":t.isLoading},null,8,["is-loading"])])}const y={components:{IconHelpCircle:i},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(e,{emit:o}){const a=v();S("emitter");const t=b(!1);function c(){var s;t.value=!0,a.deleteItem({TableID:"Contracts",RowsIDs:(s=e.data)==null?void 0:s.contractID}).then(({result:l,error:d})=>{var n;l==="SUCCESS"&&a.deleteItem({TableID:"Profiles",RowsIDs:(n=e.data)==null?void 0:n.id}).then(({result:p,error:k})=>{p==="SUCCESS"&&o("modalClose")}),t.value=!1})}return{deleteContract:c,isLoading:t}}},L=I(y,[["render",x],["__scopeId","data-v-0fce5d5b"]]);export{L as default};

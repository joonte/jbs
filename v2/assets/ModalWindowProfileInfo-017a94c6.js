import{_ as V}from"./ButtonDefault-6a3a5e1e.js";import{_ as k}from"./BasicInput-9e44931e.js";import{_ as y,e as C,r as I,o as e,c as t,a as u,t as d,F as N,x,k as E,b as p}from"./index-145d9cde.js";import"./component-adfdaade.js";const P={class:"modal__body"},T={class:"modal__title"},$={class:"modal__sub-title"},w={key:0,class:"modal__group-title"},B={__name:"ModalWindowProfileInfo",props:["data"],emits:["modalClose"],setup(i,{emit:m}){var n;const o=i,f=C(),_=I({...(n=o.data.data)==null?void 0:n.Attribs});function b(){var l;f.push(`/ContractEdit/${(l=o.data.data)==null?void 0:l.ID}`),m("modalClose")}return(l,M)=>{var r,c;const v=k,g=V;return e(),t("div",P,[u("div",T,d(o.data.data.Name),1),u("div",$,d(o.data.template.Name),1),(e(!0),t(N,null,x((c=(r=o.data.template)==null?void 0:r.Template)==null?void 0:c.Attribs,(a,s)=>(e(),t("div",{class:"modal__attributes-group",key:s},[a!=null&&a.Title?(e(),t("div",w,d(a.Title),1)):E("",!0),p(v,{label:a==null?void 0:a.Comment,placeholder:a==null?void 0:a.Example,disabled:!0,modelValue:_.value[s],"onUpdate:modelValue":h=>_.value[s]=h},null,8,["label","placeholder","modelValue","onUpdate:modelValue"])]))),128)),p(g,{class:"modal__button",onClick:b,label:"Редактировать данные"})])}}},U=y(B,[["__scopeId","data-v-e624edd0"]]);export{U as default};

import{_ as c,o as a,c as s,F as l,v as r,n as m,t as u}from"./index-58c913f0.js";const d={class:"section-navigation"},p=["onClick"],v={__name:"FormTabs",props:["tabs","modelValue"],emits:["update:modelValue"],setup(n,{emit:_}){const t=n;function i(o){_("update:modelValue",o)}return(o,g)=>(a(),s("div",d,[(a(!0),s(l,null,r(t.tabs,e=>(a(),s("div",{class:m(["section-navigation__item",t.modelValue===e.value?"section-navigation__item_active":""]),key:e.value,onClick:F=>i(e.value)},u(e.name),11,p))),128))]))}},k=c(v,[["__scopeId","data-v-1ad735ab"]]);export{k as F};
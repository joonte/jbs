import{_ as h,r as I,g as _,i as b,h as S,o as V,c as k,Z as D,ae as w,a as l,t as o,b as B,w as m,M as T,p as g,l as x,d as N}from"./index-b5b5ce3d.js";import{u as y}from"./contracts-74f05bb7.js";import{s as C}from"./multiselect-4f7d14de.js";const E=t=>(g("data-v-c223fefe"),t=t(),x(),t),P={class:"section"},L={class:"block--mobile_divider"},M={class:"form-field form-field--basic"},O=E(()=>l("div",{class:"form-field__label"},[N("Выбрать профиль"),l("span",null,"*")],-1)),j={class:"form-field-input__wrapper"},F={class:"multiselect-option"},R={__name:"BlockSelectProfile",props:["modelValue","title","items"],emits:["update:modelValue","buttonClicked"],setup(t,{emit:v}){const c=t,n=y(),i=I(null),a=_(()=>n==null?void 0:n.profileList),d=_(()=>Object.keys(a.value).map(e=>{if(a.value[e].TemplateID==="Natural"||a.value[e].TemplateID==="Juridical"||a.value[e].TemplateID==="Individual"||a.value[e].TemplateID==="NaturalPartner")return{value:a.value[e].ID,name:a.value[e].Name,contractID:a.value[e].ContractID}}).filter(Boolean));function f(e){v("update:modelValue",e)}return b(()=>{var e;a.value&&(i.value=(e=Object.keys(a.value))==null?void 0:e[0])}),S(c,()=>{i.value=c.modelValue}),(e,u)=>{var r,p;return V(),k("div",P,[D(l("h2",{class:"section-block_title"},o(c.title),513),[[w,c.title]]),l("div",L,[l("div",M,[O,l("div",j,[B(T(C),{class:"multiselect--white",placeholder:"Выбрать профиль",label:"name",modelValue:i.value,"onUpdate:modelValue":u[0]||(u[0]=s=>i.value=s),options:t.items||d.value,disabled:((r=t.items)==null?void 0:r.length)<=1||((p=d.value)==null?void 0:p.length)<=1,onChange:f},{singlelabel:m(({value:s})=>[l("span",null,"# "+o(s.value.padStart(5,"0"))+" / "+o(s.name),1)]),option:m(({option:s})=>[l("div",F,"# "+o(s.value.padStart(5,"0"))+" / "+o(s.name),1)]),_:1},8,["modelValue","options","disabled"])])])])])}}},q=h(R,[["__scopeId","data-v-c223fefe"]]);export{q as B};
import{_ as m,a7 as I,r as b,g as p,h as D,o as y,c as h,a as s,Z as T,ae as k,t as o,b as B,w as _,M as V,p as g,l as C,d as S}from"./index-5e313302.js";import{u as w}from"./contracts-3351d68f.js";import{s as N}from"./multiselect-bfc29db8.js";const x=n=>(g("data-v-0f25177a"),n=n(),C(),n),E={class:"section"},P={class:"container"},j={class:"block--mobile_divider"},O={class:"form-field form-field--basic"},R=x(()=>s("div",{class:"form-field__label"},[S("Выбрать договор"),s("span",null,"*")],-1)),F={class:"form-field-input__wrapper"},J={class:"multiselect-option"},L={__name:"BlockSelectContract",props:["modelValue","title","isBalance"],emits:["update:modelValue","buttonClicked"],setup(n,{emit:v}){const c=n,u=w();I();const i=b(null),a=p(()=>u==null?void 0:u.contractsList),r=p(()=>c.isBalance?Object.keys(a.value).map(e=>{const l=a.value[e];return l&&(l.TypeID==="Natural"||l.TypeID==="Juridical"||l.TypeID==="Default"||l.TypeID==="Public"||l.TypeID==="Individual"||l.TypeID==="NaturalPartner")?{value:l.ID,name:l.Customer,balance:l.Balance}:null}).filter(e=>e!==null&&e.balance>0):Object.keys(a.value).map(e=>(a.value[e].TypeID==="Natural"||a.value[e].TypeID==="Juridical"||a.value[e].TypeID==="Default"||a.value[e].TypeID==="Public"||a.value[e].TypeID==="Individual"||a.value[e].TypeID==="NaturalPartner")&&a.value[e].IsHidden===!1?{value:a.value[e].ID,name:a.value[e].Customer,balance:a.value[e].Balance}:null).filter(Boolean));function f(e){v("update:modelValue",e)}return D(()=>{var e;a.value&&(i.value=(e=Object.keys(a==null?void 0:a.value))==null?void 0:e[0]),console.log("FILTERED -",r)}),(e,l)=>{var d;return y(),h("div",E,[s("div",P,[T(s("h2",{class:"section-block_title"},o(c.title),513),[[k,c.title]]),s("div",j,[s("div",O,[R,s("div",F,[B(V(N),{class:"multiselect--white",placeholder:"Выбрать договор",label:"name",modelValue:i.value,"onUpdate:modelValue":l[0]||(l[0]=t=>i.value=t),options:r.value,disabled:((d=r.value)==null?void 0:d.length)<=1,onChange:f},{singlelabel:_(({value:t})=>[s("span",null,"# "+o(t.value.padStart(5,"0"))+" / "+o(t.name)+" / "+o(t.balance+" руб"),1)]),option:_(({option:t})=>[s("div",J,"# "+o(t.value.padStart(5,"0"))+" / "+o(t.name)+" / "+o(t.balance+" руб"),1)]),_:1},8,["modelValue","options","disabled"])])])])])])}}},Z=m(L,[["__scopeId","data-v-0f25177a"]]);export{Z as _};

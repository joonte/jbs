import{_ as f,r as b,g as _,i as h,o as I,c as V,a,Y as k,ae as y,t as o,b as D,w as p,L as S,p as g,l as w,d as C}from"./index-58c913f0.js";import{u as x}from"./contracts-5c545170.js";import{s as B}from"./multiselect-1db9a63f.js";const T=l=>(g("data-v-49c28b52"),l=l(),w(),l),N={class:"section"},E={class:"container"},j={class:"block--mobile_divider"},L={class:"form-field form-field--basic"},O=T(()=>a("div",{class:"form-field__label"},[C("Выбрать договор"),a("span",null,"*")],-1)),F={class:"form-field-input__wrapper"},J={class:"multiselect-option"},M={__name:"BlockSelectContract",props:["modelValue","title"],emits:["update:modelValue","buttonClicked"],setup(l,{emit:v}){const i=l,c=x(),n=b(null),t=_(()=>c==null?void 0:c.contractsList),d=_(()=>Object.keys(t.value).map(e=>{if(t.value[e].TypeID==="Natural"||t.value[e].TypeID==="Juirdical"||t.value[e].TypeID==="Default"||t.value[e].TypeID==="Individual")return{value:t.value[e].ID,name:t.value[e].Customer}}).filter(Boolean));function m(e){v("update:modelValue",e)}return h(()=>{var e;t.value&&(n.value=(e=Object.keys(t==null?void 0:t.value))==null?void 0:e[0])}),(e,u)=>{var r;return I(),V("div",N,[a("div",E,[k(a("h2",{class:"section-block_title"},o(i.title),513),[[y,i.title]]),a("div",j,[a("div",L,[O,a("div",F,[D(S(B),{class:"multiselect--white",placeholder:"Выбрать договор",label:"name",modelValue:n.value,"onUpdate:modelValue":u[0]||(u[0]=s=>n.value=s),options:d.value,disabled:((r=d.value)==null?void 0:r.length)<=1,onChange:m},{singlelabel:p(({value:s})=>[a("span",null,"# "+o(s.value.padStart(5,"0"))+" / "+o(s.name),1)]),option:p(({option:s})=>[a("div",J,"# "+o(s.value.padStart(5,"0"))+" / "+o(s.name),1)]),_:1},8,["modelValue","options","disabled"])])])])])])}}},q=f(M,[["__scopeId","data-v-49c28b52"]]);export{q as _};
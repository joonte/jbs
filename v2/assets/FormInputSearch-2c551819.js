import{o as a,c as _,a as n,Z as d,af as p,b as u,w as m,y as c,T as f,_ as h,r as v,h as x}from"./index-5f8fd0e9.js";import{I}from"./IconPlus-13feb66d.js";import{I as V}from"./IconSearch-adead29d.js";const y={class:"list-form_col list-form_col--xl"},k={class:"search-field form-field"},g={class:"input-icon"};function w(r,o,t,e,S,T){const l=V,i=I;return a(),_("div",y,[n("div",k,[d(n("input",{type:"text",placeholder:"Поиск",name:"search",autocomplete:"off","onUpdate:modelValue":o[0]||(o[0]=s=>e.search=s)},null,512),[[p,e.search]]),n("button",g,[u(f,{name:"fade",mode:"out-in"},{default:m(()=>{var s;return[((s=e.search)==null?void 0:s.length)===0||e.search===null?(a(),c(l,{key:0})):(a(),c(i,{key:1,class:"input-reset-icon",onClick:o[1]||(o[1]=b=>e.resetValue())}))]}),_:1})])])])}const B={props:{modelValue:{type:String,default:""}},emits:["update:modelValue"],setup(r,{emit:o}){const t=v("");function e(){t.value=""}return x(t,()=>{o("update:modelValue",t.value)}),{search:t,resetValue:e}}},D=h(B,[["render",w],["__scopeId","data-v-36866e64"]]);export{D as _};

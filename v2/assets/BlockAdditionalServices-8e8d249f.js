import{E as B}from"./EmptyStateBlock-2d70ce64.js";import{r as y,a as A}from"./bootstrap-vue-next.es-788a757a.js";import{_ as L}from"./ClausesKeeper-313482ce.js";import{_ as N,e as x,r as u,g as _,h as R,i as O,o as r,c as m,a as g,b as I,y as d,w as f,F as w,x as C}from"./index-90eb49f0.js";import{u as U}from"./services-ecabd454.js";import{S as b}from"./ServiceListOrderSelection-171e49ee.js";import"./IconClose-c838a5bc.js";import"./contracts-0cbe8270.js";import"./domain-589159b1.js";import"./multiselect-e18abfd5.js";import"./ButtonDefault-5aebc1f7.js";import"./BasicInput-936f29ee.js";import"./component-65c692c0.js";import"./BlockSelectContract-5c8bdad1.js";const j={class:"section"},F={class:"container"},P={__name:"BlockAdditionalServices",props:["serviceSearchParams"],emits:["update:modelValue"],setup(D,{emit:S}){const v=D,t=U();x();const n=u({}),l=u({});u(!1);const V=_(()=>t.DependServicesList),p=_(()=>{var s;return(s=Object.keys(t==null?void 0:t.ServicesList))==null?void 0:s.map(e=>{var o;return{...t==null?void 0:t.ServicesList[e],value:(o=t==null?void 0:t.ServicesList[e])==null?void 0:o.ID}}).filter(e=>{var o;return(e==null?void 0:e.ServicesGroupID)!=="1000"&&(e==null?void 0:e.IsActive)&&!(e!=null&&e.IsHidden)&&((o=V.value)==null?void 0:o.includes(e==null?void 0:e.ID))}).sort((e,o)=>Number(e==null?void 0:e.SortID)<Number(o==null?void 0:o.SortID)?-1:Number(e==null?void 0:e.SortID)>Number(o==null?void 0:o.SortID)?1:0)}),i=_(()=>Object.keys(n.value).map(s=>n.value[s]?l.value[s]:null).filter(Boolean));return R(i,()=>{S("update:modelValue",i.value)}),O(()=>{t.fetchDependServices(v.serviceSearchParams)}),(s,e)=>{const o=L,k=A,E=y,h=B;return r(),m("div",j,[g("div",F,[I(o),p.value?(r(),d(E,{key:0,class:"services__list",free:""},{default:f(()=>[(r(!0),m(w,null,C(p.value,(a,G)=>(r(),d(k,{class:"profile-notification__collapse",title:a==null?void 0:a.Name,modelValue:n.value[a.ID],"onUpdate:modelValue":c=>n.value[a.ID]=c},{default:f(()=>[I(b,{sectionID:a.ID,sectionData:a,modelValue:l.value[a.ID],"onUpdate:modelValue":c=>l.value[a.ID]=c,"update-with-page-transition":!1,"is-inline":""},null,8,["sectionID","sectionData","modelValue","onUpdate:modelValue"])]),_:2},1032,["title","modelValue","onUpdate:modelValue"]))),256))]),_:1})):(r(),d(h,{key:1,label:"Дополнительные услуги не найденыы"}))])])}}},oe=N(P,[["__scopeId","data-v-69a4d309"]]);export{oe as default};

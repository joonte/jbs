import{E as B}from"./EmptyStateBlock-153c9c6f.js";import{r as y,a as A}from"./bootstrap-vue-next.es-a97602fc.js";import{_ as L}from"./ClausesKeeper-8218189c.js";import{_ as N,e as x,r as u,g as _,h as R,i as O,o as r,c as m,a as g,b as I,y as d,w as f,F as w,x as C}from"./index-5f8fd0e9.js";import{u as U}from"./services-0121b428.js";import{S as b}from"./ServiceListOrderSelection-581762f7.js";import"./IconClose-2e1ba86d.js";import"./contracts-00b6c53b.js";import"./domain-afe1e9d6.js";import"./multiselect-482845f2.js";import"./ButtonDefault-458eab69.js";import"./BasicInput-4035fd95.js";import"./component-62d13c49.js";import"./BlockSelectContract-adea085a.js";const j={class:"section"},F={class:"container"},P={__name:"BlockAdditionalServices",props:["serviceSearchParams"],emits:["update:modelValue"],setup(D,{emit:S}){const v=D,t=U();x();const n=u({}),l=u({});u(!1);const V=_(()=>t.DependServicesList),p=_(()=>{var s;return(s=Object.keys(t==null?void 0:t.ServicesList))==null?void 0:s.map(e=>{var o;return{...t==null?void 0:t.ServicesList[e],value:(o=t==null?void 0:t.ServicesList[e])==null?void 0:o.ID}}).filter(e=>{var o;return(e==null?void 0:e.ServicesGroupID)!=="1000"&&(e==null?void 0:e.IsActive)&&!(e!=null&&e.IsHidden)&&((o=V.value)==null?void 0:o.includes(e==null?void 0:e.ID))}).sort((e,o)=>Number(e==null?void 0:e.SortID)<Number(o==null?void 0:o.SortID)?-1:Number(e==null?void 0:e.SortID)>Number(o==null?void 0:o.SortID)?1:0)}),i=_(()=>Object.keys(n.value).map(s=>n.value[s]?l.value[s]:null).filter(Boolean));return R(i,()=>{S("update:modelValue",i.value)}),O(()=>{t.fetchDependServices(v.serviceSearchParams)}),(s,e)=>{const o=L,k=A,E=y,h=B;return r(),m("div",j,[g("div",F,[I(o),p.value?(r(),d(E,{key:0,class:"services__list",free:""},{default:f(()=>[(r(!0),m(w,null,C(p.value,(a,G)=>(r(),d(k,{class:"profile-notification__collapse",title:a==null?void 0:a.Name,modelValue:n.value[a.ID],"onUpdate:modelValue":c=>n.value[a.ID]=c},{default:f(()=>[I(b,{sectionID:a.ID,sectionData:a,modelValue:l.value[a.ID],"onUpdate:modelValue":c=>l.value[a.ID]=c,"update-with-page-transition":!1,"is-inline":""},null,8,["sectionID","sectionData","modelValue","onUpdate:modelValue"])]),_:2},1032,["title","modelValue","onUpdate:modelValue"]))),256))]),_:1})):(r(),d(h,{key:1,label:"Дополнительные услуги не найденыы"}))])])}}},oe=N(P,[["__scopeId","data-v-69a4d309"]]);export{oe as default};

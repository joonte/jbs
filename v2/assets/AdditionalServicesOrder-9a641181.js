import{j as N,o as u,c as p,b as f,a as l,F as k,x as O,y as x,w,t as y,n as B,k as V,_ as L,a7 as E,e as M,f as T,r as j,g as q}from"./index-145d9cde.js";import{u as F}from"./contracts-009c925b.js";import{u as R}from"./services-42f7a1d0.js";import{S as A}from"./ServiceListOrderSelection-b1f00fc2.js";import{E as U}from"./EmptyStateBlock-a6b81e83.js";import{s as b}from"./multiselect-a2d623ca.js";import{B as C}from"./BlockBalanceAgreement-650ad03e.js";import{_ as z}from"./IconArrow-f2b12a71.js";import"./ButtonDefault-6a3a5e1e.js";import"./BasicInput-9e44931e.js";import"./component-adfdaade.js";import"./BlockSelectContract-dee687ab.js";import"./ClausesKeeper-d3875457.js";import"./IconClose-f461656c.js";import"./bootstrap-vue-next.es-759b1c9f.js";const G={class:"section"},H={class:"container"},J={class:"section-header"},K={class:"section-nav"},P=["onClick"],Q={class:"multiselect-option"},W={class:"section-body"};function X(r,n,I,e,D,a){var o,_,i,v,h,g,m;const S=C,d=z,c=N("Multiselect"),t=A;return u(),p(k,null,[f(S),l("div",G,[l("div",H,[l("div",J,[l("div",K,[(u(!0),p(k,null,O((o=e.getServices)==null?void 0:o.slice(0,3),s=>(u(),p("div",{class:B(["section-nav__item",{"section-link":!0,"section-link-active":e.section===(s==null?void 0:s.ID)}]),onClick:Z=>e.switchToSection(s==null?void 0:s.ID)},y(s==null?void 0:s.Item),11,P))),256)),((_=e.getServices)==null?void 0:_.length)>3?(u(),x(c,{key:0,class:B(["multiselect-special",{"multiselect--white additional-service__select":!0,"additional-service__select_active":(v=e.getServices)==null?void 0:v.slice(3,(i=e.getServices)==null?void 0:i.length).find(s=>(s==null?void 0:s.ID)===e.section)}]),modelValue:e.section,"onUpdate:modelValue":n[0]||(n[0]=s=>e.section=s),options:(g=e.getServices)==null?void 0:g.slice(3,(h=e.getServices)==null?void 0:h.length),label:"ID",onSelect:n[1]||(n[1]=s=>e.switchToSection(s))},{option:w(({option:s})=>[l("div",Q,y(s==null?void 0:s.Item),1)]),caret:w(()=>[f(d,{class:"icon-arrow_caret"})]),_:1},8,["modelValue","class","options"])):V("",!0)])]),l("div",W,[f(t,{sectionID:e.section,sectionData:(m=e.getServices)==null?void 0:m.find(s=>s.ID===e.section)},null,8,["sectionID","sectionData"])])])])],64)}const Y={components:{ServiceListOrderSelection:A,emptyStateBlock:U,Multiselect:b,BlockBalanceAgreement:C},async setup(){const r=E(),n=M(),I=T(),e=R(),D=F(),a=j(null);function S(c){n.replace(`/AdditionalServicesOrder?ServiceID=${c}`),a.value=c}const d=q(()=>{var c;return(c=Object.keys(e==null?void 0:e.ServicesList))==null?void 0:c.map(t=>{var o;return{...e==null?void 0:e.ServicesList[t],value:(o=e==null?void 0:e.ServicesList[t])==null?void 0:o.ID}}).filter(t=>(t==null?void 0:t.ServicesGroupID)!=="1000"&&(t==null?void 0:t.IsActive)&&!(t!=null&&t.IsHidden)).sort((t,o)=>Number(t==null?void 0:t.SortID)<Number(o==null?void 0:o.SortID)?-1:Number(t==null?void 0:t.SortID)>Number(o==null?void 0:o.SortID)?1:0)});return await e.fetchServices().then(()=>{var c,t,o,_;(c=d.value)!=null&&c.find(i=>{var v;return(i==null?void 0:i.ID)===((v=r==null?void 0:r.query)==null?void 0:v.ServiceID)})?a.value=(t=r==null?void 0:r.query)==null?void 0:t.ServiceID:(a.value=(_=(o=d.value)==null?void 0:o[0])==null?void 0:_.ID,n.replace(`/AdditionalServicesOrder?ServiceID=${a.value}`))}),await D.fetchContracts(),await I.fetchUserOrders(),await e.fetchDNSmanagerOrders(),{section:a,getServices:d,switchToSection:S}}},Se=L(Y,[["render",X],["__scopeId","data-v-b729a286"]]);export{Se as default};

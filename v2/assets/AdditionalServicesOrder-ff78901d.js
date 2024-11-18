import{j as O,o as f,c as h,b as m,a as v,F as g,x as L,y as b,w as k,t as C,n as y,k as x,_ as T,r as B,a7 as V,e as E,f as W,g as z,i as M,C as U}from"./index-3af25d4c.js";import{u as j}from"./contracts-4f21f7b6.js";import{u as q}from"./services-b0ae5192.js";import{S as A}from"./ServiceListOrderSelection-73ad9145.js";import{E as F}from"./EmptyStateBlock-25904ff1.js";import{s as H}from"./multiselect-eb4b0170.js";import{B as N}from"./BlockBalanceAgreement-4b67da5f.js";import{_ as R}from"./IconArrow-7b47b2f8.js";import"./domain-4c41df09.js";import"./ButtonDefault-b95c906d.js";import"./BasicInput-ee6974cd.js";import"./component-17343301.js";import"./BlockSelectContract-bbfe1748.js";import"./ClausesKeeper-1519f453.js";import"./IconClose-f49a70b0.js";import"./bootstrap-vue-next.es-79941670.js";const G={class:"section"},J={class:"container"},K={class:"section-header"},P={class:"section-nav"},Q=["onClick"],X={class:"multiselect-option"},Y={class:"section-body"};function Z(i,c,a,t,p,n){var r,e,s,u,d,S,D;const I=N,l=R,w=O("Multiselect"),_=A;return f(),h(g,null,[m(I),v("div",G,[v("div",J,[v("div",K,[v("div",P,[(f(!0),h(g,null,L((r=t.getServices)==null?void 0:r.slice(0,t.visibleItemsCount),o=>(f(),h("div",{class:y(["section-nav__item",{"section-link":!0,"section-link-active":t.section===(o==null?void 0:o.ID)}]),onClick:ee=>t.switchToSection(o==null?void 0:o.ID)},C(o==null?void 0:o.Item),11,Q))),256)),((e=t.getServices)==null?void 0:e.length)>t.visibleItemsCount?(f(),b(w,{key:0,class:y(["multiselect-special",{"multiselect--white additional-service__select":!0,"additional-service__select_active":(u=t.getServices)==null?void 0:u.slice(t.visibleItemsCount,(s=t.getServices)==null?void 0:s.length).find(o=>(o==null?void 0:o.ID)===t.section)}]),modelValue:t.section,"onUpdate:modelValue":c[0]||(c[0]=o=>t.section=o),options:(S=t.getServices)==null?void 0:S.slice(t.visibleItemsCount,(d=t.getServices)==null?void 0:d.length),label:"ID",onSelect:c[1]||(c[1]=o=>t.switchToSection(o))},{option:k(({option:o})=>[v("div",X,C(o==null?void 0:o.Item),1)]),caret:k(()=>[m(l,{class:"icon-arrow_caret"})]),_:1},8,["modelValue","class","options"])):x("",!0)])]),v("div",Y,[m(_,{sectionID:t.section,sectionData:(D=t.getServices)==null?void 0:D.find(o=>o.ID===t.section),onSwitchTab:t.switchToSection},null,8,["sectionID","sectionData","onSwitchTab"])])])])],64)}const $={components:{ServiceListOrderSelection:A,emptyStateBlock:F,Multiselect:H,BlockBalanceAgreement:N},async setup(){const i=B(3);let c=()=>{window.innerWidth>=1920?i.value=9:window.innerWidth>=1600?i.value=7:window.innerWidth>=1440?i.value=6:window.innerWidth>=1200?i.value=5:window.innerWidth>=992?i.value=4:i.value=3};const a=V(),t=E(),p=W(),n=q(),I=j(),l=B(null);function w(r){t.replace(`/AdditionalServicesOrder?ServiceID=${r}`),l.value=r}const _=z(()=>{var r;return(r=Object.keys(n==null?void 0:n.ServicesList))==null?void 0:r.map(e=>{var s;return{...n==null?void 0:n.ServicesList[e],value:(s=n==null?void 0:n.ServicesList[e])==null?void 0:s.ID}}).filter(e=>(e==null?void 0:e.ServicesGroupID)!=="1000"&&(e==null?void 0:e.IsActive)&&!(e!=null&&e.IsHidden)).sort((e,s)=>Number(e==null?void 0:e.SortID)<Number(s==null?void 0:s.SortID)?-1:Number(e==null?void 0:e.SortID)>Number(s==null?void 0:s.SortID)?1:0)});return M(()=>{window.addEventListener("resize",c),c()}),U(()=>{window.removeEventListener("resize",c)}),await n.fetchServices().then(()=>{var r,e,s,u;(r=_.value)!=null&&r.find(d=>{var S;return(d==null?void 0:d.ID)===((S=a==null?void 0:a.query)==null?void 0:S.ServiceID)})?l.value=(e=a==null?void 0:a.query)==null?void 0:e.ServiceID:(l.value=(u=(s=_.value)==null?void 0:s[0])==null?void 0:u.ID,t.replace(`/AdditionalServicesOrder?ServiceID=${l.value}`))}),await I.fetchContracts(),await p.fetchUserOrders(),await n.fetchDNSmanagerOrders(),{section:l,getServices:_,switchToSection:w,visibleItemsCount:i}}},we=T($,[["render",Z],["__scopeId","data-v-948a85f9"]]);export{we as default};

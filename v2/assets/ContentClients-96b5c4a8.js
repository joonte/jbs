import{o as c,c as _,b as r,a as t,F as d,x as u,y as v,t as n,p as g,l as f,_ as k,g as C}from"./index-642cdac5.js";import{u as y}from"./content-3a8d99dd.js";import"./contracts-16ef7000.js";import"./bootstrap-vue-next.es-9ff54498.js";import"./ButtonDefault-eb076df2.js";import{B}from"./BlockBalanceAgreement-ccba2ab2.js";import{E as b}from"./EmptyStateBlock-1861cff5.js";import{_ as x}from"./ClausesKeeper-12bc5d50.js";import"./IconArrow-c0196cbe.js";import"./IconClose-f1b85910.js";const L=e=>(g("data-v-2a6e24b6"),e=e(),f(),e),I={class:"section"},S={class:"container"},E=L(()=>t("div",{class:"section-header"},[t("h1",{class:"section-title"},"Клиенты")],-1)),N={key:0,class:"list"},R={class:"list-col"},U={class:"list-item_client"},w={class:"list-item__header"},A={class:"list-item__header-col"},F={class:"list-item__img"},V=["src"],$={class:"list-item__header-col"},j={class:"list-item__title"},D={class:"list-item__subtitle"},O=["href"],q={class:"list-item__text"};function z(e,i,a,o,l,H){const p=B,h=x,m=b;return c(),_(d,null,[r(p),t("div",I,[t("div",S,[E,r(h),o.getClients&&o.getClients.length>0?(c(),_("div",N,[(c(!0),_(d,null,u(o.getClients,s=>(c(),_("div",R,[t("div",U,[t("div",w,[t("div",A,[t("div",F,[t("img",{class:"list-item__image",src:s==null?void 0:s.IconURL},null,8,V)])]),t("div",$,[t("div",j,n(s==null?void 0:s.Name),1),t("div",D,[t("a",{href:s==null?void 0:s.SiteURL},n(s==null?void 0:s.SiteURL),9,O)])])]),t("div",q,n(s==null?void 0:s.Comment),1)])]))),256))])):(c(),v(m,{key:1,label:"Пока что нет добавленных клиентов"}))])])],64)}const G={async setup(){const e=y(),i=C(()=>{var a,o;return(o=(a=Object.keys(e==null?void 0:e.clientsList))==null?void 0:a.map(l=>e==null?void 0:e.clientsList[l]))==null?void 0:o.reverse()});return await e.fetchClients(),{getClients:i}}},ss=k(G,[["render",z],["__scopeId","data-v-2a6e24b6"]]);export{ss as default};
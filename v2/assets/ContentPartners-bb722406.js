import{o as c,c as _,b as d,a as t,F as i,v,x as f,t as a,p as g,l as k,_ as y,g as B}from"./index-eaeb1281.js";import{u as b}from"./content-dab41baa.js";import"./contracts-ac470ffa.js";import"./bootstrap-vue-next.es-f4c478af.js";import"./ButtonDefault-598fef28.js";import{B as L}from"./BlockBalanceAgreement-11afb494.js";import{E as P}from"./EmptyStateBlock-5faddaec.js";import{_ as x}from"./ClausesKeeper-a152fc61.js";import"./IconArrow-ce1bc7b6.js";import"./IconClose-e8029cfe.js";const S=o=>(g("data-v-58cdfb76"),o=o(),k(),o),C={class:"section"},I={class:"container"},R=S(()=>t("div",{class:"section-header"},[t("h1",{class:"section-title"},"Наши партнеры")],-1)),U={key:0,class:"list"},E={class:"list-col"},N={class:"list-item_client"},m={class:"list-item__header"},w={class:"list-item__header-col"},A={class:"list-item__img"},F=["src"],V={class:"list-item__header-col"},$={class:"list-item__title"},j={class:"list-item__subtitle"},D=["href"],O=["href"],q={class:"list-item__text"};function z(o,r,n,e,l,H){const p=L,h=x,u=P;return c(),_(i,null,[d(p),t("div",C,[t("div",I,[R,d(h),e.getPartners&&e.getPartners.length>0?(c(),_("div",U,[(c(!0),_(i,null,v(e.getPartners,s=>(c(),_("div",E,[t("div",N,[t("div",m,[t("div",w,[t("div",A,[t("img",{src:s==null?void 0:s.LogoURL,alt:""},null,8,F)])]),t("div",V,[t("div",$,a(s==null?void 0:s.Name),1),t("div",j,[t("a",{href:s==null?void 0:s.SiteURL},a(s==null?void 0:s.SiteURL),9,D),t("a",{href:"tel:"+(s==null?void 0:s.SiteURL)},a(s==null?void 0:s.Phone),9,O),t("span",null,a(s==null?void 0:s.City),1)])])]),t("div",q,a(s==null?void 0:s.Comment),1)])]))),256))])):(c(),f(u,{key:1,label:"Пока что нет добавленных партнеров"}))])])],64)}const G={async setup(){const o=b(),r=B(()=>{var n,e;return(e=(n=Object.keys(o==null?void 0:o.partnersList))==null?void 0:n.map(l=>o==null?void 0:o.partnersList[l]))==null?void 0:e.reverse()});return await o.fetchPartners(),{getPartners:r}}},ts=y(G,[["render",z],["__scopeId","data-v-58cdfb76"]]);export{ts as default};

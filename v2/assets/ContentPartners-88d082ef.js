import{o as a,c as _,b as u,a as t,F as i,y as v,z as f,t as c,p as g,m as y,_ as k,h as B,i as L,j as P}from"./index-400feb76.js";import{u as b}from"./content-e4b91d51.js";import"./contracts-d1f15c94.js";import"./bootstrap-vue-next.es-03cca149.js";import"./ButtonDefault-7609cc9e.js";import{B as S}from"./BlockBalanceAgreement-9b1029a8.js";import{E as x}from"./EmptyStateBlock-d1623b92.js";import"./IconArrow-10b76666.js";const C=e=>(g("data-v-3c845453"),e=e(),y(),e),I={class:"section"},R={class:"container"},U=C(()=>t("div",{class:"section-header"},[t("h1",{class:"section-title"},"Наши партнеры")],-1)),j={key:0,class:"list"},w={class:"list-col"},E={class:"list-item_client"},N={class:"list-item__header"},A={class:"list-item__header-col"},D={class:"list-item__img"},F=["src"],O={class:"list-item__header-col"},V={class:"list-item__title"},z={class:"list-item__subtitle"},M=["href"],$=["href"],q={class:"list-item__text"};function G(e,l,n,o,r,d){const h=S,p=x;return a(),_(i,null,[u(h),t("div",I,[t("div",R,[U,o.getPartners&&o.getPartners.length>0?(a(),_("div",j,[(a(!0),_(i,null,v(o.getPartners,s=>(a(),_("div",w,[t("div",E,[t("div",N,[t("div",A,[t("div",D,[t("img",{src:s==null?void 0:s.LogoURL,alt:""},null,8,F)])]),t("div",O,[t("div",V,c(s==null?void 0:s.Name),1),t("div",z,[t("a",{href:s==null?void 0:s.SiteURL},c(s==null?void 0:s.SiteURL),9,M),t("a",{href:"tel:"+(s==null?void 0:s.SiteURL)},c(s==null?void 0:s.Phone),9,$),t("span",null,c(s==null?void 0:s.City),1)])])]),t("div",q,c(s==null?void 0:s.Comment),1)])]))),256))])):(a(),f(p,{key:1,label:"Пока что нет добавленных партнеров"}))])])],64)}const H={async setup(){const e=b(),l=B(()=>{var o,r;return(r=(o=Object.keys(e==null?void 0:e.partnersList))==null?void 0:o.map(d=>e==null?void 0:e.partnersList[d]))==null?void 0:r.reverse()}),n=async()=>{await e.fetchPartners()};return L(n),P(()=>e.partnersList,o=>{Object.keys(o).length===0&&n()}),{getPartners:l,fetchData:n}}},m=k(H,[["render",G],["__scopeId","data-v-3c845453"]]);export{m as default};

import{o as c,c as _,b as h,a as t,F as r,x as v,y as u,t as a,p as g,l as m,_ as f,g as C}from"./index-b5b5ce3d.js";import{u as k}from"./content-b926406b.js";import"./contracts-74f05bb7.js";import"./bootstrap-vue-next.es-37aa63ea.js";import"./ButtonDefault-0006f72a.js";import{B as y}from"./BlockBalanceAgreement-a016b94f.js";import{E as B}from"./EmptyStateBlock-cd0c2748.js";import"./IconArrow-fddabae2.js";const b=e=>(g("data-v-748c435b"),e=e(),m(),e),x={class:"section"},L={class:"container"},I=b(()=>t("div",{class:"section-header"},[t("h1",{class:"section-title"},"Клиенты")],-1)),S={key:0,class:"list"},E={class:"list-col"},N={class:"list-item_client"},R={class:"list-item__header"},U={class:"list-item__header-col"},w={class:"list-item__img"},A=["src"],F={class:"list-item__header-col"},V={class:"list-item__title"},j={class:"list-item__subtitle"},D=["href"],O={class:"list-item__text"};function $(e,l,n,o,i,z){const d=y,p=B;return c(),_(r,null,[h(d),t("div",x,[t("div",L,[I,o.getClients&&o.getClients.length>0?(c(),_("div",S,[(c(!0),_(r,null,v(o.getClients,s=>(c(),_("div",E,[t("div",N,[t("div",R,[t("div",U,[t("div",w,[t("img",{class:"list-item__image",src:s==null?void 0:s.IconURL},null,8,A)])]),t("div",F,[t("div",V,a(s==null?void 0:s.Name),1),t("div",j,[t("a",{href:s==null?void 0:s.SiteURL},a(s==null?void 0:s.SiteURL),9,D)])])]),t("div",O,a(s==null?void 0:s.Comment),1)])]))),256))])):(c(),u(p,{key:1,label:"Пока что нет добавленных клиентов"}))])])],64)}const q={async setup(){const e=k(),l=C(()=>{var n,o;return(o=(n=Object.keys(e==null?void 0:e.clientsList))==null?void 0:n.map(i=>e==null?void 0:e.clientsList[i]))==null?void 0:o.reverse()});return await e.fetchClients(),{getClients:l}}},W=f(q,[["render",$],["__scopeId","data-v-748c435b"]]);export{W as default};
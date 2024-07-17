import{S}from"./ServerItem-821f79fa.js";import{o as n,c as _,b as p,a as r,F as m,x as g,y as f,p as k,l as v,_ as I,g as B}from"./index-642cdac5.js";import{u as y}from"./dedicatedServer-2accfe16.js";import{B as u}from"./BlockBalanceAgreement-ccba2ab2.js";import{E as x}from"./EmptyStateBlock-1861cff5.js";import{_ as $}from"./ClausesKeeper-12bc5d50.js";import"./IconDots-24f2aa45.js";import"./IconCard-b302365f.js";import"./StatusBadge-52b4bbab.js";import"./contracts-16ef7000.js";import"./bootstrap-vue-next.es-9ff54498.js";import"./ButtonDefault-eb076df2.js";import"./IconArrow-c0196cbe.js";import"./IconClose-f1b85910.js";const b=o=>(k("data-v-7777c4d1"),o=o(),v(),o),A={class:"section"},C={class:"container"},O=b(()=>r("div",{class:"section-header"},[r("h1",{class:"section-title"},"Заказ выделенного сервера"),r("div",{class:"section-text"},"При покупке этой услуги у вас появляется полностью укомплектованный, настроенный выделенный сервер, с операционной системой на ваш выбор, размещённый в дата-центре в Москве, по самым низким ценам.")],-1)),w={key:0,class:"list"};function E(o,i,d,e,s,t){const c=u,a=$,h=S,D=x;return n(),_(m,null,[p(c),r("div",A,[r("div",C,[O,p(a),e.getDSSchemes&&e.getDSSchemes.length?(n(),_("div",w,[(n(!0),_(m,null,g(e.sort(e.getDSSchemes),l=>(n(),f(h,{data:l,specifications:e.getDSItemSpecifications(l.ID)},null,8,["data","specifications"]))),256))])):(n(),f(D,{key:1,label:"Нет доступных серверов"}))])])],64)}const M={components:{ServerItem:S,BlockBalanceAgreement:u},async setup(){const o=y(),i=B(()=>Object.keys(o==null?void 0:o.DSSchemes).map(s=>o==null?void 0:o.DSSchemes[s]));function d(s){var c;const t=(c=i.value)==null?void 0:c.find(a=>(a==null?void 0:a.ID)===s);return[`OS: ${t==null?void 0:t.OS}`,`CPU: ${t==null?void 0:t.CPU}`,`RAM: ${t==null?void 0:t.ram} GB`,`RAID: ${t==null?void 0:t.raid}`,`DISK: ${t==null?void 0:t.disks}`]}function e(s){return s!==null?s.sort((t,c)=>t.CostMonth-c.CostMonth):s}return await o.fetchDSOrders(),await o.fetchDSSchemes(),{getDSItemSpecifications:d,sort:e,getDSSchemes:i}}},Q=I(M,[["render",E],["__scopeId","data-v-7777c4d1"]]);export{Q as default};
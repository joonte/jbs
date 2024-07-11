import{j as w,o as l,c as P,b as h,a,w as M,d as K,y,am as q,t as D,F as V,x as R,p as z,l as G,_ as J,f as Q,e as W,r as T,S as X,h as Y,g as i}from"./index-145d9cde.js";import{I as Z}from"./IconSearch-9c7c86ad.js";import{H as U}from"./HostingItem-47d6fe54.js";import{_ as A}from"./ClausesKeeper-d3875457.js";import{s as $}from"./multiselect-a2d623ca.js";import{u as tt}from"./hosting-57e879dc.js";import{u as et}from"./contracts-009c925b.js";import{B as N}from"./BlockBalanceAgreement-650ad03e.js";import{u as ot}from"./testTransforms-eec373ee.js";import{u as st}from"./globalActions-de64efb8.js";import{E as nt}from"./EmptyStateBlock-a6b81e83.js";import{_ as at}from"./FormInputSearch-a9f5bea5.js";import"./IconDots-250f781f.js";import"./IconCard-647fe573.js";import"./StatusBadge-cd3873b5.js";import"./hostStatuses-94d00f20.js";import"./HelperComponent-3b7b2692.js";import"./ButtonDefault-6a3a5e1e.js";import"./IconArrow-f2b12a71.js";import"./useTimeFunction-8602dd60.js";import"./bootstrap-vue-next.es-759b1c9f.js";import"./OrderIDBadge-32f54670.js";import"./IconClose-f461656c.js";import"./IconPlus-de34f4b6.js";const rt=n=>(z("data-v-1022871d"),n=n(),G(),n),lt={class:"section"},ct={class:"container"},it={class:"section-header"},ut=rt(()=>a("h1",{class:"section-title"},"Хостинг",-1)),dt={class:"list-form"},mt={class:"list-form_col list-form_col--sm"},_t={class:"list-form_select"},ft={class:"multiselect-option"},gt={key:0};function pt(n,c,u,o,O,v){var r,f,g;const d=N,m=w("router-link"),_=A,H=w("Multiselect"),x=at,B=U,I=nt;return l(),P(V,null,[h(d),a("div",lt,[a("div",ct,[a("div",it,[ut,h(m,{class:"btn btn--blue btn-default",to:"/HostingSchemes"},{default:M(()=>[K("Заказать")]),_:1})]),(l(),y(q,null,[h(_)],1024)),a("div",dt,[a("div",mt,[a("div",_t,[h(H,{modelValue:o.select,"onUpdate:modelValue":c[0]||(c[0]=s=>o.select=s),options:o.getUsers,disabled:((r=o.getUsers)==null?void 0:r.length)<=1,label:"name"},{singlelabel:M(({value:s})=>[a("span",null,"# "+D(s.value.padStart(5,"0"))+" / "+D(s.name),1)]),option:M(({option:s})=>[a("div",ft,"# "+D(s.value.padStart(5,"0"))+" / "+D(s.name),1)]),_:1},8,["modelValue","options","disabled"])])]),h(x,{modelValue:o.search,"onUpdate:modelValue":c[1]||(c[1]=s=>o.search=s)},null,8,["modelValue"])]),o.getHostingList&&((f=o.getHostingList)==null?void 0:f.length)>0?(l(),P("div",gt,[o.filterProducts&&((g=o.filterProducts)==null?void 0:g.length)>0?(l(!0),P(V,{key:0},R(o.filterProducts,(s,L)=>{var C,b;return l(),y(B,{"item-data":s,"user-name":`${(C=o.getUser)==null?void 0:C.Name} - ${(b=o.getContracts[s==null?void 0:s.ContractID])==null?void 0:b.Customer}`,params:o.getHostingItemParam(s),"info-link":`/HostingOrders/${s==null?void 0:s.OrderID}`,"is-last":L===o.filterProducts.length-1,onDelete:o.deleteItem,onTransfer:o.transferItem,onManage:o.orderManage},null,8,["item-data","user-name","params","info-link","is-last","onDelete","onTransfer","onManage"])}),256)):(l(),y(I,{key:1,class:"no-margin",label:"Заказы не найдены"}))])):(l(),y(I,{key:1,class:"no-margin",label:"У Вас нет заказов на хостинг"}))])])],64)}const ht={components:{IconSearch:Z,Multiselect:$,ClausesKeeper:A,HostingItem:U,BlockBalanceAgreement:N},async setup(){const n=tt(),c=Q(),u=et(),o=st(),O=W(),{formDescriptionLine:v}=ot(),d=T("*"),m=T(null),_=X("emitter");_.on("updateHostingList",()=>{n.fetchHostingOrders()}),Y(()=>{r.value.length<1&&H()});function H(){O.push({path:"/HostingSchemes"})}function x(e){_.emit("open-modal",{component:"DeleteItem",data:{id:e,message:"Вы действительно хотите удалить хостинг?",tableID:"HostingOrders",successEmit:"updateHostingList"}})}function B(e){_.emit("open-modal",{component:"OrdersTransfer",data:e})}function I(e){o.OrderManageHosting(e)}const r=i(()=>{var e;return(e=Object.keys(n==null?void 0:n.hostingList).map(t=>n==null?void 0:n.hostingList[t]))==null?void 0:e.reverse()}),f=i(()=>n==null?void 0:n.hostingSchemes),g=i(()=>u==null?void 0:u.contractsList),s=i(()=>{let e=[{value:"*",name:"Все договора"}];return r.value!==null?(r.value.forEach(t=>{var p;e.find(k=>k.value===t.ContractID)||e.push({value:t.ContractID,name:(p=g.value[t==null?void 0:t.ContractID])==null?void 0:p.Customer})}),e):{value:"*",name:"Все договора"}}),L=i(()=>c.userInfo),C=i(()=>E(b(r.value)));function b(e){return d.value!=="*"?e.filter(t=>(t==null?void 0:t.ContractID)===d.value):e}function E(e){return m.value!==null?e.filter(t=>t==null?void 0:t.Domain.toLowerCase().includes(m.value.toLowerCase())):e}function j(e){var p,k,S;const t=F(e==null?void 0:e.SchemeID);return[{text:t==null?void 0:t.Name},{text:v((p=t==null?void 0:t.SchemeParams)==null?void 0:p.limit_webdomains)},{text:v((k=t==null?void 0:t.SchemeParams)==null?void 0:k.limit_quota)},{text:v((S=t==null?void 0:t.SchemeParams)==null?void 0:S.limit_emails)},{text:`${t==null?void 0:t.CostMonth} ₽ в месяц`,bold:!0}]}function F(e){var t;return(t=f.value)==null?void 0:t[e]}return await n.fetchHostingOrders(),await n.fetchHostingSchemes(),await u.fetchContracts(),{getHostingList:r,getHostingSchemes:f,orderManage:I,filterProducts:C,getUsers:s,getUser:L,select:d,search:m,getContracts:g,deleteItem:x,transferItem:B,getHostingItemParam:j,navigateToHostingSchemes:H}}},Kt=J(ht,[["render",pt],["__scopeId","data-v-1022871d"]]);export{Kt as default};

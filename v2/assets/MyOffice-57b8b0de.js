import{j as P,o as A,c as N,b as a,a as e,w as l,d as p,t as D,F as R,p as V,l as H,_ as j,f as M,S as E,e as L,g as y}from"./index-5f8fd0e9.js";import{u as U}from"./postings-2abc5e3f.js";import{u as W}from"./contracts-00b6c53b.js";import{I as $,a as B,b as F,c as T}from"./IconWeb-f25a9411.js";import{S as x}from"./StatusBadge-fdff4dcc.js";import{B as q}from"./BlockBalanceAgreement-cf03db60.js";import{C as z}from"./bootstrap-vue-next.es-a97602fc.js";import{_ as G}from"./IconCard-3d2eebd0.js";import{_ as J}from"./ButtonDefault-458eab69.js";import{_ as K}from"./ClausesKeeper-8218189c.js";import"./IconArrow-c9744352.js";import"./IconClose-2e1ba86d.js";const Q=[{key:"ID",label:"Заказ",variant:"td-gray td-order"},{key:"ContractID",label:"Договор",variant:"td-blue"},{key:"ServiceID",label:"Услуга",variant:"td-gray td-order"},{key:"Reason",label:"Осталось дн.",variant:"td-gray td-order"},{key:"StatusID",label:"Статус",sortable:!0,variant:"td-gray td-order"},{key:"controls",label:"",variant:"td-controls"}],X={Deleted:{id:1,label:"Удален",color:"#DF5747",background:"#DF57471A"},Suspended:{id:2,label:"Заблокирован",color:"#DF5747",background:"#DF57471A"},OnService:{id:3,label:"Сервер на обслуживании",color:"#FFC107",background:"rgba(255,193,7,0.1)"}},_=n=>(V("data-v-d16ac16f"),n=n(),H(),n),Y={class:"section"},Z={class:"container"},ee={class:"section-header"},te={class:"list-grid-three-col"},se={class:"list-col"},oe={class:"list-item"},ae=_(()=>e("div",{class:"list-item__row"},[e("div",{class:"list-item__title"},"Услуги")],-1)),ne={class:"list-col"},ce={class:"list-item"},le=_(()=>e("div",{class:"list-item__row"},[e("div",{class:"list-item__title"},"Мой офис")],-1)),re={class:"list-item__row icon-row"},ie={class:"list-col"},de={class:"list-item"},_e=_(()=>e("div",{class:"list-item__row"},[e("div",{class:"list-item__title"},"Поддержка")],-1)),ue={class:"list-item__row icon-row"},me=_(()=>e("div",{class:"section-header"},[e("h2",{class:"sub-section-title"},"Требует внимания")],-1)),pe=_(()=>e("span",{class:"td-mobile"},"Заказ",-1)),ve={class:"d-block"},be=_(()=>e("span",{class:"td-mobile"},"Договор",-1)),fe=_(()=>e("span",{class:"td-mobile"},"Услуга",-1)),ge={class:"d-block"},De=_(()=>e("span",{class:"td-mobile"},"Осталось дн.",-1)),he={class:"d-block"},Se=_(()=>e("span",{class:"td-mobile"},"Статус",-1)),Ie={class:"btn__group"},ke=["onClick"],we=_(()=>e("p",null,"ОПЛАТИТЬ",-1));function ye(n,c,b,r,v,C){const h=P("BalanceAgreement"),f=K,S=$,u=P("router-link"),I=B,k=F,w=T,s=J,t=x,i=G,m=z;return A(),N(R,null,[a(h),e("div",Y,[e("div",Z,[e("div",ee,[e("h1",{class:"section-title",onClick:c[0]||(c[0]=o=>r.reloadNow())},"Домашняя страница")]),a(f,{partition:"Header:/Home"}),e("div",te,[e("div",se,[e("div",oe,[ae,a(u,{class:"list-item__row icon-row",to:"/HostingOrders"},{default:l(()=>[a(S),p("Заказать хостинг")]),_:1}),a(u,{class:"list-item__row icon-row",to:"/DomainOrders"},{default:l(()=>[a(I),p("Заказать домен")]),_:1}),a(u,{class:"list-item__row icon-row",to:"/DSOrders"},{default:l(()=>[a(k),p("Заказать выделенный сервер")]),_:1}),a(u,{class:"list-item__row icon-row",to:"/VPSOrders"},{default:l(()=>[a(w),p("Заказать VPS/VDS сервер")]),_:1})])]),e("div",ne,[e("div",ce,[e("div",null,[le,a(u,{class:"list-item__row icon-row",to:"/Contracts"},{default:l(()=>[p("Мои договора")]),_:1})]),e("div",re,[a(s,{label:"Создать договор",onClick:c[1]||(c[1]=o=>n.$router.push("/ContractMake"))})])])]),e("div",ie,[e("div",de,[e("div",null,[_e,a(u,{class:"list-item__row icon-row",to:"/Tickets"},{default:l(()=>[p("Тикеты")]),_:1}),a(u,{class:"list-item__row icon-row",to:"/Contacts"},{default:l(()=>[p("Контакты")]),_:1})]),e("div",ue,[a(s,{label:"Создать тикет",onClick:c[2]||(c[2]=o=>n.$router.push("/NewTicket"))})])])])]),me,a(m,{class:"basic-table",items:r.getOrders,fields:r.dashboardTableFields,"show-empty":!0,"per-page":5,page:1,"empty-text":"Пока не требуется никаких действий","empty-filtered-text":"Пока не требуется никаких действий",onRowClicked:c[3]||(c[3]=(o,d,g)=>r.navigateToOrderPage(o,g))},{"cell(ID)":l(o=>[pe,e("span",ve,D(o.value),1)]),"cell(ContractID)":l(o=>{var d;return[be,e("span",null,D((d=r.getContracts[o.value])==null?void 0:d.Customer),1)]}),"cell(ServiceID)":l(o=>{var d;return[fe,e("span",ge,D((d=r.getServices[o.value])==null?void 0:d.Name),1)]}),"cell(Reason)":l(o=>{var d;return[De,e("span",he,D(((d=o.item)==null?void 0:d.DaysRemainded)||0),1)]}),"cell(StatusID)":l(o=>[Se,a(t,{class:"clickable-element",status:o.value,"status-table":"Orders",onClick:d=>{var g,O;return r.openHistoryStatuses((g=o.item)==null?void 0:g.ServiceOrderID,(O=o.item)==null?void 0:O.Code)}},null,8,["status","onClick"])]),"cell(controls)":l(o=>[e("div",Ie,[e("button",{class:"btn btn-default btn--border",onClick:d=>r.navigateToProlong(o.item)},[a(i),we],8,ke)])]),_:1},8,["items","fields"])])])],64)}const Ce={components:{IconServer:$,IconDedicatedServers:F,IconWeb:B,IconVPS:T,StatusBadge:x,BalanceAgreement:q},async setup(){const n=M(),c=U(),b=W(),r=E("emitter"),v=L(),C=y(()=>{var s;return(s=Object.keys(n==null?void 0:n.userOrders).map(t=>n==null?void 0:n.userOrders[t]))==null?void 0:s.reverse().filter(t=>(t==null?void 0:t.StatusID)==="Deleted"||(t==null?void 0:t.StatusID)==="Suspended"||(t==null?void 0:t.StatusID)==="OnService"||(t==null?void 0:t.DaysRemainded)>0&&(t==null?void 0:t.DaysRemainded)<14)}),h=y(()=>b==null?void 0:b.contractsList),f=y(()=>c==null?void 0:c.servicesList);function S(){v.go(0)}function u(s,t){var i;if(!t.target.closest("button, a, .clickable-element")){let m=(i=f.value[s.ServiceID])==null?void 0:i.Code;m==="Default"||m==="ExtraIP"||m==="ISPsw"||m==="DNSmanager"?v.push(`/AdditionalServices/${s.ID}`):v.push(`/${m}Orders/${s.ID}`)}}function I(s){r.emit("open-modal",{component:"DeleteItem",data:{id:s,message:`Вы действительно хотите удалить заказ #${s}?`,tableID:"Orders"}})}function k(s,t){let i="";t==="Default"?i="Orders":i=t+"Orders",r.emit("open-modal",{component:"StatusHistory",data:{modeID:i,rowID:s}})}function w(s){var i;let t=(i=f.value[s.ServiceID])==null?void 0:i.Code;t==="Default"?v.push(`/ServiceOrderPay/${s.ID}`):v.push(`/${t}OrderPay/${s.ID}`)}return await n.fetchUserOrders(),await c.fetchServices(),await b.fetchContracts(),{getOrders:C,getContracts:h,getServices:f,dangerStatuses:X,dashboardTableFields:Q,deleteItem:I,navigateToOrderPage:u,openHistoryStatuses:k,navigateToProlong:w,reloadNow:S}}},je=j(Ce,[["render",ye],["__scopeId","data-v-d16ac16f"]]);export{je as default};

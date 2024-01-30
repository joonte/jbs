import{j as w,o as V,c as x,b as e,a as s,w as a,d as i,t as v,F as $,p as N,l as T,_ as P,f as j,R as H,g as b}from"./index-eaeb1281.js";import{u as M}from"./postings-f17285bd.js";import{u as R}from"./contracts-ac470ffa.js";import{I as S,a as D,b as k,c as I}from"./IconWeb-57ed1205.js";import{S as y}from"./StatusBadge-e3643114.js";import{B as L}from"./BlockBalanceAgreement-11afb494.js";import{C as E}from"./bootstrap-vue-next.es-f4c478af.js";import{_ as U}from"./ButtonDefault-598fef28.js";import{_ as W}from"./ClausesKeeper-a152fc61.js";import"./IconArrow-ce1bc7b6.js";import"./IconClose-e8029cfe.js";const q=[{key:"ID",label:"Заказ",variant:"td-gray td-order"},{key:"ContractID",label:"Договор",variant:"td-blue"},{key:"ServiceID",label:"Услуга",variant:"td-gray td-order"},{key:"StatusID",label:"Статус",sortable:!0,variant:"td-gray td-order"},{key:"controls",label:"",variant:"td-controls"}],z={Deleted:{id:1,label:"Удален",color:"#DF5747",background:"#DF57471A"},Suspended:{id:2,label:"Заблокирован",color:"#DF5747",background:"#DF57471A"},OnService:{id:3,label:"Сервер на обслуживании",color:"#FFC107",background:"rgba(255,193,7,0.1)"}},r=o=>(N("data-v-0d8957d6"),o=o(),T(),o),G={class:"section"},J={class:"container"},K=r(()=>s("div",{class:"section-header"},[s("h1",{class:"section-title"},"Дашбоард")],-1)),Q={class:"list-grid-three-col"},X={class:"list-col"},Y={class:"list-item"},Z=r(()=>s("div",{class:"list-item__row"},[s("div",{class:"list-item__title"},"Услуги")],-1)),ss={class:"list-col"},ts={class:"list-item"},es=r(()=>s("div",{class:"list-item__row"},[s("div",{class:"list-item__title"},"Мой офис")],-1)),os={class:"list-item__row icon-row"},as={class:"list-col"},cs={class:"list-item"},ns=r(()=>s("div",{class:"list-item__row"},[s("div",{class:"list-item__title"},"Поддержка")],-1)),rs={class:"list-item__row icon-row"},ls=r(()=>s("div",{class:"section-header"},[s("h2",{class:"sub-section-title"},"Требует внимания")],-1)),is=r(()=>s("span",{class:"td-mobile"},"Заказ",-1)),ds={class:"d-block"},_s=r(()=>s("span",{class:"td-mobile"},"Договор",-1)),ps=r(()=>s("span",{class:"td-mobile"},"Услуга",-1)),us={class:"d-block"},ms=r(()=>s("span",{class:"td-mobile"},"Статус",-1));function vs(o,c,_,d,f,h){const u=w("BalanceAgreement"),m=W,l=S,t=w("router-link"),C=D,O=k,B=I,g=U,F=y,A=E;return V(),x($,null,[e(u),s("div",G,[s("div",J,[K,e(m,{partition:"Header:/Home"}),s("div",Q,[s("div",X,[s("div",Y,[Z,e(t,{class:"list-item__row icon-row",to:"/HostingOrders"},{default:a(()=>[e(l),i("Заказать хостинг")]),_:1}),e(t,{class:"list-item__row icon-row",to:"/DomainOrders"},{default:a(()=>[e(C),i("Заказать домен")]),_:1}),e(t,{class:"list-item__row icon-row",to:"/DSOrders"},{default:a(()=>[e(O),i("Заказать выделенный сервер")]),_:1}),e(t,{class:"list-item__row icon-row",to:"/VPSOrders"},{default:a(()=>[e(B),i("Заказать VPS/VDS сервер")]),_:1})])]),s("div",ss,[s("div",ts,[s("div",null,[es,e(t,{class:"list-item__row icon-row",to:"/Contracts"},{default:a(()=>[i("Мои договора")]),_:1})]),s("div",os,[e(g,{label:"Создать договор",onClick:c[0]||(c[0]=n=>o.$router.push("/ContractMake"))})])])]),s("div",as,[s("div",cs,[s("div",null,[ns,e(t,{class:"list-item__row icon-row",to:"/Tickets"},{default:a(()=>[i("Тикеты")]),_:1}),e(t,{class:"list-item__row icon-row",to:"/Contacts"},{default:a(()=>[i("Контакты")]),_:1})]),s("div",rs,[e(g,{label:"Создать тикет",onClick:c[1]||(c[1]=n=>o.$router.push("/NewTicket"))})])])])]),ls,e(A,{class:"basic-table",items:d.getOrders,fields:d.dashboardTableFields,"show-empty":!0,"per-page":5,page:1,"empty-text":"Пока не требуется никаких действий","empty-filtered-text":"Пока не требуется никаких действий"},{"cell(ID)":a(n=>[is,s("span",ds,v(n.value),1)]),"cell(ContractID)":a(n=>{var p;return[_s,s("span",null,v((p=d.getContracts[n.value])==null?void 0:p.Customer),1)]}),"cell(ServiceID)":a(n=>{var p;return[ps,s("span",us,v((p=d.getServices[n.value])==null?void 0:p.Name),1)]}),"cell(StatusID)":a(n=>[ms,e(F,{status:n.value,"status-table":"Orders"},null,8,["status"])]),_:1},8,["items","fields"])])])],64)}const bs={components:{IconServer:S,IconDedicatedServers:k,IconWeb:D,IconVPS:I,StatusBadge:y,BalanceAgreement:L},async setup(){const o=j(),c=M(),_=R(),d=H("emitter"),f=b(()=>{var l;return(l=Object.keys(o==null?void 0:o.userOrders).map(t=>o==null?void 0:o.userOrders[t]))==null?void 0:l.reverse().filter(t=>(t==null?void 0:t.StatusID)==="Deleted"||(t==null?void 0:t.StatusID)==="Suspended"||(t==null?void 0:t.StatusID)==="OnService"||(t==null?void 0:t.DaysRemainded)>0&&(t==null?void 0:t.DaysRemainded)<14)}),h=b(()=>_==null?void 0:_.contractsList),u=b(()=>c==null?void 0:c.servicesList);function m(l){d.emit("open-modal",{component:"DeleteItem",data:{id:l,message:`Вы действительно хотите удалить заказ #${l}?`,tableID:"Orders"}})}return await o.fetchUserOrders(),await c.fetchServices(),await _.fetchContracts(),{getOrders:f,getContracts:h,getServices:u,dangerStatuses:z,dashboardTableFields:q,deleteItem:m}}},Bs=P(bs,[["render",vs],["__scopeId","data-v-0d8957d6"]]);export{Bs as default};

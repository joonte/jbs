import{o as x,c as B,a as o,t as m,b as p,w as c,d as b,p as O,l as H,_ as P,r as y,g as v,a2 as M}from"./index-3af25d4c.js";import{S as g}from"./StatusBadge-b26344fd.js";import{C as V}from"./bootstrap-vue-next.es-79941670.js";const E=[{key:"StatusDate",label:"Дата изменения",headerTitle:"Дата изменения",variant:"td-gray td-order",sortable:!0},{key:"StatusID",label:"Статус",headerTitle:"Статус",variant:"td-blue",sortable:!0},{key:"Initiator",label:"Инициатор",headerTitle:"Инициатор",variant:"td-gray td-order"},{key:"Comment",label:"Комментарий",headerTitle:"Комментарий",variant:"td-gray td-order"}],D=d=>(O("data-v-677c9ebb"),d=d(),H(),d),N={class:"modal__body"},U={class:"list-item__title"},A={class:"statuses-list"},F=D(()=>o("span",{class:"td-mobile"},"Дата изменения",-1)),j={class:"td-column-heavy"},L={class:"table-td-gray"},W=D(()=>o("span",{class:"td-mobile"},"Статус",-1)),R=D(()=>o("span",{class:"td-mobile"},"Инициатор",-1)),Y={class:"td-column-heavy"},q={class:"table-td-gray"},z=D(()=>o("span",{class:"td-mobile"},"Комментарий",-1)),G={class:"td-column-heavy"},J={class:"table-td-gray"};function K(d,i,u,r,h,I){var S,l;const _=g,n=V;return x(),B("div",N,[o("div",U,"История изменений: "+m(r.renameMode((S=u.data)==null?void 0:S.modeID))+", #"+m((l=u.data)==null?void 0:l.rowID),1),o("div",A,[p(n,{class:"basic-table",items:r.STATUSES_LIST,fields:d.statusesHistoryFields,"show-empty":!0,filterable:[],"sort-desc":r.sortDesc,"onUpdate:sortDesc":i[0]||(i[0]=e=>r.sortDesc=e),"sort-by":r.sortBy,"onUpdate:sortBy":i[1]||(i[1]=e=>r.sortBy=e),"sort-direction":r.sortDirection,"empty-text":"У вас пока нет истории статусов.","empty-filtered-text":"Статус не найден."},{"head(StatusDate)":c(()=>[b("Дата изменения")]),"head(StatusID)":c(()=>[b("Статус")]),"head(Initiator)":c(()=>[b("Инициатор")]),"head(Comment)":c(()=>[b("Комментарий")]),"cell(StatusDate)":c(e=>{var s;return[F,o("div",j,[o("div",L,m(r.formatDate((s=e.item)==null?void 0:s.StatusDate)),1)])]}),"cell(StatusID)":c(e=>{var s;return[W,p(_,{status:(s=e.item)==null?void 0:s.StatusID,"status-table":r.modeID},null,8,["status","status-table"])]}),"cell(Initiator)":c(e=>{var s;return[R,o("div",Y,[o("div",q,m((s=e.item)==null?void 0:s.Initiator),1)])]}),"cell(Comment)":c(e=>{var s;return[z,o("div",G,[o("div",J,m((s=e.item)==null?void 0:s.Comment),1)])]}),_:1},8,["items","fields","sort-desc","sort-by","sort-direction"])])])}const Q={components:{StatusBadge:g,statusesHistoryFields:E},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(d,{emit:i}){const u=y("StatusDate"),r=y(!0),h=y("desc"),I=v(()=>d.data.modeID),_=v(()=>d.data.rowID),n=y({}),S=v(()=>n.value?Object.keys(n.value).map(a=>n.value[a]).reverse().map(a=>({StatusDate:a.StatusDate,StatusID:a.StatusID,Initiator:a.Initiator,Comment:a.Comment})):[]),l=async()=>{const a=`/API/v2/StatusesHistory?ModeID=${I.value}&RowID=${_.value}`;try{const t=await M.get(a);t&&t.data?n.value=t.data:n.value={}}catch(t){console.error(t),n.value={}}};l();function e(a){let t="";switch(a){case"HostingOrders":t="Хостинг";break;case"DomainOrders":t="Домен";break;case"DSOrders":t="Выделенный сервер";break;case"VPSOrders":t="VPS/VDS Сервер";break;case"ExtraIPOrders":t="IP адрес";break;case"ISPswOrders":t="Лицензия ISPsystem";break;case"DNSmanagerOrders":t="Вторичный DNS";break;case"Contracts":t="Договор";break;case"Profiles":t="Профиль";break;case"Invoices":t="Счёт на оплату";break;case"Edesks":t="Тикет";break}return t}function s(a){const t=new Date(a*1e3),f=t.getDate().toString().padStart(2,"0"),k=(t.getMonth()+1).toString().padStart(2,"0"),w=t.getFullYear().toString(),C=t.getHours().toString().padStart(2,"0"),T=t.getMinutes().toString().padStart(2,"0");return`${f}.${k}.${w} ${C}:${T}`}return{getStatusesHistory:l,STATUSES_LIST:S,sortBy:u,sortDesc:r,sortDirection:h,modeID:I,rowID:_,formatDate:s,renameMode:e}}},tt=P(Q,[["render",K],["__scopeId","data-v-677c9ebb"]]);export{tt as default};

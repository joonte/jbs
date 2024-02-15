import{j as V,o as m,c as S,a as c,b as l,w as f,d as h,t as y,F as E,v as H,k as U,x as R,_ as j,p as J,l as K,r as N,R as Q,g as x}from"./index-87a66f57.js";import{u as W}from"./dedicatedServer-f0fd25c9.js";import{u as X}from"./contracts-63b183f3.js";import{I as Y}from"./IconSearch-c3e117a8.js";import{_ as Z}from"./HelperComponent-cba1b053.js";import{S as F}from"./StatusBadge-74310711.js";import{I as $}from"./IconRotate-ab75b3e9.js";import{f as T}from"./useTimeFunction-8602dd60.js";import{u as ee}from"./resetServer-804c8eb7.js";import{z as te,k as oe,d as ne}from"./bootstrap-vue-next.es-e0094127.js";import{_ as ae}from"./IconDots-c481ec47.js";import{_ as se}from"./IconCard-4f04d33d.js";import{_ as re}from"./OrderIDBadge-5be8d5d9.js";import{S as le}from"./Select2-1a2df089.js";import{s as ce}from"./multiselect-19c64478.js";import{_ as z}from"./FormInputSearch-f93020c4.js";import{B as G}from"./BlockBalanceAgreement-f9b358ae.js";import{u as de}from"./globalActions-a3f84ce4.js";import{E as ie}from"./EmptyStateBlock-f512ef0a.js";import{_ as _e}from"./ClausesKeeper-068355e6.js";import"./ButtonDefault-646e2a65.js";import"./IconArrow-8c8c28c1.js";import"./IconPlus-a33d0542.js";import"./IconClose-81d0053a.js";const ue={class:"hosting-item"},me={class:"hosting-item__row"},fe={class:"hosting-item__date"},De={class:"hosting-item__row"},ve={class:"hosting-item__person"},pe={key:0,class:"hosting-item__row"},Ie={class:"hosting-item__list"},ge=["innerHTML"],he={class:"hosting-item__controls"},Se={key:0,class:"btn btn--md btn--border btn--rotate btn--rotate_green"},Ce={class:"btn btn--dots"};function be(r,n,a,o,I,C){var b,i,O,t,e,u,M,P,A;const _=re,s=V("router-link"),d=F,D=V("Helper"),L=se,B=ae,v=te,w=oe,k=ne;return m(),S("div",ue,[c("div",me,[l(_,{orderID:(b=a.itemData)==null?void 0:b.OrderID},null,8,["orderID"]),l(s,{class:"hosting-item__name",to:`/DSOrders/${a.itemData.OrderID}`},{default:f(()=>[h(y(a.itemData.IP),1)]),_:1},8,["to"]),l(d,{status:(i=a.itemData)==null?void 0:i.StatusID,DaysRemainded:(O=a.itemData)==null?void 0:O.DaysRemainded,"status-table":"DSOrders"},null,8,["status","DaysRemainded"]),c("div",fe,"Дата окончания "+y(o.calculateExpirationDate((t=a.itemData)==null?void 0:t.DaysRemainded))+" | "+y(((e=a.itemData)==null?void 0:e.DaysRemainded)||0)+" дн.",1)]),c("div",De,[c("div",ve,y((u=a.itemData)==null?void 0:u.Customer),1),l(D,{"user-notice":(M=a.itemData)==null?void 0:M.UserNotice,id:(P=a.itemData)==null?void 0:P.OrderID,position:a.isLast?"bottom":"top",emit:"updateDSOrdersList"},null,8,["user-notice","id","position"])]),a.params.length?(m(),S("div",pe,[c("ul",Ie,[(m(!0),S(E,null,H(a.params,g=>(m(),S("li",{innerHTML:g},null,8,ge))),256))])])):U("",!0),c("div",he,[((A=a.itemData)==null?void 0:A.StatusID)==="Active"?(m(),S("div",Se,"Включен")):U("",!0),c("div",{class:"btn btn--md btn--bold",onClick:n[0]||(n[0]=g=>{var p;return r.$router.push(`/DSOrderPay/${(p=a.itemData)==null?void 0:p.OrderID}`)})},[l(L),h("Оплатить")]),l(k,{class:"dropdown-dots",right:""},{"button-content":f(()=>[c("div",Ce,[l(B)])]),default:f(()=>{var g;return[l(v,null,{default:f(()=>{var p;return[l(s,{to:`DSOrders/${(p=a.itemData)==null?void 0:p.OrderID}`},{default:f(()=>[h("Дополнительная информация")]),_:1},8,["to"])]}),_:1}),((g=a.itemData)==null?void 0:g.StatusID)==="Active"?(m(),R(v,{key:0,onClick:n[1]||(n[1]=p=>o.serverReset())},{default:f(()=>[h("Перезагрузить")]),_:1})):U("",!0),l(v,{onClick:n[2]||(n[2]=p=>o.transferAccount())},{default:f(()=>[h("Передать на другой аккаунт")]),_:1}),l(v,{onClick:n[3]||(n[3]=p=>o.orderManage())},{default:f(()=>[h("Управлять заказом")]),_:1}),l(w),l(v,{class:"color-red",onClick:n[4]||(n[4]=p=>o.deleteItem())},{default:f(()=>[h("Удалить")]),_:1})]}),_:1})])])}const we={components:{StatusBadge:F,IconRotate:$,Helper:Z},props:{params:{type:Array,default:()=>[]},itemData:{type:Object,default:()=>{}},isLast:{type:Boolean,default:!1}},emits:["delete","transfer","manage"],setup(r,{emit:n}){function a(s){if(!s)return T(new Date);const d=new Date,D=new Date(d.getTime()+s*24*60*60*1e3);return T(D)}function o(){var s;ee().ServerReset((s=r.itemData)==null?void 0:s.ID)}function I(){var s;n("delete",(s=r.itemData)==null?void 0:s.ID)}function C(){var s,d;n("transfer",{ServiceOrderID:(s=r.itemData)==null?void 0:s.ID,ServiceID:(d=r.itemData)==null?void 0:d.ServiceID})}function _(){var s,d,D;n("manage",{ServiceOrderID:(s=r.itemData)==null?void 0:s.ID,ServiceID:(d=r.itemData)==null?void 0:d.ServiceID,OrderID:(D=r.itemData)==null?void 0:D.OrderID})}return{deleteItem:I,serverReset:o,orderManage:_,transferAccount:C,calculateExpirationDate:a}}},q=j(we,[["render",be],["__scopeId","data-v-69d15c4b"]]),ke=r=>(J("data-v-6476733e"),r=r(),K(),r),Oe={class:"section"},ye={class:"container"},Le={class:"section-header"},Be={class:"section-title__nowrap"},Me=ke(()=>c("h1",{class:"section-title"},"Выделенные серверы",-1)),Pe={class:"list-form"},xe={class:"list-form_col list-form_col--sm"},Ae={class:"list-form_select"},Re={class:"multiselect-option"},Ve={key:0};function Ue(r,n,a,o,I,C){var w,k,b;const _=G,s=V("router-link"),d=_e,D=V("Multiselect"),L=z,B=q,v=ie;return m(),S(E,null,[l(_),c("div",Oe,[c("div",ye,[c("div",Le,[c("div",Be,[Me,l(s,{class:"btn btn--blue btn-default",to:"DSSchemes"},{default:f(()=>[h("Заказать")]),_:1})])]),l(d,{partition:"Header:/DSOrders"}),c("div",Pe,[c("div",xe,[c("div",Ae,[l(D,{modelValue:o.select,"onUpdate:modelValue":n[0]||(n[0]=i=>o.select=i),options:o.getUsers,disabled:((w=o.getUsers)==null?void 0:w.length)<=1,label:"name"},{option:f(({option:i})=>[c("div",Re,"# "+y(i.value.padStart(5,"0"))+" / "+y(i.name),1)]),_:1},8,["modelValue","options","disabled"])])]),l(L,{modelValue:o.search,"onUpdate:modelValue":n[1]||(n[1]=i=>o.search=i)},null,8,["modelValue"])]),o.getDSList&&((k=o.getDSList)==null?void 0:k.length)>0?(m(),S("div",Ve,[o.filterProducts&&((b=o.filterProducts)==null?void 0:b.length)>0?(m(!0),S(E,{key:0},H(o.filterProducts,(i,O)=>(m(),R(B,{params:o.getDSItemParam(i),"is-last":O===o.filterProducts.length-1,"item-data":i,onDelete:o.deleteItem,onTransfer:o.transferItem,onManage:o.orderManage},null,8,["params","is-last","item-data","onDelete","onTransfer","onManage"]))),256)):(m(),R(v,{key:1,class:"no-margin",label:"Заказы не найдены"}))])):(m(),R(v,{key:1,class:"no-margin",label:"У Вас нет заказов на выделенные серверы"}))])])],64)}const Ee={components:{FormInputSearch:z,IconSearch:Y,Select2:le,Multiselect:ce,DSItem:q,BlockBalanceAgreement:G},async setup(){const r=N("*"),n=N(null),a=de(),o=W(),I=X(),C=Q("emitter");C.on("updateDSOrdersList",()=>{o.fetchDSOrders()});const _=x(()=>{var t;return(t=Object.keys(o==null?void 0:o.DSList).map(e=>o==null?void 0:o.DSList[e]))==null?void 0:t.reverse()}),s=x(()=>o==null?void 0:o.DSSchemes),d=x(()=>I==null?void 0:I.contractsList),D=x(()=>B(L(_.value)));function L(t){return r.value!=="*"?t.filter(e=>(e==null?void 0:e.ContractID)===r.value):t}function B(t){return n.value!==null?t.filter(e=>(e==null?void 0:e.Customer.toLowerCase().includes(n.value.toLowerCase()))||(e==null?void 0:e.OrderID.toLowerCase().includes(n.value.toLowerCase()))||(e==null?void 0:e.Scheme.toLowerCase().includes(n.value.toLowerCase()))||(e==null?void 0:e.IP.toLowerCase().includes(n.value.toLowerCase()))||(e==null?void 0:e.disks.toLowerCase().includes(n.value.toLowerCase()))||(e==null?void 0:e.CPU.toLowerCase().includes(n.value.toLowerCase()))||(e==null?void 0:e.ram.toLowerCase().includes(n.value.toLowerCase()))):t}function v(t){a.OrderManage(t)}const w=x(()=>{let t=[{value:"*",name:"Все договора"}];return _.value!==null&&d.value!==null?(Object.keys(_.value).forEach(e=>{var u,M,P;t.find(A=>{var g;return A.value===((g=_.value[e])==null?void 0:g.ContractID)})||t.push({value:(u=_.value[e])==null?void 0:u.ContractID,name:(P=d.value[(M=_.value[e])==null?void 0:M.ContractID])==null?void 0:P.Customer})}),t):{value:"*",name:"Все договора"}});function k(t){let e=b(t==null?void 0:t.SchemeID);e===void 0&&(e=t);let u=[];return t!=null&&t.Scheme&&u.push(t==null?void 0:t.Scheme),t!=null&&t.CPU&&u.push(t==null?void 0:t.CPU),e!=null&&e.ram&&u.push("RAM "+(e==null?void 0:e.ram)+" GB"),e!=null&&e.disks&&u.push(e==null?void 0:e.disks),e!=null&&e.CostMonth&&u.push("<b>"+(e==null?void 0:e.CostMonth)+"/ месяц</b>"),u}function b(t){var e;return(e=s.value)==null?void 0:e[t]}function i(t){C.emit("open-modal",{component:"DeleteItem",data:{id:t,message:"Вы действительно хотите удалить этот сервер?",tableID:"DSOrders",successEmit:"updateDSOrdersList"}})}function O(t){C.emit("open-modal",{component:"OrdersTransfer",data:t})}return await o.fetchDSOrders(),await o.fetchDSSchemes(),await I.fetchContracts(),{getDSList:_,filterProducts:D,select:r,search:n,getUsers:w,getDSSchemes:s,getDSItemParam:k,orderManage:v,deleteItem:i,transferItem:O}}},ct=j(Ee,[["render",Ue],["__scopeId","data-v-6476733e"]]);export{ct as default};

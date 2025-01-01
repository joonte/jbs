import{j as A,o as O,c as C,a as r,Z,a9 as G,b as d,w as m,d as u,t as v,F as J,x as K,n as E,k as Q,p as W,l as X,_ as Y,e as $,r as U,h as tt,S as et}from"./index-6f846e0d.js";import{u as at}from"./vps-b2e253b0.js";import{_ as nt}from"./HelperComponent-a37be436.js";import{S as F}from"./StatusBadge-654ab79e.js";import{I as q}from"./IconRotate-26c5733c.js";import{f as z}from"./useTimeFunction-8602dd60.js";import{v as ot}from"./vpsStatuses-32ffaaf5.js";import{f as st,z as it,k as rt,d as dt}from"./bootstrap-vue-next.es-6bf04585.js";import{_ as lt}from"./IconDots-740076c4.js";import{_ as ct}from"./IconCard-b1fefd85.js";import{_ as mt}from"./OrderIDBadge-9435263c.js";const ut=n=>(W("data-v-67634e6a"),n=n(),X(),n),_t={class:"hosting-item"},Dt={class:"hosting-item__row"},ft={class:"hosting-item__row"},It={class:"hosting-item__person"},St={class:"hosting-item__row"},gt={class:"hosting-item__list"},vt=["innerHTML"],bt={class:"hosting-status__info"},ht={class:"hosting-status__date"},wt={class:"hosting-item__controls"},Ot={class:"ico"},Ct=ut(()=>r("div",{class:"hosting-item__title"},"Перезагрузить виртуальный сервер",-1)),Vt={class:"btn btn--dots"};function yt(n,a,t,o,N,S){var s,c,h,D,f,I,w,B,H,L,M,T,j;const g=st,V=mt,b=A("router-link"),y=A("Helper"),k=F,R=q,P=ct,p=lt,_=it,x=rt,e=dt;return O(),C("div",_t,[r("div",Dt,[Z(d(g,{id:"checkbox-"+((s=t.itemData)==null?void 0:s.OrderID),modelValue:o.localSelectedRows[(c=t.itemData)==null?void 0:c.OrderID],"onUpdate:modelValue":a[0]||(a[0]=i=>{var l;return o.localSelectedRows[(l=t.itemData)==null?void 0:l.OrderID]=i}),name:"checkbox-"+((h=t.itemData)==null?void 0:h.OrderID),onChange:o.updateSelectedRows},null,8,["id","modelValue","name","onChange"]),[[G,t.isSeveral]]),d(V,{orderID:(D=t.itemData)==null?void 0:D.OrderID,onClick:a[1]||(a[1]=i=>{var l;return o.toItem((l=t.itemData)==null?void 0:l.OrderID)})},null,8,["orderID"]),d(b,{class:"hosting-item__name",to:`/VPSOrders/${t.itemData.OrderID}`},{default:m(()=>[u(v(t.itemData.IP||t.itemData.Domain),1)]),_:1},8,["to"])]),r("div",ft,[r("div",It,v(t.userName),1),d(y,{"user-notice":(f=t.itemData)==null?void 0:f.UserNotice,id:(I=t.itemData)==null?void 0:I.OrderID,position:t.isLast?"bottom":"top",emit:"updateVPSList"},null,8,["user-notice","id","position"])]),r("div",St,[r("ul",gt,[(O(!0),C(J,null,K(t.params,i=>(O(),C("li",{innerHTML:i==null?void 0:i.text},null,8,vt))),256)),r("div",{class:"btn btn--md btn--bold btn-manage",onClick:a[2]||(a[2]=i=>o.orderManage())},"Управлять заказом")]),r("div",bt,[r("div",{class:E(["hosting-item__date",{danger:Number((w=t.itemData)==null?void 0:w.DaysRemainded)<14}])},v(o.calculateExpirationDate((B=t.itemData)==null?void 0:B.DaysRemainded))+" • "+v(((H=t.itemData)==null?void 0:H.DaysRemainded)||0)+" дн.",3),d(k,{class:"status-badge",status:(L=t.itemData)==null?void 0:L.StatusID,"status-table":"VPSOrders",onClick:a[3]||(a[3]=i=>{var l;return o.openHistoryStatuses((l=t.itemData)==null?void 0:l.ID)})},null,8,["status"]),r("div",ht,v(o.calculateDaysSinceStatusUpdate((M=t.itemData)==null?void 0:M.StatusDate)),1)])]),r("div",wt,[((T=t.itemData)==null?void 0:T.StatusID)==="Active"?(O(),C("div",{key:0,class:E(["btn btn--md btn--border btn--rotate btn--rotate_green button-large btn-reload",{"btn--rotate_gray":((j=t.itemData)==null?void 0:j.StatusID)!=="Active"||o.isReloading,"btn--rotate_loading":o.isReloading}]),onClick:a[4]||(a[4]=(...i)=>o.reloadItem&&o.reloadItem(...i))},[u("Включен"),r("button",null,[r("div",Ot,[d(R)])]),Ct],2)):Q("",!0),r("div",{class:"btn btn--md btn--bold",onClick:a[5]||(a[5]=i=>{var l;return n.$router.push(`/VPSOrders/${(l=t.itemData)==null?void 0:l.OrderID}/SchemeChange`)})},"Изменить тариф"),r("div",{class:"btn btn--md btn--bold",onClick:a[6]||(a[6]=i=>{var l;return n.$router.push(`/VPSOrderPay/${(l=t.itemData)==null?void 0:l.OrderID}`)})},[d(P),u("Оплатить")]),d(e,{class:"dropdown-dots",right:""},{"button-content":m(()=>[r("div",Vt,[d(p)])]),default:m(()=>[d(_,{onClick:a[7]||(a[7]=i=>o.transferAccount())},{default:m(()=>[u("Передать на другой аккаунт")]),_:1}),d(_,{onClick:a[8]||(a[8]=i=>o.openPasswordChange())},{default:m(()=>[u("Сменить пароль")]),_:1}),d(_,null,{default:m(()=>{var i;return[d(b,{to:`/VPSOrders/${(i=t.itemData)==null?void 0:i.OrderID}/SchemeChange`},{default:m(()=>[u("Изменить тариф")]),_:1},8,["to"])]}),_:1}),d(x),d(_,{class:"color-red",onClick:a[9]||(a[9]=i=>o.deleteItem())},{default:m(()=>[u("Удалить")]),_:1})]),_:1})])])}const kt={components:{StatusBadge:F,IconRotate:q,Helper:nt},props:{params:{type:Array,default:()=>[]},itemData:{type:Object,default:()=>{}},isLast:{type:Boolean,default:!1},userName:{type:String,default:""},isSeveral:{type:Boolean,default:!1},selectedRows:{type:Object,required:!0}},emits:["manage","transfer","delete"],setup(n,{emit:a}){at();const t=$(),o=U({...n.selectedRows}),N=()=>{a("update-selected-rows",o.value)};tt(()=>n.selectedRows,e=>{o.value={...e}},{deep:!0});const S=et("emitter"),g=U(!1);function V(e){if(!e)return z(new Date);const s=new Date,c=new Date(s.getTime()+e*24*60*60*1e3);return z(c)}function b(){var e;a("delete",(e=n.itemData)==null?void 0:e.ID)}function y(e){t.push(`/VPSOrders/${e}`)}function k(){var e,s;S.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(e=n.itemData)==null?void 0:e.ID,ServiceID:(s=n.itemData)==null?void 0:s.ServiceID}})}function R(e){S.emit("open-modal",{component:"StatusHistory",data:{modeID:"VPSOrders",rowID:e}})}function P(){var e,s,c;S.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(e=n.itemData)==null?void 0:e.ID,ServiceID:(s=n.itemData)==null?void 0:s.ServiceID,emitEvent:"updateVPSIDPage",Login:(c=n.itemData)==null?void 0:c.Login}})}function p(){var s;function e(c){g.value=c}S.emit("open-modal",{component:"ReloadVPS",data:{VPSOrderID:(s=n.itemData)==null?void 0:s.ID,isReloading:g.value,onReloadStatusChange:e}})}function _(){var e,s,c;a("manage",{ServiceOrderID:(e=n.itemData)==null?void 0:e.ID,ServiceID:(s=n.itemData)==null?void 0:s.ServiceID,OrderID:(c=n.itemData)==null?void 0:c.OrderID})}function x(e){if(!e)return"";const s=new Date(parseInt(e)*1e3),h=new Date-s,D=Math.floor(h/1e3),f=Math.floor(D/60),I=Math.floor(f/60),w=Math.floor(I/24);return D<60?`${D} сек. от статуса`:f<60?`${f} мин. от статуса`:I<24?`${I} ч. от статуса`:`${w} дн. от статуса`}return{vpsStatuses:ot,reloadItem:p,isReloading:g,orderManage:_,calculateExpirationDate:V,transferAccount:k,openHistoryStatuses:R,toItem:y,deleteItem:b,calculateDaysSinceStatusUpdate:x,openPasswordChange:P,localSelectedRows:o,updateSelectedRows:N}}},At=Y(kt,[["render",yt],["__scopeId","data-v-67634e6a"]]);export{At as V};
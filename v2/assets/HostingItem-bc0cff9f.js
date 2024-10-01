import{j as F,o as b,c as w,a as i,Z as W,a9 as q,b as d,t as g,d as f,F as Z,x as X,n as P,k as Y,w as m,_ as p,e as $,r as tt,h as et,S as nt,g as at}from"./index-90eb49f0.js";import{_ as J}from"./IconDots-9788fdcb.js";import{_ as K}from"./IconCard-95aab40c.js";import{S as Q}from"./StatusBadge-0d2c62ad.js";import{h as ot}from"./hostStatuses-94d00f20.js";import{_ as st}from"./HelperComponent-f1dd2245.js";import"./hosting-606725d4.js";import{f as G}from"./useTimeFunction-8602dd60.js";import{f as it,z as rt,k as dt,d as lt}from"./bootstrap-vue-next.es-788a757a.js";import{_ as ct}from"./OrderIDBadge-b66a7555.js";const mt={class:"hosting-item__row"},ut=["href"],_t={class:"hosting-domains__wrapper"},Dt={class:"hosting-domains"},ft={class:"hosting-domains__items"},gt=["innerHTML"],ht={class:"hosting-item__row"},It={class:"hosting-item__person"},vt={class:"hosting-item__row hosting-flex"},St={class:"hosting-item__list"},bt={class:"hosting-status__info"},wt={class:"hosting-status__date"},Ot={class:"hosting-item__controls"},kt={class:"btn btn--dots"};function yt(r,n,e,s,B,O){var t,a,l,h,D,u,I,S,M,V,j,U,T,E,z,A;const k=it,y=ct,H=F("Helper"),C=Q,x=K,N=J,v=F("router-link"),_=rt,R=dt,L=lt;return b(),w("div",{class:P(["hosting-item",{"hosting-item_deleted":((t=e.itemData)==null?void 0:t.StatusID)==="Deleted"}])},[i("div",mt,[W(d(k,{id:"checkbox-"+((a=e.itemData)==null?void 0:a.OrderID),modelValue:s.localSelectedRows[(l=e.itemData)==null?void 0:l.OrderID],"onUpdate:modelValue":n[0]||(n[0]=o=>{var c;return s.localSelectedRows[(c=e.itemData)==null?void 0:c.OrderID]=o}),name:"checkbox-"+((h=e.itemData)==null?void 0:h.OrderID),onChange:s.updateSelectedRows},null,8,["id","modelValue","name","onChange"]),[[q,e.isSeveral]]),d(y,{orderID:(D=e.itemData)==null?void 0:D.OrderID,onClick:n[1]||(n[1]=o=>{var c;return s.toItem((c=e.itemData)==null?void 0:c.OrderID)})},null,8,["orderID"]),i("a",{class:"hosting-item__name",href:s.fullDomainUrl,target:"_blank"},g((u=e.itemData)==null?void 0:u.Domain),9,ut),i("div",_t,[W(i("div",Dt,[f("+"+g(s.showDNSs((I=e.itemData)==null?void 0:I.Parked).count),1),i("div",ft,[i("div",{class:"hosting-domains__item",innerHTML:s.showDNSs((S=e.itemData)==null?void 0:S.Parked).html},null,8,gt)])],512),[[q,((M=e.itemData)==null?void 0:M.Parked)&&e.itemData.Parked.split(",").length>1]])])]),i("div",ht,[i("div",It,g(e.userName),1),d(H,{"user-notice":(V=e.itemData)==null?void 0:V.UserNotice,id:(j=e.itemData)==null?void 0:j.OrderID,position:e.isLast?"bottom":"top",emit:"updateHostingList"},null,8,["user-notice","id","position"])]),i("div",vt,[i("ul",St,[(b(!0),w(Z,null,X(e.params,o=>(b(),w(Z,null,[o.text?(b(),w("li",{key:0,class:P({bold:o==null?void 0:o.bold})},g(o.text),3)):Y("",!0)],64))),256)),i("div",{class:"btn btn--md btn--bold btn-manage",onClick:n[2]||(n[2]=o=>s.orderManage())},"Управлять заказом")]),i("div",bt,[i("div",{class:P(["hosting-item__date",{danger:Number((U=e.itemData)==null?void 0:U.DaysRemainded)<14}])},g(s.calculateExpirationDate((T=e.itemData)==null?void 0:T.DaysRemainded))+" • "+g(((E=e.itemData)==null?void 0:E.DaysRemainded)||0)+" дн.",3),d(C,{class:"status-badge",status:(z=e.itemData)==null?void 0:z.StatusID,"status-table":"HostingOrders",onClick:n[3]||(n[3]=o=>{var c;return s.openHistoryStatuses((c=e.itemData)==null?void 0:c.ID)})},null,8,["status"]),i("div",wt,g(s.calculateDaysSinceStatusUpdate((A=e.itemData)==null?void 0:A.StatusDate)),1)])]),i("div",Ot,[i("div",{class:"btn btn--md btn--bold",onClick:n[4]||(n[4]=o=>{var c;return r.$router.push(`/HostingOrders/${(c=e.itemData)==null?void 0:c.OrderID}/SchemeChange`)})},"Изменить тариф"),i("div",{class:"btn btn--md btn--bold",onClick:n[5]||(n[5]=o=>{var c;return r.$router.push(`/HostingOrderPay/${(c=e.itemData)==null?void 0:c.OrderID}`)})},[d(x),f("Оплатить")]),d(L,{class:"dropdown-dots",right:""},{"button-content":m(()=>[i("div",kt,[d(N)])]),default:m(()=>[d(_,null,{default:m(()=>[d(v,{to:e.infoLink},{default:m(()=>[f("Дополнительная информация")]),_:1},8,["to"])]),_:1}),d(_,null,{default:m(()=>{var o;return[d(v,{to:`/HostingOrderPay/${(o=e.itemData)==null?void 0:o.OrderID}`},{default:m(()=>[f("Продлить")]),_:1},8,["to"])]}),_:1}),d(_,null,{default:m(()=>{var o;return[d(v,{to:`/HostingOrders/${(o=e.itemData)==null?void 0:o.OrderID}/SchemeChange`},{default:m(()=>[f("Изменить тариф")]),_:1},8,["to"])]}),_:1}),d(_,{onClick:n[6]||(n[6]=o=>s.transferAccount())},{default:m(()=>[f("Передать на другой аккаунт")]),_:1}),d(_,{onClick:n[7]||(n[7]=o=>s.openPasswordChange())},{default:m(()=>[f("Сменить пароль")]),_:1}),d(R),d(_,{class:"color-red",onClick:n[8]||(n[8]=o=>s.deleteHostingItem())},{default:m(()=>[f("Удалить")]),_:1})]),_:1})])],2)}const Ht={components:{IconDots:J,IconCard:K,Helper:st,StatusBadge:Q},props:{params:{type:Array,default:()=>[]},itemData:{type:Object,default:()=>{}},userName:{type:String,default:""},infoLink:{type:String,default:"/"},isLast:{type:Boolean,default:!1},isSeveral:{type:Boolean,default:!1},selectedRows:{type:Object,required:!0}},emits:["delete","transfer","manage"],setup(r,{emit:n}){const e=$(),s=tt({...r.selectedRows}),B=()=>{n("update-selected-rows",s.value)};et(()=>r.selectedRows,t=>{s.value={...t}},{deep:!0});const O=nt("emitter"),k=at(()=>{var a,l;if(!((a=r.itemData)!=null&&a.Domain))return"";const t=(l=r.itemData)==null?void 0:l.Domain;return t.startsWith("http://")||t.startsWith("https://")?t:`https://${t}`});function y(t){e.push(`/HostingOrders/${t}`)}function H(t){if(!t)return G(new Date);const a=new Date,l=new Date(a.getTime()+t*24*60*60*1e3);return G(l)}function C(t){O.emit("open-modal",{component:"StatusHistory",data:{modeID:"HostingOrders",rowID:t}})}function x(){var t,a,l;O.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(t=r.itemData)==null?void 0:t.ID,ServiceID:(a=r.itemData)==null?void 0:a.ServiceID,emitEvent:"updateHostingIDPage",Login:(l=r.itemData)==null?void 0:l.Login}})}function N(){var t;n("delete",(t=r.itemData)==null?void 0:t.ID)}function v(t){if(t===null)return{html:"",count:0};let a=t.includes(",")?t.split(","):[t];a=a.filter(D=>{var u;return D!==((u=r.itemData)==null?void 0:u.Domain)});const l=a.join("<br>"),h=a.length;return{html:l,count:h}}function _(){var t,a;n("transfer",{ServiceOrderID:(t=r.itemData)==null?void 0:t.ID,ServiceID:(a=r.itemData)==null?void 0:a.ServiceID})}function R(){var t,a,l;n("manage",{ServiceOrderID:(t=r.itemData)==null?void 0:t.ID,ServiceID:(a=r.itemData)==null?void 0:a.ServiceID,OrderID:(l=r.itemData)==null?void 0:l.OrderID})}function L(t){if(!t)return"";const a=new Date(parseInt(t)*1e3),h=new Date-a,D=Math.floor(h/1e3),u=Math.floor(D/60),I=Math.floor(u/60),S=Math.floor(I/24);return D<60?`${D} сек. от статуса`:u<60?`${u} мин. от статуса`:I<24?`${I} ч. от статуса`:`${S} дн. от статуса`}return{hostStatuses:ot,deleteHostingItem:N,transferAccount:_,orderManage:R,calculateExpirationDate:H,fullDomainUrl:k,showDNSs:v,toItem:y,openHistoryStatuses:C,calculateDaysSinceStatusUpdate:L,openPasswordChange:x,localSelectedRows:s,updateSelectedRows:B}}},Ut=p(Ht,[["render",yt],["__scopeId","data-v-9373eabe"]]);export{Ut as H};

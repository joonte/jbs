import{j as E,o as b,c as w,a as s,b as r,t as g,Z as W,ae as Z,d as f,F as V,x as q,n as N,k as G,w as m,_ as J,e as K,S as Q,g as X}from"./index-b5b5ce3d.js";import{_ as A}from"./IconDots-5eb3cb0a.js";import{_ as R}from"./IconCard-cef3f371.js";import{S as F}from"./StatusBadge-0649a996.js";import{h as Y}from"./hostStatuses-94d00f20.js";import{_ as $}from"./HelperComponent-fa301290.js";import"./hosting-552a9dd1.js";import{f as z}from"./useTimeFunction-8602dd60.js";import{z as tt,k as et,d as nt}from"./bootstrap-vue-next.es-37aa63ea.js";import{_ as at}from"./OrderIDBadge-a6480cec.js";const ot={class:"hosting-item__row"},st=["href"],it={class:"hosting-domains__wrapper"},rt={class:"hosting-domains"},dt={class:"hosting-domains__items"},lt=["innerHTML"],mt={class:"hosting-item__row"},ct={class:"hosting-item__person"},_t={class:"hosting-item__row hosting-flex"},ut={class:"hosting-item__list"},Dt={class:"hosting-status__info"},ft={class:"hosting-status__date"},gt={class:"hosting-item__controls"},ht={class:"btn btn--dots"};function It(d,a,e,l,L,P){var n,i,h,u,c,I,S,M,B,p,j,T,U;const k=at,H=E("Helper"),O=F,y=R,C=A,v=E("router-link"),_=tt,x=et,t=nt;return b(),w("div",{class:N(["hosting-item",{"hosting-item_deleted":((n=e.itemData)==null?void 0:n.StatusID)==="Deleted"}])},[s("div",ot,[r(k,{orderID:(i=e.itemData)==null?void 0:i.OrderID,onClick:a[0]||(a[0]=o=>{var D;return l.toItem((D=e.itemData)==null?void 0:D.OrderID)})},null,8,["orderID"]),s("a",{class:"hosting-item__name",href:l.fullDomainUrl,target:"_blank"},g((h=e.itemData)==null?void 0:h.Domain),9,st),s("div",it,[W(s("div",rt,[f("+"+g(l.showDNSs((u=e.itemData)==null?void 0:u.Parked).count),1),s("div",dt,[s("div",{class:"hosting-domains__item",innerHTML:l.showDNSs((c=e.itemData)==null?void 0:c.Parked).html},null,8,lt)])],512),[[Z,((I=e.itemData)==null?void 0:I.Parked)&&e.itemData.Parked.split(",").length>1]])])]),s("div",mt,[s("div",ct,g(e.userName),1),r(H,{"user-notice":(S=e.itemData)==null?void 0:S.UserNotice,id:(M=e.itemData)==null?void 0:M.OrderID,position:e.isLast?"bottom":"top",emit:"updateHostingList"},null,8,["user-notice","id","position"])]),s("div",_t,[s("ul",ut,[(b(!0),w(V,null,q(e.params,o=>(b(),w(V,null,[o.text?(b(),w("li",{key:0,class:N({bold:o==null?void 0:o.bold})},g(o.text),3)):G("",!0)],64))),256)),s("div",{class:"btn btn--md btn--bold btn-manage",onClick:a[1]||(a[1]=o=>l.orderManage())},"Управлять заказом")]),s("div",Dt,[s("div",{class:N(["hosting-item__date",{danger:Number((B=e.itemData)==null?void 0:B.DaysRemainded)<14}])},g(l.calculateExpirationDate((p=e.itemData)==null?void 0:p.DaysRemainded))+" • "+g(((j=e.itemData)==null?void 0:j.DaysRemainded)||0)+" дн.",3),r(O,{class:"status-badge",status:(T=e.itemData)==null?void 0:T.StatusID,"status-table":"HostingOrders",onClick:a[2]||(a[2]=o=>{var D;return l.openHistoryStatuses((D=e.itemData)==null?void 0:D.ID)})},null,8,["status"]),s("div",ft,g(l.calculateDaysSinceStatusUpdate((U=e.itemData)==null?void 0:U.StatusDate)),1)])]),s("div",gt,[s("div",{class:"btn btn--md btn--bold",onClick:a[3]||(a[3]=o=>{var D;return d.$router.push(`/HostingOrderPay/${(D=e.itemData)==null?void 0:D.OrderID}`)})},[r(y),f("Оплатить")]),r(t,{class:"dropdown-dots",right:""},{"button-content":m(()=>[s("div",ht,[r(C)])]),default:m(()=>[r(_,null,{default:m(()=>[r(v,{to:e.infoLink},{default:m(()=>[f("Дополнительная информация")]),_:1},8,["to"])]),_:1}),r(_,null,{default:m(()=>{var o;return[r(v,{to:`/HostingOrderPay/${(o=e.itemData)==null?void 0:o.OrderID}`},{default:m(()=>[f("Продлить")]),_:1},8,["to"])]}),_:1}),r(_,null,{default:m(()=>{var o;return[r(v,{to:`/HostingOrders/${(o=e.itemData)==null?void 0:o.OrderID}/SchemeChange`},{default:m(()=>[f("Изменить тариф")]),_:1},8,["to"])]}),_:1}),r(_,{onClick:a[4]||(a[4]=o=>l.transferAccount())},{default:m(()=>[f("Передать на другой аккаунт")]),_:1}),r(_,{onClick:a[5]||(a[5]=o=>l.openPasswordChange())},{default:m(()=>[f("Сменить пароль")]),_:1}),r(x),r(_,{class:"color-red",onClick:a[6]||(a[6]=o=>l.deleteHostingItem())},{default:m(()=>[f("Удалить")]),_:1})]),_:1})])],2)}const vt={components:{IconDots:A,IconCard:R,Helper:$,StatusBadge:F},props:{params:{type:Array,default:()=>[]},itemData:{type:Object,default:()=>{}},userName:{type:String,default:""},infoLink:{type:String,default:"/"},isLast:{type:Boolean,default:!1}},emits:["delete","transfer","manage"],setup(d,{emit:a}){const e=K(),l=Q("emitter"),L=X(()=>{var n,i;if(!((n=d.itemData)!=null&&n.Domain))return"";const t=(i=d.itemData)==null?void 0:i.Domain;return t.startsWith("http://")||t.startsWith("https://")?t:`https://${t}`});function P(t){e.push(`/HostingOrders/${t}`)}function k(t){if(!t)return z(new Date);const n=new Date,i=new Date(n.getTime()+t*24*60*60*1e3);return z(i)}function H(t){l.emit("open-modal",{component:"StatusHistory",data:{modeID:"HostingOrders",rowID:t}})}function O(){var t,n,i;l.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(t=d.itemData)==null?void 0:t.ID,ServiceID:(n=d.itemData)==null?void 0:n.ServiceID,emitEvent:"updateHostingIDPage",Login:(i=d.itemData)==null?void 0:i.Login}})}function y(){var t;a("delete",(t=d.itemData)==null?void 0:t.ID)}function C(t){if(t===null)return{html:"",count:0};let n=t.includes(",")?t.split(","):[t];n=n.filter(u=>{var c;return u!==((c=d.itemData)==null?void 0:c.Domain)});const i=n.join("<br>"),h=n.length;return{html:i,count:h}}function v(){var t,n;a("transfer",{ServiceOrderID:(t=d.itemData)==null?void 0:t.ID,ServiceID:(n=d.itemData)==null?void 0:n.ServiceID})}function _(){var t,n,i;a("manage",{ServiceOrderID:(t=d.itemData)==null?void 0:t.ID,ServiceID:(n=d.itemData)==null?void 0:n.ServiceID,OrderID:(i=d.itemData)==null?void 0:i.OrderID})}function x(t){if(!t)return"";const n=new Date(parseInt(t)*1e3),h=new Date-n,u=Math.floor(h/1e3),c=Math.floor(u/60),I=Math.floor(c/60),S=Math.floor(I/24);return u<60?`${u} сек. от статуса`:c<60?`${c} мин. от статуса`:I<24?`${I} ч. от статуса`:`${S} дн. от статуса`}return{hostStatuses:Y,deleteHostingItem:y,transferAccount:v,orderManage:_,calculateExpirationDate:k,fullDomainUrl:L,showDNSs:C,toItem:P,openHistoryStatuses:H,calculateDaysSinceStatusUpdate:x,openPasswordChange:O}}},Lt=J(vt,[["render",It],["__scopeId","data-v-e28deebd"]]);export{Lt as H};

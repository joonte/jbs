import{j as M,o as D,c as f,a as i,b as a,d as c,t as g,k as P,F as T,x as F,n as V,w as l,_ as R,e as W,S as q,g as G}from"./index-83b4b7ba.js";import{_ as A}from"./IconDots-a66f045d.js";import{_ as E}from"./IconCard-2cc17a6b.js";import{S as U}from"./StatusBadge-0d170ac0.js";import{h as J}from"./hostStatuses-94d00f20.js";import{_ as K}from"./HelperComponent-df0c894c.js";import"./hosting-b7d822b2.js";import{f as z}from"./useTimeFunction-8602dd60.js";import{z as Q,k as X,d as Y}from"./bootstrap-vue-next.es-8f3ae81a.js";import{_ as Z}from"./OrderIDBadge-d89298b0.js";const $={class:"hosting-item__row"},tt=["href"],et={key:0,class:"hosting-item-domains"},nt={class:"hosting-item-domains__window"},ot=["innerHTML"],at={class:"hosting-item__row"},st={class:"hosting-item__person"},it={class:"hosting-item__date"},rt={class:"hosting-item__row"},dt={class:"hosting-item__list"},mt={class:"hosting-item__controls"},lt={class:"btn btn--dots"};function _t(r,o,e,d,k,H){var O,w,y,p,C,x,N,L,B,j;const I=Z,h=U,v=M("Helper"),b=E,S=A,u=M("router-link"),t=Q,s=X,m=Y;return D(),f("div",{class:V(["hosting-item",{"hosting-item_deleted":((O=e.itemData)==null?void 0:O.StatusID)==="Deleted"}])},[i("div",$,[a(I,{orderID:(w=e.itemData)==null?void 0:w.OrderID,onClick:o[0]||(o[0]=n=>{var _;return d.toItem((_=e.itemData)==null?void 0:_.OrderID)})},null,8,["orderID"]),i("a",{class:"hosting-item__name",href:d.fullDomainUrl,target:"_blank"},[c(g((y=e.itemData)==null?void 0:y.Domain),1),(p=e.itemData)!=null&&p.Parked?(D(),f("div",et,[i("div",nt,[i("div",{class:"hosting-item-domains__text",innerHTML:d.showDNSs((C=e.itemData)==null?void 0:C.Parked)},null,8,ot)])])):P("",!0)],8,tt),a(h,{status:(x=e.itemData)==null?void 0:x.StatusID,"status-table":"HostingOrders",onClick:o[1]||(o[1]=n=>{var _;return d.openHistoryStatuses((_=e.itemData)==null?void 0:_.ID)})},null,8,["status"])]),i("div",at,[i("div",st,g(e.userName),1),i("div",it,"Дата окончания "+g(d.calculateExpirationDate((N=e.itemData)==null?void 0:N.DaysRemainded))+" | "+g(((L=e.itemData)==null?void 0:L.DaysRemainded)||0)+" дн.",1),a(v,{"user-notice":(B=e.itemData)==null?void 0:B.UserNotice,id:(j=e.itemData)==null?void 0:j.OrderID,position:e.isLast?"bottom":"top",emit:"updateHostingList"},null,8,["user-notice","id","position"])]),i("div",rt,[i("ul",dt,[(D(!0),f(T,null,F(e.params,n=>(D(),f(T,null,[n.text?(D(),f("li",{key:0,class:V({bold:n==null?void 0:n.bold})},g(n.text),3)):P("",!0)],64))),256)),i("div",{class:"btn btn--md btn--bold btn-manage",onClick:o[2]||(o[2]=n=>d.orderManage())},"Управлять заказом")])]),i("div",mt,[i("div",{class:"btn btn--md btn--bold",onClick:o[3]||(o[3]=n=>{var _;return r.$router.push(`/HostingOrderPay/${(_=e.itemData)==null?void 0:_.OrderID}`)})},[a(b),c("Оплатить")]),a(m,{class:"dropdown-dots",right:""},{"button-content":l(()=>[i("div",lt,[a(S)])]),default:l(()=>[a(t,null,{default:l(()=>[a(u,{to:e.infoLink},{default:l(()=>[c("Дополнительная информация")]),_:1},8,["to"])]),_:1}),a(t,null,{default:l(()=>{var n;return[a(u,{to:`/HostingOrderPay/${(n=e.itemData)==null?void 0:n.OrderID}`},{default:l(()=>[c("Продлить")]),_:1},8,["to"])]}),_:1}),a(t,null,{default:l(()=>{var n;return[a(u,{to:`/HostingOrders/${(n=e.itemData)==null?void 0:n.OrderID}/SchemeChange`},{default:l(()=>[c("Изменить тариф")]),_:1},8,["to"])]}),_:1}),a(t,{onClick:o[4]||(o[4]=n=>d.transferAccount())},{default:l(()=>[c("Передать на другой аккаунт")]),_:1}),a(s),a(t,{class:"color-red",onClick:o[5]||(o[5]=n=>d.deleteHostingItem())},{default:l(()=>[c("Удалить")]),_:1})]),_:1})])],2)}const ct={components:{IconDots:A,IconCard:E,Helper:K,StatusBadge:U},props:{params:{type:Array,default:()=>[]},itemData:{type:Object,default:()=>{}},userName:{type:String,default:""},infoLink:{type:String,default:"/"},isLast:{type:Boolean,default:!1}},emits:["delete","transfer","manage"],setup(r,{emit:o}){const e=W(),d=q("emitter"),k=G(()=>{var s,m;if(!((s=r.itemData)!=null&&s.Domain))return"";const t=(m=r.itemData)==null?void 0:m.Domain;return t.startsWith("http://")||t.startsWith("https://")?t:`https://${t}`});function H(t){e.push(`/HostingOrders/${t}`)}function I(t){if(!t)return z(new Date);const s=new Date,m=new Date(s.getTime()+t*24*60*60*1e3);return z(m)}function h(t){d.emit("open-modal",{component:"StatusHistory",data:{modeID:"HostingOrders",rowID:t}})}function v(){var t;o("delete",(t=r.itemData)==null?void 0:t.ID)}function b(t){return t===null?"":(t.includes(",")?t.split(","):[t]).join("<br>")}function S(){var t,s;o("transfer",{ServiceOrderID:(t=r.itemData)==null?void 0:t.ID,ServiceID:(s=r.itemData)==null?void 0:s.ServiceID})}function u(){var t,s,m;o("manage",{ServiceOrderID:(t=r.itemData)==null?void 0:t.ID,ServiceID:(s=r.itemData)==null?void 0:s.ServiceID,OrderID:(m=r.itemData)==null?void 0:m.OrderID})}return{hostStatuses:J,deleteHostingItem:v,transferAccount:S,orderManage:u,calculateExpirationDate:I,fullDomainUrl:k,showDNSs:b,toItem:H,openHistoryStatuses:h}}},Ht=R(ct,[["render",_t],["__scopeId","data-v-30f8bdc9"]]);export{Ht as H};

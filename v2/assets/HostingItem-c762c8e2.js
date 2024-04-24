import{j as H,o as D,c as f,a as r,b as n,w as i,d as _,t as u,F as w,v as F,n as C,k as M,_ as P}from"./index-58c913f0.js";import{_ as N}from"./IconDots-1997c758.js";import{_ as B}from"./IconCard-94a8b1b0.js";import{S as L}from"./StatusBadge-cd8ad979.js";import{h as T}from"./hostStatuses-94d00f20.js";import{_ as R}from"./HelperComponent-8d748301.js";import"./hosting-b8e460c3.js";import{f as x}from"./useTimeFunction-8602dd60.js";import{z as U,k as q,d as G}from"./bootstrap-vue-next.es-9015192b.js";import{_ as J}from"./OrderIDBadge-064a04dd.js";const K={class:"hosting-item__row"},Q={class:"hosting-item__date"},W={class:"hosting-item__row"},X={class:"hosting-item__person"},Y={class:"hosting-item__row"},Z={class:"hosting-item__list"},$={class:"hosting-item__controls"},tt={class:"btn btn--dots"};function et(d,a,t,l,g,I){var v,b,h,p,S,k,O;const o=J,s=H("router-link"),c=L,V=H("Helper"),z=B,A=N,m=U,E=q,j=G;return D(),f("div",{class:C(["hosting-item",{"hosting-item_deleted":((v=t.itemData)==null?void 0:v.StatusID)==="Deleted"}])},[r("div",K,[n(o,{orderID:(b=t.itemData)==null?void 0:b.OrderID},null,8,["orderID"]),n(s,{class:"hosting-item__name",to:t.infoLink},{default:i(()=>{var e;return[_(u((e=t.itemData)==null?void 0:e.Domain),1)]}),_:1},8,["to"]),n(c,{status:(h=t.itemData)==null?void 0:h.StatusID,"status-table":"HostingOrders"},null,8,["status"]),r("div",Q,"Дата окончания "+u(l.calculateExpirationDate((p=t.itemData)==null?void 0:p.DaysRemainded))+" | "+u(((S=t.itemData)==null?void 0:S.DaysRemainded)||0)+" дн.",1)]),r("div",W,[r("div",X,u(t.userName),1),n(V,{"user-notice":(k=t.itemData)==null?void 0:k.UserNotice,id:(O=t.itemData)==null?void 0:O.OrderID,position:t.isLast?"bottom":"top",emit:"updateHostingList"},null,8,["user-notice","id","position"])]),r("div",Y,[r("ul",Z,[(D(!0),f(w,null,F(t.params,e=>(D(),f(w,null,[e.text?(D(),f("li",{key:0,class:C({bold:e==null?void 0:e.bold})},u(e.text),3)):M("",!0)],64))),256))])]),r("div",$,[r("div",{class:"btn btn--md btn--bold",onClick:a[0]||(a[0]=e=>{var y;return d.$router.push(`/HostingOrderPay/${(y=t.itemData)==null?void 0:y.OrderID}`)})},[n(z),_("Оплатить")]),n(j,{class:"dropdown-dots",right:""},{"button-content":i(()=>[r("div",tt,[n(A)])]),default:i(()=>[n(m,null,{default:i(()=>[n(s,{to:t.infoLink},{default:i(()=>[_("Дополнительная информация")]),_:1},8,["to"])]),_:1}),n(m,null,{default:i(()=>{var e;return[n(s,{to:`/HostingOrderPay/${(e=t.itemData)==null?void 0:e.OrderID}`},{default:i(()=>[_("Продлить")]),_:1},8,["to"])]}),_:1}),n(m,null,{default:i(()=>{var e;return[n(s,{to:`/HostingOrders/${(e=t.itemData)==null?void 0:e.OrderID}/SchemeChange`},{default:i(()=>[_("Изменить тариф")]),_:1},8,["to"])]}),_:1}),n(m,{onClick:a[1]||(a[1]=e=>l.transferAccount())},{default:i(()=>[_("Передать на другой аккаунт")]),_:1}),n(m,{onClick:a[2]||(a[2]=e=>l.orderManage())},{default:i(()=>[_("Управлять заказом")]),_:1}),n(E),n(m,{class:"color-red",onClick:a[3]||(a[3]=e=>l.deleteHostingItem())},{default:i(()=>[_("Удалить")]),_:1})]),_:1})])],2)}const nt={components:{IconDots:N,IconCard:B,Helper:R,StatusBadge:L},props:{params:{type:Array,default:()=>[]},itemData:{type:Object,default:()=>{}},userName:{type:String,default:""},infoLink:{type:String,default:"/"},isLast:{type:Boolean,default:!1}},emits:["delete","transfer","manage"],setup(d,{emit:a}){function t(o){if(!o)return x(new Date);const s=new Date,c=new Date(s.getTime()+o*24*60*60*1e3);return x(c)}function l(){var o;a("delete",(o=d.itemData)==null?void 0:o.ID)}function g(){var o,s;a("transfer",{ServiceOrderID:(o=d.itemData)==null?void 0:o.ID,ServiceID:(s=d.itemData)==null?void 0:s.ServiceID})}function I(){var o,s,c;a("manage",{ServiceOrderID:(o=d.itemData)==null?void 0:o.ID,ServiceID:(s=d.itemData)==null?void 0:s.ServiceID,OrderID:(c=d.itemData)==null?void 0:c.OrderID})}return{hostStatuses:T,deleteHostingItem:l,transferAccount:g,orderManage:I,calculateExpirationDate:t}}},ut=P(nt,[["render",et],["__scopeId","data-v-5d009bfe"]]);export{ut as H};
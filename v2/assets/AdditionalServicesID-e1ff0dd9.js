import{o as d,c as _,b,a as o,t as m,y as q,k as l,Z as oe,$ as ie,d as ae,F as ne,p as de,l as ce,_ as le,a7 as _e,e as me,g as v,r as te,S as ve,f as ue,h as Se}from"./index-83b4b7ba.js";import{u as Ie}from"./postings-05f51b78.js";import{u as De}from"./services-c044aff2.js";import{u as ge}from"./resetServer-94c466e3.js";import{u as he}from"./globalActions-633eb799.js";import{S as se}from"./StatusBadge-0d170ac0.js";import{s as Oe}from"./servicesStatuses-6293b6ac.js";import{B as re}from"./BlockBalanceAgreement-c5fad736.js";import{_ as fe,a as pe}from"./ServicesContractBalanceBlock-627f7a8d.js";import{_ as Pe}from"./ClausesKeeper-55c14b7f.js";import{_ as be}from"./ButtonDefault-dc248ec6.js";import"./contracts-623ec7a8.js";import"./bootstrap-vue-next.es-8f3ae81a.js";import"./IconArrow-80c72216.js";import"./IconProfile-34c25888.js";import"./IconCard-2cc17a6b.js";import"./IconClose-86aafc4e.js";const O=c=>(de("data-v-bbd6bf29"),c=c(),ce(),c),we={class:"section"},ke={class:"container"},ye={class:"section-header"},Ne={class:"section-title"},Ce={class:"section-label"},Ae={class:"list"},Le={class:"list-col list-col--md"},Me={class:"list-item"},xe={class:"list-item__row"},Ge={class:"list-item__title"},Be={class:"list-item__status"},Ee={class:"list-item__row"},He={class:"list-item__ul"},Te={key:0},Ve={class:"list-item__row"},je={class:"btn btn--border btn--switch"},Re=O(()=>o("span",{class:"btn-switch__text"},"Автопродление",-1)),Fe=O(()=>o("span",{class:"btn-switch__toggle"},null,-1)),qe={key:0,class:"list-col list-col--md"},Ue={class:"list-item"},Xe={class:"list-item__row"},We=O(()=>o("div",{class:"list-item__title"},"Панель управления",-1)),Ze={class:"list-item__row list-item__row--table"},ze=O(()=>o("div",{class:"list-item__item"},"Адрес",-1)),Je={key:0,class:"list-item__item"},Ke={key:1,class:"list-item__item"},Qe={key:0,class:"list-item__row list-item__row--table"},Ye=O(()=>o("div",{class:"list-item__item"},"Логин",-1)),$e={class:"list-item__item"},et={key:1,class:"list-item__row list-item__row--table"},tt=O(()=>o("div",{class:"list-item__item"},"Пароль",-1)),st={class:"list-item__item hide-button"},rt={key:0,class:"password-hidden"},ot={class:"list-item__row list-item__row--table"},it=O(()=>o("div",{class:"list-item__item"},"FTP, POP3, SMTP, IMAP",-1)),at={key:0,class:"list-item__item"},nt={key:1,class:"list-item__item"},dt={key:2,class:"list-item__row list-item__row--table"},ct=O(()=>o("div",{class:"list-item__item"},"IP адрес",-1)),lt={class:"list-item__item"};function _t(c,n,r,t,U,W){var a,M,D,x,g,h,P,k,y,G,B,E,H,N,T,V,j,R,F,e,s,i,C,A,L,Z,z,J,K,Q,Y;const X=re,f=be,p=Pe,I=fe,u=se,w=pe;return d(),_(ne,null,[b(X),o("div",we,[o("div",ke,[o("div",ye,[o("h1",Ne,m((a=t.getService)==null?void 0:a.Name),1),o("div",Ce,"# "+m((M=t.getOrder)==null?void 0:M.ID),1),((D=t.getOrder)==null?void 0:D.ServiceID)==="52000"?(d(),q(f,{key:0,class:"section-scheme",label:"Изменить тариф",onClick:n[0]||(n[0]=S=>t.changeScheme())})):l("",!0),((x=t.getOrder)==null?void 0:x.ServiceID)==="51000"?(d(),q(f,{key:1,class:"section-scheme",label:"Изменить тариф",onClick:n[1]||(n[1]=S=>t.changeISPScheme())})):l("",!0)]),b(p),o("div",Ae,[b(I,{"contract-id":(g=t.getOrder)==null?void 0:g.ContractID,onTransfer:n[2]||(n[2]=S=>t.transferItem())},null,8,["contract-id"]),o("div",Le,[o("div",Me,[o("div",xe,[o("div",Ge,m((h=t.getService)==null?void 0:h.Name),1),o("div",Be,[b(u,{status:(P=t.getOrder)==null?void 0:P.StatusID,"status-table":"Orders",onClick:n[3]||(n[3]=S=>{var $,ee;return t.openHistoryStatuses(($=t.getOrder)==null?void 0:$.ServiceOrderID,(ee=t.getOrder)==null?void 0:ee.Code)})},null,8,["status"])])]),o("div",Ee,[o("ul",He,[(k=t.getOrder)!=null&&k.DependOrderID&&((y=t.getOrder)==null?void 0:y.DependOrderID)!=="0"?(d(),_("li",Te,"Относится к заказу "+m((G=t.getOrder)==null?void 0:G.DependOrderID)+"/"+m((B=t.getDependOrderService)==null?void 0:B.Name),1)):l("",!0)])]),o("div",Ve,[b(f,{label:((E=t.getOrder)==null?void 0:E.StatusID)==="ClaimForRegister"||((H=t.getOrder)==null?void 0:H.StatusID)==="Waiting"?"Оплатить":"Продлить",onClick:n[4]||(n[4]=S=>t.navigateToProlong())},null,8,["label"]),o("label",je,[oe(o("input",{type:"checkbox","onUpdate:modelValue":n[5]||(n[5]=S=>t.isAutoProlong=S),onInput:n[6]||(n[6]=S=>t.setProlongValue())},null,544),[[ie,t.isAutoProlong]]),Re,Fe])])])]),((N=t.getOrder)==null?void 0:N.ServiceID)==="52000"||((T=t.getOrder)==null?void 0:T.ServiceID)==="51000"?(d(),_("div",qe,[o("div",Ue,[o("div",Xe,[We,((V=t.getOrder)==null?void 0:V.ServiceID)==="52000"?(d(),q(f,{key:0,class:"ml-auto",onClick:n[7]||(n[7]=S=>t.orderManageDNS()),label:"Вход"})):l("",!0),((j=t.getOrder)==null?void 0:j.ServiceID)==="51000"?(d(),q(f,{key:1,class:"ml-auto",onClick:n[8]||(n[8]=S=>t.orderManageISP()),label:"Вход"})):l("",!0)]),o("div",Ze,[ze,((R=t.getOrder)==null?void 0:R.ServiceID)==="52000"?(d(),_("div",Je,m(`https://${(F=t.getServerGroupDNS)==null?void 0:F.Address}/manager`),1)):l("",!0),((e=t.getOrder)==null?void 0:e.ServiceID)==="51000"?(d(),_("div",Ke,m((s=t.getISPOrder)==null?void 0:s.ControlPanel),1)):l("",!0)]),((i=t.getOrder)==null?void 0:i.ServiceID)==="52000"?(d(),_("div",Qe,[Ye,o("div",$e,m((C=t.getDNSmanagerOrder)==null?void 0:C.Login),1)])):l("",!0),((A=t.getOrder)==null?void 0:A.ServiceID)==="52000"?(d(),_("div",et,[tt,o("div",st,[t.passwordShow?(d(),_("div",rt,m((L=t.getDNSmanagerOrder)==null?void 0:L.Password),1)):(d(),_("button",{key:1,class:"btn btn--border btn--md hide-button",onClick:n[9]||(n[9]=S=>t.passwordShow=!0)},[b(w),ae("Показать")]))])])):l("",!0),o("div",ot,[it,((Z=t.getOrder)==null?void 0:Z.ServiceID)==="52000"?(d(),_("div",at,m((z=t.getServerGroupDNS)==null?void 0:z.Address),1)):l("",!0),((J=t.getOrder)==null?void 0:J.ServiceID)==="51000"?(d(),_("div",nt,m((K=t.getServerGroupISP)==null?void 0:K.Address),1)):l("",!0)]),((Q=t.getOrder)==null?void 0:Q.ServiceID)==="51000"?(d(),_("div",dt,[ct,o("div",lt,m((Y=t.getISPOrder)==null?void 0:Y.IP),1)])):l("",!0)])])):l("",!0)])])])],64)}const mt={components:{StatusBadge:se,BlockBalanceAgreement:re},async setup(){const c=_e(),n=me(),r=De(),t=ge(),U=he(),W=v(()=>{var e,s,i;return(i=(e=t==null?void 0:t.serverGroupsList)==null?void 0:e.Servers)==null?void 0:i[(s=h.value)==null?void 0:s.ServerID]}),X=v(()=>{var e,s,i;return(i=(e=t==null?void 0:t.serverGroupsList)==null?void 0:e.Servers)==null?void 0:i[(s=g.value)==null?void 0:s.ServerID]}),f=te(!1),p=ve("emitter"),I=Ie(),u=ue(),w=te(!1),a=v(()=>Object.keys(u==null?void 0:u.userOrders).map(e=>u==null?void 0:u.userOrders[e]).find(e=>e.ID===c.params.id)),M=v(()=>{var e,s;return I==null?void 0:I.servicesList[(s=u.userOrders[(e=a.value)==null?void 0:e.DependOrderID])==null?void 0:s.ServiceID]}),D=v(()=>{var e;return I==null?void 0:I.servicesList[(e=a.value)==null?void 0:e.ServiceID]}),x=v(()=>r==null?void 0:r.ISPswOrdersList),g=v(()=>Object.keys(r==null?void 0:r.ISPswOrdersList).map(e=>r==null?void 0:r.ISPswOrdersList[e]).find(e=>{var s;return e.OrderID===((s=a.value)==null?void 0:s.ID)})),h=v(()=>Object.keys(r==null?void 0:r.DNSmanagerOrdersList).map(e=>r==null?void 0:r.DNSmanagerOrdersList[e]).find(e=>{var s;return e.OrderID===((s=a.value)==null?void 0:s.ID)}));v(()=>Object.keys(r==null?void 0:r.ExtraIPOrdersList).map(e=>r==null?void 0:r.ExtraIPOrdersList[e]).find(e=>{var s;return e.OrderID===((s=a.value)==null?void 0:s.ID)}));const P=v(()=>r==null?void 0:r.additionalServicesScheme),k=v(()=>{const e=h.value;return e&&P.value.DNSmanager[e.SchemeID]||null}),y=v(()=>{const e=g.value;return e&&P.value.ISPsw[e.SchemeID]||null});function G(e,s){let i="";switch(s){case"Default":i="Orders";break;default:i=s+"Orders";break}p.emit("open-modal",{component:"StatusHistory",data:{modeID:i,rowID:e}})}function B(){var e,s;p.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(e=a.value)==null?void 0:e.ID,ServiceID:(s=a.value)==null?void 0:s.ServiceID}})}function E(){var e,s,i;U.OrderManageHosting({ServiceOrderID:(e=h.value)==null?void 0:e.ID,OrderID:(s=h.value)==null?void 0:s.OrderID,ServiceID:(i=h.value)==null?void 0:i.ServiceID,XMLHttpRequest:"yes"})}function H(){var e,s,i;U.OrderManageHosting({ServiceOrderID:(e=g.value)==null?void 0:e.ID,OrderID:(s=g.value)==null?void 0:s.OrderID,ServiceID:(i=g.value)==null?void 0:i.ServiceID,XMLHttpRequest:"yes"})}function N(){var e,s,i;((e=a==null?void 0:a.value)==null?void 0:e.ServiceID)==="50000"?n.push(`/ExtraIPOrderPay/${c.params.id}`):((s=a==null?void 0:a.value)==null?void 0:s.ServiceID)==="51000"?n.push(`/ISPswOrderPay/${c.params.id}`):((i=a==null?void 0:a.value)==null?void 0:i.ServiceID)==="52000"?n.push(`/DNSmanagerOrderPay/${c.params.id}`):n.push(`/ServiceOrderPay/${c.params.id}`)}function T(){var e;u.prolongOrder({IsAutoProlong:w.value?0:1,OrderID:(e=a.value)==null?void 0:e.ID})}function V(){j()}function j(){var e,s,i;p.emit("open-modal",{component:"ISPswSchemeChange",data:{ISPswID:(e=a.value)==null?void 0:e.ID,ServerGroup:(s=y.value)==null?void 0:s.ServersGroupID,Code:(i=D.value)==null?void 0:i.Code}})}function R(){F()}function F(){var e,s,i;p.emit("open-modal",{component:"DNSOrderSchemeChange",data:{DNSID:(e=a.value)==null?void 0:e.ID,ServerGroup:(s=k.value)==null?void 0:s.ServersGroupID,Code:(i=D.value)==null?void 0:i.Code}})}return Se(async()=>{var e,s,i,C,A,L;((e=a.value)==null?void 0:e.ServiceID)==="52000"&&(await r.fetchAdditionalServiceScheme((s=D.value)==null?void 0:s.Code),await r.fetchDNSmanagerOrders()),((i=a.value)==null?void 0:i.ServiceID)==="51000"&&(await r.fetchAdditionalServiceScheme((C=D.value)==null?void 0:C.Code),await r.fetchISPswOrders()),((A=a.value)==null?void 0:A.ServiceID)==="50000"&&(await r.fetchAdditionalServiceScheme((L=D.value)==null?void 0:L.Code),await r.fetchExtraIPOrders()),(c.name==="defaultDNSmanagerOrderPay"||c.name==="defaultISPswOrderPay"||c.name==="defaultExtraIPOrderPay")&&N(),console.log(c)}),await u.fetchUserOrders().then(()=>{var e;w.value=(e=a.value)==null?void 0:e.IsAutoProlong}),await I.fetchServices(),await t.fetchServerGroups(),{getService:D,getOrder:a,getDependOrderService:M,isAutoProlong:w,servicesStatuses:Oe,transferItem:B,navigateToProlong:N,setProlongValue:T,getDNSmanagerOrder:h,getISPOrder:g,getAdditionalServiceScheme:P,getDNSmanagerOrderScheme:k,getISPOrderScheme:y,changeScheme:R,ISPswOrders:x,changeISPScheme:V,getServerGroupDNS:W,getServerGroupISP:X,passwordShow:f,orderManageDNS:E,orderManageISP:H,openHistoryStatuses:G}}},At=le(mt,[["render",_t],["__scopeId","data-v-bbd6bf29"]]);export{At as default};

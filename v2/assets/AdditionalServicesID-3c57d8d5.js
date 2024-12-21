import{o as d,c as _,b as w,a as o,t as m,y as U,k as c,Z as oe,$ as ie,d as ae,F as ne,p as de,l as le,_ as ce,a7 as _e,e as me,g as v,r as te,S as ve,f as ue,i as Se}from"./index-5f8fd0e9.js";import{u as Ie}from"./postings-2abc5e3f.js";import{u as De}from"./services-0121b428.js";import{u as ge}from"./resetServer-0c21ceb4.js";import{u as he}from"./globalActions-2cf5b597.js";import{S as re}from"./StatusBadge-fdff4dcc.js";import{s as Oe}from"./servicesStatuses-6293b6ac.js";import{B as se}from"./BlockBalanceAgreement-cf03db60.js";import{_ as pe,a as fe}from"./ServicesContractBalanceBlock-e9c81061.js";import{_ as Pe}from"./ClausesKeeper-8218189c.js";import{_ as we}from"./ButtonDefault-458eab69.js";import"./contracts-00b6c53b.js";import"./bootstrap-vue-next.es-a97602fc.js";import"./IconArrow-c9744352.js";import"./IconProfile-37dffd98.js";import"./IconCard-3d2eebd0.js";import"./IconClose-2e1ba86d.js";const p=l=>(de("data-v-60d20dd7"),l=l(),le(),l),be={class:"section"},ke={class:"container"},ye={class:"section-header"},Ce={class:"section-title"},Ne={class:"section-label"},Ae={class:"list"},Me={class:"list-col list-col--md"},Le={class:"list-item"},xe={class:"list-item__row"},Ge={class:"list-item__title"},Be={class:"list-item__status"},Ee={class:"list-item__row"},He={class:"list-item__ul"},Te={key:0},Ve={class:"list-item__row"},je={class:"btn btn--border btn--switch"},Re=p(()=>o("span",{class:"btn-switch__text"},"Автопродление",-1)),Fe=p(()=>o("span",{class:"btn-switch__toggle"},null,-1)),qe={key:0,class:"list-col list-col--md"},Ue={class:"list-item"},Xe={class:"list-item__row"},We=p(()=>o("div",{class:"list-item__title"},"Панель управления",-1)),Ze={class:"list-item__row list-item__row--table"},ze=p(()=>o("div",{class:"list-item__item"},"Адрес",-1)),Je={key:0,class:"list-item__item"},Ke={key:1,class:"list-item__item"},Qe={key:0,class:"list-item__row list-item__row--table"},Ye=p(()=>o("div",{class:"list-item__item"},"Логин",-1)),$e={class:"list-item__item"},et={key:1,class:"list-item__row list-item__row--table"},tt=p(()=>o("div",{class:"list-item__item"},"Пароль",-1)),rt={class:"list-item__item hide-button"},st={key:0,class:"password-hidden"},ot={class:"list-item__row list-item__row--table"},it=p(()=>o("div",{class:"list-item__item"},"FTP, POP3, SMTP, IMAP",-1)),at={key:0,class:"list-item__item"},nt={key:1,class:"list-item__item"},dt={key:2,class:"list-item__row list-item__row--table"},lt=p(()=>o("div",{class:"list-item__item"},"IP адрес",-1)),ct={class:"list-item__item"};function _t(l,a,s,t,X,Z){var n,L,g,x,h,S,P,k,y,G,B,E,H,T,C,V,j,R,F,q,e,r,i,N,A,M,z,J,K,Q,Y;const W=se,f=we,O=Pe,D=pe,u=re,b=fe;return d(),_(ne,null,[w(W),o("div",be,[o("div",ke,[o("div",ye,[o("h1",Ce,m((n=t.getService)==null?void 0:n.Name),1),o("div",Ne,"# "+m((L=t.getOrder)==null?void 0:L.ID),1),((g=t.getOrder)==null?void 0:g.ServiceID)==="52000"?(d(),U(f,{key:0,class:"section-scheme",label:"Изменить тариф",onClick:a[0]||(a[0]=I=>t.changeScheme())})):c("",!0),((x=t.getOrder)==null?void 0:x.ServiceID)==="51000"?(d(),U(f,{key:1,class:"section-scheme",label:"Изменить тариф",onClick:a[1]||(a[1]=I=>t.changeISPScheme())})):c("",!0)]),w(O),o("div",Ae,[w(D,{"contract-id":(h=t.getOrder)==null?void 0:h.ContractID,onTransfer:a[2]||(a[2]=I=>t.transferItem())},null,8,["contract-id"]),o("div",Me,[o("div",Le,[o("div",xe,[o("div",Ge,m((S=t.getService)==null?void 0:S.Name),1),o("div",Be,[w(u,{status:(P=t.getOrder)==null?void 0:P.StatusID,"status-table":"Orders",onClick:a[3]||(a[3]=I=>{var $,ee;return t.openHistoryStatuses(($=t.getOrder)==null?void 0:$.ServiceOrderID,(ee=t.getOrder)==null?void 0:ee.Code)})},null,8,["status"])])]),o("div",Ee,[o("ul",He,[(k=t.getOrder)!=null&&k.DependOrderID&&((y=t.getOrder)==null?void 0:y.DependOrderID)!=="0"?(d(),_("li",Te,"Относится к заказу "+m((G=t.getOrder)==null?void 0:G.DependOrderID)+"/"+m((B=t.getDependOrderService)==null?void 0:B.Name),1)):c("",!0)])]),o("div",Ve,[w(f,{label:((E=t.getOrder)==null?void 0:E.StatusID)==="ClaimForRegister"||((H=t.getOrder)==null?void 0:H.StatusID)==="Waiting"?"Оплатить":"Продлить",onClick:a[4]||(a[4]=I=>t.navigateToProlong())},null,8,["label"]),o("label",je,[oe(o("input",{type:"checkbox","onUpdate:modelValue":a[5]||(a[5]=I=>t.isAutoProlong=I),onInput:a[6]||(a[6]=I=>t.setProlongValue())},null,544),[[ie,t.isAutoProlong]]),Re,Fe])])])]),((T=t.getOrder)==null?void 0:T.ServiceID)==="52000"||((C=t.getOrder)==null?void 0:C.ServiceID)==="51000"?(d(),_("div",qe,[o("div",Ue,[o("div",Xe,[We,((V=t.getOrder)==null?void 0:V.ServiceID)==="52000"?(d(),U(f,{key:0,class:"ml-auto",onClick:a[7]||(a[7]=I=>t.orderManageDNS()),label:"Вход"})):c("",!0),((j=t.getOrder)==null?void 0:j.ServiceID)==="51000"?(d(),U(f,{key:1,class:"ml-auto",onClick:a[8]||(a[8]=I=>t.orderManageISP()),label:"Вход"})):c("",!0)]),o("div",Ze,[ze,((R=t.getOrder)==null?void 0:R.ServiceID)==="52000"?(d(),_("div",Je,m(`https://${(F=t.getServerGroupDNS)==null?void 0:F.Address}/manager`),1)):c("",!0),((q=t.getOrder)==null?void 0:q.ServiceID)==="51000"?(d(),_("div",Ke,m((e=t.getISPOrder)==null?void 0:e.ControlPanel),1)):c("",!0)]),((r=t.getOrder)==null?void 0:r.ServiceID)==="52000"?(d(),_("div",Qe,[Ye,o("div",$e,m((i=t.getDNSmanagerOrder)==null?void 0:i.Login),1)])):c("",!0),((N=t.getOrder)==null?void 0:N.ServiceID)==="52000"?(d(),_("div",et,[tt,o("div",rt,[t.passwordShow?(d(),_("div",st,m((A=t.getDNSmanagerOrder)==null?void 0:A.Password),1)):(d(),_("button",{key:1,class:"btn btn--border btn--md hide-button",onClick:a[9]||(a[9]=I=>t.passwordShow=!0)},[w(b),ae("Показать")])),o("button",{class:"btn btn--border btn--md hide-button",onClick:a[10]||(a[10]=I=>t.openPasswordChange())},"Сменить пароль")])])):c("",!0),o("div",ot,[it,((M=t.getOrder)==null?void 0:M.ServiceID)==="52000"?(d(),_("div",at,m((z=t.getServerGroupDNS)==null?void 0:z.Address),1)):c("",!0),((J=t.getOrder)==null?void 0:J.ServiceID)==="51000"?(d(),_("div",nt,m((K=t.getServerGroupISP)==null?void 0:K.Address),1)):c("",!0)]),((Q=t.getOrder)==null?void 0:Q.ServiceID)==="51000"?(d(),_("div",dt,[lt,o("div",ct,m((Y=t.getISPOrder)==null?void 0:Y.IP),1)])):c("",!0)])])):c("",!0)])])])],64)}const mt={components:{StatusBadge:re,BlockBalanceAgreement:se},async setup(){const l=_e(),a=me(),s=De(),t=ge(),X=he(),Z=v(()=>{var e,r,i;return(i=(e=t==null?void 0:t.serverGroupsList)==null?void 0:e.Servers)==null?void 0:i[(r=S.value)==null?void 0:r.ServerID]}),W=v(()=>{var e,r,i;return(i=(e=t==null?void 0:t.serverGroupsList)==null?void 0:e.Servers)==null?void 0:i[(r=h.value)==null?void 0:r.ServerID]}),f=te(!1),O=ve("emitter"),D=Ie(),u=ue(),b=te(!1),n=v(()=>Object.keys(u==null?void 0:u.userOrders).map(e=>u==null?void 0:u.userOrders[e]).find(e=>e.ID===l.params.id)),L=v(()=>{var e,r;return D==null?void 0:D.servicesList[(r=u.userOrders[(e=n.value)==null?void 0:e.DependOrderID])==null?void 0:r.ServiceID]}),g=v(()=>{var e;return D==null?void 0:D.servicesList[(e=n.value)==null?void 0:e.ServiceID]}),x=v(()=>s==null?void 0:s.ISPswOrdersList),h=v(()=>Object.keys(s==null?void 0:s.ISPswOrdersList).map(e=>s==null?void 0:s.ISPswOrdersList[e]).find(e=>{var r;return e.OrderID===((r=n.value)==null?void 0:r.ID)})),S=v(()=>Object.keys(s==null?void 0:s.DNSmanagerOrdersList).map(e=>s==null?void 0:s.DNSmanagerOrdersList[e]).find(e=>{var r;return e.OrderID===((r=n.value)==null?void 0:r.ID)}));v(()=>Object.keys(s==null?void 0:s.ExtraIPOrdersList).map(e=>s==null?void 0:s.ExtraIPOrdersList[e]).find(e=>{var r;return e.OrderID===((r=n.value)==null?void 0:r.ID)}));const P=v(()=>s==null?void 0:s.additionalServicesScheme);O.on("updateDNSManagerPage",()=>{s.fetchDNSmanagerOrders()});const k=v(()=>{const e=S.value;return e&&P.value.DNSmanager[e.SchemeID]||null}),y=v(()=>{const e=h.value;return e&&P.value.ISPsw[e.SchemeID]||null});function G(e,r){let i="";switch(r){case"Default":i="Orders";break;default:i=r+"Orders";break}O.emit("open-modal",{component:"StatusHistory",data:{modeID:i,rowID:e}})}function B(){var e,r;O.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(e=S.value)==null?void 0:e.ID,ServiceID:(r=S.value)==null?void 0:r.ServiceID,emitEvent:"updateDNSManagerPage"}})}function E(){var e,r;O.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(e=n.value)==null?void 0:e.ID,ServiceID:(r=n.value)==null?void 0:r.ServiceID}})}function H(){var e,r,i;X.OrderManageHosting({ServiceOrderID:(e=S.value)==null?void 0:e.ID,OrderID:(r=S.value)==null?void 0:r.OrderID,ServiceID:(i=S.value)==null?void 0:i.ServiceID,XMLHttpRequest:"yes"})}function T(){var e,r,i;X.OrderManageHosting({ServiceOrderID:(e=h.value)==null?void 0:e.ID,OrderID:(r=h.value)==null?void 0:r.OrderID,ServiceID:(i=h.value)==null?void 0:i.ServiceID,XMLHttpRequest:"yes"})}function C(){var e,r,i;((e=n==null?void 0:n.value)==null?void 0:e.ServiceID)==="50000"?a.push(`/ExtraIPOrderPay/${l.params.id}`):((r=n==null?void 0:n.value)==null?void 0:r.ServiceID)==="51000"?a.push(`/ISPswOrderPay/${l.params.id}`):((i=n==null?void 0:n.value)==null?void 0:i.ServiceID)==="52000"?a.push(`/DNSmanagerOrderPay/${l.params.id}`):a.push(`/ServiceOrderPay/${l.params.id}`)}function V(){var e;u.prolongOrder({IsAutoProlong:b.value?0:1,OrderID:(e=n.value)==null?void 0:e.ID})}function j(){R()}function R(){var e,r,i;O.emit("open-modal",{component:"ISPswSchemeChange",data:{ISPswID:(e=n.value)==null?void 0:e.ID,ServerGroup:(r=y.value)==null?void 0:r.ServersGroupID,Code:(i=g.value)==null?void 0:i.Code}})}function F(){q()}function q(){var e,r,i;O.emit("open-modal",{component:"DNSOrderSchemeChange",data:{DNSID:(e=n.value)==null?void 0:e.ID,ServerGroup:(r=k.value)==null?void 0:r.ServersGroupID,Code:(i=g.value)==null?void 0:i.Code}})}return Se(async()=>{var e,r,i,N,A,M;((e=n.value)==null?void 0:e.ServiceID)==="52000"&&(await s.fetchAdditionalServiceScheme((r=g.value)==null?void 0:r.Code),await s.fetchDNSmanagerOrders()),((i=n.value)==null?void 0:i.ServiceID)==="51000"&&(await s.fetchAdditionalServiceScheme((N=g.value)==null?void 0:N.Code),await s.fetchISPswOrders()),((A=n.value)==null?void 0:A.ServiceID)==="50000"&&(await s.fetchAdditionalServiceScheme((M=g.value)==null?void 0:M.Code),await s.fetchExtraIPOrders()),(l.name==="defaultDNSmanagerOrderPay"||l.name==="defaultISPswOrderPay"||l.name==="defaultExtraIPOrderPay")&&C()}),await u.fetchUserOrders().then(()=>{var e;b.value=(e=n.value)==null?void 0:e.IsAutoProlong}),await D.fetchServices(),await t.fetchServerGroups(),{getService:g,getOrder:n,getDependOrderService:L,isAutoProlong:b,servicesStatuses:Oe,transferItem:E,navigateToProlong:C,setProlongValue:V,getDNSmanagerOrder:S,getISPOrder:h,getAdditionalServiceScheme:P,getDNSmanagerOrderScheme:k,getISPOrderScheme:y,changeScheme:F,ISPswOrders:x,changeISPScheme:j,getServerGroupDNS:Z,getServerGroupISP:W,passwordShow:f,orderManageDNS:H,orderManageISP:T,openHistoryStatuses:G,openPasswordChange:B}}},At=ce(mt,[["render",_t],["__scopeId","data-v-60d20dd7"]]);export{At as default};

import{j as Dt,o as S,c as I,b as c,a as t,t as a,w as O,d as b,Z as A,a9 as V,$ as ft,F as St,p as It,l as Ot,_ as bt,a7 as Ht,e as wt,f as Pt,S as kt,r as _t,g as h}from"./index-6f846e0d.js";import{_ as yt}from"./IconCard-b1fefd85.js";import{I as Ct}from"./IconProfile-df27f7d9.js";import{I as xt}from"./IconSettings-359ff9ea.js";import{_ as mt,a as gt}from"./ServicesContractBalanceBlock-8a2caf63.js";import{S as ut}from"./StatusBadge-654ab79e.js";import{h as Bt}from"./hostStatuses-94d00f20.js";import{u as Nt}from"./testTransforms-eec373ee.js";import{f as dt}from"./useTimeFunction-8602dd60.js";import{u as At}from"./hosting-aad12620.js";import{u as Et}from"./resetServer-008fdf83.js";import{u as Mt}from"./globalActions-d40367d6.js";import{_ as vt}from"./ClausesKeeper-3ae27028.js";import{s as ht}from"./SettingsEngineItem-0ae50484.js";import{B as pt}from"./BlockBalanceAgreement-4579706c.js";import{E as Tt}from"./EmptyStateBlock-c1b0587e.js";import{_ as Lt}from"./paramsAccordionBlock-a5cc854f.js";import{_ as Rt}from"./ButtonDefault-40efcd87.js";import{z as Vt}from"./bootstrap-vue-next.es-6bf04585.js";import"./contracts-b643bcd5.js";import"./IconClose-12869131.js";import"./IconArrow-37547b0b.js";/* empty css                                                                             */const m=g=>(It("data-v-b69a3b80"),g=g(),Ot(),g),Gt={key:0,class:"my-hosting-order"},Ut={class:"section"},jt={class:"container"},qt={class:"section-header"},Ft={class:"section-title"},Wt=["href"],zt={class:"section-label"},Kt={class:"section-nav"},Xt={class:"list"},Zt={class:"list-col list-col--md"},Jt={class:"list-item"},Qt={class:"list-item__row"},Yt={class:"list-item__title"},$t={class:"list-item__status"},te={class:"list-item__row"},ee={class:"list-item__ul"},se={class:"list-item__row list-item__row--column"},oe={class:"list-item__text"},ie={class:"list-item__text"},ne={class:"list-item__text"},ae={class:"list-item__row"},re={class:"btn btn--border btn--switch"},le=m(()=>t("span",{class:"btn-switch__text"},"Автопродление",-1)),ce=m(()=>t("span",{class:"btn-switch__toggle"},null,-1)),_e={class:"list-col list-col--md"},de={class:"list-item"},me={class:"list-item__row"},ge=m(()=>t("div",{class:"list-item__title"},"Панель управления",-1)),ue={class:"list-item__row list-item__row--table"},ve=m(()=>t("div",{class:"list-item__item"},"Адрес",-1)),he={class:"list-item__item"},pe={class:"list-item__row list-item__row--table"},De=m(()=>t("div",{class:"list-item__item"},"Логин",-1)),fe={class:"list-item__item"},Se={class:"list-item__row list-item__row--table"},Ie=m(()=>t("div",{class:"list-item__item"},"Пароль",-1)),Oe={class:"list-item__item hide-button"},be={key:0,class:"password-hidden"},He={class:"list-item__row list-item__row--table"},we=m(()=>t("div",{class:"list-item__item"},"FTP, POP3, SMTP, IMAP",-1)),Pe={class:"list-item__item"},ke={class:"list-col list-col--md"},ye={class:"list-item"},Ce=m(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Именные сервера")],-1)),xe={class:"list-item__row list-item__row--table"},Be={class:"list-item__item"},Ne={class:"list-item__item"},Ae={class:"list-item__item"},Ee={class:"list-item__item"},Me={key:1,class:"section"},Te={class:"container"};function Le(g,i,r,s,u,G){var w,P,k,y,C,x,v,B,N,e,o,l,U,j,q,F,W,z,K,X,Z,J,Q,Y,$,tt,et,st,ot,it,nt,at,rt,lt;const E=pt,d=Dt("router-link"),M=vt,p=mt,n=ut,D=Vt,f=ht,H=Rt,T=gt,L=Lt,R=Tt;return S(),I(St,null,[c(E),(w=s.getHostingOrderData)!=null&&w.ID?(S(),I("div",Gt,[t("div",Ut,[t("div",jt,[t("div",qt,[t("h1",Ft,[t("a",{href:s.fullDomainUrl,target:"_blank"},a((P=s.getHostingOrderData)==null?void 0:P.Domain),9,Wt)]),t("div",zt,"Хостинг # "+a((k=s.getHostingOrderData)==null?void 0:k.OrderID),1),t("div",Kt,[c(d,{class:"section-nav__item",to:`/HostingOrders/${(y=s.getHostingOrderData)==null?void 0:y.OrderID}`},{default:O(()=>[b("Хостинг")]),_:1},8,["to"])])]),c(M,{partition:"Header:/HostingOrders"}),t("div",Xt,[c(p,{"contract-id":(C=s.getHostingOrderData)==null?void 0:C.ContractID,onTransfer:i[0]||(i[0]=_=>s.transferItem())},null,8,["contract-id"]),t("div",Zt,[t("div",Jt,[t("div",Qt,[t("div",Yt,a((x=s.getHostingScheme)==null?void 0:x.Name),1),t("div",$t,[c(n,{status:(v=s.getHostingOrderData)==null?void 0:v.StatusID,"status-table":"HostingOrders",onClick:i[1]||(i[1]=_=>{var ct;return s.openHistoryStatuses((ct=s.getHostingOrderData)==null?void 0:ct.ID)})},null,8,["status"])]),c(f,null,{default:O(()=>[c(D,{onClick:i[2]||(i[2]=_=>s.changeScheme())},{default:O(()=>[b("Изменить тариф")]),_:1}),c(D,{onClick:i[3]||(i[3]=_=>s.openOrderRestore())},{default:O(()=>[b("Просмотреть учет заказа")]),_:1}),c(D,{onClick:i[4]||(i[4]=_=>s.openPasswordChange())},{default:O(()=>[b("Сменить пароль от аккаунта")]),_:1})]),_:1})]),t("div",te,[t("ul",ee,[A(t("li",null,a(s.formDescriptionLine((N=(B=s.getHostingScheme)==null?void 0:B.SchemeParams)==null?void 0:N.limit_webdomains)),513),[[V,(l=(o=(e=s.getHostingScheme)==null?void 0:e.SchemeParams)==null?void 0:o.limit_webdomains)==null?void 0:l.Value]]),A(t("li",null,a(s.formDescriptionLine((j=(U=s.getHostingScheme)==null?void 0:U.SchemeParams)==null?void 0:j.limit_quota)),513),[[V,(W=(F=(q=s.getHostingScheme)==null?void 0:q.SchemeParams)==null?void 0:F.limit_quota)==null?void 0:W.Value]]),A(t("li",null,a(s.formDescriptionLine((K=(z=s.getHostingScheme)==null?void 0:z.SchemeParams)==null?void 0:K.limit_emails)),513),[[V,(J=(Z=(X=s.getHostingScheme)==null?void 0:X.SchemeParams)==null?void 0:Z.limit_emails)==null?void 0:J.Value]])])]),t("div",se,[t("div",oe,a((Q=s.getHostingScheme)==null?void 0:Q.CostMonth)+" ₽ в месяц",1),t("div",ie,"Дней осталось "+a(((Y=s.getHostingOrderData)==null?void 0:Y.DaysRemainded)||0),1),t("div",ne,"Дата окончания "+a(s.calculateExpirationDate(($=s.getHostingOrderData)==null?void 0:$.DaysRemainded)),1)]),t("div",ae,[t("div",{class:"btn btn-default btn--blue",onClick:i[5]||(i[5]=_=>s.navigateToProlong())},a(((tt=s.getHostingOrderData)==null?void 0:tt.StatusID)==="Waiting"?"Оплатить":"Продлить"),1),t("label",re,[A(t("input",{type:"checkbox","onUpdate:modelValue":i[6]||(i[6]=_=>s.isAutoProlong=_),onInput:i[7]||(i[7]=_=>s.setProlongValue())},null,544),[[ft,s.isAutoProlong]]),le,ce])])])]),t("div",_e,[t("div",de,[t("div",me,[ge,c(H,{class:"ml-auto",onClick:i[8]||(i[8]=_=>s.orderManage()),label:"Вход"})]),t("div",ue,[ve,t("div",he,a(`https://${(et=s.getServerGroup)==null?void 0:et.Address}/manager`),1)]),t("div",pe,[De,t("div",fe,a((st=s.getHostingOrderData)==null?void 0:st.Login),1)]),t("div",Se,[Ie,t("div",Oe,[s.passwordShow?(S(),I("div",be,a(s.getHostingOrderData.Password),1)):(S(),I("button",{key:1,class:"btn btn--border btn--md hide-button",onClick:i[9]||(i[9]=_=>s.passwordShow=!0)},[c(T),b("Показать")])),t("button",{class:"btn btn--border btn--md hide-button",onClick:i[10]||(i[10]=_=>s.openPasswordChange())},"Сменить пароль")])]),t("div",He,[we,t("div",Pe,a((ot=s.getServerGroup)==null?void 0:ot.Address),1)])])]),t("div",ke,[t("div",ye,[Ce,t("div",xe,[t("div",Be,a((it=s.getHostingOrderData)==null?void 0:it.Ns1Name),1),t("div",Ne,a((nt=s.getHostingOrderData)==null?void 0:nt.Ns2Name),1),t("div",Ae,a((at=s.getHostingOrderData)==null?void 0:at.Ns3Name),1),t("div",Ee,a((rt=s.getHostingOrderData)==null?void 0:rt.Ns4Name),1)])])])])])]),c(L,{"params-list":(lt=s.getHostingScheme)==null?void 0:lt.SchemeParams,label:"Общая информация","main-param":"InternalName"},null,8,["params-list"])])):(S(),I("div",Me,[t("div",Te,[c(R,{label:"Заказ не найден"})])]))],64)}const Re={components:{IconCard:yt,IconProfile:Ct,IconSettings:xt,IconEye:gt,StatusBadge:ut,ClausesKeeper:vt,settingsEngineItem:ht,ServicesContractBalanceBlock:mt,BlockBalanceAgreement:pt},async setup(){const g=Ht(),i=wt(),r=At(),s=Et(),u=Pt(),G=Mt(),{formDescriptionLine:E}=Nt(),d=kt("emitter");d.on("updateHostingIDPage",()=>{r.fetchHostingOrders()});const M=_t(!1),p=_t(!1),n=h(()=>Object.keys(r==null?void 0:r.hostingList).map(e=>r==null?void 0:r.hostingList[e]).find(e=>e.OrderID===g.params.id)),D=h(()=>r==null?void 0:r.hostingSchemes),f=h(()=>{var e,o;return(o=D.value)==null?void 0:o[(e=n.value)==null?void 0:e.SchemeID]}),H=h(()=>{var e,o;return(o=u==null?void 0:u.userOrders)==null?void 0:o[(e=n.value)==null?void 0:e.OrderID]}),T=h(()=>{var e,o,l;return(l=(e=s==null?void 0:s.serverGroupsList)==null?void 0:e.Servers)==null?void 0:l[(o=n.value)==null?void 0:o.ServerID]}),L=h(()=>{var o;if(!((o=n.value)!=null&&o.Domain))return"";const e=n.value.Domain;return e.startsWith("http://")||e.startsWith("https://")?e:`https://${e}`});function R(e){if(!e)return dt(new Date);const o=new Date,l=new Date(o.getTime()+e*24*60*60*1e3);return dt(l)}function w(e){d.emit("open-modal",{component:"StatusHistory",data:{modeID:"HostingOrders",rowID:e}})}function P(){var e,o;d.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(e=n.value)==null?void 0:e.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID}})}function k(){var e;u.prolongOrder({IsAutoProlong:p.value?0:1,OrderID:(e=n.value)==null?void 0:e.OrderID})}function y(){var e;i.push(`/HostingOrders/${(e=n.value)==null?void 0:e.OrderID}/SchemeChange`),v()}function C(){var e;i.push(`/HostingOrderPay/${(e=n.value)==null?void 0:e.OrderID}`)}function x(){var e,o,l;G.OrderManageHosting({ServiceOrderID:(e=n.value)==null?void 0:e.ID,OrderID:(o=n.value)==null?void 0:o.OrderID,ServiceID:(l=n.value)==null?void 0:l.ServiceID,XMLHttpRequest:"yes"})}function v(){var e,o;d.emit("open-modal",{component:"HostingOrderSchemeChange",data:{hostingID:(e=n.value)==null?void 0:e.ID,ServerGroup:(o=f.value)==null?void 0:o.ServersGroupID,emitEvent:"updateHostingIDPage"}})}function B(){var e,o;d.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(e=n.value)==null?void 0:e.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID,emitEvent:"updateHostingIDPage"}})}function N(){var e,o,l;d.emit("open-modal",{component:"OrderRestore",data:{OrderID:(e=n.value)==null?void 0:e.OrderID,CostDay:(o=f.value)==null?void 0:o.CostDay,DaysRemainded:(l=n.value)==null?void 0:l.DaysRemainded,emitEvent:"updateHostingIDPage"}})}return await r.fetchHostingOrders(),await r.fetchHostingSchemes(),await s.fetchServerGroups().then(()=>{g.name==="default.MyHostingOrderSchemeChange"&&v()}),await u.fetchUserOrders().then(()=>{var e;p.value=(e=H.value)==null?void 0:e.IsAutoProlong}),{getHostingOrderData:n,getHostingScheme:f,getUserOrder:H,getServerGroup:T,isAutoProlong:p,passwordShow:M,openOrderRestore:N,openPackageChangeModal:v,orderManage:x,navigateToProlong:C,setProlongValue:k,openPasswordChange:B,hostStatuses:Bt,transferItem:P,formDescriptionLine:E,calculateExpirationDate:R,changeScheme:y,fullDomainUrl:L,openHistoryStatuses:w}}},ls=bt(Re,[["render",Le],["__scopeId","data-v-b69a3b80"]]);export{ls as default};
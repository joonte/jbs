import{j as xt,o as g,c as S,b as l,a as t,t as a,Z as Ot,a9 as Rt,n as At,d as I,w as f,k as Bt,$ as Et,F as Mt,p as Nt,l as Ut,_ as Gt,f as Tt,a7 as Lt,e as Ht,r as L,S as jt,g as E}from"./index-5f8fd0e9.js";import{u as zt}from"./contracts-00b6c53b.js";import{u as Ft}from"./vps-08633800.js";import{u as Wt}from"./globalActions-2cf5b597.js";import{u as Zt}from"./resetServer-0c21ceb4.js";import{_ as qt}from"./IconCard-3d2eebd0.js";import{I as Jt}from"./IconProfile-37dffd98.js";import{I as Kt}from"./IconSettings-c1c25174.js";import{_ as Qt,a as Pt}from"./ServicesContractBalanceBlock-e9c81061.js";import{S as Ct}from"./StatusBadge-fdff4dcc.js";import{f as wt}from"./useTimeFunction-8602dd60.js";import{v as Xt}from"./vpsStatuses-32ffaaf5.js";import{s as kt}from"./SettingsEngineItem-9382d1a0.js";import{B as yt}from"./BlockBalanceAgreement-cf03db60.js";import{E as Yt}from"./EmptyStateBlock-153c9c6f.js";import{_ as $t}from"./ButtonDefault-458eab69.js";import{z as te}from"./bootstrap-vue-next.es-a97602fc.js";import{_ as ee}from"./ClausesKeeper-8218189c.js";import{I as se}from"./IconRotate-00011174.js";import"./IconArrow-c9744352.js";import"./IconClose-2e1ba86d.js";const c=_=>(Nt("data-v-e86e2c17"),_=_(),Ut(),_),oe={key:0,class:"my-vps-order"},ie={class:"section"},ne={class:"container"},ae={class:"section-header"},re={class:"section-title"},le={class:"ico"},de={class:"section-label"},ce={class:"section-nav"},_e={class:"list"},me={class:"list-col list-col--md"},ve={class:"list-item"},ue={class:"list-item__row"},he={class:"list-item__title"},pe={class:"list-item__status"},ge={class:"list-item__row"},Se={class:"list-item__ul"},Ie={key:0},De={class:"list-item__row list-item__row--column"},be={class:"list-item__text"},Ve={class:"list-item__text"},fe={class:"list-item__text"},Oe={class:"list-item__row"},we={class:"btn btn--border btn--switch"},Pe=c(()=>t("span",{class:"btn-switch__text"},"Автопродление",-1)),Ce=c(()=>t("span",{class:"btn-switch__toggle"},null,-1)),ke={class:"list-col list-col--md"},ye={class:"list-item"},xe={class:"list-item__row"},Re=c(()=>t("div",{class:"list-item__title"},"Панель управления",-1)),Ae={class:"list-item__row list-item__row--table"},Be=c(()=>t("div",{class:"list-item__item"},"Адрес",-1)),Ee={class:"list-item__item"},Me={class:"list-item__row list-item__row--table"},Ne=c(()=>t("div",{class:"list-item__item"},"Логин",-1)),Ue={class:"list-item__item"},Ge={class:"list-item__row list-item__row--table password-box"},Te=c(()=>t("div",{class:"list-item__item"},"Пароль",-1)),Le={class:"list-item__item hide-button"},He={key:0,class:"password-hidden"},je={class:"list-item__row list-item__row--table"},ze=c(()=>t("div",{class:"list-item__item"},"IP адрес",-1)),Fe={class:"list-item__item"},We={class:"list-col list-col--md"},Ze={class:"list-item"},qe=c(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Именные сервера")],-1)),Je={class:"list-item__row list-item__row--table"},Ke=c(()=>t("div",{class:"list-item__item"},"Первичный сервер",-1)),Qe={class:"list-item__item"},Xe={class:"list-item__row list-item__row--table"},Ye=c(()=>t("div",{class:"list-item__item"},"Вторичный сервер",-1)),$e={class:"list-item__item"},ts={class:"list-col list-col--md"},es={class:"list-item"},ss=c(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Паркованный домен")],-1)),os={class:"list-item__row list-item__row--table"},is=c(()=>t("div",{class:"list-item__item"},"Паркованный домен",-1)),ns={class:"list-item__item"},as=c(()=>t("div",{class:"section"},[t("div",{class:"container"},[t("div",{class:"text"},[t("h3",null,"Что входит в стоимость"),t("p",null,"Для подключения дополнительных услуг"),t("ul",null,[t("li",null,"Размещение в РФ, Москва"),t("li",null,"Предустановленная ОС FreeBSD / CentOS / Debian / Ubuntu / Windows"),t("li",null,"IP адрес, с возможностью расширения"),t("li",null,"Бесплатная панель управления ISPManager Lite"),t("li",null,"Круглосуточная поддержка")])])])],-1)),rs={key:1,class:"section"},ls={class:"container"};function ds(_,i,u,e,D,M){var P,C,k,y,x,p,R,A,B,s,o,r,H,j,z,F,W,Z,q,J,K,Q,X,Y,$,tt,et,st,ot,it,nt,at,rt,lt,dt,ct,_t,mt,vt,ut,ht,pt,gt,St,It,Dt,bt,Vt,ft;const O=yt,h=Ct,b=se,m=xt("router-link"),N=ee,n=Qt,v=te,U=kt,w=$t,G=Pt,T=Yt;return g(),S(Mt,null,[l(O),(P=e.getVpsOrder)!=null&&P.ID?(g(),S("div",oe,[t("div",ie,[t("div",ne,[t("div",ae,[t("h1",re,a((C=e.getVpsOrder)==null?void 0:C.Domain),1),l(h,{status:(k=e.getVpsOrder)==null?void 0:k.StatusID,"status-table":"VPSOrders",onClick:i[0]||(i[0]=d=>{var V;return e.openHistoryStatuses((V=e.getVpsOrder)==null?void 0:V.ID)})},null,8,["status"]),Ot(t("button",{class:At(["btn btn--md btn--border btn--rotate btn--rotate_green button-large",{"btn--rotate_gray":((y=e.getVpsOrder)==null?void 0:y.StatusID)!=="Active"||e.isReloading,"btn--rotate_loading":e.isReloading}]),title:"Перезагрузить виртуальный сервер",onClick:i[1]||(i[1]=(...d)=>e.reloadItem&&e.reloadItem(...d))},[I("Включен"),t("div",le,[l(b)])],2),[[Rt,((x=e.getVpsOrder)==null?void 0:x.StatusID)==="Active"]]),t("div",de,"VPS/VDS сервер # "+a((p=e.getVpsOrder)==null?void 0:p.OrderID),1),t("div",ce,[l(m,{class:"section-nav__item",to:"#"},{default:f(()=>[I("Сервер")]),_:1})])]),l(N),t("div",_e,[l(n,{"contract-id":(R=e.getVpsOrder)==null?void 0:R.ContractID,onTransfer:i[2]||(i[2]=d=>e.transferItem())},null,8,["contract-id"]),t("div",me,[t("div",ve,[t("div",ue,[t("div",he,a((A=e.getVpsScheme)==null?void 0:A.Name),1),t("div",pe,[l(h,{status:(B=e.getVpsOrder)==null?void 0:B.StatusID,"status-table":"VPSOrders",onClick:i[3]||(i[3]=d=>{var V;return e.openHistoryStatuses((V=e.getVpsOrder)==null?void 0:V.ID)})},null,8,["status"])]),l(U,null,{default:f(()=>[l(v,{onClick:i[4]||(i[4]=d=>e.changeScheme())},{default:f(()=>[I("Изменить тариф")]),_:1}),l(v,{onClick:i[5]||(i[5]=d=>e.openOrderRestore())},{default:f(()=>[I("Просмотреть учет заказа")]),_:1}),l(v,{onClick:i[6]||(i[6]=d=>e.openPasswordChange())},{default:f(()=>[I("Сменить пароль от аккаунта")]),_:1})]),_:1})]),t("div",ge,[t("ul",Se,[(r=(o=(s=e.getVpsScheme)==null?void 0:s.SchemeParams)==null?void 0:o.os)!=null&&r.value?(g(),S("li",Ie,a((z=(j=(H=e.getVpsScheme)==null?void 0:H.SchemeParams)==null?void 0:j.os)==null?void 0:z.value),1)):Bt("",!0),t("li",null,a(`${(Z=(W=(F=e.getVpsScheme)==null?void 0:F.SchemeParams)==null?void 0:W.hdd_mib)==null?void 0:Z.InternalName} ${(K=(J=(q=e.getVpsScheme)==null?void 0:q.SchemeParams)==null?void 0:J.hdd_mib)==null?void 0:K.Value} ${(Y=(X=(Q=e.getVpsScheme)==null?void 0:Q.SchemeParams)==null?void 0:X.hdd_mib)==null?void 0:Y.Unit}`),1),t("li",null,a(`RAM ${(et=(tt=($=e.getVpsScheme)==null?void 0:$.SchemeParams)==null?void 0:tt.ram_mib)==null?void 0:et.Value} ${(it=(ot=(st=e.getVpsScheme)==null?void 0:st.SchemeParams)==null?void 0:ot.ram_mib)==null?void 0:it.Unit}`),1),t("li",null,a(`Канал ${(rt=(at=(nt=e.getVpsScheme)==null?void 0:nt.SchemeParams)==null?void 0:at.net_bandwidth_mbitps)==null?void 0:rt.Value} ${(ct=(dt=(lt=e.getVpsScheme)==null?void 0:lt.SchemeParams)==null?void 0:dt.net_bandwidth_mbitps)==null?void 0:ct.Unit}`),1)])]),t("div",De,[t("div",be,a((_t=e.getVpsScheme)==null?void 0:_t.CostMonth)+" ₽ в месяц",1),t("div",Ve,"Дней осталось "+a((mt=e.getVpsOrder)==null?void 0:mt.DaysRemainded),1),t("div",fe,"Дата окончания "+a(e.calculateExpirationDate((vt=e.getVpsOrder)==null?void 0:vt.DaysRemainded)),1)]),t("div",Oe,[l(w,{label:((ut=e.getVpsOrder)==null?void 0:ut.StatusID)==="Suspended"||((ht=e.getVpsOrder)==null?void 0:ht.StatusID)==="Waiting"?"Оплатить":"Продлить",onClick:i[7]||(i[7]=d=>e.navigateToProlong())},null,8,["label"]),t("label",we,[Ot(t("input",{type:"checkbox","onUpdate:modelValue":i[8]||(i[8]=d=>e.isAutoProlong=d),onInput:i[9]||(i[9]=d=>e.setProlongValue())},null,544),[[Et,e.isAutoProlong]]),Pe,Ce])])])]),t("div",ke,[t("div",ye,[t("div",xe,[Re,l(w,{class:"ml-auto",onClick:i[10]||(i[10]=d=>e.orderManage()),label:"Вход"})]),t("div",Ae,[Be,t("div",Ee,a(`https://${(pt=e.getServerGroup)==null?void 0:pt.Address}/manager`),1)]),t("div",Me,[Ne,t("div",Ue,a((gt=e.getVpsOrder)==null?void 0:gt.Login),1)]),t("div",Ge,[Te,t("div",Le,[e.passwordShow?(g(),S("div",He,a((St=e.getVpsOrder)==null?void 0:St.Password),1)):(g(),S("button",{key:1,class:"btn btn--border btn--md password-box",onClick:i[11]||(i[11]=d=>e.passwordShow=!0)},[l(G),I("Показать")])),t("button",{class:"btn btn--border btn--md hide-button",onClick:i[12]||(i[12]=d=>e.openPasswordChange())},"Сменить пароль")])]),t("div",je,[ze,t("div",Fe,a((It=e.getVpsOrder)!=null&&It.IP?(Dt=e.getVpsOrder)==null?void 0:Dt.IP:"-"),1)])])]),t("div",We,[t("div",Ze,[qe,t("div",Je,[Ke,t("div",Qe,a((bt=e.getVpsOrder)==null?void 0:bt.Ns1Name),1)]),t("div",Xe,[Ye,t("div",$e,a((Vt=e.getVpsOrder)==null?void 0:Vt.Ns2Name),1)])])]),t("div",ts,[t("div",es,[ss,t("div",os,[is,t("div",ns,a((ft=e.getVpsOrder)==null?void 0:ft.Domain),1)])])])])])]),as])):(g(),S("div",rs,[t("div",ls,[l(T,{label:"Заказ не найден"})])]))],64)}const cs={components:{IconCard:qt,IconProfile:Jt,IconSettings:Kt,IconEye:Pt,StatusBadge:Ct,settingsEngineItem:kt,BlockBalanceAgreement:yt},async setup(){const _=Ft(),i=zt(),u=Tt(),e=Wt(),D=Zt(),M=Lt(),O=Ht(),h=L(!1),b=L(!1),m=jt("emitter");m.on("updateVPSIDPage",()=>{_.fetchVPS()});const N=L(!1),n=E(()=>Object.keys(_.vpsList).map(s=>_.vpsList[s]).find(s=>s.OrderID===M.params.id)),v=E(()=>{var s,o;return(o=_.vpsSchemes)==null?void 0:o[(s=n.value)==null?void 0:s.SchemeID]}),U=E(()=>{var s,o;return(o=u==null?void 0:u.userOrders)==null?void 0:o[(s=n.value)==null?void 0:s.OrderID]});function w(){var o;function s(r){h.value=r}m.emit("open-modal",{component:"ReloadVPS",data:{VPSOrderID:(o=n.value)==null?void 0:o.ID,isReloading:h.value,onReloadStatusChange:s}})}function G(s){m.emit("open-modal",{component:"StatusHistory",data:{modeID:"VPSOrders",rowID:s}})}const T=E(()=>{var s,o,r;return(r=(s=D==null?void 0:D.serverGroupsList)==null?void 0:s.Servers)==null?void 0:r[(o=n.value)==null?void 0:o.ServerID]});function P(s){if(!s)return wt(new Date);const o=new Date,r=new Date(o.getTime()+s*24*60*60*1e3);return wt(r)}function C(){var s,o;m.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID}})}function k(){var s;O.push(`/VPSOrders/${(s=n.value)==null?void 0:s.OrderID}/SchemeChange`),p()}function y(){var s;O.push(`/VPSOrderPay/${(s=n.value)==null?void 0:s.OrderID}`)}function x(){var s;u.prolongOrder({IsAutoProlong:b.value?0:1,OrderID:(s=n.value)==null?void 0:s.OrderID})}function p(){var s,o;m.emit("open-modal",{component:"VPSOrderSchemeChange",data:{VpsID:(s=n.value)==null?void 0:s.ID,ServerGroup:(o=v.value)==null?void 0:o.ServersGroupID,emitEvent:"updateVPSIDPage"}})}function R(){var s,o;m.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID,emitEvent:"updateVPSIDPage"}})}function A(){var s,o,r;m.emit("open-modal",{component:"OrderRestore",data:{OrderID:(s=n.value)==null?void 0:s.OrderID,CostDay:(o=v.value)==null?void 0:o.CostDay,DaysRemainded:(r=n.value)==null?void 0:r.DaysRemainded,emitEvent:"updateVPSIDPage"}})}function B(){var s,o,r;e.OrderManage({ServiceOrderID:(s=n.value)==null?void 0:s.ID,OrderID:(o=n.value)==null?void 0:o.OrderID,ServiceID:(r=n.value)==null?void 0:r.ServiceID})}return await _.fetchVPS(),await _.fetchVPSSchemes(),await D.fetchServerGroups(),await i.fetchContracts().then(()=>{M.name==="default.MyVPSOrderSchemeChange"&&p()}),await u.fetchUserOrders().then(()=>{var s;b.value=(s=U.value)==null?void 0:s.IsAutoProlong}),{getVpsOrder:n,passwordShow:N,getVpsScheme:v,isAutoProlong:b,setProlongValue:x,navigateToProlong:y,transferItem:C,vpsStatuses:Xt,openPackageChangeModal:p,openPasswordChange:R,openOrderRestore:A,orderManage:B,calculateExpirationDate:P,changeScheme:k,reloadItem:w,isReloading:h,getServerGroup:T,openHistoryStatuses:G}}},As=Gt(cs,[["render",ds],["__scopeId","data-v-e86e2c17"]]);export{As as default};

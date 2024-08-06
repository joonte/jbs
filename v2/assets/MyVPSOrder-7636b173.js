import{j as xt,o as g,c as S,b as l,a as t,t as a,Z as Ot,ae as Rt,n as At,d as I,w as b,k as Bt,$ as Mt,F as Et,p as Nt,l as Gt,_ as Ut,f as Tt,a7 as Lt,e as Ht,r as L,S as jt,g as E,h as zt}from"./index-83b4b7ba.js";import{u as Ft}from"./contracts-623ec7a8.js";import{u as Wt}from"./vps-0d37158a.js";import{u as Zt}from"./globalActions-633eb799.js";import{u as qt}from"./resetServer-94c466e3.js";import{_ as Jt}from"./IconCard-2cc17a6b.js";import{I as Kt}from"./IconProfile-34c25888.js";import{I as Qt}from"./IconSettings-bac5439e.js";import{_ as Xt,a as Pt}from"./ServicesContractBalanceBlock-627f7a8d.js";import{S as Ct}from"./StatusBadge-0d170ac0.js";import{f as wt}from"./useTimeFunction-8602dd60.js";import{v as Yt}from"./vpsStatuses-32ffaaf5.js";import{s as yt}from"./SettingsEngineItem-a9081d79.js";import{B as kt}from"./BlockBalanceAgreement-c5fad736.js";import{E as $t}from"./EmptyStateBlock-68a1c33e.js";import{_ as te}from"./ButtonDefault-dc248ec6.js";import{z as ee}from"./bootstrap-vue-next.es-8f3ae81a.js";import{_ as se}from"./ClausesKeeper-55c14b7f.js";import{I as oe}from"./IconRotate-e7e6c8d6.js";import"./IconArrow-80c72216.js";import"./IconClose-86aafc4e.js";const d=_=>(Nt("data-v-e597c29b"),_=_(),Gt(),_),ie={key:0,class:"my-vps-order"},ne={class:"section"},ae={class:"container"},re={class:"section-header"},le={class:"section-title"},ce={class:"ico"},de={class:"section-label"},_e={class:"section-nav"},me={class:"list"},ve={class:"list-col list-col--md"},ue={class:"list-item"},he={class:"list-item__row"},pe={class:"list-item__title"},ge={class:"list-item__status"},Se={class:"list-item__row"},Ie={class:"list-item__ul"},De={key:0},Ve={class:"list-item__row list-item__row--column"},fe={class:"list-item__text"},be={class:"list-item__text"},Oe={class:"list-item__text"},we={class:"list-item__row"},Pe={class:"btn btn--border btn--switch"},Ce=d(()=>t("span",{class:"btn-switch__text"},"Автопродление",-1)),ye=d(()=>t("span",{class:"btn-switch__toggle"},null,-1)),ke={class:"list-col list-col--md"},xe={class:"list-item"},Re={class:"list-item__row"},Ae=d(()=>t("div",{class:"list-item__title"},"Панель управления",-1)),Be={class:"list-item__row list-item__row--table"},Me=d(()=>t("div",{class:"list-item__item"},"Адрес",-1)),Ee={class:"list-item__item"},Ne={class:"list-item__row list-item__row--table"},Ge=d(()=>t("div",{class:"list-item__item"},"Логин",-1)),Ue={class:"list-item__item"},Te={class:"list-item__row list-item__row--table password-box"},Le=d(()=>t("div",{class:"list-item__item"},"Пароль",-1)),He={class:"list-item__item"},je={key:0,class:"password-hidden"},ze={class:"list-item__row list-item__row--table"},Fe=d(()=>t("div",{class:"list-item__item"},"IP адрес",-1)),We={class:"list-item__item"},Ze={class:"list-col list-col--md"},qe={class:"list-item"},Je=d(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Именные сервера")],-1)),Ke={class:"list-item__row list-item__row--table"},Qe=d(()=>t("div",{class:"list-item__item"},"Первичный сервер",-1)),Xe={class:"list-item__item"},Ye={class:"list-item__row list-item__row--table"},$e=d(()=>t("div",{class:"list-item__item"},"Вторичный сервер",-1)),ts={class:"list-item__item"},es={class:"list-col list-col--md"},ss={class:"list-item"},os=d(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Паркованный домен")],-1)),is={class:"list-item__row list-item__row--table"},ns=d(()=>t("div",{class:"list-item__item"},"Паркованный домен",-1)),as={class:"list-item__item"},rs=d(()=>t("div",{class:"section"},[t("div",{class:"container"},[t("div",{class:"text"},[t("h3",null,"Что входит в стоимость"),t("p",null,"Для подключения дополнительных услуг"),t("ul",null,[t("li",null,"Размещение в РФ, Москва"),t("li",null,"Предустановленная ОС FreeBSD / CentOS / Debian / Ubuntu / Windows"),t("li",null,"IP адрес, с возможностью расширения"),t("li",null,"Бесплатная панель управления ISPManager Lite"),t("li",null,"Круглосуточная поддержка")])])])],-1)),ls={key:1,class:"section"},cs={class:"container"};function ds(_,i,u,e,D,N){var C,y,k,x,R,p,A,B,M,s,o,r,H,j,z,F,W,Z,q,J,K,Q,X,Y,$,tt,et,st,ot,it,nt,at,rt,lt,ct,dt,_t,mt,vt,ut,ht,pt,gt,St,It,Dt,Vt,ft,bt;const O=kt,h=Ct,V=oe,m=xt("router-link"),G=se,n=Xt,v=ee,U=yt,w=te,T=Pt,P=$t;return g(),S(Et,null,[l(O),(C=e.getVpsOrder)!=null&&C.ID?(g(),S("div",ie,[t("div",ne,[t("div",ae,[t("div",re,[t("h1",le,a((y=e.getVpsOrder)==null?void 0:y.Domain),1),l(h,{status:(k=e.getVpsOrder)==null?void 0:k.StatusID,"status-table":"VPSOrders",onClick:i[0]||(i[0]=c=>{var f;return e.openHistoryStatuses((f=e.getVpsOrder)==null?void 0:f.ID)})},null,8,["status"]),Ot(t("button",{class:At(["btn btn--md btn--border btn--rotate btn--rotate_green button-large",{"btn--rotate_gray":((x=e.getVpsOrder)==null?void 0:x.StatusID)!=="Active"||e.isReloading,"btn--rotate_loading":e.isReloading}]),title:"Перезагрузить выделенный сервер",onClick:i[1]||(i[1]=(...c)=>e.reloadItem&&e.reloadItem(...c))},[I("Включен"),t("div",ce,[l(V)])],2),[[Rt,((R=e.getVpsOrder)==null?void 0:R.StatusID)==="Active"]]),t("div",de,"VPS/VDS сервер # "+a((p=e.getVpsOrder)==null?void 0:p.OrderID),1),t("div",_e,[l(m,{class:"section-nav__item",to:"#"},{default:b(()=>[I("Сервер")]),_:1})])]),l(G),t("div",me,[l(n,{"contract-id":(A=e.getVpsOrder)==null?void 0:A.ContractID,onTransfer:i[2]||(i[2]=c=>e.transferItem())},null,8,["contract-id"]),t("div",ve,[t("div",ue,[t("div",he,[t("div",pe,a((B=e.getVpsScheme)==null?void 0:B.Name),1),t("div",ge,[l(h,{status:(M=e.getVpsOrder)==null?void 0:M.StatusID,"status-table":"VPSOrders",onClick:i[3]||(i[3]=c=>{var f;return e.openHistoryStatuses((f=e.getVpsOrder)==null?void 0:f.ID)})},null,8,["status"])]),l(U,null,{default:b(()=>[l(v,{onClick:i[4]||(i[4]=c=>e.changeScheme())},{default:b(()=>[I("Изменить тариф")]),_:1}),l(v,{onClick:i[5]||(i[5]=c=>e.openOrderRestore())},{default:b(()=>[I("Просмотреть учет заказа")]),_:1}),l(v,{onClick:i[6]||(i[6]=c=>e.openPasswordChange())},{default:b(()=>[I("Сменить пароль от аккаунта")]),_:1})]),_:1})]),t("div",Se,[t("ul",Ie,[(r=(o=(s=e.getVpsScheme)==null?void 0:s.SchemeParams)==null?void 0:o.os)!=null&&r.value?(g(),S("li",De,a((z=(j=(H=e.getVpsScheme)==null?void 0:H.SchemeParams)==null?void 0:j.os)==null?void 0:z.value),1)):Bt("",!0),t("li",null,a(`${(Z=(W=(F=e.getVpsScheme)==null?void 0:F.SchemeParams)==null?void 0:W.hdd_mib)==null?void 0:Z.InternalName} ${(K=(J=(q=e.getVpsScheme)==null?void 0:q.SchemeParams)==null?void 0:J.hdd_mib)==null?void 0:K.Value} ${(Y=(X=(Q=e.getVpsScheme)==null?void 0:Q.SchemeParams)==null?void 0:X.hdd_mib)==null?void 0:Y.Unit}`),1),t("li",null,a(`RAM ${(et=(tt=($=e.getVpsScheme)==null?void 0:$.SchemeParams)==null?void 0:tt.ram_mib)==null?void 0:et.Value} ${(it=(ot=(st=e.getVpsScheme)==null?void 0:st.SchemeParams)==null?void 0:ot.ram_mib)==null?void 0:it.Unit}`),1),t("li",null,a(`Канал ${(rt=(at=(nt=e.getVpsScheme)==null?void 0:nt.SchemeParams)==null?void 0:at.net_bandwidth_mbitps)==null?void 0:rt.Value} ${(dt=(ct=(lt=e.getVpsScheme)==null?void 0:lt.SchemeParams)==null?void 0:ct.net_bandwidth_mbitps)==null?void 0:dt.Unit}`),1)])]),t("div",Ve,[t("div",fe,a((_t=e.getVpsScheme)==null?void 0:_t.CostMonth)+" ₽ в месяц",1),t("div",be,"Дней осталось "+a((mt=e.getVpsOrder)==null?void 0:mt.DaysRemainded),1),t("div",Oe,"Дата окончания "+a(e.calculateExpirationDate((vt=e.getVpsOrder)==null?void 0:vt.DaysRemainded)),1)]),t("div",we,[l(w,{label:((ut=e.getVpsOrder)==null?void 0:ut.StatusID)==="Suspended"||((ht=e.getVpsOrder)==null?void 0:ht.StatusID)==="Waiting"?"Оплатить":"Продлить",onClick:i[7]||(i[7]=c=>e.navigateToProlong())},null,8,["label"]),t("label",Pe,[Ot(t("input",{type:"checkbox","onUpdate:modelValue":i[8]||(i[8]=c=>e.isAutoProlong=c),onInput:i[9]||(i[9]=c=>e.setProlongValue())},null,544),[[Mt,e.isAutoProlong]]),Ce,ye])])])]),t("div",ke,[t("div",xe,[t("div",Re,[Ae,l(w,{class:"ml-auto",onClick:i[10]||(i[10]=c=>e.orderManage()),label:"Вход"})]),t("div",Be,[Me,t("div",Ee,a(`https://${(pt=e.getServerGroup)==null?void 0:pt.Address}/manager`),1)]),t("div",Ne,[Ge,t("div",Ue,a((gt=e.getVpsOrder)==null?void 0:gt.Login),1)]),t("div",Te,[Le,t("div",He,[e.passwordShow?(g(),S("div",je,a((St=e.getVpsOrder)==null?void 0:St.Password),1)):(g(),S("button",{key:1,class:"btn btn--border btn--md password-box",onClick:i[11]||(i[11]=c=>e.passwordShow=!0)},[l(T),I("Показать")]))])]),t("div",ze,[Fe,t("div",We,a((It=e.getVpsOrder)!=null&&It.IP?(Dt=e.getVpsOrder)==null?void 0:Dt.IP:"-"),1)])])]),t("div",Ze,[t("div",qe,[Je,t("div",Ke,[Qe,t("div",Xe,a((Vt=e.getVpsOrder)==null?void 0:Vt.Ns1Name),1)]),t("div",Ye,[$e,t("div",ts,a((ft=e.getVpsOrder)==null?void 0:ft.Ns2Name),1)])])]),t("div",es,[t("div",ss,[os,t("div",is,[ns,t("div",as,a((bt=e.getVpsOrder)==null?void 0:bt.Domain),1)])])])])])]),rs])):(g(),S("div",ls,[t("div",cs,[l(P,{label:"Заказ не найден"})])]))],64)}const _s={components:{IconCard:Jt,IconProfile:Kt,IconSettings:Qt,IconEye:Pt,StatusBadge:Ct,settingsEngineItem:yt,BlockBalanceAgreement:kt},async setup(){const _=Wt(),i=Ft(),u=Tt(),e=Zt(),D=qt(),N=Lt(),O=Ht(),h=L(!1),V=L(!1),m=jt("emitter");m.on("updateVPSIDPage",()=>{_.fetchVPS()});const G=L(!1),n=E(()=>Object.keys(_.vpsList).map(s=>_.vpsList[s]).find(s=>s.OrderID===N.params.id)),v=E(()=>{var s,o;return(o=_.vpsSchemes)==null?void 0:o[(s=n.value)==null?void 0:s.SchemeID]}),U=E(()=>{var s,o;return(o=u==null?void 0:u.userOrders)==null?void 0:o[(s=n.value)==null?void 0:s.OrderID]});function w(){var o;function s(r){h.value=r}m.emit("open-modal",{component:"ReloadVPS",data:{VPSOrderID:(o=n.value)==null?void 0:o.ID,isReloading:h.value,onReloadStatusChange:s}})}function T(s){m.emit("open-modal",{component:"StatusHistory",data:{modeID:"VPSOrders",rowID:s}})}const P=E(()=>{var s,o,r;return(r=(s=D==null?void 0:D.serverGroupsList)==null?void 0:s.Servers)==null?void 0:r[(o=n.value)==null?void 0:o.ServerID]});zt(()=>{console.log("getServerGroup: ",P)});function C(s){if(!s)return wt(new Date);const o=new Date,r=new Date(o.getTime()+s*24*60*60*1e3);return wt(r)}function y(){var s,o;m.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID}})}function k(){var s;O.push(`/VPSOrders/${(s=n.value)==null?void 0:s.OrderID}/SchemeChange`),p()}function x(){var s;O.push(`/VPSOrderPay/${(s=n.value)==null?void 0:s.OrderID}`)}function R(){var s;u.prolongOrder({IsAutoProlong:V.value?0:1,OrderID:(s=n.value)==null?void 0:s.OrderID})}function p(){var s,o;m.emit("open-modal",{component:"VPSOrderSchemeChange",data:{VpsID:(s=n.value)==null?void 0:s.ID,ServerGroup:(o=v.value)==null?void 0:o.ServersGroupID,emitEvent:"updateVPSIDPage"}})}function A(){var s,o;m.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID,emitEvent:"updateVPSIDPage"}})}function B(){var s,o,r;m.emit("open-modal",{component:"OrderRestore",data:{OrderID:(s=n.value)==null?void 0:s.OrderID,CostDay:(o=v.value)==null?void 0:o.CostDay,DaysRemainded:(r=n.value)==null?void 0:r.DaysRemainded,emitEvent:"updateVPSIDPage"}})}function M(){var s,o,r;e.OrderManage({ServiceOrderID:(s=n.value)==null?void 0:s.ID,OrderID:(o=n.value)==null?void 0:o.OrderID,ServiceID:(r=n.value)==null?void 0:r.ServiceID})}return await _.fetchVPS(),await _.fetchVPSSchemes(),await D.fetchServerGroups(),await i.fetchContracts().then(()=>{N.name==="default.MyVPSOrderSchemeChange"&&p()}),await u.fetchUserOrders().then(()=>{var s;V.value=(s=U.value)==null?void 0:s.IsAutoProlong}),{getVpsOrder:n,passwordShow:G,getVpsScheme:v,isAutoProlong:V,setProlongValue:R,navigateToProlong:x,transferItem:y,vpsStatuses:Yt,openPackageChangeModal:p,openPasswordChange:A,openOrderRestore:B,orderManage:M,calculateExpirationDate:C,changeScheme:k,reloadItem:w,isReloading:h,getServerGroup:P,openHistoryStatuses:T}}},Bs=Ut(_s,[["render",ds],["__scopeId","data-v-e597c29b"]]);export{Bs as default};

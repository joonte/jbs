import{j as yt,o as g,c as S,b as l,a as t,t as a,Y as bt,ae as Rt,n as xt,d as I,w as V,k as At,Z as Bt,F as Mt,p as Et,l as Nt,_ as Gt,f as Ut,a7 as Tt,e as Lt,r as U,R as jt,g as B,i as zt}from"./index-58c913f0.js";import{u as Ft}from"./contracts-5c545170.js";import{u as Wt}from"./vps-5e51a184.js";import{u as Yt}from"./globalActions-45df61a2.js";import{u as Zt}from"./resetServer-f032e468.js";import{_ as qt}from"./IconCard-94a8b1b0.js";import{I as Ht}from"./IconProfile-dd717302.js";import{I as Jt}from"./IconSettings-0cf0fa33.js";import{I as Ot}from"./IconEye-5deceda6.js";import{S as Pt}from"./StatusBadge-cd8ad979.js";import{f as wt}from"./useTimeFunction-8602dd60.js";import{v as Kt}from"./vpsStatuses-32ffaaf5.js";import{s as Ct}from"./SettingsEngineItem-7706180f.js";import{B as kt}from"./BlockBalanceAgreement-59500b1f.js";import{E as Qt}from"./EmptyStateBlock-e5319013.js";import{_ as Xt}from"./ButtonDefault-7a589010.js";import{z as $t}from"./bootstrap-vue-next.es-9015192b.js";import{_ as te}from"./ServicesContractBalanceBlock-a4202c70.js";import{_ as ee}from"./ClausesKeeper-7b7ef561.js";import{I as se}from"./IconRotate-53e3b019.js";import"./IconArrow-bff54646.js";import"./IconClose-3552080e.js";const c=_=>(Et("data-v-29851ac0"),_=_(),Nt(),_),oe={key:0,class:"my-vps-order"},ie={class:"section"},ne={class:"container"},ae={class:"section-header"},re={class:"section-title"},le={class:"ico"},ce={class:"section-label"},_e={class:"section-nav"},de={class:"list"},me={class:"list-col list-col--md"},ve={class:"list-item"},ue={class:"list-item__row"},he={class:"list-item__title"},pe={class:"list-item__status"},ge={class:"list-item__row"},Se={class:"list-item__ul"},Ie={key:0},fe={class:"list-item__row list-item__row--column"},De={class:"list-item__text"},Ve={class:"list-item__text"},be={class:"list-item__text"},we={class:"list-item__row"},Oe={class:"btn btn--border btn--switch"},Pe=c(()=>t("span",{class:"btn-switch__text"},"Автопродление",-1)),Ce=c(()=>t("span",{class:"btn-switch__toggle"},null,-1)),ke={class:"list-col list-col--md"},ye={class:"list-item"},Re={class:"list-item__row"},xe=c(()=>t("div",{class:"list-item__title"},"Панель управления",-1)),Ae={class:"list-item__row list-item__row--table"},Be=c(()=>t("div",{class:"list-item__item"},"Адрес",-1)),Me={class:"list-item__item"},Ee={class:"list-item__row list-item__row--table"},Ne=c(()=>t("div",{class:"list-item__item"},"Логин",-1)),Ge={class:"list-item__item"},Ue={class:"list-item__row list-item__row--table password-box"},Te=c(()=>t("div",{class:"list-item__item"},"Пароль",-1)),Le={class:"list-item__item"},je={key:0,class:"password-hidden"},ze={class:"list-item__row list-item__row--table"},Fe=c(()=>t("div",{class:"list-item__item"},"IP адрес",-1)),We={class:"list-item__item"},Ye={class:"list-col list-col--md"},Ze={class:"list-item"},qe=c(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Именные сервера")],-1)),He={class:"list-item__row list-item__row--table"},Je=c(()=>t("div",{class:"list-item__item"},"Первичный сервер",-1)),Ke={class:"list-item__item"},Qe={class:"list-item__row list-item__row--table"},Xe=c(()=>t("div",{class:"list-item__item"},"Вторичный сервер",-1)),$e={class:"list-item__item"},ts={class:"list-col list-col--md"},es={class:"list-item"},ss=c(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Паркованный домен")],-1)),os={class:"list-item__row list-item__row--table"},is=c(()=>t("div",{class:"list-item__item"},"Паркованный домен",-1)),ns={class:"list-item__item"},as=c(()=>t("div",{class:"section"},[t("div",{class:"container"},[t("div",{class:"text"},[t("h3",null,"Что входит в стоимость"),t("p",null,"Для подключения дополнительных услуг"),t("ul",null,[t("li",null,"Размещение в РФ, Москва"),t("li",null,"Предустановленная ОС FreeBSD / CentOS / Debian / Ubuntu / Windows"),t("li",null,"IP адрес, с возможностью расширения"),t("li",null,"Бесплатная панель управления ISPManager Lite"),t("li",null,"Круглосуточная поддержка")])])])],-1)),rs={key:1,class:"section"},ls={class:"container"};function cs(_,i,u,e,f,M){var P,C,k,y,p,R,x,A,s,o,r,T,L,j,z,F,W,Y,Z,q,H,J,K,Q,X,$,tt,et,st,ot,it,nt,at,rt,lt,ct,_t,dt,mt,vt,ut,ht,pt,gt,St,It,ft,Dt,Vt;const b=kt,h=Pt,D=se,m=yt("router-link"),E=ee,n=te,v=$t,N=Ct,w=Xt,O=Ot,G=Qt;return g(),S(Mt,null,[l(b),(P=e.getVpsOrder)!=null&&P.ID?(g(),S("div",oe,[t("div",ie,[t("div",ne,[t("div",ae,[t("h1",re,a((C=e.getVpsOrder)==null?void 0:C.Domain),1),l(h,{status:(k=e.getVpsOrder)==null?void 0:k.StatusID,"status-table":"VPSOrders"},null,8,["status"]),bt(t("button",{class:xt(["btn btn--md btn--border btn--rotate btn--rotate_green button-large",{"btn--rotate_gray":((y=e.getVpsOrder)==null?void 0:y.StatusID)!=="Active"||e.isReloading,"btn--rotate_loading":e.isReloading}]),title:"Перезагрузить выделенный сервер",onClick:i[0]||(i[0]=(...d)=>e.reloadItem&&e.reloadItem(...d))},[I("Включен"),t("div",le,[l(D)])],2),[[Rt,((p=e.getVpsOrder)==null?void 0:p.StatusID)==="Active"]]),t("div",ce,"VPS/VDS сервер # "+a((R=e.getVpsOrder)==null?void 0:R.OrderID),1),t("div",_e,[l(m,{class:"section-nav__item",to:"#"},{default:V(()=>[I("Сервер")]),_:1})])]),l(E),t("div",de,[l(n,{"contract-id":(x=e.getVpsOrder)==null?void 0:x.ContractID,onTransfer:i[1]||(i[1]=d=>e.transferItem())},null,8,["contract-id"]),t("div",me,[t("div",ve,[t("div",ue,[t("div",he,a((A=e.getVpsScheme)==null?void 0:A.Name),1),t("div",pe,[l(h,{status:(s=e.getVpsOrder)==null?void 0:s.StatusID,"status-table":"VPSOrders"},null,8,["status"])]),l(N,null,{default:V(()=>[l(v,{onClick:i[2]||(i[2]=d=>e.changeScheme())},{default:V(()=>[I("Изменить тариф")]),_:1}),l(v,{onClick:i[3]||(i[3]=d=>e.openOrderRestore())},{default:V(()=>[I("Просмотреть учет заказа")]),_:1}),l(v,{onClick:i[4]||(i[4]=d=>e.openPasswordChange())},{default:V(()=>[I("Сменить пароль от аккаунта")]),_:1})]),_:1})]),t("div",ge,[t("ul",Se,[(T=(r=(o=e.getVpsScheme)==null?void 0:o.SchemeParams)==null?void 0:r.os)!=null&&T.value?(g(),S("li",Ie,a((z=(j=(L=e.getVpsScheme)==null?void 0:L.SchemeParams)==null?void 0:j.os)==null?void 0:z.value),1)):At("",!0),t("li",null,a(`${(Y=(W=(F=e.getVpsScheme)==null?void 0:F.SchemeParams)==null?void 0:W.hdd_mib)==null?void 0:Y.InternalName} ${(H=(q=(Z=e.getVpsScheme)==null?void 0:Z.SchemeParams)==null?void 0:q.hdd_mib)==null?void 0:H.Value} ${(Q=(K=(J=e.getVpsScheme)==null?void 0:J.SchemeParams)==null?void 0:K.hdd_mib)==null?void 0:Q.Unit}`),1),t("li",null,a(`RAM ${(tt=($=(X=e.getVpsScheme)==null?void 0:X.SchemeParams)==null?void 0:$.ram_mib)==null?void 0:tt.Value} ${(ot=(st=(et=e.getVpsScheme)==null?void 0:et.SchemeParams)==null?void 0:st.ram_mib)==null?void 0:ot.Unit}`),1),t("li",null,a(`Канал ${(at=(nt=(it=e.getVpsScheme)==null?void 0:it.SchemeParams)==null?void 0:nt.net_bandwidth_mbitps)==null?void 0:at.Value} ${(ct=(lt=(rt=e.getVpsScheme)==null?void 0:rt.SchemeParams)==null?void 0:lt.net_bandwidth_mbitps)==null?void 0:ct.Unit}`),1)])]),t("div",fe,[t("div",De,a((_t=e.getVpsScheme)==null?void 0:_t.CostMonth)+" ₽ в месяц",1),t("div",Ve,"Дней осталось "+a((dt=e.getVpsOrder)==null?void 0:dt.DaysRemainded),1),t("div",be,"Дата окончания "+a(e.calculateExpirationDate((mt=e.getVpsOrder)==null?void 0:mt.DaysRemainded)),1)]),t("div",we,[l(w,{label:((vt=e.getVpsOrder)==null?void 0:vt.StatusID)==="Suspended"||((ut=e.getVpsOrder)==null?void 0:ut.StatusID)==="Waiting"?"Оплатить":"Продлить",onClick:i[5]||(i[5]=d=>e.navigateToProlong())},null,8,["label"]),t("label",Oe,[bt(t("input",{type:"checkbox","onUpdate:modelValue":i[6]||(i[6]=d=>e.isAutoProlong=d),onInput:i[7]||(i[7]=d=>e.setProlongValue())},null,544),[[Bt,e.isAutoProlong]]),Pe,Ce])])])]),t("div",ke,[t("div",ye,[t("div",Re,[xe,l(w,{class:"ml-auto",onClick:i[8]||(i[8]=d=>e.orderManage()),label:"Вход"})]),t("div",Ae,[Be,t("div",Me,a(`https://${(ht=e.getServerGroup)==null?void 0:ht.Address}/manager`),1)]),t("div",Ee,[Ne,t("div",Ge,a((pt=e.getVpsOrder)==null?void 0:pt.Login),1)]),t("div",Ue,[Te,t("div",Le,[e.passwordShow?(g(),S("div",je,a((gt=e.getVpsOrder)==null?void 0:gt.Password),1)):(g(),S("button",{key:1,class:"btn btn--border btn--md password-box",onClick:i[9]||(i[9]=d=>e.passwordShow=!0)},[l(O),I("Показать")]))])]),t("div",ze,[Fe,t("div",We,a((St=e.getVpsOrder)!=null&&St.IP?(It=e.getVpsOrder)==null?void 0:It.IP:"-"),1)])])]),t("div",Ye,[t("div",Ze,[qe,t("div",He,[Je,t("div",Ke,a((ft=e.getVpsOrder)==null?void 0:ft.Ns1Name),1)]),t("div",Qe,[Xe,t("div",$e,a((Dt=e.getVpsOrder)==null?void 0:Dt.Ns2Name),1)])])]),t("div",ts,[t("div",es,[ss,t("div",os,[is,t("div",ns,a((Vt=e.getVpsOrder)==null?void 0:Vt.Domain),1)])])])])])]),as])):(g(),S("div",rs,[t("div",ls,[l(G,{label:"Заказ не найден"})])]))],64)}const _s={components:{IconCard:qt,IconProfile:Ht,IconSettings:Jt,IconEye:Ot,StatusBadge:Pt,settingsEngineItem:Ct,BlockBalanceAgreement:kt},async setup(){const _=Wt(),i=Ft(),u=Ut(),e=Yt(),f=Zt(),M=Tt(),b=Lt(),h=U(!1),D=U(!1),m=jt("emitter");m.on("updateVPSIDPage",()=>{_.fetchVPS()});const E=U(!1),n=B(()=>Object.keys(_.vpsList).map(s=>_.vpsList[s]).find(s=>s.OrderID===M.params.id)),v=B(()=>{var s,o;return(o=_.vpsSchemes)==null?void 0:o[(s=n.value)==null?void 0:s.SchemeID]}),N=B(()=>{var s,o;return(o=u==null?void 0:u.userOrders)==null?void 0:o[(s=n.value)==null?void 0:s.OrderID]});function w(){var o;function s(r){h.value=r}m.emit("open-modal",{component:"ReloadVPS",data:{VPSOrderID:(o=n.value)==null?void 0:o.ID,isReloading:h.value,onReloadStatusChange:s}})}const O=B(()=>{var s,o,r;return(r=(s=f==null?void 0:f.serverGroupsList)==null?void 0:s.Servers)==null?void 0:r[(o=n.value)==null?void 0:o.ServerID]});zt(()=>{console.log("getServerGroup: ",O)});function G(s){if(!s)return wt(new Date);const o=new Date,r=new Date(o.getTime()+s*24*60*60*1e3);return wt(r)}function P(){var s,o;m.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID}})}function C(){var s;b.push(`/VPSOrders/${(s=n.value)==null?void 0:s.OrderID}/SchemeChange`),p()}function k(){var s;b.push(`/VPSOrderPay/${(s=n.value)==null?void 0:s.OrderID}`)}function y(){var s;u.prolongOrder({IsAutoProlong:D.value?0:1,OrderID:(s=n.value)==null?void 0:s.OrderID})}function p(){var s,o;m.emit("open-modal",{component:"VPSOrderSchemeChange",data:{VpsID:(s=n.value)==null?void 0:s.ID,ServerGroup:(o=v.value)==null?void 0:o.ServersGroupID,emitEvent:"updateVPSIDPage"}})}function R(){var s,o;m.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID,emitEvent:"updateVPSIDPage"}})}function x(){var s,o,r;m.emit("open-modal",{component:"OrderRestore",data:{OrderID:(s=n.value)==null?void 0:s.OrderID,CostDay:(o=v.value)==null?void 0:o.CostDay,DaysRemainded:(r=n.value)==null?void 0:r.DaysRemainded,emitEvent:"updateVPSIDPage"}})}function A(){var s,o,r;e.OrderManage({ServiceOrderID:(s=n.value)==null?void 0:s.ID,OrderID:(o=n.value)==null?void 0:o.OrderID,ServiceID:(r=n.value)==null?void 0:r.ServiceID})}return await _.fetchVPS(),await _.fetchVPSSchemes(),await f.fetchServerGroups(),await i.fetchContracts().then(()=>{M.name==="default.MyVPSOrderSchemeChange"&&p()}),await u.fetchUserOrders().then(()=>{var s;D.value=(s=N.value)==null?void 0:s.IsAutoProlong}),{getVpsOrder:n,passwordShow:E,getVpsScheme:v,isAutoProlong:D,setProlongValue:y,navigateToProlong:k,transferItem:P,vpsStatuses:Kt,openPackageChangeModal:p,openPasswordChange:R,openOrderRestore:x,orderManage:A,calculateExpirationDate:G,changeScheme:C,reloadItem:w,isReloading:h,getServerGroup:O}}},Bs=Gt(_s,[["render",cs],["__scopeId","data-v-29851ac0"]]);export{Bs as default};
import{j as Pt,o as h,c as p,b as r,a as t,t as a,Y as bt,ae as kt,d as u,w as b,k as yt,Z as Ct,F as xt,p as At,l as Bt,_ as Rt,f as Mt,a7 as Nt,e as Et,r as ft,R as Ut,g as C}from"./index-eaeb1281.js";import{u as Tt}from"./contracts-ac470ffa.js";import{u as Lt}from"./vps-b95bdc06.js";import{u as jt}from"./globalActions-08c4f434.js";import{_ as Ft}from"./IconCard-6f1a0464.js";import{I as Gt}from"./IconProfile-d6caea69.js";import{I as zt}from"./IconSettings-cd8e71ea.js";import{I as Vt}from"./IconEye-054b5c41.js";import{S as wt}from"./StatusBadge-e3643114.js";import{v as Wt}from"./vpsStatuses-32ffaaf5.js";import{s as Dt}from"./SettingsEngineItem-dee737e0.js";import{B as Ot}from"./BlockBalanceAgreement-11afb494.js";import{E as Yt}from"./EmptyStateBlock-5faddaec.js";import{_ as Zt}from"./ButtonDefault-598fef28.js";import{z as qt}from"./bootstrap-vue-next.es-f4c478af.js";import{_ as Ht}from"./ServicesContractBalanceBlock-dae08a80.js";import{_ as Jt}from"./ClausesKeeper-a152fc61.js";import{I as Kt}from"./IconRotate-d8606eae.js";import"./IconArrow-ce1bc7b6.js";import"./IconClose-e8029cfe.js";const l=_=>(At("data-v-fc934f80"),_=_(),Bt(),_),Qt={key:0,class:"my-vps-order"},Xt={class:"section"},$t={class:"container"},te={class:"section-header"},ee={class:"section-title"},se={class:"btn btn--md btn--border btn--rotate btn--rotate_green button-large"},oe={class:"ico"},ie={class:"section-label"},ne={class:"section-nav"},ae={class:"list"},re={class:"list-col list-col--md"},le={class:"list-item"},_e={class:"list-item__row"},ce={class:"list-item__title"},de={class:"list-item__status"},me={class:"list-item__row"},ve={class:"list-item__ul"},he={key:0},pe={class:"list-item__row list-item__row--column"},ue={class:"list-item__text"},ge={class:"list-item__text"},Se={class:"list-item__row"},Ie={class:"btn btn--border btn--switch"},be=l(()=>t("span",{class:"btn-switch__text"},"Автопродление",-1)),fe=l(()=>t("span",{class:"btn-switch__toggle"},null,-1)),Ve={class:"list-col list-col--md"},we={class:"list-item"},De={class:"list-item__row"},Oe=l(()=>t("div",{class:"list-item__title"},"Панель управления",-1)),Pe={class:"list-item__row list-item__row--table"},ke=l(()=>t("div",{class:"list-item__item"},"Адрес",-1)),ye={class:"list-item__item"},Ce={class:"list-item__row list-item__row--table"},xe=l(()=>t("div",{class:"list-item__item"},"Логин",-1)),Ae={class:"list-item__item"},Be={class:"list-item__row list-item__row--table password-box"},Re=l(()=>t("div",{class:"list-item__item"},"Пароль",-1)),Me={class:"list-item__item"},Ne={key:0,class:"password-hidden"},Ee={class:"list-item__row list-item__row--table"},Ue=l(()=>t("div",{class:"list-item__item"},"IP адрес",-1)),Te={class:"list-item__item"},Le={class:"list-col list-col--md"},je={class:"list-item"},Fe=l(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Именные сервера")],-1)),Ge={class:"list-item__row list-item__row--table"},ze=l(()=>t("div",{class:"list-item__item"},"Первичный сервер",-1)),We={class:"list-item__item"},Ye={class:"list-item__row list-item__row--table"},Ze=l(()=>t("div",{class:"list-item__item"},"Вторичный сервер",-1)),qe={class:"list-item__item"},He={class:"list-col list-col--md"},Je={class:"list-item"},Ke=l(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Паркованный домен")],-1)),Qe={class:"list-item__row list-item__row--table"},Xe=l(()=>t("div",{class:"list-item__item"},"Паркованный домен",-1)),$e={class:"list-item__item"},ts=l(()=>t("div",{class:"section"},[t("div",{class:"container"},[t("div",{class:"text"},[t("h3",null,"Что входит в стоимость"),t("p",null,"Для подключения дополнительных услуг"),t("ul",null,[t("li",null,"Размещение в РФ, Москва"),t("li",null,"Предустановленная ОС FreeBSD / CentOS / Debian / Ubuntu / Windows"),t("li",null,"IP адрес, с возможностью расширения"),t("li",null,"Бесплатная панель управления ISPManager Lite"),t("li",null,"Круглосуточная поддержка")])])])],-1)),es={key:1,class:"section"},ss={class:"container"};function os(_,i,v,e,x,A){var V,w,s,o,m,B,R,M,N,E,U,T,L,j,F,G,z,W,Y,Z,q,H,J,K,Q,X,$,tt,et,st,ot,it,nt,at,rt,lt,_t,ct,dt,mt,vt,ht,pt,ut,gt,St,It;const g=Ot,d=wt,D=Kt,n=Pt("router-link"),S=Jt,O=Ht,I=qt,P=Dt,f=Zt,k=Vt,y=Yt;return h(),p(xt,null,[r(g),(V=e.getVpsOrder)!=null&&V.ID?(h(),p("div",Qt,[t("div",Xt,[t("div",$t,[t("div",te,[t("h1",ee,a((w=e.getVpsOrder)==null?void 0:w.Domain),1),r(d,{status:(s=e.getVpsOrder)==null?void 0:s.StatusID,"status-table":"VPSOrders"},null,8,["status"]),bt(t("button",se,[u("Включен"),t("div",oe,[r(D)])],512),[[kt,((o=e.getVpsOrder)==null?void 0:o.StatusID)==="Active"]]),t("div",ie,"VPS/VDS сервер # "+a((m=e.getVpsOrder)==null?void 0:m.OrderID),1),t("div",ne,[r(n,{class:"section-nav__item",to:"#"},{default:b(()=>[u("Сервер")]),_:1})])]),r(S),t("div",ae,[r(O,{"contract-id":(B=e.getVpsOrder)==null?void 0:B.ContractID,onTransfer:i[0]||(i[0]=c=>e.transferItem())},null,8,["contract-id"]),t("div",re,[t("div",le,[t("div",_e,[t("div",ce,a((R=e.getVpsScheme)==null?void 0:R.Name),1),t("div",de,[r(d,{status:(M=e.getVpsOrder)==null?void 0:M.StatusID,"status-table":"VPSOrders"},null,8,["status"])]),r(P,null,{default:b(()=>[r(I,{onClick:i[1]||(i[1]=c=>e.openPackageChangeModal())},{default:b(()=>[u("Изменить тариф")]),_:1}),r(I,{onClick:i[2]||(i[2]=c=>e.openOrderRestore())},{default:b(()=>[u("Просмотреть учет заказа")]),_:1}),r(I,{onClick:i[3]||(i[3]=c=>e.openPasswordChange())},{default:b(()=>[u("Сменить пароль от аккаунта")]),_:1})]),_:1})]),t("div",me,[t("ul",ve,[(U=(E=(N=e.getVpsScheme)==null?void 0:N.SchemeParams)==null?void 0:E.os)!=null&&U.value?(h(),p("li",he,a((j=(L=(T=e.getVpsScheme)==null?void 0:T.SchemeParams)==null?void 0:L.os)==null?void 0:j.value),1)):yt("",!0),t("li",null,a(`${(z=(G=(F=e.getVpsScheme)==null?void 0:F.SchemeParams)==null?void 0:G.hdd_mib)==null?void 0:z.InternalName} ${(Z=(Y=(W=e.getVpsScheme)==null?void 0:W.SchemeParams)==null?void 0:Y.hdd_mib)==null?void 0:Z.Value} ${(J=(H=(q=e.getVpsScheme)==null?void 0:q.SchemeParams)==null?void 0:H.hdd_mib)==null?void 0:J.Unit}`),1),t("li",null,a(`RAM ${(X=(Q=(K=e.getVpsScheme)==null?void 0:K.SchemeParams)==null?void 0:Q.ram_mib)==null?void 0:X.Value} ${(et=(tt=($=e.getVpsScheme)==null?void 0:$.SchemeParams)==null?void 0:tt.ram_mib)==null?void 0:et.Unit}`),1),t("li",null,a(`Канал ${(it=(ot=(st=e.getVpsScheme)==null?void 0:st.SchemeParams)==null?void 0:ot.net_bandwidth_mbitps)==null?void 0:it.Value} ${(rt=(at=(nt=e.getVpsScheme)==null?void 0:nt.SchemeParams)==null?void 0:at.net_bandwidth_mbitps)==null?void 0:rt.Unit}`),1)])]),t("div",pe,[t("div",ue,a((lt=e.getVpsScheme)==null?void 0:lt.CostMonth)+" ₽ в месяц",1),t("div",ge,"Дней осталось "+a((_t=e.getVpsOrder)==null?void 0:_t.DaysRemainded),1)]),t("div",Se,[r(f,{label:((ct=e.getVpsOrder)==null?void 0:ct.StatusID)==="Suspended"||((dt=e.getVpsOrder)==null?void 0:dt.StatusID)==="Waiting"?"Оплатить":"Продлить",onClick:i[4]||(i[4]=c=>e.navigateToProlong())},null,8,["label"]),t("label",Ie,[bt(t("input",{type:"checkbox","onUpdate:modelValue":i[5]||(i[5]=c=>e.isAutoProlong=c),onInput:i[6]||(i[6]=c=>e.setProlongValue())},null,544),[[Ct,e.isAutoProlong]]),be,fe])])])]),t("div",Ve,[t("div",we,[t("div",De,[Oe,r(f,{class:"ml-auto",onClick:i[7]||(i[7]=c=>e.orderManage()),label:"Вход"})]),t("div",Pe,[ke,t("div",ye,a(`https://${(mt=_.getServerGroup)==null?void 0:mt.Address}/manager`),1)]),t("div",Ce,[xe,t("div",Ae,a((vt=e.getVpsOrder)==null?void 0:vt.Login),1)]),t("div",Be,[Re,t("div",Me,[e.passwordShow?(h(),p("div",Ne,a((ht=e.getVpsOrder)==null?void 0:ht.Password),1)):(h(),p("button",{key:1,class:"btn btn--border btn--md password-box",onClick:i[8]||(i[8]=c=>e.passwordShow=!0)},[r(k),u("Показать")]))])]),t("div",Ee,[Ue,t("div",Te,a((pt=e.getVpsOrder)!=null&&pt.IP?(ut=e.getVpsOrder)==null?void 0:ut.IP:"-"),1)])])]),t("div",Le,[t("div",je,[Fe,t("div",Ge,[ze,t("div",We,a((gt=e.getVpsOrder)==null?void 0:gt.Ns1Name),1)]),t("div",Ye,[Ze,t("div",qe,a((St=e.getVpsOrder)==null?void 0:St.Ns2Name),1)])])]),t("div",He,[t("div",Je,[Ke,t("div",Qe,[Xe,t("div",$e,a((It=e.getVpsOrder)==null?void 0:It.Domain),1)])])])])])]),ts])):(h(),p("div",es,[t("div",ss,[r(y,{label:"Заказ не найден"})])]))],64)}const is={components:{IconCard:Ft,IconProfile:Gt,IconSettings:zt,IconEye:Vt,StatusBadge:wt,settingsEngineItem:Dt,BlockBalanceAgreement:Ot},async setup(){const _=Lt(),i=Tt(),v=Mt(),e=jt(),x=Nt(),A=Et(),g=ft(!1),d=Ut("emitter");d.on("updateVPSIDPage",()=>{_.fetchVPS()});const D=ft(!1),n=C(()=>Object.keys(_.vpsList).map(s=>_.vpsList[s]).find(s=>s.OrderID===x.params.id)),S=C(()=>{var s,o;return(o=_.vpsSchemes)==null?void 0:o[(s=n.value)==null?void 0:s.SchemeID]}),O=C(()=>{var s,o;return(o=v==null?void 0:v.userOrders)==null?void 0:o[(s=n.value)==null?void 0:s.OrderID]});function I(){var s,o;d.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID}})}function P(){var s;A.push(`/VPSOrderPay/${(s=n.value)==null?void 0:s.OrderID}`)}function f(){var s;v.prolongOrder({IsAutoProlong:g.value?0:1,OrderID:(s=n.value)==null?void 0:s.OrderID})}function k(){var s,o;d.emit("open-modal",{component:"VPSOrderSchemeChange",data:{VpsID:(s=n.value)==null?void 0:s.ID,ServerGroup:(o=S.value)==null?void 0:o.ServersGroupID,emitEvent:"updateVPSIDPage"}})}function y(){var s,o;d.emit("open-modal",{component:"OrderPasswordChange",data:{OrderID:(s=n.value)==null?void 0:s.ID,ServiceID:(o=n.value)==null?void 0:o.ServiceID,emitEvent:"updateVPSIDPage"}})}function V(){var s,o,m;d.emit("open-modal",{component:"OrderRestore",data:{OrderID:(s=n.value)==null?void 0:s.OrderID,CostDay:(o=S.value)==null?void 0:o.CostDay,DaysRemainded:(m=n.value)==null?void 0:m.DaysRemainded,emitEvent:"updateVPSIDPage"}})}function w(){var s,o,m;e.OrderManage({ServiceOrderID:(s=n.value)==null?void 0:s.ID,OrderID:(o=n.value)==null?void 0:o.OrderID,ServiceID:(m=n.value)==null?void 0:m.ServiceID})}return await _.fetchVPS(),await _.fetchVPSSchemes(),await i.fetchContracts(),await v.fetchUserOrders().then(()=>{var s;g.value=(s=O.value)==null?void 0:s.IsAutoProlong}),{getVpsOrder:n,passwordShow:D,getVpsScheme:S,isAutoProlong:g,setProlongValue:f,navigateToProlong:P,transferItem:I,vpsStatuses:Wt,openPackageChangeModal:k,openPasswordChange:y,openOrderRestore:V,orderManage:w}}},Os=Rt(is,[["render",os],["__scopeId","data-v-fc934f80"]]);export{Os as default};

import{j as J,o as u,c as v,b as r,a as t,t as c,w as K,d as Q,x as $,k as x,Y as tt,Z as et,F as ot,p as st,l as it,_ as nt,a7 as at,f as ct,e as lt,R as rt,r as P,g as B,i as _t}from"./index-7c70979b.js";import{u as mt}from"./domain-f452bc4d.js";import{u as dt}from"./contracts-6e790117.js";import{f as R,a as ut}from"./useTimeFunction-8602dd60.js";import{_ as Dt}from"./IconCard-b77c66c4.js";import{I as gt}from"./IconProfile-a16fdf82.js";import{I as vt}from"./IconSettings-c1ba93d3.js";import{I as ht}from"./IconEye-22337704.js";import{S as z}from"./StatusBadge-d1ca4bcf.js";import{B as G}from"./BlockBalanceAgreement-e7636133.js";import{E as ft}from"./EmptyStateBlock-12618634.js";import{_ as pt}from"./ServicesContractBalanceBlock-da86e2da.js";import{_ as It}from"./ButtonDefault-cdd68300.js";import{_ as bt}from"./ClausesKeeper-db215214.js";import"./bootstrap-vue-next.es-44836407.js";import"./IconArrow-6bef9064.js";import"./IconClose-358a4a84.js";const wt={Waiting:{id:1,label:"Ожидание оплаты",color:"#459FD1",background:"#ADC1F04C"},ClaimForRegister:{id:2,label:"Заявка на регистрацию",color:"#FFC107",background:"#FFC10733"},ForContractRegister:{id:3,label:"Для регистрации договора",color:"#FFC107",background:"#FFC10733"},OnContractRegister:{id:4,label:"На регистрации договора",color:"#FFC107",background:"#FFC10733"}},l=_=>(st("data-v-def193c8"),_=_(),it(),_),kt={key:0,class:"section"},St={class:"container"},Ct={class:"section-header"},Ot={class:"section-title"},Ft={class:"section-label"},Nt={class:"section-nav"},yt={class:"list"},xt={class:"list-col list-col--md"},Pt={class:"list-item"},Bt={class:"list-item__row"},Rt={class:"list-item__title"},Et={class:"list-item__status"},At={class:"list-item__row"},Tt={class:"list-item__ul"},Vt={key:0},Ut={class:"list-item__row list-item__row--column"},Wt={key:0,class:"list-item__text"},jt={class:"list-item__text"},Lt={class:"list-item__text"},Mt={class:"list-item__row"},qt={class:"btn btn--border btn--switch"},Ht=l(()=>t("span",{class:"btn-switch__text"},"Автопродление",-1)),Xt=l(()=>t("span",{class:"btn-switch__toggle"},null,-1)),Yt={class:"list-col list-col--md"},Zt={class:"list-item"},zt={class:"list-item__row list-item__row--nowrap"},Gt=l(()=>t("div",{class:"list-item__title"},"Именные сервера",-1)),Jt={class:"list-item__row list-item__row--table"},Kt=l(()=>t("div",{class:"list-item__item"},"Первичный сервер",-1)),Qt={class:"list-item__item"},$t={class:"list-item__row list-item__row--table"},te=l(()=>t("div",{class:"list-item__item"},"Вторичный сервер",-1)),ee={class:"list-item__item"},oe={class:"list-col list-col--md"},se={class:"list-item"},ie=l(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Контактные данные")],-1)),ne={class:"list-item__row list-item__row--table"},ae=l(()=>t("div",{class:"list-item__item"},"Скрыть данные в WhoIs",-1)),ce={class:"btn btn--switch"},le=["checked"],re=l(()=>t("span",{class:"btn-switch__toggle"},null,-1)),_e={class:"list-item__row list-item__row--table"},me=l(()=>t("div",{class:"list-item__item"},"Последнее обновление адрес",-1)),de={class:"list-item__item"},ue={key:1,class:"section"},De={class:"container"};function ge(_,i,a,o,E,h){var I,b,w,k,S,C,O,e,n,m,y,A,T,V,U,W,j,L,M,q,H,X,Y,Z;const D=G,f=J("router-link"),p=bt,g=It,F=pt,s=z,N=ft;return u(),v(ot,null,[r(D),(I=o.getDomain)!=null&&I.ID?(u(),v("div",kt,[t("div",St,[t("div",Ct,[t("h1",Ot,c((b=o.getDomain)==null?void 0:b.Domain),1),t("div",Ft,"Домен # "+c((w=o.getDomain)==null?void 0:w.OrderID),1),t("div",Nt,[r(f,{class:"section-nav__item",to:`/DomainOrders/${(k=o.getDomain)==null?void 0:k.OrderID}`},{default:K(()=>[Q("Домен")]),_:1},8,["to"])])]),r(p),((S=o.getDomain)==null?void 0:S.StatusID)==="Waiting"||((C=o.getDomain)==null?void 0:C.StatusID)==="ClaimForRegister"||((O=o.getDomain)==null?void 0:O.StatusID)==="ForContractRegister"||((e=o.getDomain)==null?void 0:e.StatusID)==="ForRegister"||((n=o.getDomain)==null?void 0:n.StatusID)==="ForTransfer"?(u(),$(g,{key:0,class:"btn-mn__bot",label:"Определить владельца",onClick:i[0]||(i[0]=d=>o.designateOwner())})):x("",!0),t("div",yt,[r(F,{"contract-id":(m=o.getDomain)==null?void 0:m.ContractID,onTransfer:i[1]||(i[1]=d=>o.transferItem())},null,8,["contract-id"]),t("div",xt,[t("div",Pt,[t("div",Bt,[t("div",Rt,c((y=o.getDomain)==null?void 0:y.Domain),1),t("div",Et,[r(s,{status:(A=o.getDomain)==null?void 0:A.StatusID,"status-table":"DomainOrders"},null,8,["status"])])]),t("div",At,[t("ul",Tt,[(T=o.getDomainScheme)!=null&&T.PackageID?(u(),v("li",Vt,c((V=o.getDomainScheme)==null?void 0:V.PackageID),1)):x("",!0)])]),t("div",Ut,[(U=o.getDomainScheme)!=null&&U.CostProlong?(u(),v("div",Wt,c((W=o.getDomainScheme)==null?void 0:W.CostProlong)+" ₽ продлить на 12 месяцев",1)):x("",!0),t("div",jt,"Дней осталось "+c(((j=o.getDomain)==null?void 0:j.DaysRemainded)||0),1),t("div",Lt,"Дата окончания "+c(o.calculateExpirationDate((L=o.getDomain)==null?void 0:L.DaysRemainded)),1)]),t("div",Mt,[r(g,{label:((M=o.getDomain)==null?void 0:M.StatusID)==="ClaimForRegister"||((q=o.getDomain)==null?void 0:q.StatusID)==="Waiting"?"Оплатить":"Продлить",onClick:i[2]||(i[2]=d=>o.navigateToProlong())},null,8,["label"]),t("label",qt,[tt(t("input",{type:"checkbox","onUpdate:modelValue":i[3]||(i[3]=d=>o.isAutoProlong=d),onInput:i[4]||(i[4]=d=>o.setProlongValue())},null,544),[[et,o.isAutoProlong]]),Ht,Xt])])])]),t("div",Yt,[t("div",Zt,[t("div",zt,[Gt,r(g,{class:"ml-auto",label:"Редактировать",onClick:i[5]||(i[5]=d=>o.toggleDomainsEdit())})]),t("div",Jt,[Kt,t("div",Qt,c((H=o.getDomain)==null?void 0:H.Ns1Name),1)]),t("div",$t,[te,t("div",ee,c((X=o.getDomain)==null?void 0:X.Ns2Name),1)])])]),t("div",oe,[t("div",se,[ie,t("div",ne,[ae,t("label",ce,[t("input",{type:"checkbox",checked:(Y=o.getDomain)==null?void 0:Y.IsPrivateWhoIs,disabled:""},null,8,le),re])]),t("div",_e,[me,t("div",de,c(o.lastUpdate((Z=o.getDomain)==null?void 0:Z.StatusDate)),1)])])])])])])):(u(),v("div",ue,[t("div",De,[r(N,{label:"Заказ не найден"})])]))],64)}const ve={components:{IconCard:Dt,IconProfile:gt,IconSettings:vt,IconEye:ht,StatusBadge:z,BlockBalanceAgreement:G},async setup(){const _=at(),i=ct(),a=mt(),o=dt(),E=lt(),h=rt("emitter"),D=P(null),f=P(!1),p=P({});function g(){h.emit("open-modal",{component:"DomainsEdit",data:s.value})}function F(){var e;a.DomainOrderNsChange({DomainOrderID:Number((e=s.value)==null?void 0:e.ID),XMLHttpRequest:"yes"}).then(n=>{let{result:m,error:y}=n;m==="SUCCESS"&&(f.value=!1,a.fetchDomains())})}const s=B(()=>Object.keys(a==null?void 0:a.domainsList).map(e=>a==null?void 0:a.domainsList[e]).find(e=>e.OrderID===_.params.id)),N=B(()=>{var e,n;return(n=a==null?void 0:a.domainSchemes)==null?void 0:n[(e=s.value)==null?void 0:e.SchemeID]}),I=B(()=>{var e,n;return(n=i==null?void 0:i.userOrders)==null?void 0:n[(e=s.value)==null?void 0:e.OrderID]});function b(e){if(!e)return R(new Date);const n=new Date,m=new Date(n.getTime()+e*24*60*60*1e3);return R(m)}function w(e){return e?R(ut(e)):""}function k(){try{const e={DomainOrderID:s==null?void 0:s.value.ID,Domain:s==null?void 0:s.value.Domain};h.emit("open-modal",{component:"DomainSelectOwner",data:e})}catch(e){console.error(e)}}function S(){var e,n;h.emit("open-modal",{component:"OrdersTransfer",data:{ServiceOrderID:(e=s.value)==null?void 0:e.ID,ServiceID:(n=s.value)==null?void 0:n.ServiceID}})}function C(){var e;i.prolongOrder({IsAutoProlong:D.value?0:1,OrderID:(e=s.value)==null?void 0:e.OrderID})}function O(){var e;E.push(`/DomainOrderPay/${(e=s.value)==null?void 0:e.OrderID}`)}return _t(()=>{var e,n;p.value={Ns1Name:(e=s.value)==null?void 0:e.Ns1Name,Ns2Name:(n=s.value)==null?void 0:n.Ns2Name}}),await a.fetchDomains(),await a.fetchDomainSchemes(),await o.fetchProfiles(),await i.fetchUserOrders().then(()=>{var e;D.value=(e=I.value)==null?void 0:e.IsAutoProlong}),{getDomain:s,getDomainScheme:N,isAutoProlong:D,setProlongValue:C,navigateToProlong:O,domainStatuses:wt,transferItem:S,lastUpdate:w,designateOwner:k,calculateExpirationDate:b,domainsEdit:f,domainNames:p,saveDomains:F,toggleDomainsEdit:g}}},Ee=nt(ve,[["render",ge],["__scopeId","data-v-def193c8"]]);export{Ee as default};

import{E as se}from"./EmptyStateBlock-03e9aa9e.js";import{_ as le}from"./BlockOrderPayBalance-ed8218c2.js";import{_ as ne}from"./ClausesKeeper-b1ff97f6.js";import{_ as ce}from"./ButtonDefault-a62141a4.js";import{_ as ie,a7 as re,e as _e,r as Y,g as I,i as de,ag as w,o as r,c as _,b as d,a as o,t as i,F as C,x as ue,Z as ee,a9 as oe,k as ve,p as me,l as pe}from"./index-59dbe3d3.js";import{u as he}from"./contracts-d90f6cbb.js";import{u as De}from"./services-05e05079.js";/* empty css                                                                             */import{B as be}from"./BlockBalanceAgreement-f093da58.js";import"./bootstrap-vue-next.es-faf1dad1.js";import"./IconHelp-0189edd5.js";import"./globalActions-d9fee8ed.js";import"./BasicInput-1e25083b.js";import"./component-ff695e34.js";import"./IconClose-0bef5a37.js";import"./IconArrow-ad4e318c.js";const N=k=>(me("data-v-17208449"),k=k(),pe(),k),ke={key:0,class:"hosting-sheme"},Se={class:"section"},ge={class:"container"},ye={class:"section-header"},fe={class:"section-header__wrapper"},Ne=N(()=>o("h1",{class:"section-title"},"Оплата заказа вторичного DNS",-1)),Be={class:"section-label"},Ie={key:0,class:"section"},we={class:"container"},Ce=N(()=>o("div",{class:"total-block"},null,-1)),Oe={class:"total-block__row divider-bottom"},xe=N(()=>o("div",{class:"total-block__col"},"Цена тарифа",-1)),Pe={class:"total-block__col"},Ae={class:"total-block__row no-divider"},Ee={class:"total-block__col"},Fe={class:"total-block__col"},Le={class:"total-block__row total-block__row--big economy-line"},Re=N(()=>o("div",{class:"total-block__col red"},"Выгода",-1)),Me={class:"total-block__col flex-col"},Ue={class:"red"},Ve={class:"total-block__row total-block__row--big"},$e={class:"total-block__col"},je={class:"total-block__col"},Te={key:1,class:"section"},qe={class:"container"},Ze={key:1,class:"section"},ze={class:"container"},Ge={__name:"AdditionalServicesProlongDNSmanager",async setup(k){let u,v;const O=he(),s=De(),ae=re(),x=_e(),a=Y(null),S=Y(!1),c=I(()=>Object.keys(s==null?void 0:s.DNSmanagerOrdersList).map(t=>s==null?void 0:s.DNSmanagerOrdersList[t]).find(t=>t.OrderID===ae.params.id)),g=I(()=>{var t;return(t=s==null?void 0:s.additionalServicesScheme)!=null&&t.DNSmanager?Object.keys(s.additionalServicesScheme.DNSmanager).map(l=>{const e=s.additionalServicesScheme.DNSmanager[l];return{...e,value:e==null?void 0:e.ID,name:e==null?void 0:e.Name,cost:e==null?void 0:e.CostDay,month:e==null?void 0:e.CostMonth}}).filter(l=>{var e;return(l==null?void 0:l.ID)==((e=c.value)==null?void 0:e.SchemeID)}):[]}),B=I(()=>O.contractsList);function P(){var m,f,p,h,D;S.value=!0;const t=((m=B.value[c.ContractID])==null?void 0:m.Balance)>((f=a==null?void 0:a.value)==null?void 0:f.price)||((p=g.value[0])==null?void 0:p.cost)==="0.00"&&((h=g.value[0])==null?void 0:h.month)==="0.00",l=!t,e={DNSmanagerOrderID:c.value.ID,DaysPay:(D=a.value)==null?void 0:D.actualDays,IsNoBasket:t,IsUseBasket:l,PayMessage:""};s.DNSOrderPay(e).then(b=>{b==="UseBasket"?x.push("/Basket"):b==="NoBasket"?x.push("/AdditionalServices?ServiceID=52000"):console.error("Ошибка оплаты заказа"),S.value=!0})}function y(t){return Number(t).toFixed(2)||0}function A(){var l,e;let t=0;return(e=(l=a.value)==null?void 0:l.discounts)==null||e.Bonuses.map(m=>{t+=+m.Economy}),t}function te(t){a.value=t}return de(()=>{s.fetchAdditionalServiceScheme("DNSmanager")}),[u,v]=w(()=>s.fetchDNSmanagerOrders()),await u,v(),[u,v]=w(()=>s.fetchAdditionalServiceScheme("DNSmanager")),await u,v(),[u,v]=w(()=>O.fetchContracts()),await u,v(),(t,l)=>{var h,D,b,E,F,L,R,M,U,V,$,j,T,q,Z,z,G,H,J,K,Q,W,X;const e=ce,m=ne,f=le,p=se;return r(),_(C,null,[d(be),c.value?(r(),_("div",ke,[o("div",Se,[o("div",ge,[o("div",ye,[o("div",fe,[Ne,d(e,{class:"btn--wide",label:((h=B.value[c.value.ContractID])==null?void 0:h.Balance)>((D=a.value)==null?void 0:D.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:a.value===null||((b=a.value)==null?void 0:b.price)===null,"is-loading":S.value,onClick:l[0]||(l[0]=n=>P())},null,8,["label","disabled","is-loading"])]),o("div",Be,i((E=g.value[0])==null?void 0:E.Name),1)])]),d(m)]),d(f,{contractID:(F=c.value)==null?void 0:F.ContractID,daysRemainded:(L=c.value)==null?void 0:L.DaysRemainded,serviceID:"52000",scheme:g.value[0],orderID:(R=c.value)==null?void 0:R.OrderID,isOrderID:!0,onSelect:te},null,8,["contractID","daysRemainded","scheme","orderID"]),c.value?(r(),_("div",Ie,[o("div",we,[Ce,((V=(U=(M=a.value)==null?void 0:M.discounts)==null?void 0:U.Bonuses)==null?void 0:V.length)>0?(r(),_(C,{key:0},[o("div",Oe,[xe,o("div",Pe,i(y(($=a.value)==null?void 0:$.price))+" ₽",1)]),(r(!0),_(C,null,ue((T=(j=a.value)==null?void 0:j.discounts)==null?void 0:T.Bonuses,n=>(r(),_("div",Ae,[o("div",Ee,"Скидка "+i(n==null?void 0:n.Discount)+"% на "+i(n==null?void 0:n.Days)+" дней",1),o("div",Fe,"-"+i(y(n==null?void 0:n.Economy))+" ₽",1)]))),256)),ee(o("div",Le,[Re,o("div",Me,[o("span",null,[o("s",null,i(y((q=a.value)==null?void 0:q.price))+" ₽",1)]),o("span",Ue,"-"+i(A())+" ₽",1)])],512),[[oe,((G=(z=(Z=a.value)==null?void 0:Z.discounts)==null?void 0:z.Bonuses)==null?void 0:G.length)>0]])],64)):ve("",!0),ee(o("div",Ve,[o("div",$e,"Итого за "+i((H=a.value)==null?void 0:H.label),1),o("div",je,i(y(((J=a.value)==null?void 0:J.price)-A()))+" ₽",1)],512),[[oe,(K=a.value)==null?void 0:K.price]]),d(e,{class:"btn--wide",label:((Q=B.value[c.value.ContractID])==null?void 0:Q.Balance)>((W=a.value)==null?void 0:W.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:a.value===null||((X=a.value)==null?void 0:X.price)===null,"is-loading":S.value,onClick:l[1]||(l[1]=n=>P())},null,8,["label","disabled","is-loading"])])])):(r(),_("div",Te,[o("div",qe,[d(p,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(r(),_("div",Ze,[o("div",ze,[d(p,{label:"не удалось найти заказ хостинга"})])]))],64)}}},ro=ie(Ge,[["__scopeId","data-v-17208449"]]);export{ro as default};

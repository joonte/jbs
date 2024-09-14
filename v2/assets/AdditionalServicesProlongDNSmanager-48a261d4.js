import{E as se}from"./EmptyStateBlock-cd0c2748.js";import{_ as le}from"./BlockOrderPayBalance-c29bc7b2.js";import{_ as ne}from"./ClausesKeeper-3a1c11ba.js";import{_ as ce}from"./ButtonDefault-0006f72a.js";import{_ as ie,a7 as re,e as _e,r as Y,g as w,i as de,ag as I,o as r,c as _,b as d,a,t as i,F as C,x as ue,Z as ee,ae,k as ve,p as me,l as pe}from"./index-b5b5ce3d.js";import{u as he}from"./contracts-74f05bb7.js";import{u as be}from"./services-18148891.js";/* empty css                                                                             */import{B as De}from"./BlockBalanceAgreement-a016b94f.js";import"./bootstrap-vue-next.es-37aa63ea.js";import"./IconHelp-12aa8ba1.js";import"./globalActions-6936f67f.js";import"./BasicInput-a3395017.js";import"./component-2368399e.js";import"./IconClose-cd6d3917.js";import"./IconArrow-fddabae2.js";const N=k=>(me("data-v-d88e894a"),k=k(),pe(),k),ke={key:0,class:"hosting-sheme"},ge={class:"section"},Se={class:"container"},ye={class:"section-header"},fe={class:"section-header__wrapper"},Ne=N(()=>a("h1",{class:"section-title"},"Оплата заказа вторичного DNS",-1)),Be={class:"section-label"},we={key:0,class:"section"},Ie={class:"container"},Ce=N(()=>a("div",{class:"total-block"},null,-1)),Oe={class:"total-block__row divider-bottom"},xe=N(()=>a("div",{class:"total-block__col"},"Цена тарифа",-1)),Pe={class:"total-block__col"},Ae={class:"total-block__row no-divider"},Ee={class:"total-block__col"},Fe={class:"total-block__col"},Le={class:"total-block__row total-block__row--big economy-line"},Re=N(()=>a("div",{class:"total-block__col red"},"Выгода",-1)),Me={class:"total-block__col flex-col"},Ue={class:"red"},Ve={class:"total-block__row total-block__row--big"},$e={class:"total-block__col"},je={class:"total-block__col"},Te={key:1,class:"section"},qe={class:"container"},Ze={key:1,class:"section"},ze={class:"container"},Ge={__name:"AdditionalServicesProlongDNSmanager",async setup(k){let u,v;const O=he(),s=be(),oe=re(),x=_e(),o=Y(null),g=Y(!1),c=w(()=>Object.keys(s==null?void 0:s.DNSmanagerOrdersList).map(t=>s==null?void 0:s.DNSmanagerOrdersList[t]).find(t=>t.OrderID===oe.params.id)),S=w(()=>{var t;return(t=s==null?void 0:s.additionalServicesScheme)!=null&&t.DNSmanager?Object.keys(s.additionalServicesScheme.DNSmanager).map(l=>{const e=s.additionalServicesScheme.DNSmanager[l];return{...e,value:e==null?void 0:e.ID,name:e==null?void 0:e.Name,cost:e==null?void 0:e.CostDay,month:e==null?void 0:e.CostMonth}}).filter(l=>{var e;return(l==null?void 0:l.ID)==((e=c.value)==null?void 0:e.SchemeID)}):[]}),B=w(()=>O.contractsList);function P(){var m,f,p,h,b;g.value=!0;const t=((m=B.value[c.ContractID])==null?void 0:m.Balance)>((f=o==null?void 0:o.value)==null?void 0:f.price)||((p=S.value[0])==null?void 0:p.cost)==="0.00"&&((h=S.value[0])==null?void 0:h.month)==="0.00",l=!t,e={DNSmanagerOrderID:c.value.ID,DaysPay:(b=o.value)==null?void 0:b.actualDays,IsNoBasket:t,IsUseBasket:l,PayMessage:""};s.DNSOrderPay(e).then(D=>{D==="UseBasket"?x.push("/Basket"):D==="NoBasket"?x.push("/AdditionalServices"):console.error("Ошибка оплаты заказа"),g.value=!0})}function y(t){return Number(t).toFixed(2)||0}function A(){var l,e;let t=0;return(e=(l=o.value)==null?void 0:l.discounts)==null||e.Bonuses.map(m=>{t+=+m.Economy}),t}function te(t){o.value=t}return de(()=>{s.fetchAdditionalServiceScheme("DNSmanager")}),[u,v]=I(()=>s.fetchDNSmanagerOrders()),await u,v(),[u,v]=I(()=>s.fetchAdditionalServiceScheme("DNSmanager")),await u,v(),[u,v]=I(()=>O.fetchContracts()),await u,v(),(t,l)=>{var h,b,D,E,F,L,R,M,U,V,$,j,T,q,Z,z,G,H,J,K,Q,W,X;const e=ce,m=ne,f=le,p=se;return r(),_(C,null,[d(De),c.value?(r(),_("div",ke,[a("div",ge,[a("div",Se,[a("div",ye,[a("div",fe,[Ne,d(e,{class:"btn--wide",label:((h=B.value[c.value.ContractID])==null?void 0:h.Balance)>((b=o.value)==null?void 0:b.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||((D=o.value)==null?void 0:D.price)===null,"is-loading":g.value,onClick:l[0]||(l[0]=n=>P())},null,8,["label","disabled","is-loading"])]),a("div",Be,i((E=S.value[0])==null?void 0:E.Name),1)])]),d(m)]),d(f,{contractID:(F=c.value)==null?void 0:F.ContractID,daysRemainded:(L=c.value)==null?void 0:L.DaysRemainded,serviceID:"52000",scheme:S.value[0],orderID:(R=c.value)==null?void 0:R.OrderID,isOrderID:!0,onSelect:te},null,8,["contractID","daysRemainded","scheme","orderID"]),c.value?(r(),_("div",we,[a("div",Ie,[Ce,((V=(U=(M=o.value)==null?void 0:M.discounts)==null?void 0:U.Bonuses)==null?void 0:V.length)>0?(r(),_(C,{key:0},[a("div",Oe,[xe,a("div",Pe,i(y(($=o.value)==null?void 0:$.price))+" ₽",1)]),(r(!0),_(C,null,ue((T=(j=o.value)==null?void 0:j.discounts)==null?void 0:T.Bonuses,n=>(r(),_("div",Ae,[a("div",Ee,"Скидка "+i(n==null?void 0:n.Discount)+"% на "+i(n==null?void 0:n.Days)+" дней",1),a("div",Fe,"-"+i(y(n==null?void 0:n.Economy))+" ₽",1)]))),256)),ee(a("div",Le,[Re,a("div",Me,[a("span",null,[a("s",null,i(y((q=o.value)==null?void 0:q.price))+" ₽",1)]),a("span",Ue,"-"+i(A())+" ₽",1)])],512),[[ae,((G=(z=(Z=o.value)==null?void 0:Z.discounts)==null?void 0:z.Bonuses)==null?void 0:G.length)>0]])],64)):ve("",!0),ee(a("div",Ve,[a("div",$e,"Итого за "+i((H=o.value)==null?void 0:H.label),1),a("div",je,i(y(((J=o.value)==null?void 0:J.price)-A()))+" ₽",1)],512),[[ae,(K=o.value)==null?void 0:K.price]]),d(e,{class:"btn--wide",label:((Q=B.value[c.value.ContractID])==null?void 0:Q.Balance)>((W=o.value)==null?void 0:W.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||((X=o.value)==null?void 0:X.price)===null,"is-loading":g.value,onClick:l[1]||(l[1]=n=>P())},null,8,["label","disabled","is-loading"])])])):(r(),_("div",Te,[a("div",qe,[d(p,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(r(),_("div",Ze,[a("div",ze,[d(p,{label:"не удалось найти заказ хостинга"})])]))],64)}}},ra=ie(Ge,[["__scopeId","data-v-d88e894a"]]);export{ra as default};

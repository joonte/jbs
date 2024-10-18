import{E as ae}from"./EmptyStateBlock-03e9aa9e.js";import{_ as le}from"./BlockOrderPayBalance-ed8218c2.js";import{_ as ce}from"./ClausesKeeper-b1ff97f6.js";import{_ as ne}from"./ButtonDefault-a62141a4.js";import{_ as ie,a7 as re,e as _e,r as Y,g as x,i as de,ag as B,o as r,c as _,b as d,a as t,t as i,F as E,x as ue,Z as ee,a9 as te,k as ve,p as pe,l as me}from"./index-59dbe3d3.js";import{u as he}from"./contracts-d90f6cbb.js";import{u as be}from"./services-05e05079.js";/* empty css                                                                             */import{B as ke}from"./BlockBalanceAgreement-f093da58.js";import"./bootstrap-vue-next.es-faf1dad1.js";import"./IconHelp-0189edd5.js";import"./globalActions-d9fee8ed.js";import"./BasicInput-1e25083b.js";import"./component-ff695e34.js";import"./IconClose-0bef5a37.js";import"./IconArrow-ad4e318c.js";const S=f=>(pe("data-v-fcf157ee"),f=f(),me(),f),fe={key:0,class:"hosting-sheme"},Ie={class:"section"},ye={class:"container"},ge={class:"section-header"},De={class:"section-header__wrapper"},Se=S(()=>t("h1",{class:"section-title"},"Оплата заказа IP адреса",-1)),Pe={class:"section-label"},xe={key:0,class:"section"},Be={class:"container"},Ee=S(()=>t("div",{class:"total-block"},null,-1)),we={class:"total-block__row divider-bottom"},Ce=S(()=>t("div",{class:"total-block__col"},"Цена тарифа",-1)),Oe={class:"total-block__col"},Ne={class:"total-block__row no-divider"},Ae={class:"total-block__col"},Fe={class:"total-block__col"},Le={class:"total-block__row total-block__row--big economy-line"},Re=S(()=>t("div",{class:"total-block__col red"},"Выгода",-1)),Me={class:"total-block__col flex-col"},Ue={class:"red"},Ve={class:"total-block__row total-block__row--big"},$e={class:"total-block__col"},je={class:"total-block__col"},Te={key:1,class:"section"},qe={class:"container"},Ze={key:1,class:"section"},ze={class:"container"},Ge={__name:"AdditionalServicesProlongExtraIP",async setup(f){let u,v;const w=he(),a=be(),oe=re(),C=_e(),o=Y(null),I=Y(!1),n=x(()=>Object.keys(a==null?void 0:a.ExtraIPOrdersList).map(s=>a==null?void 0:a.ExtraIPOrdersList[s]).find(s=>s.OrderID===oe.params.id)),y=x(()=>{var s;return(s=a==null?void 0:a.additionalServicesScheme)!=null&&s.ExtraIP?Object.keys(a.additionalServicesScheme.ExtraIP).map(l=>{const e=a.additionalServicesScheme.ExtraIP[l];return{...e,value:e==null?void 0:e.ID,name:e==null?void 0:e.Name,cost:e==null?void 0:e.CostDay,month:e==null?void 0:e.CostMonth}}).filter(l=>{var e;return(l==null?void 0:l.ID)==((e=n.value)==null?void 0:e.SchemeID)}):[]}),P=x(()=>w.contractsList);function O(){var p,D,m,h,b;I.value=!0;const s=((p=P.value[n.ContractID])==null?void 0:p.Balance)>((D=o==null?void 0:o.value)==null?void 0:D.price)||((m=y.value[0])==null?void 0:m.cost)==="0.00"&&((h=y.value[0])==null?void 0:h.month)==="0.00",l=!s,e={ExtraIPOrderID:n.value.ID,DaysPay:(b=o.value)==null?void 0:b.actualDays,IsNoBasket:s,IsUseBasket:l,PayMessage:""};a.ExtraIPOrderPay(e).then(k=>{k==="UseBasket"?C.push("/Basket"):k==="NoBasket"?C.push("/AdditionalServices"):console.error("Ошибка оплаты заказа"),I.value=!0})}function g(s){return Number(s).toFixed(2)||0}function N(){var l,e;let s=0;return(e=(l=o.value)==null?void 0:l.discounts)==null||e.Bonuses.map(p=>{s+=+p.Economy}),s}function se(s){o.value=s}return de(()=>{a.fetchAdditionalServiceScheme("ExtraIP")}),[u,v]=B(()=>a.fetchExtraIPOrders()),await u,v(),[u,v]=B(()=>a.fetchAdditionalServiceScheme("ExtraIP")),await u,v(),[u,v]=B(()=>w.fetchContracts()),await u,v(),(s,l)=>{var h,b,k,A,F,L,R,M,U,V,$,j,T,q,Z,z,G,H,J,K,Q,W,X;const e=ne,p=ce,D=le,m=ae;return r(),_(E,null,[d(ke),n.value?(r(),_("div",fe,[t("div",Ie,[t("div",ye,[t("div",ge,[t("div",De,[Se,d(e,{class:"btn--wide",label:((h=P.value[n.value.ContractID])==null?void 0:h.Balance)>((b=o.value)==null?void 0:b.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||((k=o.value)==null?void 0:k.price)===null,"is-loading":I.value,onClick:l[0]||(l[0]=c=>O())},null,8,["label","disabled","is-loading"])]),t("div",Pe,i((A=y.value[0])==null?void 0:A.Name),1)])]),d(p)]),d(D,{contractID:(F=n.value)==null?void 0:F.ContractID,daysRemainded:(L=n.value)==null?void 0:L.DaysRemainded,serviceID:"50000",scheme:y.value[0],orderID:(R=n.value)==null?void 0:R.OrderID,isOrderID:!0,onSelect:se},null,8,["contractID","daysRemainded","scheme","orderID"]),n.value?(r(),_("div",xe,[t("div",Be,[Ee,((V=(U=(M=o.value)==null?void 0:M.discounts)==null?void 0:U.Bonuses)==null?void 0:V.length)>0?(r(),_(E,{key:0},[t("div",we,[Ce,t("div",Oe,i(g(($=o.value)==null?void 0:$.price))+" ₽",1)]),(r(!0),_(E,null,ue((T=(j=o.value)==null?void 0:j.discounts)==null?void 0:T.Bonuses,c=>(r(),_("div",Ne,[t("div",Ae,"Скидка "+i(c==null?void 0:c.Discount)+"% на "+i(c==null?void 0:c.Days)+" дней",1),t("div",Fe,"-"+i(g(c==null?void 0:c.Economy))+" ₽",1)]))),256)),ee(t("div",Le,[Re,t("div",Me,[t("span",null,[t("s",null,i(g((q=o.value)==null?void 0:q.price))+" ₽",1)]),t("span",Ue,"-"+i(N())+" ₽",1)])],512),[[te,((G=(z=(Z=o.value)==null?void 0:Z.discounts)==null?void 0:z.Bonuses)==null?void 0:G.length)>0]])],64)):ve("",!0),ee(t("div",Ve,[t("div",$e,"Итого за "+i((H=o.value)==null?void 0:H.label),1),t("div",je,i(g(((J=o.value)==null?void 0:J.price)-N()))+" ₽",1)],512),[[te,(K=o.value)==null?void 0:K.price]]),d(e,{class:"btn--wide",label:((Q=P.value[n.value.ContractID])==null?void 0:Q.Balance)>((W=o.value)==null?void 0:W.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||((X=o.value)==null?void 0:X.price)===null,"is-loading":I.value,onClick:l[1]||(l[1]=c=>O())},null,8,["label","disabled","is-loading"])])])):(r(),_("div",Te,[t("div",qe,[d(m,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(r(),_("div",Ze,[t("div",ze,[d(m,{label:"не удалось найти заказ хостинга"})])]))],64)}}},rt=ie(Ge,[["__scopeId","data-v-fcf157ee"]]);export{rt as default};

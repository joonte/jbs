import{E as ie}from"./EmptyStateBlock-1861cff5.js";import{_ as _e}from"./BlockOrderPayBalance-74ae5c26.js";import{_ as re}from"./ClausesKeeper-12bc5d50.js";import{_ as de}from"./ButtonDefault-eb076df2.js";import{_ as ue,a7 as me,e as ve,S as pe,r as b,g as B,ag as w,o as _,c as r,b as d,a as e,t as c,F as D,x as he,k as be,Z as oe,ae,p as ge,l as ye}from"./index-642cdac5.js";import{u as ke}from"./hosting-73fbf392.js";import{u as fe}from"./contracts-16ef7000.js";import{_ as De}from"./paramsAccordionBlock-7d256e45.js";import{B as Ie}from"./BlockBalanceAgreement-ccba2ab2.js";import"./bootstrap-vue-next.es-9ff54498.js";import"./IconHelp-41d48e37.js";import"./globalActions-c9fc22a3.js";import"./BasicInput-e305f811.js";import"./component-faf9e0c1.js";import"./IconClose-f1b85910.js";import"./IconArrow-c0196cbe.js";const le=g=>(ge("data-v-d717b517"),g=g(),ye(),g),Be={key:0,class:"hosting-sheme"},we={class:"section"},Se={class:"container"},Ce={class:"section-header"},He={class:"section-header__wrapper"},Pe={class:"section-title"},xe={class:"section-label"},Ee={class:"section"},Oe={class:"container"},Fe={class:"total-block"},Ne={class:"total-block__row divider-bottom"},Le=le(()=>e("div",{class:"total-block__col"},"Цена тарифа",-1)),Re={class:"total-block__col"},$e={class:"total-block__row no-divider"},Ae={class:"total-block__col"},Te={class:"total-block__col"},Ve={class:"total-block__row total-block__row--big economy-line"},je=le(()=>e("div",{class:"total-block__col red"},"Выгода",-1)),Ke={class:"total-block__col flex-col"},Ue={class:"red"},Ze={class:"total-block__row total-block__row--big"},qe={class:"total-block__col"},ze={class:"total-block__col"},Ge={key:1,class:"section"},Je={class:"container"},Me={key:1,class:"section"},Qe={class:"container"},We={__name:"HostingProlong",async setup(g){let m,v;const a=ke(),S=fe(),ne=me(),C=ve();pe("emitter"),b(null),b(null),b("");const s=b(null),y=b(!1),n=B(()=>Object.keys(a==null?void 0:a.hostingList).map(t=>a==null?void 0:a.hostingList[t]).find(t=>t.OrderID===ne.params.id));console.log("getHostingOrderData",n);const u=B(()=>{var t,o;return(o=a==null?void 0:a.hostingSchemes)==null?void 0:o[(t=n.value)==null?void 0:t.SchemeID]});console.log("getHostingScheme",u);const H=B(()=>S.contractsList);function k(t){return Number(t).toFixed(2)||0}function P(){var o,i;let t=0;return(i=(o=s.value)==null?void 0:o.discounts)==null||i.Bonuses.map(p=>{t+=+p.Economy}),t}function x(){var t,o,i;y.value=!0,a.HostingOrderPay({HostingOrderID:(t=n.value)==null?void 0:t.ID,DaysPayFromBallance:((o=s.value)==null?void 0:o.daysFromBalance)||0,DaysPay:(i=s.value)==null?void 0:i.actualDays,IsChange:!0}).then(p=>{var h;let{result:f,error:I}=p;f==="SUCCESS"?C.push(`/HostingOrders/${(h=n.value)==null?void 0:h.ID}`):f==="BASKET"&&C.push("/Basket"),y.value=!1})}function ce(t){s.value=t}return[m,v]=w(()=>a.fetchHostingOrders()),await m,v(),[m,v]=w(()=>a.fetchHostingSchemes()),await m,v(),[m,v]=w(()=>S.fetchContracts()),await m,v(),(t,o)=>{var h,E,O,F,N,L,R,$,A,T,V,j,K,U,Z,q,z,G,J,M,Q,W,X,Y,ee,se,te;const i=de,p=re,f=_e,I=ie;return _(),r(D,null,[d(Ie),n.value?(_(),r("div",Be,[e("div",we,[e("div",Se,[e("div",Ce,[e("div",He,[e("h1",Pe,c((h=n.value)==null?void 0:h.Domain),1),d(i,{class:"btn--wide",label:((E=H.value[n.value.ContractID])==null?void 0:E.Balance)>((O=s.value)==null?void 0:O.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:s.value===null||((F=s.value)==null?void 0:F.price)===null,"is-loading":y.value,onClick:o[0]||(o[0]=l=>x())},null,8,["label","disabled","is-loading"])]),e("div",xe,c((N=u.value)==null?void 0:N.Name),1)]),d(p)])]),d(De,{"params-list":(L=u.value)==null?void 0:L.SchemeParams,label:"Общая информация","main-param":"InternalName"},null,8,["params-list"]),(R=u.value)!=null&&R.IsPayed&&(($=u.value)!=null&&$.IsProlong)||!((A=u.value)!=null&&A.IsPayed)?(_(),r(D,{key:0},[d(f,{contractID:(T=n.value)==null?void 0:T.ContractID,daysRemainded:(V=n.value)==null?void 0:V.DaysRemainded,scheme:u.value,isOrderID:!0,onSelect:ce},null,8,["contractID","daysRemainded","scheme"]),e("div",Ee,[e("div",Oe,[e("div",Fe,[((U=(K=(j=s.value)==null?void 0:j.discounts)==null?void 0:K.Bonuses)==null?void 0:U.length)>0?(_(),r(D,{key:0},[e("div",Ne,[Le,e("div",Re,c(k((Z=s.value)==null?void 0:Z.price))+" ₽",1)]),(_(!0),r(D,null,he((z=(q=s.value)==null?void 0:q.discounts)==null?void 0:z.Bonuses,l=>(_(),r("div",$e,[e("div",Ae,"Скидка "+c(l==null?void 0:l.Discount)+"% на "+c(l==null?void 0:l.Days)+" дней",1),e("div",Te,"-"+c(k(l==null?void 0:l.Economy))+" ₽",1)]))),256))],64)):be("",!0),oe(e("div",Ve,[je,e("div",Ke,[e("span",null,[e("s",null,c(k((G=s.value)==null?void 0:G.price))+" ₽",1)]),e("span",Ue,"-"+c(P())+" ₽",1)])],512),[[ae,((Q=(M=(J=s.value)==null?void 0:J.discounts)==null?void 0:M.Bonuses)==null?void 0:Q.length)>0]]),oe(e("div",Ze,[e("div",qe,"Итого за "+c((W=s.value)==null?void 0:W.label),1),e("div",ze,c(k(((X=s.value)==null?void 0:X.price)-P()))+" ₽",1)],512),[[ae,(Y=s.value)==null?void 0:Y.price]])]),d(i,{class:"btn--wide",label:((ee=H.value[n.value.ContractID])==null?void 0:ee.Balance)>((se=s.value)==null?void 0:se.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:s.value===null||((te=s.value)==null?void 0:te.price)===null,"is-loading":y.value,onClick:o[1]||(o[1]=l=>x())},null,8,["label","disabled","is-loading"])])])],64)):(_(),r("div",Ge,[e("div",Je,[d(I,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(_(),r("div",Me,[e("div",Qe,[d(I,{label:"не удалось найти заказ хостинга"})])]))],64)}}},vs=ue(We,[["__scopeId","data-v-d717b517"]]);export{vs as default};

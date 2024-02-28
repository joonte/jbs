import{E as ne}from"./EmptyStateBlock-320e221f.js";import{_ as ie}from"./BlockOrderPayBalance-3b8537b3.js";import{_ as _e}from"./ClausesKeeper-54c941b5.js";import{_ as re}from"./ButtonDefault-cf4dc84b.js";import{_ as de,a7 as ue,e as me,R as ve,r as g,g as B,ag as w,o as _,c as r,b as d,a as e,t as c,F as D,v as pe,k as he,Y as se,ae as oe,p as ge,l as be}from"./index-d9e5d80f.js";import{u as ke}from"./hosting-efa4851e.js";import{u as fe}from"./contracts-0b0db280.js";import{_ as ye}from"./paramsAccordionBlock-00f678c6.js";import{B as De}from"./BlockBalanceAgreement-bd5f9bec.js";import"./bootstrap-vue-next.es-b0c2a08e.js";import"./IconHelp-a06df538.js";import"./globalActions-567e83be.js";import"./BasicInput-59bbbbe3.js";import"./component-febbb147.js";import"./IconClose-b8cba5ec.js";import"./IconArrow-3cf18905.js";const ae=b=>(ge("data-v-a69f21e1"),b=b(),be(),b),Ie={key:0,class:"hosting-sheme"},Be={class:"section"},we={class:"container"},Se={class:"section-header"},Ce={class:"section-header__wrapper"},He={class:"section-title"},Pe={class:"section-label"},Ee={class:"section"},Oe={class:"container"},xe={class:"total-block"},Fe={class:"total-block__row divider-bottom"},Ne=ae(()=>e("div",{class:"total-block__col"},"Цена тарифа",-1)),Le={class:"total-block__col"},$e={class:"total-block__row no-divider"},Ae={class:"total-block__col"},Re={class:"total-block__col"},Te={class:"total-block__row total-block__row--big economy-line"},Ve=ae(()=>e("div",{class:"total-block__col red"},"Выгода",-1)),je={class:"total-block__col flex-col"},Ke={class:"red"},Ue={class:"total-block__row total-block__row--big"},Ye={class:"total-block__col"},qe={class:"total-block__col"},ze={key:1,class:"section"},Ge={class:"container"},Je={key:1,class:"section"},Me={class:"container"},Qe={__name:"HostingProlong",async setup(b){let m,v;const a=ke(),S=fe(),le=ue(),C=me();ve("emitter"),g(null),g(null),g("");const t=g(null),k=g(!1),n=B(()=>Object.keys(a==null?void 0:a.hostingList).map(s=>a==null?void 0:a.hostingList[s]).find(s=>s.OrderID===le.params.id));console.log("getHostingOrderData",n);const u=B(()=>{var s,o;return(o=a==null?void 0:a.hostingSchemes)==null?void 0:o[(s=n.value)==null?void 0:s.SchemeID]});console.log("getHostingScheme",u);const H=B(()=>S.contractsList);function f(s){return Number(s).toFixed(2)||0}function P(){var o,i;let s=0;return(i=(o=t.value)==null?void 0:o.discounts)==null||i.Bonuses.map(p=>{s+=+p.Economy}),s}function E(){var s,o,i;k.value=!0,a.HostingOrderPay({HostingOrderID:(s=n.value)==null?void 0:s.ID,DaysPayFromBallance:((o=t.value)==null?void 0:o.daysFromBalance)||0,DaysPay:(i=t.value)==null?void 0:i.actualDays,IsChange:!0}).then(p=>{var h;let{result:y,error:I}=p;y==="SUCCESS"?C.push(`/HostingOrders/${(h=n.value)==null?void 0:h.ID}`):y==="BASKET"&&C.push("/Basket"),k.value=!1})}function ce(s){t.value=s}return[m,v]=w(()=>a.fetchHostingOrders()),await m,v(),[m,v]=w(()=>a.fetchHostingSchemes()),await m,v(),[m,v]=w(()=>S.fetchContracts()),await m,v(),(s,o)=>{var h,O,x,F,N,L,$,A,R,T,V,j,K,U,Y,q,z,G,J,M,Q,W,X,Z,ee,te;const i=re,p=_e,y=ie,I=ne;return _(),r(D,null,[d(De),n.value?(_(),r("div",Ie,[e("div",Be,[e("div",we,[e("div",Se,[e("div",Ce,[e("h1",He,c((h=n.value)==null?void 0:h.Domain),1),d(i,{class:"btn--wide",label:((O=H.value[n.value.ContractID])==null?void 0:O.Balance)>((x=t.value)==null?void 0:x.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:t.value===null||((F=t.value)==null?void 0:F.price)===null,"is-loading":k.value,onClick:o[0]||(o[0]=l=>E())},null,8,["label","disabled","is-loading"])]),e("div",Pe,c((N=u.value)==null?void 0:N.Name),1)]),d(p)])]),d(ye,{"params-list":(L=u.value)==null?void 0:L.SchemeParams,label:"Общая информация","main-param":"InternalName"},null,8,["params-list"]),($=u.value)!=null&&$.IsPayed&&((A=u.value)!=null&&A.IsProlong)||!((R=u.value)!=null&&R.IsPayed)?(_(),r(D,{key:0},[d(y,{contractID:(T=n.value)==null?void 0:T.ContractID,scheme:u.value,isOrderID:!0,onSelect:ce},null,8,["contractID","scheme"]),e("div",Ee,[e("div",Oe,[e("div",xe,[((K=(j=(V=t.value)==null?void 0:V.discounts)==null?void 0:j.Bonuses)==null?void 0:K.length)>0?(_(),r(D,{key:0},[e("div",Fe,[Ne,e("div",Le,c(f((U=t.value)==null?void 0:U.price))+" ₽",1)]),(_(!0),r(D,null,pe((q=(Y=t.value)==null?void 0:Y.discounts)==null?void 0:q.Bonuses,l=>(_(),r("div",$e,[e("div",Ae,"Скидка "+c(l==null?void 0:l.Discount)+"% на "+c(l==null?void 0:l.Days)+" дней",1),e("div",Re,"-"+c(f(l==null?void 0:l.Economy))+" ₽",1)]))),256))],64)):he("",!0),se(e("div",Te,[Ve,e("div",je,[e("span",null,[e("s",null,c(f((z=t.value)==null?void 0:z.price))+" ₽",1)]),e("span",Ke,"-"+c(P())+" ₽",1)])],512),[[oe,((M=(J=(G=t.value)==null?void 0:G.discounts)==null?void 0:J.Bonuses)==null?void 0:M.length)>0]]),se(e("div",Ue,[e("div",Ye,"Итого за "+c((Q=t.value)==null?void 0:Q.label),1),e("div",qe,c(f(((W=t.value)==null?void 0:W.price)-P()))+" ₽",1)],512),[[oe,(X=t.value)==null?void 0:X.price]])]),d(i,{class:"btn--wide",label:((Z=H.value[n.value.ContractID])==null?void 0:Z.Balance)>((ee=t.value)==null?void 0:ee.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:t.value===null||((te=t.value)==null?void 0:te.price)===null,"is-loading":k.value,onClick:o[1]||(o[1]=l=>E())},null,8,["label","disabled","is-loading"])])])],64)):(_(),r("div",ze,[e("div",Ge,[d(I,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(_(),r("div",Je,[e("div",Me,[d(I,{label:"не удалось найти заказ хостинга"})])]))],64)}}},mt=de(Qe,[["__scopeId","data-v-a69f21e1"]]);export{mt as default};

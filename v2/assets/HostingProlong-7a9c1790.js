import{E as st}from"./EmptyStateBlock-5a6a4cd4.js";import{_ as ot}from"./ButtonDefault-cd183bf8.js";import{_ as at}from"./BlockOrderPayBalance-8fab58d2.js";import{_ as lt}from"./ClausesKeeper-b9906182.js";import{_ as ct,a6 as nt,e as it,R as _t,r as h,g as I,af as w,o as _,c as r,a as t,t as c,b as m,F as J,v as rt,k as dt,Y as M,ad as Q,p as ut,l as mt}from"./index-1c309346.js";import{u as pt}from"./hosting-fd5f9472.js";import{u as vt}from"./contracts-7b919c67.js";import{_ as ht}from"./paramsAccordionBlock-10c330d0.js";import"./bootstrap-vue-next.es-b799f76d.js";import"./IconHelp-648316fb.js";import"./globalActions-8efdfe11.js";import"./BasicInput-33bf464f.js";import"./component2-139d1cad.js";import"./component-a2c8255f.js";import"./IconClose-7cf50ca7.js";import"./IconArrow-6927efe5.js";const W=b=>(ut("data-v-36a960db"),b=b(),mt(),b),bt={key:0,class:"hosting-sheme"},gt={class:"section"},kt={class:"container"},ft={class:"section-header"},yt={class:"section-title"},Dt={class:"section-label"},It={key:0,class:"section"},wt={class:"container"},Bt={class:"total-block"},St={class:"total-block__row divider-bottom"},Ct=W(()=>t("div",{class:"total-block__col"},"Цена тарифа",-1)),Ht={class:"total-block__col"},Et={class:"total-block__row no-divider"},Pt={class:"total-block__col"},xt={class:"total-block__col"},Ot={class:"total-block__row total-block__row--big economy-line"},Ft=W(()=>t("div",{class:"total-block__col red"},"Выгода",-1)),Nt={class:"total-block__col flex-col"},Lt={class:"red"},$t={class:"total-block__row total-block__row--big"},Rt={class:"total-block__col"},Tt={class:"total-block__col"},Vt={key:1,class:"section"},jt={class:"container"},At={key:1,class:"section"},Kt={class:"container"},Ut={__name:"HostingProlong",async setup(b){let d,u;const o=pt(),B=vt(),X=nt(),S=it();_t("emitter"),h(null),h(null),h("");const s=h(null),y=h(!1),n=I(()=>Object.keys(o==null?void 0:o.hostingList).map(e=>o==null?void 0:o.hostingList[e]).find(e=>e.OrderID===X.params.id)),g=I(()=>{var e,a;return(a=o==null?void 0:o.hostingSchemes)==null?void 0:a[(e=n.value)==null?void 0:e.SchemeID]}),Z=I(()=>B.contractsList);function k(e){return Number(e).toFixed(2)||0}function C(){var a,i;let e=0;return(i=(a=s.value)==null?void 0:a.discounts)==null||i.Bonuses.map(p=>{e+=+p.Economy}),e}function tt(){var e,a,i;y.value=!0,o.HostingOrderPay({HostingOrderID:(e=n.value)==null?void 0:e.ID,DaysPayFromBallance:((a=s.value)==null?void 0:a.daysFromBalance)||0,DaysPay:(i=s.value)==null?void 0:i.actualDays,IsChange:!0}).then(p=>{var v;let{result:f,error:D}=p;f==="SUCCESS"?S.push(`/HostingOrders/${(v=n.value)==null?void 0:v.ID}`):f==="BASKET"&&S.push("/Basket"),y.value=!1})}function et(e){s.value=e}return[d,u]=w(()=>o.fetchHostingOrders()),await d,u(),[d,u]=w(()=>o.fetchHostingSchemes()),await d,u(),[d,u]=w(()=>B.fetchContracts()),await d,u(),(e,a)=>{var v,H,E,P,x,O,F,N,L,$,R,T,V,j,A,K,U,Y,q,z,G;const i=lt,p=at,f=ot,D=st;return n.value?(_(),r("div",bt,[t("div",gt,[t("div",kt,[t("div",ft,[t("h1",yt,c((v=n.value)==null?void 0:v.Domain),1),t("div",Dt,c((H=g.value)==null?void 0:H.Name),1)]),m(i)])]),m(ht,{"params-list":(E=g.value)==null?void 0:E.SchemeParams,label:"Общая информация","main-param":"InternalName"},null,8,["params-list"]),m(p,{contractID:(P=n.value)==null?void 0:P.ContractID,scheme:g.value,isOrderID:!0,onSelect:et},null,8,["contractID","scheme"]),(x=g.value)!=null&&x.IsProlong?(_(),r("div",It,[t("div",wt,[t("div",Bt,[((N=(F=(O=s.value)==null?void 0:O.discounts)==null?void 0:F.Bonuses)==null?void 0:N.length)>0?(_(),r(J,{key:0},[t("div",St,[Ct,t("div",Ht,c(k((L=s.value)==null?void 0:L.price))+" ₽",1)]),(_(!0),r(J,null,rt((R=($=s.value)==null?void 0:$.discounts)==null?void 0:R.Bonuses,l=>(_(),r("div",Et,[t("div",Pt,"Скидка "+c(l==null?void 0:l.Discount)+"% на "+c(l==null?void 0:l.Days)+" дней",1),t("div",xt,"-"+c(k(l==null?void 0:l.Economy))+" ₽",1)]))),256))],64)):dt("",!0),M(t("div",Ot,[Ft,t("div",Nt,[t("span",null,[t("s",null,c(k((T=s.value)==null?void 0:T.price))+" ₽",1)]),t("span",Lt,"-"+c(C())+" ₽",1)])],512),[[Q,((A=(j=(V=s.value)==null?void 0:V.discounts)==null?void 0:j.Bonuses)==null?void 0:A.length)>0]]),M(t("div",$t,[t("div",Rt,"Итого за "+c((K=s.value)==null?void 0:K.label),1),t("div",Tt,c(k(((U=s.value)==null?void 0:U.price)-C()))+" ₽",1)],512),[[Q,(Y=s.value)==null?void 0:Y.price]])]),m(f,{class:"btn--wide",label:((q=Z.value[n.value.ContractID])==null?void 0:q.Balance)>((z=s.value)==null?void 0:z.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:s.value===null||((G=s.value)==null?void 0:G.price)===null,"is-loading":y.value,onClick:a[0]||(a[0]=l=>tt())},null,8,["label","disabled","is-loading"])])])):(_(),r("div",Vt,[t("div",jt,[m(D,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(_(),r("div",At,[t("div",Kt,[m(D,{label:"не удалось найти заказ хостинга"})])]))}}},ce=ct(Ut,[["__scopeId","data-v-36a960db"]]);export{ce as default};

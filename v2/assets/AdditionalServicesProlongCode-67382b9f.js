import{E as he}from"./EmptyStateBlock-153c9c6f.js";import{_ as ke}from"./BlockOrderPayBalance-cdc8f4d7.js";import{_ as ye}from"./ClausesKeeper-8218189c.js";import{_ as be}from"./ButtonDefault-458eab69.js";import{_ as Ie,a7 as Se,e as De,r as ie,g as C,i as re,O as fe,ag as P,o as r,c as _,b as I,a as o,t as d,y as de,k as O,F as E,x as ge,Z as _e,a9 as ue,p as we,l as Be}from"./index-5f8fd0e9.js";import{u as Ce}from"./contracts-00b6c53b.js";import{u as Pe}from"./services-0121b428.js";/* empty css                                                                             */import{B as Oe}from"./BlockBalanceAgreement-cf03db60.js";import"./bootstrap-vue-next.es-a97602fc.js";import"./IconHelp-9923896d.js";import"./globalActions-2cf5b597.js";import"./component-62d13c49.js";import"./IconClose-2e1ba86d.js";import"./IconArrow-c9744352.js";const ve=S=>(we("data-v-02715a80"),S=S(),Be(),S),Ee={key:0,class:"hosting-sheme"},Ne={class:"section"},Ue={class:"container"},xe={class:"section-header"},Ae={class:"section-header__wrapper"},Fe={class:"section-title"},Te={class:"section-label"},Le={key:1,class:"section"},Re={class:"container"},Me={class:"total-block"},$e={class:"total-block__row divider-bottom"},Ve=ve(()=>o("div",{class:"total-block__col"},"Цена тарифа",-1)),je={class:"total-block__col"},Ke={class:"total-block__row no-divider"},qe={class:"total-block__col"},Ze={class:"total-block__col"},ze={class:"total-block__row total-block__row--big economy-line"},Ge=ve(()=>o("div",{class:"total-block__col red"},"Выгода",-1)),He={class:"total-block__col flex-col"},Je={class:"red"},Qe={class:"total-block__row total-block__row--big"},We={class:"total-block__col"},Xe={class:"total-block__col"},Ye={key:2,class:"section"},eo={class:"container"},oo={key:1,class:"section"},so={class:"container"},to={__name:"AdditionalServicesProlongCode",async setup(S){let u,v;const N=Ce(),l=Pe(),pe=Se(),U=De(),t=ie(null),D=ie(!1),n=C(()=>Object.keys(l==null?void 0:l.ISPswOrdersList).map(s=>l==null?void 0:l.ISPswOrdersList[s]).find(s=>s.OrderID===pe.params.id)),x=s=>{s.key==="Enter"&&s.ctrlKey&&B()};re(()=>{document.addEventListener("keyup",x)}),fe(()=>{document.removeEventListener("keyup",x)});const c=C(()=>{var s;return(s=l==null?void 0:l.additionalServicesScheme)!=null&&s.ISPsw?Object.keys(l.additionalServicesScheme.ISPsw).map(a=>{const e=l.additionalServicesScheme.ISPsw[a];return{...e,value:e==null?void 0:e.ID,name:e==null?void 0:e.Name,cost:e==null?void 0:e.CostDay,month:e==null?void 0:e.CostMonth}}).filter(a=>{var e;return(a==null?void 0:a.ID)==((e=n.value)==null?void 0:e.SchemeID)}):[]}),w=C(()=>N.contractsList);function B(){var m,g,h,k,y;D.value=!0;const s=((m=w.value[n.value.ContractID])==null?void 0:m.Balance)>(p(((g=t.value)==null?void 0:g.price)-f())??0)||((h=c.value[0])==null?void 0:h.cost)==="0.00"&&((k=c.value[0])==null?void 0:k.month)==="0.00",a=!s;console.log(s,"isNoBasket"),console.log(a,"Basket");const e={ISPswOrderID:n.value.ID,DaysPay:(y=t.value)==null?void 0:y.actualDays,IsNoBasket:s,IsUseBasket:a,PayMessage:""};l.ISPswOrderPay(e).then(b=>{b==="UseBasket"?U.push("/Basket"):b==="NoBasket"?U.push("/AdditionalServices?ServiceID=51000"):console.error("Ошибка оплаты заказа"),D.value=!0})}function p(s){return Number(s).toFixed(2)||0}function f(){var a,e;let s=0;return(e=(a=t.value)==null?void 0:a.discounts)==null||e.Bonuses.map(m=>{s+=+m.Economy}),console.log(s,"econom"),s}function me(s){t.value=s}return re(()=>{l.fetchAdditionalServiceScheme("ISPsw"),console.log(t.value,"selected")}),[u,v]=P(()=>l.fetchISPswOrders()),await u,v(),[u,v]=P(()=>l.fetchAdditionalServiceScheme("ISPsw")),await u,v(),[u,v]=P(()=>N.fetchContracts()),await u,v(),(s,a)=>{var k,y,b,A,F,T,L,R,M,$,V,j,K,q,Z,z,G,H,J,Q,W,X,Y,ee,oe,se,te,ae,le,ne,ce;const e=be,m=ye,g=ke,h=he;return r(),_(E,null,[I(Oe),n.value?(r(),_("div",Ee,[o("div",Ne,[o("div",Ue,[o("div",xe,[o("div",Ae,[o("h1",Fe,"Оплата заказа ПО ISPsystem, "+d(((k=n.value)==null?void 0:k.IP)||""),1),((y=c.value[0])==null?void 0:y.ConsiderTypeID)!=="Upon"?(r(),de(e,{key:0,class:"btn--wide",label:parseFloat((b=w.value[n.value.ContractID])==null?void 0:b.Balance)>=parseFloat((A=t.value)==null?void 0:A.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:t.value===null||((F=t.value)==null?void 0:F.price)===null,"is-loading":D.value,onClick:a[0]||(a[0]=i=>B())},null,8,["label","disabled","is-loading"])):O("",!0)]),o("div",Te,d((T=c.value[0])==null?void 0:T.Name),1)])]),I(m)]),((L=c.value[0])==null?void 0:L.ConsiderTypeID)!=="Upon"?(r(),de(g,{key:0,contractID:(R=n.value)==null?void 0:R.ContractID,daysRemainded:(M=n.value)==null?void 0:M.DaysRemainded,serviceID:"51000",scheme:c.value[0],orderID:($=n.value)==null?void 0:$.OrderID,isOrderID:!0,onSelect:me},null,8,["contractID","daysRemainded","scheme","orderID"])):O("",!0),n.value?(r(),_("div",Le,[o("div",Re,[o("div",Me,[((K=(j=(V=t.value)==null?void 0:V.discounts)==null?void 0:j.Bonuses)==null?void 0:K.length)>0?(r(),_(E,{key:0},[o("div",$e,[Ve,o("div",je,d(p((q=t.value)==null?void 0:q.price))+" ₽",1)]),(r(!0),_(E,null,ge((z=(Z=t.value)==null?void 0:Z.discounts)==null?void 0:z.Bonuses,i=>(r(),_("div",Ke,[o("div",qe,"Скидка "+d(i==null?void 0:i.Discount)+"% на "+d(i==null?void 0:i.Days)+" дней",1),o("div",Ze,"-"+d(p(i==null?void 0:i.Economy))+" ₽",1)]))),256)),_e(o("div",ze,[Ge,o("div",He,[o("span",null,[o("s",null,d(p((G=t.value)==null?void 0:G.price))+" ₽",1)]),o("span",Je,"-"+d(f())+" ₽",1)])],512),[[ue,((Q=(J=(H=t.value)==null?void 0:H.discounts)==null?void 0:J.Bonuses)==null?void 0:Q.length)>0]])],64)):O("",!0),_e(o("div",Qe,[o("div",We,d(((W=c.value[0])==null?void 0:W.ConsiderTypeID)==="Upon"?"Итого":`Итого за ${(X=t.value)==null?void 0:X.label}`),1),o("div",Xe,d(((Y=c.value[0])==null?void 0:Y.ConsiderTypeID)==="Upon"?(ee=c.value[0])==null?void 0:ee.CostMonth:p(((oe=t.value)==null?void 0:oe.price)-f()))+" ₽",1)],512),[[ue,((se=t.value)==null?void 0:se.price)||((te=c.value[0])==null?void 0:te.ConsiderTypeID)==="Upon"]])]),I(e,{class:"btn--wide",label:parseFloat((ae=w.value[n.value.ContractID])==null?void 0:ae.Balance)>=(p(((le=t.value)==null?void 0:le.price)-f())??0)?"Оплатить c баланса договора":"Добавить в корзину",disabled:(t.value===null||((ne=t.value)==null?void 0:ne.price)===null)&&((ce=c.value[0])==null?void 0:ce.ConsiderTypeID)!=="Upon","is-loading":D.value,onClick:a[1]||(a[1]=i=>B())},null,8,["label","disabled","is-loading"])])])):(r(),_("div",Ye,[o("div",eo,[I(h,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(r(),_("div",oo,[o("div",so,[I(h,{label:"не удалось найти заказ хостинга"})])]))],64)}}},Io=Ie(to,[["__scopeId","data-v-02715a80"]]);export{Io as default};

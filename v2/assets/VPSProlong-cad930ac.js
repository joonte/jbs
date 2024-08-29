import{E as le}from"./EmptyStateBlock-9f752916.js";import{_ as ce}from"./ButtonDefault-7c216fc6.js";import{_ as ne}from"./BlockOrderPayBalance-e0feb4e8.js";import{_ as re}from"./ClausesKeeper-eb954229.js";import{_ as _e,e as ie,a7 as de,r as g,g as k,h as ue,O as ve,ag as f,o as l,c,b as p,a as t,F as D,t as n,x as pe,k as me,Z as Y,ae as ee,p as he,l as ke}from"./index-5e313302.js";import{u as Se}from"./vps-80bde7d9.js";import{u as be}from"./contracts-3351d68f.js";import{B as ye}from"./BlockBalanceAgreement-bb87adc1.js";import"./bootstrap-vue-next.es-a19a8b41.js";import"./IconHelp-37cb81ff.js";import"./globalActions-a7429ad5.js";import"./BasicInput-be1c88a0.js";import"./component-6a5a025c.js";import"./IconClose-1f889a2e.js";import"./IconArrow-555f9d45.js";const P=S=>(he("data-v-77251004"),S=S(),ke(),S),ge={key:0,class:"vps-scheme-page"},fe={class:"section"},De={class:"container"},Pe=P(()=>t("div",{class:"section-header"},[t("h1",{class:"section-title"},"Оплата заказа VPS/VDS сервера")],-1)),Ie={key:0,class:"section"},Ve={class:"container"},Be={class:"total-block"},we={class:"total-block__row divider-bottom"},Ce=P(()=>t("div",{class:"total-block__col"},"Цена тарифа",-1)),Ee={class:"total-block__col"},Oe={class:"total-block__row no-divider"},xe={class:"total-block__col"},Le={class:"total-block__col"},Fe={class:"total-block__row total-block__row--big economy-line"},Re=P(()=>t("div",{class:"total-block__col red"},"Выгода",-1)),Ne={class:"total-block__col flex-col"},Ae={class:"red"},Ke={class:"total-block__row total-block__row--big"},Te={class:"total-block__col"},je={class:"total-block__col"},Ue={key:1,class:"section"},$e={class:"container"},He={key:1,class:"section"},Me={class:"container"},Ze={__name:"VPSProlong",async setup(S){let _,i;const r=Se(),I=be(),V=ie(),te=de(),o=g(null),m=g(null),y=g(!1),oe=k(()=>Object.keys(r.vpsSchemes).map(e=>r.vpsSchemes[e])),d=k(()=>Object.keys(r.vpsList).map(e=>r.vpsList[e]).find(e=>e.OrderID===te.params.id)),B=k(()=>I.contractsList);console.log("getContracts",B);const h=k(()=>oe.value.find(e=>{var s;return(e==null?void 0:e.ID)===((s=d.value)==null?void 0:s.SchemeID)}));console.log("getSelectedPackage",h);function b(e){return Number(e).toFixed(2)||0}function w(){var s,u;let e=0;return(u=(s=o.value)==null?void 0:s.discounts)==null||u.Bonuses.map(v=>{e+=+v.Economy}),e}function se(e){o.value=e}function C(){var e,s;y.value=!0,r.VPSOrderPay({VPSOrderID:(e=d.value)==null?void 0:e.ID,DaysPay:(s=o.value)==null?void 0:s.actualDays,IsChange:!0}).then(u=>{let{result:v,error:O}=u;v==="SUCCESS"?V.push("/VPSOrders"):v==="BASKET"&&V.push("/Basket"),y.value=!0})}const E=e=>{e.key==="Enter"&&e.ctrlKey&&!ae.value&&C()},ae=k(()=>o.value===null||m.value===null);return ue(()=>{document.addEventListener("keyup",E)}),ve(()=>{document.removeEventListener("keyup",E)}),[_,i]=f(()=>r.fetchVPSSchemes()),await _,i(),[_,i]=f(()=>I.fetchContracts()),await _,i(),[_,i]=f(()=>r.fetchVPS().then(()=>{var e;m.value=(e=d.value)==null?void 0:e.ContractID})),await _,i(),(e,s)=>{var L,F,R,N,A,K,T,j,U,$,H,M,Z,q,z,G,J,Q,W,X;const u=re,v=ne,O=ce,x=le;return l(),c(D,null,[p(ye),d.value?(l(),c("div",ge,[t("div",fe,[t("div",De,[Pe,p(u,{partition:"Header:/VPSSchemes"})])]),p(v,{contractID:m.value,scheme:h.value,serviceID:"30000",daysRemainded:(L=d.value)==null?void 0:L.DaysRemainded,isOrderID:!0,orderID:(F=d.value)==null?void 0:F.OrderID,onSelect:se},null,8,["contractID","scheme","daysRemainded","orderID"]),(R=h.value)!=null&&R.IsPayed&&((N=h.value)!=null&&N.IsProlong)||!((A=h.value)!=null&&A.IsPayed)?(l(),c("div",Ie,[t("div",Ve,[t("div",Be,[((j=(T=(K=o.value)==null?void 0:K.discounts)==null?void 0:T.Bonuses)==null?void 0:j.length)>0?(l(),c(D,{key:0},[t("div",we,[Ce,t("div",Ee,n(b((U=o.value)==null?void 0:U.price))+" ₽",1)]),(l(!0),c(D,null,pe((H=($=o.value)==null?void 0:$.discounts)==null?void 0:H.Bonuses,a=>(l(),c("div",Oe,[t("div",xe,"Скидка "+n(a==null?void 0:a.Discount)+"% на "+n(a==null?void 0:a.Days)+" дней",1),t("div",Le,"-"+n(b(a==null?void 0:a.Economy))+" ₽",1)]))),256))],64)):me("",!0),Y(t("div",Fe,[Re,t("div",Ne,[t("span",null,[t("s",null,n(b((M=o.value)==null?void 0:M.price))+" ₽",1)]),t("span",Ae,"-"+n(w())+" ₽",1)])],512),[[ee,((z=(q=(Z=o.value)==null?void 0:Z.discounts)==null?void 0:q.Bonuses)==null?void 0:z.length)>0]]),Y(t("div",Ke,[t("div",Te,"Итого за "+n((G=o.value)==null?void 0:G.label),1),t("div",je,n(b(((J=o.value)==null?void 0:J.price)-w()))+" ₽",1)],512),[[ee,(Q=o.value)==null?void 0:Q.price]])]),p(O,{class:"btn--wide",label:((W=B.value[m.value])==null?void 0:W.Balance)>((X=o.value)==null?void 0:X.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||m.value===null,"is-loading":y.value,onClick:s[0]||(s[0]=a=>C())},null,8,["label","disabled","is-loading"])])])):(l(),c("div",Ue,[t("div",$e,[p(x,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(l(),c("div",He,[t("div",Me,[p(x,{label:"Заказ VPS/VDS сервера не найден"})])]))],64)}}},nt=_e(Ze,[["__scopeId","data-v-77251004"]]);export{nt as default};

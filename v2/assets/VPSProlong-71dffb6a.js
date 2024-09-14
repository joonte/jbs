import{E as le}from"./EmptyStateBlock-cd0c2748.js";import{_ as ce}from"./ButtonDefault-0006f72a.js";import{_ as ne}from"./BlockOrderPayBalance-c29bc7b2.js";import{_ as re}from"./ClausesKeeper-3a1c11ba.js";import{_ as _e,e as ie,a7 as de,r as f,g as h,i as ue,O as ve,ag as g,o as l,c,b as p,a as t,F as D,t as n,x as pe,k as me,Z as X,ae as Y,p as he,l as be}from"./index-b5b5ce3d.js";import{u as ke}from"./vps-26e9e5ee.js";import{u as ye}from"./contracts-74f05bb7.js";import{B as Se}from"./BlockBalanceAgreement-a016b94f.js";import"./bootstrap-vue-next.es-37aa63ea.js";import"./IconHelp-12aa8ba1.js";import"./globalActions-6936f67f.js";import"./BasicInput-a3395017.js";import"./component-2368399e.js";import"./IconClose-cd6d3917.js";import"./IconArrow-fddabae2.js";const I=b=>(he("data-v-3e9de59b"),b=b(),be(),b),fe={key:0,class:"vps-scheme-page"},ge={class:"section"},De={class:"container"},Ie=I(()=>t("div",{class:"section-header"},[t("h1",{class:"section-title"},"Оплата заказа VPS/VDS сервера")],-1)),Pe={key:0,class:"section"},Ve={class:"container"},Be={class:"total-block"},we={class:"total-block__row divider-bottom"},Ee=I(()=>t("div",{class:"total-block__col"},"Цена тарифа",-1)),Ce={class:"total-block__col"},Oe={class:"total-block__row no-divider"},xe={class:"total-block__col"},Le={class:"total-block__col"},Fe={class:"total-block__row total-block__row--big economy-line"},Re=I(()=>t("div",{class:"total-block__col red"},"Выгода",-1)),Ne={class:"total-block__col flex-col"},Ae={class:"red"},Ke={class:"total-block__row total-block__row--big"},Te={class:"total-block__col"},je={class:"total-block__col"},Ue={key:1,class:"section"},$e={class:"container"},He={key:1,class:"section"},Me={class:"container"},Ze={__name:"VPSProlong",async setup(b){let _,i;const r=ke(),P=ye(),V=ie(),ee=de(),o=f(null),m=f(null),S=f(!1),te=h(()=>Object.keys(r.vpsSchemes).map(e=>r.vpsSchemes[e])),d=h(()=>Object.keys(r.vpsList).map(e=>r.vpsList[e]).find(e=>e.OrderID===ee.params.id)),oe=h(()=>P.contractsList),k=h(()=>te.value.find(e=>{var s;return(e==null?void 0:e.ID)===((s=d.value)==null?void 0:s.SchemeID)}));function y(e){return Number(e).toFixed(2)||0}function B(){var s,u;let e=0;return(u=(s=o.value)==null?void 0:s.discounts)==null||u.Bonuses.map(v=>{e+=+v.Economy}),e}function se(e){o.value=e}function w(){var e,s;S.value=!0,r.VPSOrderPay({VPSOrderID:(e=d.value)==null?void 0:e.ID,DaysPay:(s=o.value)==null?void 0:s.actualDays,IsChange:!0}).then(u=>{let{result:v,error:C}=u;v==="SUCCESS"?V.push("/VPSOrders"):v==="BASKET"&&V.push("/Basket"),S.value=!0})}const E=e=>{e.key==="Enter"&&e.ctrlKey&&!ae.value&&w()},ae=h(()=>o.value===null||m.value===null);return ue(()=>{document.addEventListener("keyup",E)}),ve(()=>{document.removeEventListener("keyup",E)}),[_,i]=g(()=>r.fetchVPSSchemes()),await _,i(),[_,i]=g(()=>P.fetchContracts()),await _,i(),[_,i]=g(()=>r.fetchVPS().then(()=>{var e;m.value=(e=d.value)==null?void 0:e.ContractID})),await _,i(),(e,s)=>{var x,L,F,R,N,A,K,T,j,U,$,H,M,Z,q,z,G,J,Q,W;const u=re,v=ne,C=ce,O=le;return l(),c(D,null,[p(Se),d.value?(l(),c("div",fe,[t("div",ge,[t("div",De,[Ie,p(u,{partition:"Header:/VPSSchemes"})])]),p(v,{contractID:m.value,scheme:k.value,serviceID:"30000",daysRemainded:(x=d.value)==null?void 0:x.DaysRemainded,isOrderID:!0,orderID:(L=d.value)==null?void 0:L.OrderID,onSelect:se},null,8,["contractID","scheme","daysRemainded","orderID"]),(F=k.value)!=null&&F.IsPayed&&((R=k.value)!=null&&R.IsProlong)||!((N=k.value)!=null&&N.IsPayed)?(l(),c("div",Pe,[t("div",Ve,[t("div",Be,[((T=(K=(A=o.value)==null?void 0:A.discounts)==null?void 0:K.Bonuses)==null?void 0:T.length)>0?(l(),c(D,{key:0},[t("div",we,[Ee,t("div",Ce,n(y((j=o.value)==null?void 0:j.price))+" ₽",1)]),(l(!0),c(D,null,pe(($=(U=o.value)==null?void 0:U.discounts)==null?void 0:$.Bonuses,a=>(l(),c("div",Oe,[t("div",xe,"Скидка "+n(a==null?void 0:a.Discount)+"% на "+n(a==null?void 0:a.Days)+" дней",1),t("div",Le,"-"+n(y(a==null?void 0:a.Economy))+" ₽",1)]))),256))],64)):me("",!0),X(t("div",Fe,[Re,t("div",Ne,[t("span",null,[t("s",null,n(y((H=o.value)==null?void 0:H.price))+" ₽",1)]),t("span",Ae,"-"+n(B())+" ₽",1)])],512),[[Y,((q=(Z=(M=o.value)==null?void 0:M.discounts)==null?void 0:Z.Bonuses)==null?void 0:q.length)>0]]),X(t("div",Ke,[t("div",Te,"Итого за "+n((z=o.value)==null?void 0:z.label),1),t("div",je,n(y(((G=o.value)==null?void 0:G.price)-B()))+" ₽",1)],512),[[Y,(J=o.value)==null?void 0:J.price]])]),p(C,{class:"btn--wide",label:((Q=oe.value[m.value])==null?void 0:Q.Balance)>((W=o.value)==null?void 0:W.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||m.value===null,"is-loading":S.value,onClick:s[0]||(s[0]=a=>w())},null,8,["label","disabled","is-loading"])])])):(l(),c("div",Ue,[t("div",$e,[p(O,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(l(),c("div",He,[t("div",Me,[p(O,{label:"Заказ VPS/VDS сервера не найден"})])]))],64)}}},nt=_e(Ze,[["__scopeId","data-v-3e9de59b"]]);export{nt as default};

import{_ as et}from"./ButtonDefault-4233c21f.js";import{_ as lt}from"./BlockOrderPayBalance-f20d5ae2.js";import{_ as ot}from"./BlockSelectContract-394eb8bc.js";import{_ as at}from"./ClausesKeeper-ce1dcc35.js";import{_ as ct,a7 as nt,e as it,S as _t,r as B,g as S,i as rt,O as dt,ag as O,o as b,c as p,b as h,a as s,t as a,F as g,x as Q,k as ut,Z as W,a9 as X,p as vt,l as bt}from"./index-00a0bf0d.js";import{u as pt}from"./dedicatedServer-88b6ea12.js";import{u as mt}from"./contracts-8ab155ab.js";import{B as ht}from"./BlockBalanceAgreement-fb81df3d.js";import"./bootstrap-vue-next.es-be8a8de1.js";import"./IconHelp-6dba2040.js";import"./globalActions-72d167c2.js";import"./BasicInput-95ab4d38.js";import"./component-89555e8b.js";import"./multiselect-9faec1a5.js";import"./IconClose-97a82d3d.js";import"./IconArrow-74dc045b.js";const f=k=>(vt("data-v-a53ccb14"),k=k(),bt(),k),ft={class:"dedicated-server-order"},kt={class:"section"},Dt={class:"container"},yt={class:"section-header"},xt={class:"section-title"},It=f(()=>s("div",{class:"section-label"},"Выделенный сервер",-1)),St={class:"section"},gt={class:"container"},wt={class:"list"},Bt={class:"list-col list-col--xl"},Ot={class:"list-item"},Ct=f(()=>s("div",{class:"list-item__row"},[s("div",{class:"list-item__title"},"Конфигурация")],-1)),Et={class:"list-item__row list-item__table"},Nt={class:"list-item__table-col"},Lt={class:"list-item__label"},Pt={class:"list-item__text"},Vt=f(()=>s("div",{class:"section"},[s("div",{class:"container"},[s("div",{class:"section-subtitle"},"Что входит в стоимость"),s("div",{class:"text"},[s("p",null,"Для подключения дополнительных услуг"),s("ul",null,[s("li",null,"Размещение в РФ, Москва"),s("li",null,"IP адрес, с возможностью расширения"),s("li",null,"Бесплатная панель управления ISPManager Lite"),s("li",null,"Круглосуточная поддержка")])])])],-1)),Ft={class:"section"},Ut={class:"container"},$t={class:"total-block"},At={class:"total-block__row divider-bottom"},Kt=f(()=>s("div",{class:"total-block__col"},"Цена тарифа",-1)),Rt={class:"total-block__col"},Tt={class:"total-block__row no-divider"},jt={class:"total-block__col"},Mt={class:"total-block__col"},Zt={class:"total-block__row total-block__row--big economy-line"},qt=f(()=>s("div",{class:"total-block__col red"},"Выгода",-1)),zt={class:"total-block__col flex-col"},Gt={class:"red"},Ht={class:"total-block__row total-block__row--big"},Jt={class:"total-block__col"},Qt={class:"total-block__col"},Wt={__name:"DSSchemesOrder",async setup(k){let r,d;const C=mt(),i=pt(),E=nt(),N=it();_t("emitter");const o=B(null),D=B(!1),c=B(null),m=S(()=>C.contractsList),u=S(()=>i==null?void 0:i.DSSchemes),y=S(()=>{var t;return(t=u==null?void 0:u.value)==null?void 0:t[E.params.id]}),L=t=>{t.key==="Enter"&&t.ctrlKey&&!Y.value&&V()},Y=S(()=>o.value===null||c.value===null);rt(()=>{document.addEventListener("keyup",L)}),dt(()=>{document.removeEventListener("keyup",L)});function tt(t){o.value=t}function P(){var e,_;let t=0;return(_=(e=o.value)==null?void 0:e.discounts)==null||_.Bonuses.map(n=>{t+=+n.Economy}),t}function x(t){var v;const e=Number((v=y.value)==null?void 0:v.CostInstall)||0,_=Number(t)||0,n=(e+_).toFixed(2);return Number(n)}function st(){var e;const t=(e=u==null?void 0:u.value)==null?void 0:e[E.params.id];return[{label:"Процессор",text:(t==null?void 0:t.CPU)||"-"},{label:"Объём оперативной памяти",text:(t==null?void 0:t.ram)||"-"},{label:"Характеристики жёстких дисков",text:(t==null?void 0:t.disks)||"-"},{label:"Предустановленная ОС",text:(t==null?void 0:t.OS)||"-"},{label:"RAID",text:(t==null?void 0:t.raid)||"-"},{label:"Месячный трафик",text:t!=null&&t.trafflimit?`${t==null?void 0:t.trafflimit} ГБ`:"-"},{label:"Скорость канала",text:t!=null&&t.chrate?`${t==null?void 0:t.chrate} Мбит/с`:"-"}]}function V(){D.value=!0,i.DSOrder({ContractID:c.value,DSSchemeID:y.value.ID}).then(({result:t,info:e,error:_})=>{var n;t==="SUCCESS"?i.DSOrderPay({DSOrderID:e==null?void 0:e.DSOrderID,DaysPay:(n=o.value)==null?void 0:n.actualDays,IsChange:!0}).then(v=>{let{result:I,error:w}=v;I==="SUCCESS"?N.push("/DSOrders"):I==="BASKET"&&N.push("/Basket"),D.value=!1}):D.value=!1})}return[r,d]=O(()=>i.fetchDSOrders()),await r,d(),[r,d]=O(()=>i.fetchDSSchemes()),await r,d(),[r,d]=O(()=>C.fetchContracts().then(()=>{var t;m.value&&(c.value=(t=Object.keys(m==null?void 0:m.value))==null?void 0:t[0])})),await r,d(),(t,e)=>{var w,F,U,$,A,K,R,T,j,M,Z,q,z,G,H,J;const _=at,n=ot,v=lt,I=et;return b(),p(g,null,[h(ht),s("div",ft,[s("div",kt,[s("div",Dt,[s("div",yt,[s("h1",xt,a((w=y.value)==null?void 0:w.Name),1),It]),h(_)])]),s("div",St,[s("div",gt,[s("div",wt,[s("div",Bt,[s("div",Ot,[Ct,s("div",Et,[(b(!0),p(g,null,Q(st(),l=>(b(),p("div",Nt,[s("div",Lt,a(l.label),1),s("div",Pt,a(l.text),1)]))),256))])])])])])]),Vt,h(n,{title:"Оформление услуги",modelValue:c.value,"onUpdate:modelValue":e[0]||(e[0]=l=>c.value=l)},null,8,["modelValue"]),h(v,{contractID:c.value,scheme:y.value,serviceID:"40000",isOrderID:!1,onSelect:tt},null,8,["contractID","scheme"]),s("div",Ft,[s("div",Ut,[s("div",$t,[(($=(U=(F=o.value)==null?void 0:F.discounts)==null?void 0:U.Bonuses)==null?void 0:$.length)>0?(b(),p(g,{key:0},[s("div",At,[Kt,s("div",Rt,a(x((A=o.value)==null?void 0:A.price))+" ₽",1)]),(b(!0),p(g,null,Q((R=(K=o.value)==null?void 0:K.discounts)==null?void 0:R.Bonuses,l=>(b(),p("div",Tt,[s("div",jt,"Скидка "+a(l==null?void 0:l.Discount)+"% на "+a(l==null?void 0:l.Days)+" дней",1),s("div",Mt,"-"+a(x(l==null?void 0:l.Economy))+" ₽",1)]))),256))],64)):ut("",!0),W(s("div",Zt,[qt,s("div",zt,[s("span",null,[s("s",null,a(x((T=o.value)==null?void 0:T.price))+" ₽",1)]),s("span",Gt,"-"+a(P())+" ₽",1)])],512),[[X,((Z=(M=(j=o.value)==null?void 0:j.discounts)==null?void 0:M.Bonuses)==null?void 0:Z.length)>0]]),W(s("div",Ht,[s("div",Jt,"Итого за "+a((q=o.value)==null?void 0:q.label),1),s("div",Qt,a(x(((z=o.value)==null?void 0:z.price)-P()))+" ₽",1)],512),[[X,(G=o.value)==null?void 0:G.price]])]),h(I,{class:"btn--wide",label:((H=m.value[c.value])==null?void 0:H.Balance)>((J=o.value)==null?void 0:J.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||c.value===null,"is-loading":D.value,onClick:e[1]||(e[1]=l=>V())},null,8,["label","disabled","is-loading"])])])])],64)}}},bs=ct(Wt,[["__scopeId","data-v-a53ccb14"]]);export{bs as default};

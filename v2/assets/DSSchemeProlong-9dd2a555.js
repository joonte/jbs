import{E as it}from"./EmptyStateBlock-5a6a4cd4.js";import{_ as _t}from"./ButtonDefault-cd183bf8.js";import{_ as nt}from"./BlockOrderPayBalance-8fab58d2.js";import{_ as rt}from"./ClausesKeeper-b9906182.js";import{_ as dt,r as W,a6 as ut,e as vt,R as bt,g as k,af as D,o as i,c as n,a as s,t as c,b as y,F as I,v as X,k as pt,Y as Z,ad as tt,x as st,p as ht,l as mt}from"./index-1c309346.js";import{u as ft}from"./dedicatedServer-2cc92dfc.js";import{u as kt}from"./contracts-7b919c67.js";import"./bootstrap-vue-next.es-b799f76d.js";import"./IconHelp-648316fb.js";import"./globalActions-8efdfe11.js";import"./BasicInput-33bf464f.js";import"./component2-139d1cad.js";import"./component-a2c8255f.js";import"./IconClose-7cf50ca7.js";const m=p=>(ht("data-v-0a5e2426"),p=p(),mt(),p),Dt={key:0,class:"domain-scheme"},yt={class:"section"},It={class:"container"},gt={class:"section-header"},xt=m(()=>s("h1",{class:"section-title"},"Оплата заказа выделенного сервера",-1)),wt={class:"section-label"},Bt={class:"section"},Ct={class:"container"},Et={class:"list"},Ot={class:"list-col list-col--xl"},Pt={class:"list-item"},St=m(()=>s("div",{class:"list-item__row"},[s("div",{class:"list-item__title"},"Конфигурация")],-1)),$t={class:"list-item__row list-item__table"},Ft={class:"list-item__table-col"},Lt={class:"list-item__label"},Nt={class:"list-item__text"},Rt={class:"section"},At={class:"container"},Tt={class:"total-block"},Vt={class:"total-block__row divider-bottom"},jt=m(()=>s("div",{class:"total-block__col"},"Цена тарифа",-1)),Ut={class:"total-block__col"},Kt={class:"total-block__row no-divider"},Yt={class:"total-block__col"},qt={class:"total-block__col"},zt={class:"total-block__row total-block__row--big economy-line"},Gt=m(()=>s("div",{class:"total-block__col red"},"Выгода",-1)),Ht={class:"total-block__col flex-col"},Jt={class:"red"},Mt={class:"total-block__row total-block__row--big"},Qt={class:"total-block__col"},Wt={class:"total-block__col"},Xt={key:1,class:"section"},Zt={class:"container"},ts={__name:"DSSchemeProlong",async setup(p){let r,d;const o=W(null),l=ft(),g=kt(),ot=ut(),x=vt();bt("emitter");const f=W(!1),_=k(()=>Object.keys(l==null?void 0:l.DSList).map(t=>l==null?void 0:l.DSList[t]).find(t=>t.OrderID===ot.params.id)),b=k(()=>{var t,a;return(a=l==null?void 0:l.DSSchemes)==null?void 0:a[(t=_.value)==null?void 0:t.SchemeID]}),et=k(()=>g.contractsList);function w(){var a,u;let t=0;return(u=(a=o.value)==null?void 0:a.discounts)==null||u.Bonuses.map(v=>{t+=+v.Economy}),t}function h(t){return Number(t).toFixed(2)||0}function lt(){const t=b==null?void 0:b.value;return[{label:"Процессор",text:(t==null?void 0:t.CPU)||"-"},{label:"Объём оперативной памяти",text:(t==null?void 0:t.ram)||"-"},{label:"Характеристики жёстких дисков",text:(t==null?void 0:t.disks)||"-"},{label:"Предустановленная ОС",text:(t==null?void 0:t.OS)||"-"},{label:"RAID",text:(t==null?void 0:t.raid)||"-"},{label:"Месячный трафик",text:t!=null&&t.trafflimit?`${t==null?void 0:t.trafflimit} ГБ`:"-"},{label:"Скорость канала",text:t!=null&&t.chrate?`${t==null?void 0:t.chrate} Мбит/с`:"-"}]}function at(){var t,a;f.value=!0,l.DSOrderPay({DSOrderID:(t=_.value)==null?void 0:t.ID,DaysPay:(a=o.value)==null?void 0:a.actualDays,IsChange:!0}).then(u=>{let{result:v,error:B}=u;v==="SUCCESS"?x.push("/DSOrders"):v==="BASKET"&&x.push("/Basket"),f.value=!1})}function ct(t){o.value=t}return[r,d]=D(()=>l.fetchDSOrders()),await r,d(),[r,d]=D(()=>l.fetchDSSchemes()),await r,d(),[r,d]=D(()=>g.fetchContracts()),await r,d(),(t,a)=>{var E,O,P,S,$,F,L,N,R,A,T,V,j,U,K,Y,q,z,G,H,J,M,Q;const u=rt,v=nt,B=_t,C=it;return(E=_.value)!=null&&E.ID&&((O=b.value)!=null&&O.ID)?(i(),n("div",Dt,[s("div",yt,[s("div",It,[s("div",gt,[xt,s("div",wt,c((P=_.value)!=null&&P.IP?`#${(S=_.value)==null?void 0:S.IP}`:""),1)]),y(u)])]),s("div",Bt,[s("div",Ct,[s("div",Et,[s("div",Ot,[s("div",Pt,[St,s("div",$t,[(i(!0),n(I,null,X(lt(),e=>(i(),n("div",Ft,[s("div",Lt,c(e.label),1),s("div",Nt,c(e.text),1)]))),256))])])])])])]),y(v,{contractID:($=_.value)==null?void 0:$.ContractID,scheme:b.value,serviceID:"40000",isOrderID:!1,onSelect:ct},null,8,["contractID","scheme"]),s("div",Rt,[s("div",At,[s("div",Tt,[((N=(L=(F=o.value)==null?void 0:F.discounts)==null?void 0:L.Bonuses)==null?void 0:N.length)>0?(i(),n(I,{key:0},[s("div",Vt,[jt,s("div",Ut,c(h((R=o.value)==null?void 0:R.price))+" ₽",1)]),(i(!0),n(I,null,X((T=(A=o.value)==null?void 0:A.discounts)==null?void 0:T.Bonuses,e=>(i(),n("div",Kt,[s("div",Yt,"Скидка "+c(e==null?void 0:e.Discount)+"% на "+c(e==null?void 0:e.Days)+" дней",1),s("div",qt,"-"+c(h(e==null?void 0:e.Economy))+" ₽",1)]))),256))],64)):pt("",!0),Z(s("div",zt,[Gt,s("div",Ht,[s("span",null,[s("s",null,c(h((V=o.value)==null?void 0:V.price))+" ₽",1)]),s("span",Jt,"-"+c(w())+" ₽",1)])],512),[[tt,((K=(U=(j=o.value)==null?void 0:j.discounts)==null?void 0:U.Bonuses)==null?void 0:K.length)>0]]),Z(s("div",Mt,[s("div",Qt,"Итого за "+c((Y=o.value)==null?void 0:Y.label),1),s("div",Wt,c(h(((q=o.value)==null?void 0:q.price)-w()))+" ₽",1)],512),[[tt,(z=o.value)==null?void 0:z.price]])]),y(B,{class:"btn--wide",label:((H=et.value[(G=_.value)==null?void 0:G.ContractID])==null?void 0:H.Balance)>((J=o.value)==null?void 0:J.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:o.value===null||((M=o.value)==null?void 0:M.price)===null,"is-loading":f.value,onClick:a[0]||(a[0]=e=>at())},null,8,["label","disabled","is-loading"])])])])):(i(),n("div",Xt,[s("div",Zt,[(Q=_.value)!=null&&Q.ID?(i(),st(C,{key:0,label:"Заказ не может быть продлен"})):(i(),st(C,{key:1,label:"Заказ не найден"}))])]))}}},ps=dt(ts,[["__scopeId","data-v-0a5e2426"]]);export{ps as default};

import{E as kt}from"./EmptyStateBlock-d1623b92.js";import{_ as yt}from"./BlockOrderPayBalance-605ea2fc.js";import{_ as ft}from"./ClausesKeeper-d8c25fea.js";import{_ as Dt}from"./ButtonDefault-7609cc9e.js";import{_ as It,r as _t,f as gt,e as wt,U as xt,h as y,i as Bt,P as Ct,ag as w,o as c,c as _,b as p,a as s,t as i,F as f,y as rt,l as Pt,$ as dt,a9 as ut,z as vt,p as Et,m as Ot}from"./index-400feb76.js";import{u as Lt}from"./dedicatedServer-5c757cde.js";import{u as St}from"./contracts-d1f15c94.js";import{B as $t}from"./BlockBalanceAgreement-9b1029a8.js";import"./bootstrap-vue-next.es-03cca149.js";import"./IconHelp-39b892d8.js";import"./globalActions-9f4dbd2c.js";import"./component-5bf95be3.js";import"./IconClose-20ed8b3b.js";import"./IconArrow-10b76666.js";const D=m=>(Et("data-v-926263cc"),m=m(),Ot(),m),Rt={key:0,class:"domain-scheme"},Ft={class:"section"},At={class:"container"},Nt={class:"section-header"},Ut={class:"section-header__wrapper"},Kt=D(()=>s("h1",{class:"section-title"},"Оплата заказа выделенного сервера",-1)),Tt={class:"section-label"},Vt={class:"section"},jt={class:"container"},zt={class:"list"},Mt={class:"list-col list-col--xl"},qt={class:"list-item"},Gt=D(()=>s("div",{class:"list-item__row"},[s("div",{class:"list-item__title"},"Конфигурация")],-1)),Ht={class:"list-item__row list-item__table"},Jt={class:"list-item__table-col"},Qt={class:"list-item__label"},Wt={class:"list-item__text"},Xt={key:0,class:"section"},Yt={class:"container"},Zt={class:"total-block"},ts={class:"total-block__row divider-bottom"},ss=D(()=>s("div",{class:"total-block__col"},"Цена тарифа",-1)),es={class:"total-block__col"},os={class:"total-block__row no-divider"},ls={class:"total-block__col"},as={class:"total-block__col"},cs={class:"total-block__row total-block__row--big economy-line"},ns=D(()=>s("div",{class:"total-block__col red"},"Выгода",-1)),is={class:"total-block__col flex-col"},_s={class:"red"},rs={class:"total-block__row total-block__row--big"},ds={class:"total-block__col"},us={class:"total-block__col"},vs={key:1,class:"section"},bs={class:"container"},ps={key:1,class:"section"},ms={class:"container"},hs={__name:"DSSchemeProlong",async setup(m){let u,v;const e=_t(null),a=Lt(),x=St(),bt=gt(),B=wt();xt("emitter");const h=_t(!1),n=y(()=>Object.keys(a==null?void 0:a.DSList).map(t=>a==null?void 0:a.DSList[t]).find(t=>t.OrderID===bt.params.id)),r=y(()=>{var t,l;return(l=a==null?void 0:a.DSSchemes)==null?void 0:l[(t=n.value)==null?void 0:t.SchemeID]}),C=y(()=>x.contractsList),P=t=>{t.key==="Enter"&&t.ctrlKey&&!pt.value&&I()},pt=y(()=>{var t;return e.value===null||((t=e.value)==null?void 0:t.price)===null});Bt(()=>{document.addEventListener("keyup",P)}),Ct(()=>{document.removeEventListener("keyup",P)});function E(){var l,d;let t=0;return(d=(l=e.value)==null?void 0:l.discounts)==null||d.Bonuses.map(b=>{t+=+b.Economy}),t}function k(t){return Number(t).toFixed(2)||0}function mt(){const t=r==null?void 0:r.value;return[{label:"Процессор",text:(t==null?void 0:t.CPU)||"-"},{label:"Объём оперативной памяти",text:(t==null?void 0:t.ram)||"-"},{label:"Характеристики жёстких дисков",text:(t==null?void 0:t.disks)||"-"},{label:"Предустановленная ОС",text:(t==null?void 0:t.OS)||"-"},{label:"RAID",text:(t==null?void 0:t.raid)||"-"},{label:"Месячный трафик",text:t!=null&&t.trafflimit?`${t==null?void 0:t.trafflimit} ГБ`:"-"},{label:"Скорость канала",text:t!=null&&t.chrate?`${t==null?void 0:t.chrate} Мбит/с`:"-"}]}function I(){var t,l;h.value=!0,a.DSOrderPay({DSOrderID:(t=n.value)==null?void 0:t.ID,DaysPay:(l=e.value)==null?void 0:l.actualDays,IsChange:!0}).then(d=>{let{result:b,error:O}=d;b==="SUCCESS"?B.push("/DSOrders"):b==="BASKET"&&B.push("/Basket"),h.value=!1})}function ht(t){e.value=t}return[u,v]=w(()=>a.fetchDSOrders()),await u,v(),[u,v]=w(()=>a.fetchDSSchemes()),await u,v(),[u,v]=w(()=>x.fetchContracts()),await u,v(),(t,l)=>{var L,S,$,R,F,A,N,U,K,T,V,j,z,M,q,G,H,J,Q,W,X,Y,Z,tt,st,et,ot,lt,at,ct,nt,it;const d=Dt,b=ft,O=yt,g=kt;return c(),_(f,null,[p($t),(L=n.value)!=null&&L.ID&&((S=r.value)!=null&&S.ID)?(c(),_("div",Rt,[s("div",Ft,[s("div",At,[s("div",Nt,[s("div",Ut,[Kt,p(d,{class:"btn--wide",label:((R=C.value[($=n.value)==null?void 0:$.ContractID])==null?void 0:R.Balance)>((F=e.value)==null?void 0:F.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:e.value===null||((A=e.value)==null?void 0:A.price)===null,"is-loading":h.value,onClick:l[0]||(l[0]=o=>I())},null,8,["label","disabled","is-loading"])]),s("div",Tt,i((N=n.value)!=null&&N.IP?`#${(U=n.value)==null?void 0:U.IP}`:""),1)]),p(b)])]),s("div",Vt,[s("div",jt,[s("div",zt,[s("div",Mt,[s("div",qt,[Gt,s("div",Ht,[(c(!0),_(f,null,rt(mt(),o=>(c(),_("div",Jt,[s("div",Qt,i(o.label),1),s("div",Wt,i(o.text),1)]))),256))])])])])])]),p(O,{contractID:(K=n.value)==null?void 0:K.ContractID,scheme:r.value,serviceID:"40000",daysRemainded:(T=n.value)==null?void 0:T.DaysRemainded,isOrderID:!0,orderID:(V=n.value)==null?void 0:V.OrderID,onSelect:ht},null,8,["contractID","scheme","daysRemainded","orderID"]),(j=r.value)!=null&&j.IsPayed&&((z=r.value)!=null&&z.IsProlong)||!((M=r.value)!=null&&M.IsPayed)?(c(),_("div",Xt,[s("div",Yt,[s("div",Zt,[((H=(G=(q=e.value)==null?void 0:q.discounts)==null?void 0:G.Bonuses)==null?void 0:H.length)>0?(c(),_(f,{key:0},[s("div",ts,[ss,s("div",es,i(k((J=e.value)==null?void 0:J.price))+" ₽",1)]),(c(!0),_(f,null,rt((W=(Q=e.value)==null?void 0:Q.discounts)==null?void 0:W.Bonuses,o=>(c(),_("div",os,[s("div",ls,"Скидка "+i(o==null?void 0:o.Discount)+"% на "+i(o==null?void 0:o.Days)+" дней",1),s("div",as,"-"+i(k(o==null?void 0:o.Economy))+" ₽",1)]))),256))],64)):Pt("",!0),dt(s("div",cs,[ns,s("div",is,[s("span",null,[s("s",null,i(k((X=e.value)==null?void 0:X.price))+" ₽",1)]),s("span",_s,"-"+i(E())+" ₽",1)])],512),[[ut,((tt=(Z=(Y=e.value)==null?void 0:Y.discounts)==null?void 0:Z.Bonuses)==null?void 0:tt.length)>0]]),dt(s("div",rs,[s("div",ds,"Итого за "+i((st=e.value)==null?void 0:st.label),1),s("div",us,i(k(((et=e.value)==null?void 0:et.price)-E()))+" ₽",1)],512),[[ut,(ot=e.value)==null?void 0:ot.price]])]),p(d,{class:"btn--wide",label:((at=C.value[(lt=n.value)==null?void 0:lt.ContractID])==null?void 0:at.Balance)>((ct=e.value)==null?void 0:ct.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:e.value===null||((nt=e.value)==null?void 0:nt.price)===null,"is-loading":h.value,onClick:l[1]||(l[1]=o=>I())},null,8,["label","disabled","is-loading"])])])):(c(),_("div",vs,[s("div",bs,[p(g,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(c(),_("div",ps,[s("div",ms,[(it=n.value)!=null&&it.ID?(c(),vt(g,{key:0,label:"Заказ не может быть продлен"})):(c(),vt(g,{key:1,label:"Заказ не найден"}))])]))],64)}}},Ss=It(hs,[["__scopeId","data-v-926263cc"]]);export{Ss as default};

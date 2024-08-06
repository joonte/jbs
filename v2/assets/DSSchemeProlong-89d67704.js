import{E as hs}from"./EmptyStateBlock-68a1c33e.js";import{_ as ms}from"./BlockOrderPayBalance-6a44874e.js";import{_ as ks}from"./ClausesKeeper-55c14b7f.js";import{_ as fs}from"./ButtonDefault-dc248ec6.js";import{_ as ys,r as ns,a7 as Ds,e as Is,S as gs,g as I,ag as g,o as c,c as _,b as p,a as t,t as n,F as f,x as _s,k as xs,Z as rs,ae as ds,y as us,p as ws,l as Bs}from"./index-83b4b7ba.js";import{u as Cs}from"./dedicatedServer-5ad75190.js";import{u as Ps}from"./contracts-623ec7a8.js";import{B as Os}from"./BlockBalanceAgreement-c5fad736.js";import"./bootstrap-vue-next.es-8f3ae81a.js";import"./IconHelp-c62d5911.js";import"./globalActions-633eb799.js";import"./BasicInput-3f1e9fd6.js";import"./component-abb1f5a9.js";import"./IconClose-86aafc4e.js";import"./IconArrow-80c72216.js";const y=h=>(ws("data-v-35775665"),h=h(),Bs(),h),Es={key:0,class:"domain-scheme"},Ss={class:"section"},Rs={class:"container"},$s={class:"section-header"},Fs={class:"section-header__wrapper"},Ls=y(()=>t("h1",{class:"section-title"},"Оплата заказа выделенного сервера",-1)),As={class:"section-label"},Ns={class:"section"},Ts={class:"container"},Vs={class:"list"},js={class:"list-col list-col--xl"},Us={class:"list-item"},Ks=y(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Конфигурация")],-1)),Zs={class:"list-item__row list-item__table"},qs={class:"list-item__table-col"},zs={class:"list-item__label"},Gs={class:"list-item__text"},Hs={key:0,class:"section"},Js={class:"container"},Ms={class:"total-block"},Qs={class:"total-block__row divider-bottom"},Ws=y(()=>t("div",{class:"total-block__col"},"Цена тарифа",-1)),Xs={class:"total-block__col"},Ys={class:"total-block__row no-divider"},st={class:"total-block__col"},tt={class:"total-block__col"},et={class:"total-block__row total-block__row--big economy-line"},ot=y(()=>t("div",{class:"total-block__col red"},"Выгода",-1)),lt={class:"total-block__col flex-col"},at={class:"red"},ct={class:"total-block__row total-block__row--big"},it={class:"total-block__col"},nt={class:"total-block__col"},_t={key:1,class:"section"},rt={class:"container"},dt={key:1,class:"section"},ut={class:"container"},vt={__name:"DSSchemeProlong",async setup(h){let u,v;const e=ns(null),a=Cs(),x=Ps(),vs=Ds(),w=Is();gs("emitter");const m=ns(!1),i=I(()=>Object.keys(a==null?void 0:a.DSList).map(s=>a==null?void 0:a.DSList[s]).find(s=>s.OrderID===vs.params.id)),r=I(()=>{var s,l;return(l=a==null?void 0:a.DSSchemes)==null?void 0:l[(s=i.value)==null?void 0:s.SchemeID]}),B=I(()=>x.contractsList);function C(){var l,d;let s=0;return(d=(l=e.value)==null?void 0:l.discounts)==null||d.Bonuses.map(b=>{s+=+b.Economy}),s}function k(s){return Number(s).toFixed(2)||0}function bs(){const s=r==null?void 0:r.value;return[{label:"Процессор",text:(s==null?void 0:s.CPU)||"-"},{label:"Объём оперативной памяти",text:(s==null?void 0:s.ram)||"-"},{label:"Характеристики жёстких дисков",text:(s==null?void 0:s.disks)||"-"},{label:"Предустановленная ОС",text:(s==null?void 0:s.OS)||"-"},{label:"RAID",text:(s==null?void 0:s.raid)||"-"},{label:"Месячный трафик",text:s!=null&&s.trafflimit?`${s==null?void 0:s.trafflimit} ГБ`:"-"},{label:"Скорость канала",text:s!=null&&s.chrate?`${s==null?void 0:s.chrate} Мбит/с`:"-"}]}function P(){var s,l;m.value=!0,a.DSOrderPay({DSOrderID:(s=i.value)==null?void 0:s.ID,DaysPay:(l=e.value)==null?void 0:l.actualDays,IsChange:!0}).then(d=>{let{result:b,error:O}=d;b==="SUCCESS"?w.push("/DSOrders"):b==="BASKET"&&w.push("/Basket"),m.value=!1})}function ps(s){e.value=s}return[u,v]=g(()=>a.fetchDSOrders()),await u,v(),[u,v]=g(()=>a.fetchDSSchemes()),await u,v(),[u,v]=g(()=>x.fetchContracts()),await u,v(),(s,l)=>{var E,S,R,$,F,L,A,N,T,V,j,U,K,Z,q,z,G,H,J,M,Q,W,X,Y,ss,ts,es,os,ls,as,cs,is;const d=fs,b=ks,O=ms,D=hs;return c(),_(f,null,[p(Os),(E=i.value)!=null&&E.ID&&((S=r.value)!=null&&S.ID)?(c(),_("div",Es,[t("div",Ss,[t("div",Rs,[t("div",$s,[t("div",Fs,[Ls,p(d,{class:"btn--wide",label:(($=B.value[(R=i.value)==null?void 0:R.ContractID])==null?void 0:$.Balance)>((F=e.value)==null?void 0:F.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:e.value===null||((L=e.value)==null?void 0:L.price)===null,"is-loading":m.value,onClick:l[0]||(l[0]=o=>P())},null,8,["label","disabled","is-loading"])]),t("div",As,n((A=i.value)!=null&&A.IP?`#${(N=i.value)==null?void 0:N.IP}`:""),1)]),p(b)])]),t("div",Ns,[t("div",Ts,[t("div",Vs,[t("div",js,[t("div",Us,[Ks,t("div",Zs,[(c(!0),_(f,null,_s(bs(),o=>(c(),_("div",qs,[t("div",zs,n(o.label),1),t("div",Gs,n(o.text),1)]))),256))])])])])])]),p(O,{contractID:(T=i.value)==null?void 0:T.ContractID,scheme:r.value,serviceID:"40000",daysRemainded:(V=i.value)==null?void 0:V.DaysRemainded,isOrderID:!0,orderID:(j=i.value)==null?void 0:j.OrderID,onSelect:ps},null,8,["contractID","scheme","daysRemainded","orderID"]),(U=r.value)!=null&&U.IsPayed&&((K=r.value)!=null&&K.IsProlong)||!((Z=r.value)!=null&&Z.IsPayed)?(c(),_("div",Hs,[t("div",Js,[t("div",Ms,[((G=(z=(q=e.value)==null?void 0:q.discounts)==null?void 0:z.Bonuses)==null?void 0:G.length)>0?(c(),_(f,{key:0},[t("div",Qs,[Ws,t("div",Xs,n(k((H=e.value)==null?void 0:H.price))+" ₽",1)]),(c(!0),_(f,null,_s((M=(J=e.value)==null?void 0:J.discounts)==null?void 0:M.Bonuses,o=>(c(),_("div",Ys,[t("div",st,"Скидка "+n(o==null?void 0:o.Discount)+"% на "+n(o==null?void 0:o.Days)+" дней",1),t("div",tt,"-"+n(k(o==null?void 0:o.Economy))+" ₽",1)]))),256))],64)):xs("",!0),rs(t("div",et,[ot,t("div",lt,[t("span",null,[t("s",null,n(k((Q=e.value)==null?void 0:Q.price))+" ₽",1)]),t("span",at,"-"+n(C())+" ₽",1)])],512),[[ds,((Y=(X=(W=e.value)==null?void 0:W.discounts)==null?void 0:X.Bonuses)==null?void 0:Y.length)>0]]),rs(t("div",ct,[t("div",it,"Итого за "+n((ss=e.value)==null?void 0:ss.label),1),t("div",nt,n(k(((ts=e.value)==null?void 0:ts.price)-C()))+" ₽",1)],512),[[ds,(es=e.value)==null?void 0:es.price]])]),p(d,{class:"btn--wide",label:((ls=B.value[(os=i.value)==null?void 0:os.ContractID])==null?void 0:ls.Balance)>((as=e.value)==null?void 0:as.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:e.value===null||((cs=e.value)==null?void 0:cs.price)===null,"is-loading":m.value,onClick:l[1]||(l[1]=o=>P())},null,8,["label","disabled","is-loading"])])])):(c(),_("div",_t,[t("div",rt,[p(D,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(c(),_("div",dt,[t("div",ut,[(is=i.value)!=null&&is.ID?(c(),us(D,{key:0,label:"Заказ не может быть продлен"})):(c(),us(D,{key:1,label:"Заказ не найден"}))])]))],64)}}},Ot=ys(vt,[["__scopeId","data-v-35775665"]]);export{Ot as default};

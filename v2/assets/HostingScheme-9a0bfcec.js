import{E as be}from"./EmptyStateBlock-cd0c2748.js";import{_ as De}from"./ButtonDefault-0006f72a.js";import{_ as ke}from"./BlockOrderPayBalance-c29bc7b2.js";import{_ as re,a7 as ie,e as ue,r as w,i as _e,o as k,c as f,a as e,n as le,t as n,b as v,Z as W,ae as j,m as ae,F as A,S as fe,g as x,O as ye,ag as q,w as ce,x as ne,k as ge,p as Ie,l as Ce}from"./index-b5b5ce3d.js";import{u as Be}from"./hosting-552a9dd1.js";import{u as Ee}from"./contracts-74f05bb7.js";import{u as Oe}from"./domain-24e2548a.js";import{u as we}from"./services-18148891.js";import{_ as Ve}from"./paramsAccordionBlock-8a0da5ad.js";import{D as $e}from"./DomainCheckBlock-1fda6dd3.js";import{_ as Ne}from"./BlockSelectContract-03150afa.js";import{_ as Pe}from"./ClausesKeeper-3a1c11ba.js";import He from"./BlockAdditionalServices-11c39980.js";import{B as xe}from"./BlockBalanceAgreement-a016b94f.js";import"./bootstrap-vue-next.es-37aa63ea.js";import"./IconHelp-12aa8ba1.js";import"./globalActions-6936f67f.js";import"./BasicInput-a3395017.js";import"./component-2368399e.js";import"./IconArrow-fddabae2.js";/* empty css                                                                             */import"./multiselect-4f7d14de.js";import"./IconClose-cd6d3917.js";import"./ServiceListOrderSelection-c2e6f723.js";const Ae={class:"section"},Re={class:"container"},Fe={class:"section-nav"},Ue={__name:"BlockOrderServiceWrapper",props:{basic_title:{type:String,default:"basic_title"},modelValue:{type:String,default:""}},emits:["update:modelValue"],setup(V,{emit:u}){const _=V,m=ie(),I=ue(),a=w("");function p(){return m.fullPath.includes("?section=Services")}function T(d=null){d?(a.value="Services",I.replace({query:{section:"Services"}})):(a.value="",I.replace({query:{}})),u("update:modelValue",d||"")}return _e(()=>{m.query.section==="Services"&&(a.value="Services")}),(d,l)=>{const C=Pe;return k(),f(A,null,[e("div",Ae,[e("div",Re,[e("div",Fe,[e("div",{class:le(["section-nav__item",[p()?"":"active"]]),onClick:l[0]||(l[0]=$=>T())},n(_.basic_title),3),e("div",{class:le(["section-nav__item",[p()?"active":""]]),onClick:l[1]||(l[1]=$=>T("Services"))},"Услуги",2)])])]),v(C),e("div",null,[W(e("div",null,[ae(d.$slots,"page",{},void 0,!0)],512),[[j,a.value===""]]),W(e("div",null,[ae(d.$slots,"services",{},void 0,!0)],512),[[j,a.value!==""]])])],64)}}},Le=re(Ue,[["__scopeId","data-v-e8bb070c"]]);const G=V=>(Ie("data-v-ade0929d"),V=V(),Ce(),V),Te={class:"hosting-sheme"},Ke={class:"section"},qe={class:"container"},We={class:"section-header"},je={class:"section-title"},Ge=G(()=>e("div",{class:"section-label"},"Хостинг",-1)),ze={class:"section"},Me={class:"container"},Ze={class:"total-block"},Je={class:"total-block__row divider-bottom"},Qe=G(()=>e("div",{class:"total-block__col"},"Цена тарифа",-1)),Xe={class:"total-block__col"},Ye={class:"total-block__row no-divider"},et={class:"total-block__col"},tt={class:"total-block__col"},st={class:"total-block__row"},ot=G(()=>e("div",{class:"total-block__col"},"Дополнительные услуги",-1)),lt={class:"total-block__col"},at={class:"total-block__row no-divider"},ct={class:"total-block__col"},nt={class:"total-block__col"},rt={class:"total-block__row total-block__row--big economy-line"},it=G(()=>e("div",{class:"total-block__col red"},"Выгода",-1)),ut={class:"total-block__col flex-col"},_t={class:"red"},dt={class:"total-block__row total-block__row--big"},vt={class:"total-block__col"},mt={class:"total-block__col"},pt={key:1,class:"section"},ht={class:"container"},St={__name:"HostingScheme",async setup(V){let u,_;const m=Be(),I=Ee(),a=Oe(),p=we();fe("emitter");const T=ie(),d=ue(),l=w(null),C=w([]),$=w(!1),y=w(null),i=w(null),Z=w(""),J=t=>{t.key==="Enter"&&t.ctrlKey&&!de.value&&ee()},de=x(()=>l.value===null||y.value===null),N=x(()=>{var t;return(t=m==null?void 0:m.hostingSchemes)==null?void 0:t[T.params.id]}),ve=x(()=>I==null?void 0:I.contractsList),Q=x(()=>p==null?void 0:p.ServicesList),me=x(()=>{var t;return(t=Object.keys(a==null?void 0:a.domainSchemes))==null?void 0:t.map(o=>{var c;return{...a==null?void 0:a.domainSchemes[o],value:(c=a==null?void 0:a.domainSchemes[o])==null?void 0:c.ID}})}),X=x(()=>{let t=0;return C.value.forEach(o=>{t+=o==null?void 0:o.Price}),B(t)||0});_e(()=>{document.addEventListener("keyup",J)}),ye(()=>{document.removeEventListener("keyup",J)});function Y(){var o,c;let t=0;return(c=(o=l.value)==null?void 0:o.discounts)==null||c.Bonuses.map(h=>{t+=+h.Economy}),t}function B(t){return Number(t).toFixed(2)||0}function pe(t){i.value=t}function he(t){l.value=t}async function Se(t){var o;for(const c of C.value){let h="SUCCESS",S=(o=Q.value[c.ServiceID])==null?void 0:o.Code;if(await p.ServiceOrder({Code:S,DependOrderID:t,...c,ContractID:y.value}).then(({result:E,info:r,error:P})=>{if(E==="SUCCESS"){let b={};S==="Default"?b.ServiceOrderID=r==null?void 0:r.ServiceOrderID:b[`${S}OrderID`]=r==null?void 0:r.ServiceOrderID,p.ServiceOrderPay(S,b).then(O=>{let{result:g,error:R}=O;g==="SUCCESS"?d.push("/AdditionalServices}"):g==="BASKET"&&d.push("/Basket")})}else h="ERROR"}),h==="ERROR")break}}function ee(){var t,o,c,h,S,E,r,P,b,O,g;$.value=!0,m.HostingOrder({ContractID:y.value,Domain:`${((t=i.value)==null?void 0:t.line)||""}${(o=i.value)!=null&&o.line&&((c=i.value)!=null&&c.Name)?"."+(((h=i.value)==null?void 0:h.Name)||""):""}`,DomainName:(S=i.value)==null?void 0:S.line,HostingSchemeID:(E=N.value)==null?void 0:E.ID,DomainSchemeID:(r=i.value)!=null&&r.line&&((P=i.value)!=null&&P.ID)?(b=i.value)==null?void 0:b.ID:null,DomainTypeID:(O=i.value)!=null&&O.line?(g=i.value)!=null&&g.Name?"Order":"Nothing":"None"}).then(({result:R,info:D,error:z})=>{var F,U;R==="SUCCESS"?(Se(D==null?void 0:D.OrderID),m.HostingOrderPay({HostingOrderID:D==null?void 0:D.HostingOrderID,DaysPayFromBallance:((F=l.value)==null?void 0:F.daysFromBalance)||0,DaysPay:(U=l.value)==null?void 0:U.actualDays}).then(K=>{let{result:L,error:M}=K;L==="SUCCESS"?d.push("/HostingOrders"):L==="BASKET"&&d.push("/Basket"),$.value=!1})):$.value=!1})}return[u,_]=q(()=>a.fetchDomainSchemes()),await u,_(),[u,_]=q(()=>m.fetchHostingSchemes()),await u,_(),[u,_]=q(()=>I.fetchContracts()),await u,_(),[u,_]=q(()=>p.fetchServices()),await u,_(),(t,o)=>{var E,r,P,b,O,g,R,D,z,F,U,K,L,M,te,se,oe;const c=ke,h=De,S=be;return k(),f(A,null,[v(xe),e("div",Te,[(E=N.value)!=null&&E.ID?(k(),f(A,{key:0},[e("div",Ke,[e("div",qe,[e("div",We,[e("h1",je,n((r=N.value)==null?void 0:r.Name),1),Ge])])]),v(Le,{basic_title:"Хостинг",modelValue:Z.value,"onUpdate:modelValue":o[2]||(o[2]=s=>Z.value=s)},{page:ce(()=>{var s;return[v(Ve,{"params-list":(s=N.value)==null?void 0:s.SchemeParams,label:"Общая информация","main-param":"InternalName"},null,8,["params-list"]),v(Ne,{title:"Договор",modelValue:y.value,"onUpdate:modelValue":o[0]||(o[0]=H=>y.value=H)},null,8,["modelValue"]),v(c,{contractID:y.value,scheme:N.value,serviceID:"10000",isOrderID:!1,onSelect:he},null,8,["contractID","scheme"]),v($e,{"domain-list":me.value,onSelect:pe},null,8,["domain-list"])]}),services:ce(()=>{var s;return[v(He,{modelValue:C.value,"onUpdate:modelValue":o[1]||(o[1]=H=>C.value=H),serviceSearchParams:{ServiceID:"10000",ServersGroupID:(s=N.value)==null?void 0:s.ServersGroupID}},null,8,["modelValue","serviceSearchParams"])]}),_:1},8,["modelValue"]),e("div",ze,[e("div",Me,[W(e("div",Ze,[((O=(b=(P=l.value)==null?void 0:P.discounts)==null?void 0:b.Bonuses)==null?void 0:O.length)>0?(k(),f(A,{key:0},[e("div",Je,[Qe,e("div",Xe,n(B((g=l.value)==null?void 0:g.price))+" ₽",1)]),(k(!0),f(A,null,ne((D=(R=l.value)==null?void 0:R.discounts)==null?void 0:D.Bonuses,s=>(k(),f("div",Ye,[e("div",et,"Скидка "+n(s==null?void 0:s.Discount)+"% на "+n(s==null?void 0:s.Days)+" дней",1),e("div",tt,"-"+n(B(s==null?void 0:s.Economy))+" ₽",1)]))),256))],64)):ge("",!0),e("div",st,[ot,e("div",lt,n(X.value)+" ₽",1)]),(k(!0),f(A,null,ne(C.value,s=>{var H;return k(),f("div",at,[e("div",ct,n((H=Q.value[s==null?void 0:s.ServiceID])==null?void 0:H.Name),1),e("div",nt,n(B(s==null?void 0:s.Price))+" ₽",1)])}),256)),W(e("div",rt,[it,e("div",ut,[e("span",null,[e("s",null,n(B((z=l.value)==null?void 0:z.price))+" ₽",1)]),e("span",_t,"-"+n(B(Y()))+" ₽",1)])],512),[[j,((K=(U=(F=l.value)==null?void 0:F.discounts)==null?void 0:U.Bonuses)==null?void 0:K.length)>0]]),e("div",dt,[e("div",vt,"Итого за "+n((L=l.value)==null?void 0:L.label),1),e("div",mt,n(B(((M=l.value)==null?void 0:M.price)-Y()+ +X.value))+" ₽",1)])],512),[[j,(te=l.value)==null?void 0:te.price]]),v(h,{class:"btn--wide",label:((se=ve.value[y.value])==null?void 0:se.Balance)>((oe=l.value)==null?void 0:oe.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:l.value===null||y.value===null,"is-loading":$.value,onClick:o[3]||(o[3]=s=>ee())},null,8,["label","disabled","is-loading"])])])],64)):(k(),f("div",pt,[e("div",ht,[v(S,{label:"Тариф не найден"})])]))])],64)}}},Kt=re(St,[["__scopeId","data-v-ade0929d"]]);export{Kt as default};

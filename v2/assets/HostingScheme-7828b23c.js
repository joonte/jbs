import{E as pe}from"./EmptyStateBlock-1861cff5.js";import{_ as he}from"./ButtonDefault-eb076df2.js";import{_ as Se}from"./BlockOrderPayBalance-74ae5c26.js";import{_ as ce,a7 as ne,e as re,r as E,h as be,o as k,c as g,a as e,n as se,t as n,b as v,Z as j,ae as G,m as oe,F as x,S as De,g as T,ag as W,w as le,x as ae,k as ke,p as ge,l as fe}from"./index-642cdac5.js";import{u as ye}from"./hosting-73fbf392.js";import{u as Ie}from"./contracts-16ef7000.js";import{u as Ce}from"./domain-a475aef2.js";import{u as Be}from"./services-70bbf297.js";import{_ as Oe}from"./paramsAccordionBlock-7d256e45.js";import{D as we}from"./DomainCheckBlock-a6fc2fa2.js";import{_ as Ee}from"./BlockSelectContract-bb5d421b.js";import{_ as $e}from"./ClausesKeeper-12bc5d50.js";import Ve from"./BlockAdditionalServices-703df388.js";import{B as Ne}from"./BlockBalanceAgreement-ccba2ab2.js";import"./bootstrap-vue-next.es-9ff54498.js";import"./IconHelp-41d48e37.js";import"./globalActions-c9fc22a3.js";import"./BasicInput-e305f811.js";import"./component-faf9e0c1.js";import"./IconArrow-c0196cbe.js";import"./multiselect-bb2ec7ab.js";import"./IconClose-f1b85910.js";import"./ServiceListOrderSelection-9fb63652.js";const Pe={class:"section"},He={class:"container"},xe={class:"section-nav"},Ae={__name:"BlockOrderServiceWrapper",props:{basic_title:{type:String,default:"basic_title"},modelValue:{type:String,default:""}},emits:["update:modelValue"],setup($,{emit:_}){const u=$,m=ne(),y=re(),a=E("");function p(){return m.fullPath.includes("?section=Services")}function L(d=null){d?(a.value="Services",y.replace({query:{section:"Services"}})):(a.value="",y.replace({query:{}})),_("update:modelValue",d||"")}return be(()=>{m.query.section==="Services"&&(a.value="Services")}),(d,l)=>{const I=$e;return k(),g(x,null,[e("div",Pe,[e("div",He,[e("div",xe,[e("div",{class:se(["section-nav__item",[p()?"":"active"]]),onClick:l[0]||(l[0]=V=>L())},n(u.basic_title),3),e("div",{class:se(["section-nav__item",[p()?"active":""]]),onClick:l[1]||(l[1]=V=>L("Services"))},"Услуги",2)])])]),v(I),e("div",null,[j(e("div",null,[oe(d.$slots,"page",{},void 0,!0)],512),[[G,a.value===""]]),j(e("div",null,[oe(d.$slots,"services",{},void 0,!0)],512),[[G,a.value!==""]])])],64)}}},Re=ce(Ae,[["__scopeId","data-v-e8bb070c"]]);const K=$=>(ge("data-v-62073730"),$=$(),fe(),$),Fe={class:"hosting-sheme"},Ue={class:"section"},Te={class:"container"},Le={class:"section-header"},qe={class:"section-title"},We=K(()=>e("div",{class:"section-label"},"Хостинг",-1)),je={class:"section"},Ge={class:"container"},Ke={class:"total-block"},ze={class:"total-block__row divider-bottom"},Me=K(()=>e("div",{class:"total-block__col"},"Цена тарифа",-1)),Ze={class:"total-block__col"},Je={class:"total-block__row no-divider"},Qe={class:"total-block__col"},Xe={class:"total-block__col"},Ye={class:"total-block__row"},et=K(()=>e("div",{class:"total-block__col"},"Дополнительные услуги",-1)),tt={class:"total-block__col"},st={class:"total-block__row no-divider"},ot={class:"total-block__col"},lt={class:"total-block__col"},at={class:"total-block__row total-block__row--big economy-line"},ct=K(()=>e("div",{class:"total-block__col red"},"Выгода",-1)),nt={class:"total-block__col flex-col"},rt={class:"red"},it={class:"total-block__row total-block__row--big"},_t={class:"total-block__col"},ut={class:"total-block__col"},dt={key:1,class:"section"},vt={class:"container"},mt={__name:"HostingScheme",async setup($){let _,u;const m=ye(),y=Ie(),a=Ce(),p=Be();De("emitter");const L=ne(),d=re(),l=E(null),I=E([]),V=E(!1),C=E(null),i=E(null),Z=E(""),N=T(()=>{var o;return(o=m==null?void 0:m.hostingSchemes)==null?void 0:o[L.params.id]}),ie=T(()=>y==null?void 0:y.contractsList),J=T(()=>p==null?void 0:p.ServicesList),_e=T(()=>{var o;return(o=Object.keys(a==null?void 0:a.domainSchemes))==null?void 0:o.map(s=>{var c;return{...a==null?void 0:a.domainSchemes[s],value:(c=a==null?void 0:a.domainSchemes[s])==null?void 0:c.ID}})}),Q=T(()=>{let o=0;return I.value.forEach(s=>{o+=s==null?void 0:s.Price}),B(o)||0});function X(){var s,c;let o=0;return(c=(s=l.value)==null?void 0:s.discounts)==null||c.Bonuses.map(h=>{o+=+h.Economy}),o}function B(o){return Number(o).toFixed(2)||0}function ue(o){i.value=o}function de(o){l.value=o}async function ve(o){var s;for(const c of I.value){console.log(c);let h="SUCCESS",S=(s=J.value[c.ServiceID])==null?void 0:s.Code;if(await p.ServiceOrder({Code:S,DependOrderID:o,...c,ContractID:C.value}).then(({result:O,info:r,error:P})=>{if(O==="SUCCESS"){let b={};S==="Default"?b.ServiceOrderID=r==null?void 0:r.ServiceOrderID:b[`${S}OrderID`]=r==null?void 0:r.ServiceOrderID,p.ServiceOrderPay(S,b).then(w=>{let{result:f,error:A}=w;f==="SUCCESS"?d.push("/AdditionalServices}"):f==="BASKET"&&d.push("/Basket")})}else h="ERROR"}),h==="ERROR")break}}function me(){var o,s,c,h,S,O,r,P,b,w,f;V.value=!0,m.HostingOrder({ContractID:C.value,Domain:`${((o=i.value)==null?void 0:o.line)||""}${(s=i.value)!=null&&s.line&&((c=i.value)!=null&&c.Name)?"."+(((h=i.value)==null?void 0:h.Name)||""):""}`,DomainName:(S=i.value)==null?void 0:S.line,HostingSchemeID:(O=N.value)==null?void 0:O.ID,DomainSchemeID:(r=i.value)!=null&&r.line&&((P=i.value)!=null&&P.ID)?(b=i.value)==null?void 0:b.ID:null,DomainTypeID:(w=i.value)!=null&&w.line?(f=i.value)!=null&&f.Name?"Order":"Nothing":"None"}).then(({result:A,info:D,error:z})=>{var R,F;A==="SUCCESS"?(ve(D==null?void 0:D.OrderID),m.HostingOrderPay({HostingOrderID:D==null?void 0:D.HostingOrderID,DaysPayFromBallance:((R=l.value)==null?void 0:R.daysFromBalance)||0,DaysPay:(F=l.value)==null?void 0:F.actualDays}).then(q=>{let{result:U,error:M}=q;U==="SUCCESS"?d.push("/HostingOrders"):U==="BASKET"&&d.push("/Basket"),V.value=!1})):V.value=!1})}return[_,u]=W(()=>a.fetchDomainSchemes()),await _,u(),[_,u]=W(()=>m.fetchHostingSchemes()),await _,u(),[_,u]=W(()=>y.fetchContracts()),await _,u(),[_,u]=W(()=>p.fetchServices()),await _,u(),(o,s)=>{var O,r,P,b,w,f,A,D,z,R,F,q,U,M,Y,ee,te;const c=Se,h=he,S=pe;return k(),g(x,null,[v(Ne),e("div",Fe,[(O=N.value)!=null&&O.ID?(k(),g(x,{key:0},[e("div",Ue,[e("div",Te,[e("div",Le,[e("h1",qe,n((r=N.value)==null?void 0:r.Name),1),We])])]),v(Re,{basic_title:"Хостинг",modelValue:Z.value,"onUpdate:modelValue":s[2]||(s[2]=t=>Z.value=t)},{page:le(()=>{var t;return[v(Oe,{"params-list":(t=N.value)==null?void 0:t.SchemeParams,label:"Общая информация","main-param":"InternalName"},null,8,["params-list"]),v(Ee,{title:"Договор",modelValue:C.value,"onUpdate:modelValue":s[0]||(s[0]=H=>C.value=H)},null,8,["modelValue"]),v(c,{contractID:C.value,scheme:N.value,isOrderID:!1,onSelect:de},null,8,["contractID","scheme"]),v(we,{"domain-list":_e.value,onSelect:ue},null,8,["domain-list"])]}),services:le(()=>{var t;return[v(Ve,{modelValue:I.value,"onUpdate:modelValue":s[1]||(s[1]=H=>I.value=H),serviceSearchParams:{ServiceID:"10000",ServersGroupID:(t=N.value)==null?void 0:t.ServersGroupID}},null,8,["modelValue","serviceSearchParams"])]}),_:1},8,["modelValue"]),e("div",je,[e("div",Ge,[j(e("div",Ke,[((w=(b=(P=l.value)==null?void 0:P.discounts)==null?void 0:b.Bonuses)==null?void 0:w.length)>0?(k(),g(x,{key:0},[e("div",ze,[Me,e("div",Ze,n(B((f=l.value)==null?void 0:f.price))+" ₽",1)]),(k(!0),g(x,null,ae((D=(A=l.value)==null?void 0:A.discounts)==null?void 0:D.Bonuses,t=>(k(),g("div",Je,[e("div",Qe,"Скидка "+n(t==null?void 0:t.Discount)+"% на "+n(t==null?void 0:t.Days)+" дней",1),e("div",Xe,"-"+n(B(t==null?void 0:t.Economy))+" ₽",1)]))),256))],64)):ke("",!0),e("div",Ye,[et,e("div",tt,n(Q.value)+" ₽",1)]),(k(!0),g(x,null,ae(I.value,t=>{var H;return k(),g("div",st,[e("div",ot,n((H=J.value[t==null?void 0:t.ServiceID])==null?void 0:H.Name),1),e("div",lt,n(B(t==null?void 0:t.Price))+" ₽",1)])}),256)),j(e("div",at,[ct,e("div",nt,[e("span",null,[e("s",null,n(B((z=l.value)==null?void 0:z.price))+" ₽",1)]),e("span",rt,"-"+n(B(X()))+" ₽",1)])],512),[[G,((q=(F=(R=l.value)==null?void 0:R.discounts)==null?void 0:F.Bonuses)==null?void 0:q.length)>0]]),e("div",it,[e("div",_t,"Итого за "+n((U=l.value)==null?void 0:U.label),1),e("div",ut,n(B(((M=l.value)==null?void 0:M.price)-X()+ +Q.value))+" ₽",1)])],512),[[G,(Y=l.value)==null?void 0:Y.price]]),v(h,{class:"btn--wide",label:((ee=ie.value[C.value])==null?void 0:ee.Balance)>((te=l.value)==null?void 0:te.price)?"Оплатить c баланса договора":"Добавить в корзину",disabled:l.value===null||C.value===null,"is-loading":V.value,onClick:s[3]||(s[3]=t=>me())},null,8,["label","disabled","is-loading"])])])],64)):(k(),g("div",dt,[e("div",vt,[v(S,{label:"Тариф не найден"})])]))])],64)}}},Ft=ce(mt,[["__scopeId","data-v-62073730"]]);export{Ft as default};
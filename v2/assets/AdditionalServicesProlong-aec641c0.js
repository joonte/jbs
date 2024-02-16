import{o as A,c as F,b as C,a as t,t as I,F as B,k as w,p as x,l as V,_ as N,a7 as E,e as L,f as q,r as D,g as u,i as M,a2 as R}from"./index-c7c24b76.js";import{u as U}from"./postings-8eba728f.js";import{u as J}from"./services-78d5ede3.js";import{_ as K}from"./ButtonDefault-626291b9.js";import{_ as T}from"./ClausesKeeper-e7acf2fb.js";import{B as z}from"./BlockBalanceAgreement-f11c65ba.js";import"./IconClose-447d7f82.js";import"./contracts-397b53fe.js";import"./bootstrap-vue-next.es-3f66213d.js";import"./IconArrow-dfacb3d4.js";const g=d=>(x("data-v-6db66b4b"),d=d(),V(),d),G={class:"additional-service-prolong"},H={class:"section"},Q={class:"container"},W={class:"section-header"},X=g(()=>t("h1",{class:"section-title"},"Оплата заказа дополнительной услуги",-1)),Y={class:"section-label"},Z={class:"section"},$={class:"container"},j={class:"service-prolong__data"},ee={class:"form-field"},se=g(()=>t("div",{class:"form-field__label"},"Стоимость заказа",-1)),te=["value"],oe={class:"form-field"},re={class:"form-field__label"},ae=g(()=>t("input",{type:"text",placeholder:"0 руб",value:"1",disabled:""},null,-1)),ie={class:"form-field"},ne=g(()=>t("div",{class:"form-field__label"},"Всего к оплате",-1)),ce=["value"],le={class:"section"},de={class:"container"},_e={class:"service-prolong__data"},ue={class:"total-block"},ve={class:"total-block__row total-block__row--big"},pe=g(()=>t("div",{class:"total-block__col"},"Итого",-1)),me={class:"total-block__col"};function fe(d,v,p,r,k,y){var S,f,h,b,O,_;const c=z,m=T,P=K;return A(),F(B,null,[C(c),t("div",G,[t("div",H,[t("div",Q,[t("div",W,[X,t("div",Y,I((S=r.getService)==null?void 0:S.Name)+" # "+I((f=r.getOrder)==null?void 0:f.ID),1)]),C(m)])]),(h=r.getOrder)!=null&&h.IsPayed&&((b=r.getOrder)!=null&&b.IsProlong)||!((O=r.getOrder)!=null&&O.IsPayed)?(A(),F(B,{key:0},[t("div",Z,[t("div",$,[t("div",j,[t("label",ee,[se,t("input",{type:"text",placeholder:"0 руб",value:r.orderCost+" руб",disabled:""},null,8,te)]),t("label",oe,[t("div",re,"Количество ("+I((_=r.getService)==null?void 0:_.Measure)+")",1),ae]),t("label",ie,[ne,t("input",{type:"text",placeholder:"0 руб",value:r.getServicePrice()+" руб",disabled:""},null,8,ce)])])])]),t("div",le,[t("div",de,[t("div",_e,[t("div",ue,[t("div",ve,[pe,t("div",me,I(r.getServicePrice()||"0")+" ₽",1)])]),C(P,{class:"btn--wide",label:"Оплатить",onClick:v[0]||(v[0]=e=>r.orderPay()),"is-loading":r.isLoading},null,8,["is-loading"])])])])],64)):w("",!0)])],64)}const he={async setup(){const d=E(),v=L(),p=U(),r=q(),k=J(),y=D(!1),c=u(()=>Object.keys(r==null?void 0:r.userOrders).map(e=>r==null?void 0:r.userOrders[e]).find(e=>e.ID===d.params.id));u(()=>{var e,s;return(s=(e=P.value)==null?void 0:e.find(o=>{var a,i,n;return o.value===((n=(i=(a=c.value)==null?void 0:a.OrdersFields)==null?void 0:i[0])==null?void 0:n.Value)}))==null?void 0:s.price}),u(()=>{var e,s;return(s=(e=S.value)==null?void 0:e.find(o=>{var a,i,n;return o.value===((n=(i=(a=c.value)==null?void 0:a.OrdersFields)==null?void 0:i[1])==null?void 0:n.Value)}))==null?void 0:s.price});const m=D(0),P=u(()=>{var e,s,o,a,i;return(i=(a=(o=(s=(e=_.value)==null?void 0:e.ServicesFields)==null?void 0:s[0])==null?void 0:o.Options)==null?void 0:a.split(`
`))==null?void 0:i.map(n=>{let l=n.split("=");return{value:l[0],name:l[1],price:l[2]}})}),S=u(()=>{var e,s,o,a,i;return(i=(a=(o=(s=(e=_.value)==null?void 0:e.ServicesFields)==null?void 0:s[1])==null?void 0:o.Options)==null?void 0:a.split(`
`))==null?void 0:i.map(n=>{let l=n.split("=");return{value:l[0],name:l[1],price:l[2]}})});async function f(e){try{const o=(await R.get(`/ServiceOrderPay?OrderID=${e}&JSON=1`)).data,a=h(o);let i=1;return o.AmountPay&&(i=parseInt(o.AmountPay)||1),a}catch(s){return console.error("Ошибка при получении данных о стоимости заказа",s),null}}function h(e){let s=0;for(const o in e)e.hasOwnProperty(o)&&(typeof e[o]=="object"?s+=h(e[o]):o==="Cost"&&!isNaN(e[o])&&(s+=parseFloat(e[o])));return s}function b(){return((parseFloat(m.value)||0)*1).toFixed(2)}function O(){var e,s;y.value=!0,k.ServiceOrderPay((e=c.value)==null?void 0:e.Code,{AmountPay:"1",ServiceOrderID:(s=c.value)==null?void 0:s.ID,IsChange:!0}).then(o=>{var n;let{result:a,error:i}=o;a==="SUCCESS"?v.push(`/AdditionalServices/${(n=c.value)==null?void 0:n.ID}`):a==="BASKET"&&v.push("/Basket"),y.value=!1})}M(async()=>{if(c.value){const e=c.value.ID,s=await f(e);s!==null&&(m.value=s)}});const _=u(()=>{var e;return p==null?void 0:p.servicesList[(e=c.value)==null?void 0:e.ServiceID]});return await r.fetchUserOrders(),await p.fetchServices(),{getOrder:c,getService:_,getServicePrice:b,isLoading:y,orderPay:O,fetchOrderCost:f,orderCost:m}}},Fe=N(he,[["render",fe],["__scopeId","data-v-6db66b4b"]]);export{Fe as default};

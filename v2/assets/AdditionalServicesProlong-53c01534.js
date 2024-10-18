import{o as A,c as F,b as k,a as o,t as O,F as B,k as w,p as x,l as V,_ as N,a7 as E,e as L,f as q,r as D,g as _,i as M,a2 as R}from"./index-59dbe3d3.js";import{u as U}from"./postings-ab558ece.js";import{u as J}from"./services-05e05079.js";import{_ as K}from"./ButtonDefault-a62141a4.js";import{_ as T}from"./ClausesKeeper-b1ff97f6.js";import{B as z}from"./BlockBalanceAgreement-f093da58.js";import"./IconClose-0bef5a37.js";import"./contracts-d90f6cbb.js";import"./bootstrap-vue-next.es-faf1dad1.js";import"./IconArrow-ad4e318c.js";const f=d=>(x("data-v-27d70981"),d=d(),V(),d),G={class:"additional-service-prolong"},H={class:"section"},Q={class:"container"},W={class:"section-header"},X=f(()=>o("h1",{class:"section-title"},"Оплата заказа дополнительной услуги",-1)),Y={class:"section-label"},Z={class:"section"},$={class:"container"},j={class:"service-prolong__data"},ee={class:"form-field"},te=f(()=>o("div",{class:"form-field__label"},"Стоимость заказа",-1)),se=["value"],oe={class:"form-field"},re={class:"form-field__label"},ae=f(()=>o("input",{type:"text",placeholder:"0 руб",value:"1",disabled:""},null,-1)),ie={class:"form-field"},ce=f(()=>o("div",{class:"form-field__label"},"Всего к оплате",-1)),ne=["value"],le={class:"section"},de={class:"container"},_e={class:"service-prolong__data"},ue={class:"total-block"},ve={class:"total-block__row total-block__row--big"},pe=f(()=>o("div",{class:"total-block__col"},"Итого",-1)),me={class:"total-block__col"};function fe(d,u,v,r,I,h){var S,p,m,y;const n=z,g=T,b=K;return A(),F(B,null,[k(n),o("div",G,[o("div",H,[o("div",Q,[o("div",W,[X,o("div",Y,O((S=r.getService)==null?void 0:S.Name)+" # "+O((p=r.getOrder)==null?void 0:p.ID),1)]),k(g)])]),r.getOrder?(A(),F(B,{key:0},[o("div",Z,[o("div",$,[o("div",j,[o("label",ee,[te,o("input",{type:"text",placeholder:"0 руб",value:((m=r.getService)==null?void 0:m.Cost)+" руб",disabled:""},null,8,se)]),o("label",oe,[o("div",re,"Количество ("+O((y=r.getService)==null?void 0:y.Measure)+")",1),ae]),o("label",ie,[ce,o("input",{type:"text",placeholder:"0 руб",value:r.getServicePrice()+" руб",disabled:""},null,8,ne)])])])]),o("div",le,[o("div",de,[o("div",_e,[o("div",ue,[o("div",ve,[pe,o("div",me,O(r.getServicePrice()||"0")+" ₽",1)])]),k(b,{class:"btn--wide",label:"Оплатить",onClick:u[0]||(u[0]=P=>r.orderPay()),"is-loading":r.isLoading},null,8,["is-loading"])])])])],64)):w("",!0)])],64)}const he={async setup(){const d=E(),u=L(),v=U(),r=q(),I=J(),h=D(!1),n=_(()=>Object.keys(r==null?void 0:r.userOrders).map(e=>r==null?void 0:r.userOrders[e]).find(e=>e.ID===d.params.id));_(()=>{var e,t;return(t=(e=b.value)==null?void 0:e.find(s=>{var a,i,c;return s.value===((c=(i=(a=n.value)==null?void 0:a.OrdersFields)==null?void 0:i[0])==null?void 0:c.Value)}))==null?void 0:t.price}),_(()=>{var e,t;return(t=(e=S.value)==null?void 0:e.find(s=>{var a,i,c;return s.value===((c=(i=(a=n.value)==null?void 0:a.OrdersFields)==null?void 0:i[1])==null?void 0:c.Value)}))==null?void 0:t.price});const g=D(0),b=_(()=>{var e,t,s,a,i;return(i=(a=(s=(t=(e=C.value)==null?void 0:e.ServicesFields)==null?void 0:t[0])==null?void 0:s.Options)==null?void 0:a.split(`
`))==null?void 0:i.map(c=>{let l=c.split("=");return{value:l[0],name:l[1],price:l[2]}})}),S=_(()=>{var e,t,s,a,i;return(i=(a=(s=(t=(e=C.value)==null?void 0:e.ServicesFields)==null?void 0:t[1])==null?void 0:s.Options)==null?void 0:a.split(`
`))==null?void 0:i.map(c=>{let l=c.split("=");return{value:l[0],name:l[1],price:l[2]}})});async function p(e){try{const s=(await R.get(`/ServiceOrderPay?OrderID=${e}&JSON=1`)).data,a=m(s);let i=1;return s.AmountPay&&(i=parseInt(s.AmountPay)||1),a}catch(t){return console.error("Ошибка при получении данных о стоимости заказа",t),null}}function m(e){let t=0;for(const s in e)e.hasOwnProperty(s)&&(typeof e[s]=="object"?t+=m(e[s]):s==="Cost"&&!isNaN(e[s])&&(t+=parseFloat(e[s])));return t}function y(){var s;return((parseFloat((s=C.value)==null?void 0:s.Cost)||0)*1).toFixed(2)}function P(){var e,t;h.value=!0,I.ServiceOrderPay((e=n.value)==null?void 0:e.Code,{AmountPay:"1",ServiceOrderID:(t=n.value)==null?void 0:t.ID,IsChange:!0}).then(s=>{var c;let{result:a,error:i}=s;a==="SUCCESS"?u.push(`/AdditionalServices/${(c=n.value)==null?void 0:c.ID}`):a==="BASKET"&&u.push("/Basket"),h.value=!1})}M(async()=>{if(n.value){const e=n.value.ID,t=await p(e);t!==null&&(g.value=t)}});const C=_(()=>{var e;return v==null?void 0:v.servicesList[(e=n.value)==null?void 0:e.ServiceID]});return await r.fetchUserOrders(),await v.fetchServices(),{getOrder:n,getService:C,getServicePrice:y,isLoading:h,orderPay:P,fetchOrderCost:p,orderCost:g}}},Fe=N(he,[["render",fe],["__scopeId","data-v-27d70981"]]);export{Fe as default};

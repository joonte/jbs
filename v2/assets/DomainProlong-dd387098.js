import{o as a,c as n,b as _,a as e,t as p,F as L,k as A,p as K,l as M,_ as R,a7 as V,e as j,S as U,r as W,h as N,O as q,g as y}from"./index-5e313302.js";import{u as z}from"./domain-ae28df08.js";import{u as G}from"./contracts-3351d68f.js";import{s as H}from"./multiselect-bfc29db8.js";import{B as T}from"./BlockBalanceAgreement-bb87adc1.js";import{E as J}from"./EmptyStateBlock-9f752916.js";import{_ as Q}from"./ButtonDefault-7c216fc6.js";import{_ as X}from"./ClausesKeeper-eb954229.js";import"./bootstrap-vue-next.es-a19a8b41.js";import"./IconArrow-555f9d45.js";import"./IconClose-1f889a2e.js";const m=s=>(K("data-v-dc5580bd"),s=s(),M(),s),Y={key:0,class:"domain-scheme"},Z={class:"section"},$={class:"container"},oo={class:"section-header"},eo={key:0,class:"section-title"},to={key:1,class:"section-title"},so={key:2,class:"section-title"},ao={class:"section-label"},no={class:"section"},io={class:"container"},co={class:"domain-prolong__data"},lo={key:0,class:"form-field"},ro=m(()=>e("div",{class:"form-field__label"},"Стоимость заказа (в год)",-1)),_o=["value"],mo={class:"form-field"},ho=m(()=>e("div",{class:"form-field__label"},"Стоимость продления (в год)",-1)),go=["value"],Do=m(()=>e("label",{class:"form-field"},[e("div",{class:"form-field__label"},"Количество лет"),e("input",{type:"text",placeholder:"1",value:"1",disabled:""})],-1)),fo={key:1,class:"form-field__msg"},uo={class:"form-field"},vo=m(()=>e("div",{class:"form-field__label"},"Всего к оплате",-1)),po=["value"],yo={class:"section"},bo={class:"container"},So={class:"domain-prolong__data"},ko={class:"total-block"},Co={class:"total-block__row total-block__row--big"},Io=m(()=>e("div",{class:"total-block__col"},"Итого за 1 год",-1)),Po={class:"total-block__col"},Bo={key:1,class:"section"},Fo={class:"container"},xo={key:1,class:"section"},Oo={class:"container"};function Eo(s,c,b,o,h,u){var t,d,f,r,v,S,k,C,I,P,B,F,x,O,E,w;const g=T,i=X,l=Q,D=J;return a(),n(L,null,[_(g),o.getDomain?(a(),n("div",Y,[e("div",Z,[e("div",$,[e("div",oo,[o.getDomain.StatusID==="Waiting"?(a(),n("h1",eo,"Оплата заказа доменного имени")):o.getDomain.StatusID==="ForTransfer"?(a(),n("h1",to,"Оплата переноса домена")):(a(),n("h1",so,"Оплата продления доменного имени")),e("div",ao,p((t=o.getDomain)!=null&&t.DomainName?`#${(d=o.getDomain)==null?void 0:d.DomainName}`:""),1)]),_(i)])]),(f=o.getDomainSchemes)!=null&&f.IsPayed&&((r=o.getDomainSchemes)!=null&&r.IsProlong)||!((v=o.getDomainSchemes)!=null&&v.IsPayed)?(a(),n(L,{key:0},[e("div",no,[e("div",io,[e("div",co,[o.getDomain.StatusID==="Waiting"||o.getDomain.StatusID==="ForTransfer"?(a(),n("label",lo,[ro,e("input",{type:"text",placeholder:"0 руб",value:((S=o.getDomainSchemes)==null?void 0:S.CostOrder)+" руб",disabled:""},null,8,_o)])):A("",!0),e("label",mo,[ho,e("input",{type:"text",placeholder:"0 руб",value:((k=o.getDomainSchemes)==null?void 0:k.CostProlong)+" руб",disabled:""},null,8,go)]),Do,(C=o.getDomain)!=null&&C.AdditionalCost?(a(),n("div",fo,[e("p",null,p((I=o.getDomain)==null?void 0:I.AdditionalCost.Message),1)])):A("",!0),e("label",uo,[vo,e("input",{type:"text",placeholder:"0 руб",value:o.getDomain.IsPayed?parseFloat((B=o.getDomainSchemes)==null?void 0:B.CostProlong).toFixed(2)+" руб":parseFloat((P=o.getDomainSchemes)==null?void 0:P.CostOrder).toFixed(2)+" руб",disabled:""},null,8,po)])])])]),e("div",yo,[e("div",bo,[e("div",So,[e("div",ko,[e("div",Co,[Io,e("div",Po,p(o.getDomain.IsPayed?parseFloat((x=o.getDomainSchemes)==null?void 0:x.CostProlong).toFixed(2)||"0.00":parseFloat((F=o.getDomainSchemes)==null?void 0:F.CostOrder).toFixed(2))+" ₽",1)])]),_(l,{class:"btn--wide",label:+((O=o.getContract)==null?void 0:O.Balance)>(o.getDomain.StatusID==="ClaimForRegister"?+((E=o.getDomainSchemes)==null?void 0:E.CostOrder):+((w=o.getDomainSchemes)==null?void 0:w.CostProlong))?"Оплатить c баланса договора":"Добавить в корзину",onClick:c[0]||(c[0]=Lo=>o.orderPay()),"is-loading":o.isLoading},null,8,["label","is-loading"])])])])],64)):(a(),n("div",Bo,[e("div",Fo,[_(D,{class:"no-margin",label:"Заказ нельзя продлить"})])]))])):(a(),n("div",xo,[e("div",Oo,[_(D,{label:"Заказ домена не найден"})])]))],64)}const wo={components:{Multiselect:H,BlockBalanceAgreement:T},async setup(){const s=z(),c=G(),b=V(),o=j();U("emitter");const h=W(!1);function u(){var t,d;h.value=!0,s.DomainOrderPay({DomainOrderID:(t=i.value)==null?void 0:t.ID,DaysToProlong:(d=l.value)==null?void 0:d.DaysToProlong}).then(f=>{let{result:r,error:v}=f;r==="SUCCESS"?o.push("/DomainOrders"):r==="BASKET"&&o.push("/Basket"),h.value=!1})}const g=t=>{t.key==="Enter"&&t.ctrlKey&&u()};N(()=>{document.addEventListener("keyup",g)}),q(()=>{document.removeEventListener("keyup",g)});const i=y(()=>Object.keys(s==null?void 0:s.domainsList).map(t=>s==null?void 0:s.domainsList[t]).find(t=>t.OrderID===b.params.id)),l=y(()=>{var t;return s==null?void 0:s.domainSchemes[(t=i.value)==null?void 0:t.SchemeID]}),D=y(()=>{var t;return c.contractsList[(t=i.value)==null?void 0:t.ContractID]});return N(()=>{console.log(i,"Domain"),console.log(l,"Scheme")}),await s.fetchDomains(),await s.fetchDomainSchemes(),await c.fetchContracts(),{getDomain:i,getDomainSchemes:l,getContract:D,isLoading:h,orderPay:u}}},zo=R(wo,[["render",Eo],["__scopeId","data-v-dc5580bd"]]);export{zo as default};

import{j as te,o as ae,c as le,a as o,n as S,Z as F,an as T,b as M,t as H,p as oe,l as ne,_ as se,e as re,r as b,g as D,i as J,h as ie}from"./index-642cdac5.js";import{u as de}from"./services-70bbf297.js";import{u as ce}from"./contracts-16ef7000.js";import{s as ue}from"./multiselect-bb2ec7ab.js";import{_ as pe}from"./ButtonDefault-eb076df2.js";const w=c=>(oe("data-v-2bcc8212"),c=c(),ne(),c),ve={class:"modal__body"},be=w(()=>o("div",{class:"list-item__title"},"Оплата заказа вторичного DNS",-1)),me={class:"form"},_e=w(()=>o("span",{class:"form-input_border-content"},[o("span",{class:"form-input_border-mark"}),o("span",{class:"form-input_border-info"},[o("span",{class:"form-input_border-address"},"Выбор периода оплаты")])],-1)),fe=w(()=>o("span",{class:"form-input_border-content"},[o("span",{class:"form-input_border-mark"}),o("span",{class:"form-input_border-info"},[o("span",{class:"form-input_border-address"},"Выбор даты окончания")])],-1)),Se={class:"modal__input-row select-date"},De=["disabled"],he={class:"form-input_border-content"},ye=w(()=>o("span",{class:"form-input_border-mark"},null,-1)),Oe={class:"form-input_border-info"},ge=w(()=>o("span",{class:"form-input_border-address"},"Выбор даты окончания",-1)),Me={class:"modal__input-title"},we={class:"modal__buttons-wrap"};function Ie(c,s,E,t,I,N){const _=te("Multiselect"),y=pe;return ae(),le("div",ve,[be,o("div",me,[o("label",{class:S(["form-input_border",{"disabled-label":t.selectedOption!=="month"}])},[F(o("input",{type:"radio",name:"Command",value:"month","onUpdate:modelValue":s[0]||(s[0]=n=>t.selectedOption=n)},null,512),[[T,t.selectedOption]]),_e],2),M(_,{class:S(["multiselect--white",{"multiselect--disabled":t.selectedOption!=="month"}]),type:"text",modelValue:t.selectedMonth,"onUpdate:modelValue":s[1]||(s[1]=n=>t.selectedMonth=n),options:t.monthsOptions,placeholder:"Период оплаты",label:"label","track-by":"value",disabled:t.selectedOption!=="month"},null,8,["class","modelValue","options","disabled"]),o("label",{class:S(["form-input_border",{"disabled-label":t.selectedOption!=="period"}])},[F(o("input",{type:"radio",name:"Command",value:"period","onUpdate:modelValue":s[2]||(s[2]=n=>t.selectedOption=n)},null,512),[[T,t.selectedOption]]),fe],2),o("div",Se,[M(_,{class:S(["multiselect--white",{"multiselect--disabled":t.selectedOption!=="period"}]),options:t.yearsOptions,type:"text",modelValue:t.selectedYear,"onUpdate:modelValue":s[3]||(s[3]=n=>t.selectedYear=n),placeholder:"Год",disabled:t.selectedOption!=="period"},null,8,["options","class","modelValue","disabled"]),M(_,{class:S(["multiselect--white",{"multiselect--disabled":t.selectedOption!=="period"}]),options:t.monthOptions,type:"text",modelValue:t.selectedMonthS,"onUpdate:modelValue":s[4]||(s[4]=n=>t.selectedMonthS=n),placeholder:"Месяц",disabled:t.selectedOption!=="period"},null,8,["options","class","modelValue","disabled"]),M(_,{class:S(["multiselect--white",{"multiselect--disabled":t.selectedOption!=="period"}]),options:t.daysOptions,type:"text",modelValue:t.selectedDay,"onUpdate:modelValue":s[5]||(s[5]=n=>t.selectedDay=n),placeholder:"День",disabled:t.selectedOption!=="period"},null,8,["options","class","modelValue","disabled"])]),o("label",{class:S(["form-input_border",{"disabled-label":t.selectedOption!=="balance"}])},[F(o("input",{type:"radio",name:"Command",value:"balance",disabled:t.daysBalanceLasts===0,"onUpdate:modelValue":s[6]||(s[6]=n=>t.selectedOption=n)},null,8,De),[[T,t.selectedOption]]),o("span",he,[ye,o("span",Oe,[ge,o("div",Me,"Остаток денег на балансе ("+H(t.getContract.balance)+"), хватит на "+H(t.daysBalanceLasts)+" дней",1)])])],2)]),o("div",we,[M(y,{class:"select-contract__button modal__button",label:t.buttonText,onClick:s[7]||(s[7]=n=>t.payService())},null,8,["label"])])])}const Ne={components:{Multiselect:ue},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(c,{emit:s}){var Z;const E=re(),t=(Z=c.data)==null?void 0:Z.ExpirationDate.split("."),I=parseInt(t[2],10),N=parseInt(t[1],10)-1,_=parseInt(t[0],10),y=new Date,n=y.getFullYear(),R=y.getMonth();y.getDate();const i=de(),B=ce(),O=b("month"),u=b(null),C=b(null),p=b(null),h=b(null);let V=I,k=N+1,q=_;V<n&&(V=n),I===n&&N<=R&&(k=R+2,q=1),k>12&&(k=1,V+=1),h.value=q.toString().padStart(2,"0");const A=b([]);for(let e=n;e<=n+3;e++)A.value.push({label:e.toString(),value:e});const U=b([{label:"Январь",value:"01"},{label:"Февраль",value:"02"},{label:"Март",value:"03"},{label:"Апрель",value:"04"},{label:"Май",value:"05"},{label:"Июнь",value:"06"},{label:"Июль",value:"07"},{label:"Август",value:"08"},{label:"Сентябрь",value:"09"},{label:"Октябрь",value:"10"},{label:"Ноябрь",value:"11"},{label:"Декабрь",value:"12"}]),W=e=>{const l=new Date().getFullYear(),a=new Date().getMonth()+1;U.value=[];for(let r=1;r<=12;r++)(e>l||r>=a)&&U.value.push({label:r.toString().padStart(2,"0"),value:r.toString().padStart(2,"0")});e==l?u.value=(a%12+1).toString().padStart(2,"0"):u.value="01"},P=b([]),Y=(e,l)=>{const a=new Date(l,e,0).getDate();P.value=[];for(let v=1;v<=a;v++)P.value.push({label:v.toString().padStart(2,"0"),value:v.toString().padStart(2,"0")});const r=new Date,d=r.getFullYear(),f=r.getMonth()+1,x=r.getDate();l==d&&e==f?h.value=x.toString().padStart(2,"0"):h.value="01"},K=D(()=>i==null?void 0:i.DNSmanagerOrdersList),j=D(()=>{const e=K.value;if(!(!e||typeof e!="object"))return Object.keys(e).map(l=>e[l]).find(l=>{var a;return l.ID===((a=c.data)==null?void 0:a.ServiceOrderID)})}),m=D(()=>{var e;return(e=i==null?void 0:i.additionalServicesScheme)!=null&&e.DNSmanager?Object.keys(i.additionalServicesScheme.DNSmanager).map(l=>{const a=i.additionalServicesScheme.DNSmanager[l];return{...a,value:a==null?void 0:a.ID,name:a==null?void 0:a.Name,cost:a==null?void 0:a.CostDay,month:a==null?void 0:a.CostMonth}}).filter(l=>{var a;return(l==null?void 0:l.ID)==((a=j.value)==null?void 0:a.SchemeID)}):[]}),Q=D(()=>B==null?void 0:B.contractsList),L=D(()=>{const e=Object.values(Q.value).find(l=>{var a;return l&&l.ID===((a=c.data)==null?void 0:a.contractID)});return e?{value:e.ID,name:e.Customer,balance:e.Balance}:null}),g=D(()=>{var e;if(L.value&&m.value.length>0){const l=L.value.balance,a=(e=m.value[0])==null?void 0:e.cost,r=parseFloat(l),d=parseFloat(a);if(!isNaN(r)&&!isNaN(d)&&d>0)return Math.floor(r/d)}return 0}),z=b([{label:"1 мес.",value:1},{label:"2 мес.",value:2},{label:"3 мес.",value:3},{label:"6 мес.",value:6},{label:"9 мес.",value:9},{label:"12 мес.",value:12},{label:"24 мес.",value:24},{label:"36 мес.",value:36}]);function X(){var d,f,x;let e;if(O.value==="balance")e=g.value;else if(O.value==="period"){const v=new Date(p.value,parseInt(u.value)-1,h.value),ee=new Date(I,N,_),G=v-ee;e=G>0?Math.ceil(G/(1e3*60*60*24)):0}else e=C.value*30;const l=e<g.value||((d=m.value[0])==null?void 0:d.cost)==="0.00"&&((f=m.value[0])==null?void 0:f.month)==="0.00",a=!l,r={DNSmanagerOrderID:(x=c.data)==null?void 0:x.ServiceOrderID,DaysPay:e,IsNoBasket:l,IsUseBasket:a,PayMessage:""};console.log(`requestData - ${r}`),i.DNSOrderPay(r).then(v=>{v==="UseBasket"?(s("modalClose"),E.push("/Basket")):v==="NoBasket"?s("modalClose"):console.error("Ошибка оплаты заказа")}),console.log(`Количество дней: ${e}`)}const $=D(()=>{var l,a;let e;if(O.value==="period"){const r=new Date(p.value,parseInt(u.value)-1,h.value),d=new Date,f=r-d;e=f>0?Math.ceil(f/(1e3*60*60*24)):0}else O.value==="balance"?e=g.value:e=C.value*30;return e===0?"Оплатить с баланса договора":e>g.value&&((l=m.value[0])==null?void 0:l.cost)!=="0.00"&&((a=m.value[0])==null?void 0:a.month)!=="0.00"?"Добавить в корзину":"Оплатить с баланса договора"});return J(p,e=>{W(e),Y(u.value,e)}),J(u,e=>{e&&p.value&&Y(e,p.value)}),ie(()=>{i.fetchAdditionalServiceScheme("DNSmanager"),p.value=V.toString(),u.value=k.toString().padStart(2,"0"),C.value=z.value[0].value,W(p.value),Y(u.value,p.value),console.log(m),console.log(j)}),{selectedMonth:C,selectedYear:p,selectedDay:h,monthsOptions:z,payService:X,getContract:L,getISPswOrder:j,getISPswScheme:m,daysBalanceLasts:g,selectedOption:O,selectedMonthS:u,yearsOptions:A,monthOptions:U,daysOptions:P,buttonText:$}}},Ue=se(Ne,[["render",Ie],["__scopeId","data-v-2bcc8212"]]);export{Ue as default};

import{_ as $,r as C,i as w,h as ee,o as u,c as D,a as s,t as k,m as oe,k as O,p as ae,l as ne,j as le,b as q,y as Z,w as de,F as P,x as W,d as F,Z as re,af as ie,f as se,e as ce,a7 as ue,S as _e,g as V}from"./index-145d9cde.js";import{u as fe}from"./contracts-009c925b.js";import{u as De}from"./services-42f7a1d0.js";import{s as ve}from"./multiselect-a2d623ca.js";import{_ as Se}from"./ButtonDefault-6a3a5e1e.js";import{_ as he}from"./BasicInput-9e44931e.js";import{_ as Ie}from"./BlockSelectContract-dee687ab.js";import{_ as Oe}from"./ClausesKeeper-d3875457.js";const pe={props:{modelValue:{type:Object,default:()=>{}},data:{type:Object,default:()=>{}},name:{type:String,default:""},checkbox:{type:Boolean,default:!1}},emits:["update:modelValue"],setup(l,{emit:f}){const c=C(null);function a(){f("update:modelValue",l.data)}return w(l,()=>{c.value=l.modelValue}),ee(()=>{c.value=l.modelValue}),{updateValue:a,inputValue:c}}},ye=l=>(ae("data-v-414dbe12"),l=l(),ne(),l),ke={class:"form-input_border"},be=["checked","name","type"],Ce={class:"form-input_border-content"},ge=ye(()=>s("span",{class:"form-input_border-mark"},null,-1)),Ve={class:"form-input_border-item form-input_border-item--left"},me={class:"form-input_border-item form-input_border-item--right"};function Ae(l,f,c,a,S,H){var i,m,A;return u(),D("label",ke,[s("input",{checked:((i=a.inputValue)==null?void 0:i.value)===((m=c.data)==null?void 0:m.value),name:c.name,type:c.checkbox?"checkbox":"radio",onInput:f[0]||(f[0]=(...p)=>a.updateValue&&a.updateValue(...p))},null,40,be),s("span",Ce,[ge,s("span",Ve,k((A=c.data)==null?void 0:A.name),1),c.data.price?oe(l.$slots,"default",{key:0},()=>{var p;return[s("span",me,k((p=c.data)==null?void 0:p.price),1)]},!0):O("",!0)])])}const te=$(pe,[["render",Ae],["__scopeId","data-v-414dbe12"]]),z=l=>(ae("data-v-55526140"),l=l(),ne(),l),Ne={key:0,class:"section"},Be={class:"section-header"},je={class:"section-title"},xe={key:2,class:"form-field form-field--basic"},Ee=z(()=>s("div",{class:"form-field__label"},[F("IP адрес лицензии"),s("span",null,"*")],-1)),Pe={class:"form-field-input__wrapper"},we={key:3,class:"form-field form-field--basic"},Fe=z(()=>s("div",{class:"form-field__label"},[F("Выбрать заказ"),s("span",null,"*")],-1)),Ue={class:"form-field-input__wrapper"},Me={class:"multiselect-option"},Te={class:"form-field form-field--basic"},Le={class:"form-field__label"},Re={key:0},Ke={class:"form-field-input__wrapper"},qe=["name","onUpdate:modelValue"],We={key:5,class:"section"},He={class:"list"},Ze={class:"list-col list-col--md"},ze=z(()=>s("div",{class:"list-item__title"},[F("Тариф"),s("span",null,"*")],-1)),Ge={key:6,class:"section"},Je={class:"list"},Qe={class:"list-col list-col--md"},Xe={class:"list-item__title"},Ye={key:0},$e={key:7,class:"section"},ea={class:"total-block block--divider"},aa={class:"total-block__row total-block__row--big"},na={class:"total-block__col"},ta={class:"total-block__col"},oa={key:8,class:"additional-service__inline-price"};function la(l,f,c,a,S,H){var h,M,T,N,j,L,R,K,x;const i=Oe,m=Ie,A=he,p=le("Multiselect"),y=te,U=Se;return u(),D(P,null,[c.isInline?O("",!0):(u(),D("div",Ne,[s("div",Be,[s("h1",je,k((h=c.sectionData)==null?void 0:h.Item),1),q(i,{partition:`Header:${((M=c.sectionData)==null?void 0:M.Code)==="Default"?c.sectionID:`/${(T=c.sectionData)==null?void 0:T.Code}Orders`}`},null,8,["partition"])])])),c.isInline?O("",!0):(u(),Z(m,{key:1,class:"additional-service__contract-select",title:"Договор",modelValue:a.contractSelect,"onUpdate:modelValue":f[0]||(f[0]=d=>a.contractSelect=d)},null,8,["modelValue"])),a.DependOrderID==="0"&&c.sectionID==="51000"?(u(),D("div",xe,[Ee,s("div",Pe,[q(A,{name:"ServiceIP",placeholder:"0.0.0.0",modelValue:a.serviceIP,"onUpdate:modelValue":f[1]||(f[1]=d=>a.serviceIP=d)},null,8,["modelValue"])])])):O("",!0),((N=a.getDependOrders)==null?void 0:N.length)>0?(u(),D("div",we,[Fe,s("div",Ue,[q(p,{class:"multiselect--white",placeholder:"Все заказы",label:"name","track-by":"value",options:a.getDependOrders,modelValue:a.DependOrderID,"onUpdate:modelValue":f[2]||(f[2]=d=>a.DependOrderID=d)},{option:de(({option:d})=>[s("div",Me,k(d.name),1)]),_:1},8,["options","modelValue"])])])):O("",!0),((j=a.getInputFields)==null?void 0:j.length)>0?(u(!0),D(P,{key:4},W(a.getInputFields,d=>(u(),D("div",Te,[s("div",Le,[F(k(d==null?void 0:d.Name),1),d!=null&&d.IsDuty?(u(),D("span",Re,"*")):O("",!0)]),s("div",Ke,[re(s("input",{name:`ID${d==null?void 0:d.ID}`,"onUpdate:modelValue":b=>a.formData[`ID${d==null?void 0:d.ID}`]=b},null,8,qe),[[ie,a.formData[`ID${d==null?void 0:d.ID}`]]])])]))),256)):O("",!0),((L=c.sectionData)==null?void 0:L.Code)!=="Default"&&((R=a.getAdditionalServicesSchemes)==null?void 0:R.length)>0?(u(),D("div",We,[s("div",He,[s("div",Ze,[ze,(u(!0),D(P,null,W(a.getAdditionalServicesSchemes,d=>{var b,g;return u(),Z(y,{name:`${(b=c.sectionData)==null?void 0:b.Code}SchemeID`,checkbox:!1,data:d,modelValue:a.formData[`${(g=c.sectionData)==null?void 0:g.Code}SchemeID`],"onUpdate:modelValue":f[3]||(f[3]=E=>{var t;return a.formData[`${(t=c.sectionData)==null?void 0:t.Code}SchemeID`]=E})},null,8,["name","data","modelValue"])}),256))])])])):O("",!0),((K=a.getSelectFields)==null?void 0:K.length)>0?(u(),D("div",Ge,[s("div",Je,[(u(!0),D(P,null,W(a.getSelectFields,d=>(u(),D("div",Qe,[s("div",Xe,[F(k(d==null?void 0:d.Name),1),d!=null&&d.IsDuty?(u(),D("span",Ye,"*")):O("",!0)]),(u(!0),D(P,null,W(a.getSelectFieldOptions(d),b=>(u(),Z(y,{name:`ID${d==null?void 0:d.ID}`,checkbox:!1,data:b,modelValue:a.formData[`ID${d==null?void 0:d.ID}`],"onUpdate:modelValue":g=>a.formData[`ID${d==null?void 0:d.ID}`]=g},null,8,["name","data","modelValue","onUpdate:modelValue"]))),256))]))),256))])])):O("",!0),c.isInline?(u(),D("div",oa,k(a.finalPrice()||0)+" ₽",1)):(u(),D("div",$e,[s("div",ea,[s("div",aa,[s("div",na,"Итого за 1 "+k((x=c.sectionData)==null?void 0:x.Measure),1),s("div",ta,k(a.finalPrice()||0)+" ₽",1)])]),q(U,{class:"btn--wide",disabled:a.getButtonDisableCondition,label:"Заказать","is-loading":a.isLoading,onClick:f[4]||(f[4]=d=>a.payForOrder())},null,8,["disabled","is-loading"])]))],64)}const da={components:{InputBorderCheckboxRadio:te,Multiselect:ve},props:{modelValue:{type:Object,default:()=>{}},sectionID:{type:String,default:""},sectionData:{type:Object,default:()=>({})},isInline:{type:Boolean,default:!1},updateWithPageTransition:{type:Boolean,default:!0}},emits:["updateList","update:modelValue"],setup(l,{emit:f}){const c=fe(),a=De();se();const S=ce(),H=ue(),i=C({}),m=C(null),A=C(null),p=C(null),y=C(null),U=C(null),h=C(!1);_e("emitter");const M=V(()=>{var t,o;return(o=Object.keys((a==null?void 0:a.additionalServicesScheme[(t=l.sectionData)==null?void 0:t.Code])||{}))==null?void 0:o.map(e=>{var v,_,I,B,G,J,Q,X,Y;const n=((_=a==null?void 0:a.additionalServicesScheme[(v=l.sectionData)==null?void 0:v.Code][e])==null?void 0:_.DependOrders)||{},r=Object.keys(n);return{...a==null?void 0:a.additionalServicesScheme[(I=l.sectionData)==null?void 0:I.Code][e],value:(G=a==null?void 0:a.additionalServicesScheme[(B=l.sectionData)==null?void 0:B.Code][e])==null?void 0:G.ID,price:(Q=a==null?void 0:a.additionalServicesScheme[(J=l.sectionData)==null?void 0:J.Code][e])==null?void 0:Q.CostMonth,name:(Y=a==null?void 0:a.additionalServicesScheme[(X=l.sectionData)==null?void 0:X.Code][e])==null?void 0:Y.Name,dependOrders:r}}).sort((e,n)=>Number(e==null?void 0:e.SortID)<Number(n==null?void 0:n.SortID)?-1:Number(e==null?void 0:e.SortID)>Number(n==null?void 0:n.SortID)?1:0).filter(e=>y.value==="0"?(e==null?void 0:e.IsActive)&&e.dependOrders.length===0:y.value===null?e==null?void 0:e.IsActive:(e==null?void 0:e.IsActive)&&e.dependOrders.includes(y.value))}),T=V(()=>{var o;let t=!1;return(o=j.value)==null||o.forEach(e=>{e!=null&&e.IsDuty&&(i.value[`ID${e==null?void 0:e.ID}`]===null||i.value[`ID${e==null?void 0:e.ID}`]===void 0||i.value[`ID${e==null?void 0:e.ID}`]==="")&&(t=!0)}),t}),N=V(()=>{var t;return(t=l.sectionData)==null?void 0:t.ServicesFields.filter(o=>(o==null?void 0:o.TypeID)==="Select").sort((o,e)=>Number(o==null?void 0:o.SortID)<Number(e==null?void 0:e.SortID)?-1:Number(o==null?void 0:o.SortID)>Number(e==null?void 0:e.SortID)?1:0)}),j=V(()=>{var t;return(t=l.sectionData)==null?void 0:t.ServicesFields.filter(o=>(o==null?void 0:o.TypeID)==="Input").sort((o,e)=>Number(o==null?void 0:o.SortID)<Number(e==null?void 0:e.SortID)?-1:Number(o==null?void 0:o.SortID)>Number(e==null?void 0:e.SortID)?1:0)}),L=V(()=>{var v;const t=a==null?void 0:a.additionalServicesScheme[(v=l.sectionData)==null?void 0:v.Code];let o=[];if(!t)return o;const e=Object.values(t).filter(_=>(_==null?void 0:_.IsActive)&&(_==null?void 0:_.DependOrders)).flatMap(_=>Object.entries(_.DependOrders)),n=new Map;e.forEach(([_,I])=>{const B=`${_}:${I}`;n.has(B)||n.set(B,{name:I,value:_})});const r=Array.from(n.values());return l.sectionID==="51000"?o=[{name:"Без заказа",value:"0"},...r]:o=r,o}),R=V(()=>{var e;const t=a==null?void 0:a.additionalServicesScheme[(e=l.sectionData)==null?void 0:e.Code];return t?Object.values(t).filter(n=>(n==null?void 0:n.IsActive)&&(n==null?void 0:n.DependOrders)).flatMap(n=>Object.entries(n.DependOrders)).map(([n,r])=>({name:r,value:n})):[]}),K=V(()=>{var n,r,v;const t=(r=i.value[`${(n=l.sectionData)==null?void 0:n.Code}SchemeID`])==null?void 0:r.ID,o=a==null?void 0:a.additionalServicesScheme[(v=l.sectionData)==null?void 0:v.Code];if(!t)return R.value;const e=o==null?void 0:o[t];return e&&e.DependOrders?Object.entries(e.DependOrders).map(([_,I])=>({name:I,value:_})):[]});function x(){var o;let t=0;return Object.keys(i.value).map(e=>{var n,r;(n=i.value[e])!=null&&n.price&&(t=t+Number((r=i.value[e])==null?void 0:r.price))}),t+Number((o=l.sectionData)==null?void 0:o.Cost)}function d(t){var o,e;return(e=(o=t==null?void 0:t.Options)==null?void 0:o.split(`
`))==null?void 0:e.map(n=>{let r=n.split("=");return{value:r[0],name:r[1],price:r[2]}})}function b(){var o;h.value=!0;let t={};(o=Object.keys(i.value))==null||o.forEach(e=>{var n,r;t[e]=(n=i.value[e])!=null&&n.value?(r=i.value[e])==null?void 0:r.value:i.value[e]}),t.ContractID=p.value,t.ServiceID=l.sectionID,t.DependOrderID=y.value,y.value==="0"&&(t.IP=U.value),l.sectionID==="50000"?a.ExtraIPOrder(t).then(({result:e,info:n,error:r})=>{h.value=!1,e==="SUCCESS"?S.push(`/AdditionalServices?ServiceID=${l.sectionID}`):e==="BASKET"&&S.push("/Basket")}):l.sectionID==="51000"?a.ISPswOrder(t).then(({result:e})=>{h.value=!1,e==="SUCCESS"?S.push(`/AdditionalServices?ServiceID=${l.sectionID}`):e==="BASKET"&&S.push("/Basket")}):l.sectionID==="52000"?a.DNSmanagerOrder(t).then(({result:e,info:n,error:r})=>{h.value=!1,e==="SUCCESS"?S.push(`/AdditionalServices?ServiceID=${l.sectionID}`):e==="BASKET"&&S.push("/Basket")}):a.ServiceOrder(t).then(({result:e,info:n,error:r})=>{var v;e==="SUCCESS"?a.ServiceOrderPay((v=l.sectionData)==null?void 0:v.Code,{AmountPay:"1",ServiceOrderID:n==null?void 0:n.ServiceOrderID,IsChange:!0}).then(_=>{let{result:I,error:B}=_;I==="SUCCESS"?S.push(`/AdditionalServices?ServiceID=${l.sectionID}`):I==="BASKET"&&S.push("/Basket"),h.value=!1}):h.value=!1})}function g(){var o;let t={};(o=Object.keys(i.value))==null||o.forEach(e=>{var n,r;t[e]=(n=i.value[e])!=null&&n.value?(r=i.value[e])==null?void 0:r.value:i.value[e]}),t.ServiceID=l.sectionID,t.Price=x(),f("update:modelValue",t)}function E(){var t,o;if(l.modelValue){const e=(t=Object.keys(l.modelValue))==null?void 0:t.find(n=>n.includes("SchemeID"));e&&((o=i.value[e])!=null&&o.ID||(i.value[e]={}),i.value[e].ID=l.modelValue[e])}}return w(()=>{var t;return i.value[`${(t=l.sectionData)==null?void 0:t.Code}SchemeID`]},t=>{t&&t.ID?console.log(`Выбран новый тариф с ID: ${t.ID}`):console.log("Тариф не выбран")}),w(H,()=>{var t,o,e;E(),l.updateWithPageTransition&&((t=N.value)==null||t.forEach(n=>{var r;i.value[`ID${n==null?void 0:n.ID}`]&&(i.value[`ID${n==null?void 0:n.ID}`]=(r=d(n))==null?void 0:r.find(v=>v.value===(n==null?void 0:n.Default)))}),((o=l.sectionData)==null?void 0:o.Code)!=="Default"&&a.fetchAdditionalServiceScheme((e=l.sectionData)==null?void 0:e.Code))}),w(i.value,()=>{g()}),w(l,()=>{E()}),ee(async()=>{var t,o,e;(t=N.value)==null||t.forEach(n=>{var r;i.value[`ID${n==null?void 0:n.ID}`]=(r=d(n))==null?void 0:r.find(v=>v.value===(n==null?void 0:n.Default))}),((o=l.sectionData)==null?void 0:o.Code)!=="Default"&&await a.fetchAdditionalServiceScheme((e=l.sectionData)==null?void 0:e.Code),E(),g()}),{contractSelect:p,contractsStore:c,getSelectFields:N,getInputFields:j,selectServerModel:m,isLoading:h,getButtonDisableCondition:T,getAdditionalServicesSchemes:M,getSelectFieldOptions:d,getDependOrders:L,DependOrderID:y,operationSystem:A,formData:i,finalPrice:x,payForOrder:b,filteredDependOrders:K,serviceIP:U}}},va=$(da,[["render",la],["__scopeId","data-v-55526140"]]);export{va as S};

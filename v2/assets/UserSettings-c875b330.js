import{o as b,c as k,b as n,a as o,w as _,d as m,p as S,l as C,_ as D,f as w,r as p,g as y}from"./index-90eb49f0.js";import{_ as f}from"./BasicInput-936f29ee.js";import{I as B}from"./IconUpload-f81d9d2b.js";import{_ as v}from"./ClausesKeeper-313482ce.js";import{B as g}from"./BlockBalanceAgreement-267acfcf.js";import{_ as U}from"./ButtonDefault-5aebc1f7.js";import{f as P}from"./bootstrap-vue-next.es-788a757a.js";import"./component-65c692c0.js";import"./IconClose-c838a5bc.js";import"./contracts-0cbe8270.js";import"./IconArrow-380053a0.js";const i=s=>(S("data-v-f53d758b"),s=s(),C(),s),x={class:"section"},N={class:"container"},A=i(()=>o("div",{class:"section-header"},[o("h1",{class:"section-title"},"Настройки")],-1)),E={class:"profile-settings"},O={class:"profile-settings__item"},W=i(()=>o("div",{class:"profile-settings__title"},"Система поддержки пользователей (Тикеты)",-1)),Y={class:"profile-settings__item"},L=i(()=>o("div",{class:"profile-settings__title"},"Интерфейс",-1)),K={class:"profile-settings__item"},T=i(()=>o("div",{class:"profile-settings__title"},"Счета",-1)),j=i(()=>o("div",{class:"note"},"На какой период выписывать автоматически выставляемые счета на продление (дни 10-9999)",-1)),q={class:"profile-settings__item"};function z(s,e,l,t,u,d){const r=g,h=v,c=P,I=f,V=U;return b(),k("div",x,[n(r),o("div",N,[A,n(h),o("div",E,[o("div",O,[W,n(c,{id:"display-thumbnails",name:"display-thumbnails",value:"Yes","unchecked-value":"No",modelValue:t.formData.EdeskImagesPreview,"onUpdate:modelValue":e[0]||(e[0]=a=>t.formData.EdeskImagesPreview=a)},{default:_(()=>[m("Отображать миниатюры изображений")]),_:1},8,["modelValue"])]),o("div",Y,[L,n(c,{id:"display-counterparties",name:"display-counterparties",value:"Yes","unchecked-value":"No",modelValue:t.formData.ShowContractsWithoutOrders,"onUpdate:modelValue":e[1]||(e[1]=a=>t.formData.ShowContractsWithoutOrders=a)},{default:_(()=>[m("Отображать договора без услуг")]),_:1},8,["modelValue"])]),o("div",K,[T,n(c,{id:"issue-invoices",name:"issue-invoices",value:"Yes","unchecked-value":"No",modelValue:t.formData.CreateInvoicesAutomatically,"onUpdate:modelValue":e[2]||(e[2]=a=>t.formData.CreateInvoicesAutomatically=a)},{default:_(()=>[m("Выписывать счета автоматически")]),_:1},8,["modelValue"]),n(I,{label:"Период выписки счетов",name:"billing-period",placeholder:"365",modelValue:t.formData.InvoicingPeriod,"onUpdate:modelValue":e[3]||(e[3]=a=>t.formData.InvoicingPeriod=a),isNumber:"",vertical:""},null,8,["modelValue"]),j]),o("div",q,[n(V,{onClick:e[4]||(e[4]=a=>t.saveChanges()),"is-loading":t.isLoading,label:"Сохранить изменения"},null,8,["is-loading"])])])])])}const F={components:{IconUpload:B,BasicInput:f,ClausesKeeper:v,BlockBalanceAgreement:g},async setup(){const s=w(),e=p(!1),l=p({EdeskImagesPreview:null,ShowContractsWithoutOrders:null,CreateInvoicesAutomatically:null,InvoicingPeriod:0}),t=y(()=>{var d,r;return(r=(d=s.userInfo)==null?void 0:d.Params)==null?void 0:r.Settings});function u(){e.value=!0,s.userPersonalDataSettings({...l.value}).then(()=>{e.value=!1})}return await s.fetchUserData().then(()=>{l.value={...t.value}}),{formData:l,getUserSettings:t,isLoading:e,saveChanges:u}}},oe=D(F,[["render",z],["__scopeId","data-v-f53d758b"]]);export{oe as default};

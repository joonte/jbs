import{_ as p}from"./ButtonDefault-34d9e627.js";import{_ as m,u,o as b,c as f,a as c,b as n,p as C,l as I}from"./index-33bd9cf4.js";const h=o=>(C("data-v-7e1d5347"),o=o(),I(),o),v={class:"modal__body"},$=h(()=>c("div",{class:"list-item__title"},"Вы действительно хотите отписаться от уведомлений?",-1)),g={class:"modal__buttons"},y={__name:"ModalWindowUnsubscribeConfirmation",props:["data"],emits:["modalClose"],setup(o,{emit:_}){const s=o,d=u();function i(){_("modalClose")}function l(){var e,a,t;d.confirmUnsubscribe(`ContactID=${(e=s.data)==null?void 0:e.ContactID}&TypeID=${(a=s.data)==null?void 0:a.TypeID}&Code=${(t=s.data)==null?void 0:t.Code}&IsConfirm=${1}`).then(r=>{console.log(r)})}return(e,a)=>{const t=p;return b(),f("div",v,[$,c("div",g,[n(t,{class:"modal__button",label:"Да",onClick:l}),n(t,{class:"modal__button",label:"Нет",onClick:i})])])}}},k=m(y,[["__scopeId","data-v-7e1d5347"]]);export{k as default};

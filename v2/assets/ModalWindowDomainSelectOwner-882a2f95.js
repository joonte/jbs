import{_ as N}from"./ButtonDefault-4233c21f.js";import{f as M}from"./bootstrap-vue-next.es-be8a8de1.js";import{_ as P,r as m,e as U,S as $,g as w,i as V,O as j,o as A,c as W,a as c,t as h,b as _,w as E,d as K}from"./index-00a0bf0d.js";import{u as T}from"./domain-e5e5c8a9.js";import{u as q}from"./contracts-8ab155ab.js";import{B as z}from"./BlockSelectProfile-204e4062.js";import"./multiselect-9faec1a5.js";const G={class:"modal__body"},H={class:"list-item__title"},J={class:"modal__input-row"},L={class:"modal__input-row"},Q={class:"modal__buttons-wrap"},X={__name:"ModalWindowDomainSelectOwner",props:["data"],emits:["modalClose","buttonClicked"],setup(f,{emit:x}){const i=f,p=T(),v=q(),d=m(null),r=m(!1),D=m(!1);m("Natural"),U();const g=$("emitter"),u=w(()=>{const t=Object.values(p.domainsList);return i.data.OrderID!==void 0&&i.data.OrderID!==null?t.filter(e=>e.OrderID.toString()===i.data.OrderID.toString()):null}),n=w(()=>v==null?void 0:v.profileList),b=w(()=>Object.keys(n==null?void 0:n.value).map(t=>{var e,o,a,l;return{value:(o=(e=n.value)==null?void 0:e[t])==null?void 0:o.ID,name:(l=(a=n.value)==null?void 0:a[t])==null?void 0:l.Name}}));V(()=>{(b==null?void 0:b.value.length)<1&&g.emit("open-modal",{component:"NewProfile"})});function O(){x("modalClose")}function B(){g.emit("open-modal",{component:"AcceptCreateProfile"})}function k(){var o;D.value=!0;let t;u.value&&u.value!==void 0?t=u.value[0].ID:t=i.data.DomainOrderID;const e={DomainOrderID:t,OwnerTypeID:"Profile",ProfileID:(o=n.value[d.value])==null?void 0:o.ID,Agree:r.value?"yes":"no"};p.DomainSelectOwner(e).then(({result:a,resultData:l})=>{a==="SUCCESS"&&O(),D.value=!1})}const y=t=>{t.key==="Enter"&&k()};return V(()=>{document.addEventListener("keyup",y),p.fetchDomains()}),j(()=>{document.removeEventListener("keyup",y)}),(t,e)=>{var l,C,S,I;const o=M,a=N;return A(),W("div",G,[c("div",H,"Владелец домена "+h((l=f.data)!=null&&l.Domain?(C=f.data)==null?void 0:C.Domain:(I=(S=u.value)==null?void 0:S[0])==null?void 0:I.Domain),1),c("div",J,[_(z,{title:"",modelValue:d.value,"onUpdate:modelValue":e[0]||(e[0]=s=>d.value=s),domainOwner:!0,onButtonClicked:e[1]||(e[1]=s=>O())},null,8,["modelValue"])]),c("div",L,[_(o,{modelValue:r.value,"onUpdate:modelValue":e[2]||(e[2]=s=>r.value=s)},{default:E(()=>[K("Согласен на передачу моих персональных данных регистратору")]),_:1},8,["modelValue"])]),c("div",Q,[_(a,{class:"select-contract__button modal__button",label:"Создать новый профиль",onClick:e[3]||(e[3]=s=>B())}),_(a,{class:"modal__button",label:"Сохранить",disabled:!r.value||d.value===null,"is-loading":D.value,onClick:e[4]||(e[4]=s=>k())},null,8,["disabled","is-loading"])])])}}},ae=P(X,[["__scopeId","data-v-b6a082f4"]]);export{ae as default};

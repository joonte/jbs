import{_ as S}from"./ButtonDefault-cdd68300.js";import{f as x}from"./bootstrap-vue-next.es-44836407.js";import{_ as y,r,e as B,R as N,g as D,i as O,o as M,c as P,a as u,t as $,b as m,w as I,d as U}from"./index-7c70979b.js";import{u as W}from"./domain-f452bc4d.js";import{u as j}from"./contracts-6e790117.js";import{B as A}from"./BlockSelectProfile-ed7504cb.js";import"./multiselect-42ffb1f2.js";const T={class:"modal__body"},h={class:"list-item__title"},q={class:"modal__input-row"},z={class:"modal__input-row"},G={class:"modal__buttons-wrap"},H={__name:"ModalWindowDomainSelectOwner",props:["data"],emits:["modalClose","buttonClicked"],setup(f,{emit:w}){const g=f,C=W(),c=j(),i=r(null),d=r(!1),_=r(!1);r("Natural"),B();const v=N("emitter"),o=D(()=>c==null?void 0:c.profileList),p=D(()=>Object.keys(o==null?void 0:o.value).map(n=>{var e,t,a,s;return{value:(t=(e=o.value)==null?void 0:e[n])==null?void 0:t.ID,name:(s=(a=o.value)==null?void 0:a[n])==null?void 0:s.Name}}));O(()=>{(p==null?void 0:p.value.length)<1&&v.emit("open-modal",{component:"NewProfile"})});function b(){w("modalClose")}function k(){v.emit("open-modal",{component:"AcceptCreateProfile"})}function V(){var e;_.value=!0;const n={DomainOrderID:g.data.DomainOrderID,OwnerTypeID:"Profile",ProfileID:(e=o.value[i.value])==null?void 0:e.ID,Agree:d.value?"yes":"no"};C.DomainSelectOwner(n).then(({result:t,resultData:a})=>{t==="SUCCESS"&&b(),_.value=!1})}return(n,e)=>{var s;const t=x,a=S;return M(),P("div",T,[u("div",h,"Владелец домена "+$((s=f.data)==null?void 0:s.Domain),1),u("div",q,[m(A,{title:"",modelValue:i.value,"onUpdate:modelValue":e[0]||(e[0]=l=>i.value=l),onButtonClicked:e[1]||(e[1]=l=>b())},null,8,["modelValue"])]),u("div",z,[m(t,{modelValue:d.value,"onUpdate:modelValue":e[2]||(e[2]=l=>d.value=l)},{default:I(()=>[U("Согласен на передачу моих персональных данных регистратору")]),_:1},8,["modelValue"])]),u("div",G,[m(a,{class:"select-contract__button modal__button",label:"Создать новый профиль",onClick:e[3]||(e[3]=l=>k())}),m(a,{class:"modal__button",label:"Сохранить",disabled:!d.value||i.value===null,"is-loading":_.value,onClick:e[4]||(e[4]=l=>V())},null,8,["disabled","is-loading"])])])}}},E=y(H,[["__scopeId","data-v-f5b1e584"]]);export{E as default};

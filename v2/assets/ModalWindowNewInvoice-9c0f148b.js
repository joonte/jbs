import{_ as m}from"./ButtonDefault-cd183bf8.js";import{_ as b}from"./BlockSelectContract-13fe66da.js";import{_ as v,R as f,e as g,o as w,c as C,a as s,b as l,p as k,l as I}from"./index-1c309346.js";import"./contracts-7b919c67.js";import"./multiselect-b7ad6876.js";const B=t=>(k("data-v-1b3e9b80"),t=t(),I(),t),N={class:"modal__body"},V=B(()=>s("div",{class:"list-item__title"},"Новый счёт",-1)),$={class:"modal__input-row"},M={class:"modal__btn-group"},y={__name:"ModalWindowNewInvoice",emits:["modalClose","buttonClicked"],setup(t,{emit:a}){const c=f("emitter"),i=g();function d(){c.emit("open-modal",{component:"AcceptCreateProfile"})}function p(e){i.push(`/Balance/${e}`),a("modalClose")}function u(){a("modalClose")}return(e,o)=>{const r=b,_=m;return w(),C("div",N,[V,s("div",$,[l(r,{title:"",modelValue:e._contract_select,"onUpdate:modelValue":o[0]||(o[0]=n=>e._contract_select=n),onButtonClicked:o[1]||(o[1]=n=>u())},null,8,["modelValue"])]),s("div",M,[l(_,{class:"select-contract__button modal__button",label:"Создать новый профиль",onClick:o[2]||(o[2]=n=>d())}),l(_,{class:"modal__button",label:"Выписать новый счёт",disabled:e._contract_select===null,"is-loading":e._is_button_loading,onClick:o[3]||(o[3]=n=>p(e._contract_select))},null,8,["disabled","is-loading"])])])}}},j=v(y,[["__scopeId","data-v-1b3e9b80"]]);export{j as default};

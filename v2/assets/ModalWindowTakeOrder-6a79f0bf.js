import{o as p,c as u,a as l,b as _,p as m,l as f,_ as v,r as i}from"./index-58c913f0.js";import{u as I}from"./globalActions-45df61a2.js";import{_ as O}from"./ButtonDefault-7a589010.js";import{_ as b}from"./BlockSelectContract-d0777105.js";import"./contracts-5c545170.js";import"./multiselect-1db9a63f.js";const k=o=>(m("data-v-e05d8688"),o=o(),f(),o),C={class:"modal__body"},S=k(()=>l("div",{class:"list-item__title"},"Перенос услуги",-1)),T={class:"modal__input-row"},g={class:"modal__input-row"};function w(o,e,a,t,d,c){const n=b,r=O;return p(),u("div",C,[S,l("div",T,[_(n,{title:"",modelValue:t._contract_select,"onUpdate:modelValue":e[0]||(e[0]=s=>t._contract_select=s),onButtonClicked:e[1]||(e[1]=s=>o.closeModal())},null,8,["modelValue"])]),l("div",g,[_(r,{class:"modal__button",label:"Сохранить",onClick:e[2]||(e[2]=s=>t.acceptOrder())})])])}const y={props:{data:{type:Object,default:()=>{}}},emits:["modalClose","buttonClicked"],setup(o,{emit:e}){const a=i(""),t=i(o.data.OrdersTransferID||""),d=I();async function c(){console.log(a.value),console.log(t.value.OrdersTransferID);const n={ContractID:a.value,OrdersTransferID:t.value.OrdersTransferID};try{const{result:r,error:s}=await d.OrdersTransfer(n);r==="SUCCESS"?e("modalClose"):console.error("Error:",s)}catch(r){console.error("Error:",r.message)}}return{acceptOrder:c,orderTransferId:t,_contract_select:a}}},M=v(y,[["render",w],["__scopeId","data-v-e05d8688"]]);export{M as default};
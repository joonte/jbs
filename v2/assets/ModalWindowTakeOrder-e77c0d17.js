import{o as u,c as m,a as c,b as _,p as f,l as v,_ as k,r as i,i as y,O}from"./index-6f846e0d.js";import{u as I}from"./globalActions-d40367d6.js";import{_ as b}from"./ButtonDefault-40efcd87.js";import{_ as C}from"./BlockSelectContract-579fe474.js";import"./contracts-b643bcd5.js";import"./multiselect-265f6f4c.js";const S=o=>(f("data-v-87826712"),o=o(),v(),o),E={class:"modal__body"},T=S(()=>c("div",{class:"list-item__title"},"Перенос услуги",-1)),w={class:"modal__input-row"},B={class:"modal__input-row"};function D(o,e,a,r,l,d){const n=C,s=b;return u(),m("div",E,[T,c("div",w,[_(n,{title:"",modelValue:r._contract_select,"onUpdate:modelValue":e[0]||(e[0]=t=>r._contract_select=t),onButtonClicked:e[1]||(e[1]=t=>o.closeModal())},null,8,["modelValue"])]),c("div",B,[_(s,{class:"modal__button",label:"Сохранить",onClick:e[2]||(e[2]=t=>r.acceptOrder())})])])}const V={props:{data:{type:Object,default:()=>{}}},emits:["modalClose","buttonClicked"],setup(o,{emit:e}){const a=i(""),r=i(o.data.OrdersTransferID||""),l=I();async function d(){const s={ContractID:a.value,OrdersTransferID:r.value.OrdersTransferID};try{const{result:t,error:p}=await l.OrdersTransfer(s);t==="SUCCESS"?e("modalClose"):console.error("Error:",p)}catch(t){console.error("Error:",t.message)}}const n=s=>{s.key==="Enter"&&d()};return y(()=>{document.addEventListener("keyup",n)}),O(()=>{document.removeEventListener("keyup",n)}),{acceptOrder:d,orderTransferId:r,_contract_select:a}}},L=k(V,[["render",D],["__scopeId","data-v-87826712"]]);export{L as default};
import{_ as y,r as c,h as _,i as V,j as b,o as s,c as o,d as v,t as m,k as d,a as f,b as g,n as p,p as k,l as h}from"./index-b5b5ce3d.js";import{c as B}from"./component-2368399e.js";const S={components:{"imask-input":B},props:{modelValue:[String,Number],label:{type:String,default:""},vertical:{type:Boolean,default:!1},name:{type:String,default:""},placeholder:{type:String,default:""},isPassword:{type:Boolean,default:!1},isNumber:{type:Boolean,default:!1},necessarily:{type:Boolean,default:!1},errorMessage:{type:String,default:null},disabled:{type:Boolean,default:!1}},emits:["update:modelValue","input"],setup(t,{emit:l}){const e=c(null),n=c(!1);function u(){let a=e.value;(t.name==="Name"||t.name==="Sourname"||t.name==="Lastname")&&(a=a.replace(/\s/g,""),a=a.charAt(0).toUpperCase()+a.slice(1)),l("update:modelValue",a),l("input",a)}function i(){n.value=!0}function r(a){if(a==="BornDate"||a==="PasportDate")return"00.00.0000";if(a==="Inn"||a==="Kpp")return"0000000000";if(a==="Index"||a==="PasportNum")return"000000";if(a==="Bik")return"000000000";if(a==="BankAccount")return"00000000000000000000";if(a==="PasportLine")return"0000";if(a==="phoneNumberSMS")return"70000000000"}return _(t,()=>{e.value=t.modelValue}),_(e,()=>{u()}),V(()=>{e.value=t.modelValue}),{inputValue:e,isIconPressed:n,onMouseEvent:i,updateValue:u,getMask:r}}},I=t=>(k("data-v-212d2581"),t=t(),h(),t),w={key:0,class:"form-field__label"},M={key:0},x=I(()=>f("span",null,"*",-1)),P=[x],C={key:0,class:"form-field-input__error-message"};function D(t,l,e,n,u,i){const r=b("imask-input");return s(),o("div",{class:p([{"form-field__column":e.vertical},"form-field"])},[e.label!==null&&e.label!==""?(s(),o("div",w,[v(m(e.label)+" ",1),e.necessarily?(s(),o("span",M,P)):d("",!0)])):d("",!0),f("div",{class:p(["form-field-input__wrapper",e.disabled?"form-field-input__wrapper_disabled":""])},[g(r,{modelValue:n.inputValue,"onUpdate:modelValue":l[0]||(l[0]=a=>n.inputValue=a),name:e.name,placeholder:e.placeholder,disabled:e.disabled,type:e.isPassword?"password":e.isNumber?"number":"text",onInput:n.updateValue,mask:n.getMask(e.name)},null,8,["modelValue","name","placeholder","disabled","type","onInput","mask"]),e.errorMessage?(s(),o("div",C,m(e.errorMessage),1)):d("",!0)],2)],2)}const L=y(S,[["render",D],["__scopeId","data-v-212d2581"]]);export{L as _};
import{o as f,c as u,b as e,a as r,p as w,l as v,_ as g,f as P,r as d}from"./index-59dbe3d3.js";import{_ as c}from"./ClausesKeeper-b1ff97f6.js";import{B as i}from"./BlockBalanceAgreement-f093da58.js";import{_ as h}from"./ButtonDefault-a62141a4.js";import{_ as B}from"./BasicInput-1e25083b.js";import"./IconClose-0bef5a37.js";import"./contracts-d90f6cbb.js";import"./bootstrap-vue-next.es-faf1dad1.js";import"./IconArrow-ad4e318c.js";import"./component-ff695e34.js";const V=a=>(w("data-v-f68f4989"),a=a(),v(),a),b={class:"section"},k={class:"container"},C=V(()=>r("div",{class:"section-header"},[r("h1",{class:"section-title"}," Сменить пароль")],-1)),D={class:"form-offset_label"};function S(a,o,n,s,I,U){const _=i,m=c,l=B,p=h;return f(),u("div",b,[e(_),r("div",k,[C,e(m),e(l,{label:"Новый пароль",name:"New_password","is-password":"",modelValue:s.formData.password,"onUpdate:modelValue":o[0]||(o[0]=t=>s.formData.password=t)},null,8,["modelValue"]),e(l,{label:"Подтвердить пароль",name:"Confirm_Password","is-password":"",modelValue:s.formData.confirmPassword,"onUpdate:modelValue":o[1]||(o[1]=t=>s.formData.confirmPassword=t)},null,8,["modelValue"]),r("div",D,[e(p,{"is-loading":s.isLoading,label:"Изменить пароль",onClick:s.changePassword},null,8,["is-loading","onClick"])])])])}const x={components:{ClausesKeeper:c,BlockBalanceAgreement:i},setup(){const a=P(),o=d({password:"",confirmPassword:""}),n=d(!1);function s(){o.value.password!==""&&o.value.confirmPassword!==""&&o.value.password===o.value.confirmPassword&&(n.value=!0,a.userPasswordChange({Password:o.value.password,_Password:o.value.confirmPassword}).then(()=>{n.value=!1}))}return{formData:o,isLoading:n,changePassword:s}}},G=g(x,[["render",S],["__scopeId","data-v-f68f4989"]]);export{G as default};

import{o as g,c as w,a as e,t as b,b as p,Y as c,ae as d,p as D,l as U,_ as y,f as N,a6 as P,e as S,R as x,r as u,g as v}from"./index-ed1f28f8.js";import{u as I}from"./contracts-cedf6a91.js";import{s as R}from"./multiselect-198a8c51.js";import{_ as C}from"./ButtonDefault-a8aacc82.js";import{_ as E}from"./ClausesKeeper-9aa8ead7.js";import"./IconClose-a1e58263.js";const l=o=>(D("data-v-7a5ace9a"),o=o(),U(),o),V={class:"create-referral"},k={class:"section"},B={class:"container"},L={class:"section-header"},M=l(()=>e("h1",{class:"section-title"},"Создать реферала",-1)),j={class:"section-label"},A={class:"section"},T={class:"container"},Y={class:"create-referral__data"},q={class:"form-field"},z=l(()=>e("div",{class:"form-field__label"},"ФИО",-1)),F={class:"form-field"},G=l(()=>e("div",{class:"form-field__label"},"Email",-1)),H={class:"form-field"},J=l(()=>e("div",{class:"form-field__label"},"Пароль",-1)),K={class:"form-field"},O=l(()=>e("div",{class:"form-field__label"},"Подтвердить пароль",-1)),Q={class:"section"},W={class:"container"},X={class:"create-referral__data"};function Z(o,s,m,t,n,r){var i;const _=E,f=C;return g(),w("div",V,[e("div",k,[e("div",B,[e("div",L,[M,e("div",j,b((i=t.getUserInfo)==null?void 0:i.Name),1)]),p(_)])]),e("div",A,[e("div",T,[e("div",Y,[e("label",q,[z,c(e("input",{type:"text",placeholder:"","onUpdate:modelValue":s[0]||(s[0]=a=>t.formData.Name=a)},null,512),[[d,t.formData.Name]])]),e("label",F,[G,c(e("input",{type:"text","onUpdate:modelValue":s[1]||(s[1]=a=>t.formData.Email=a)},null,512),[[d,t.formData.Email]])]),e("label",H,[J,c(e("input",{type:"password","onUpdate:modelValue":s[2]||(s[2]=a=>t.formData.Password=a)},null,512),[[d,t.formData.Password]])]),e("label",K,[O,c(e("input",{type:"password","onUpdate:modelValue":s[3]||(s[3]=a=>t.formData._Password=a)},null,512),[[d,t.formData._Password]])])])])]),e("div",Q,[e("div",W,[e("div",X,[p(f,{class:"btn--wide",label:"Зарегистрировать","is-loading":t.isLoading,onClick:s[4]||(s[4]=a=>t.createReferral())},null,8,["is-loading"])])])])])}const $={components:{Multiselect:R},async setup(){const o=N(),s=I(),m=P(),t=S();x("emitter");const n=u(!1),r=u({Email:"",Name:"",Password:"",_Password:""}),_=v(()=>o.userInfo),f=v(()=>s.contractsList[m.params.id]);function i(){r.value.Email.length>0&&r.value.Password.length>0&&r.value.Name.length>0&&r.value._Password.length>0&&(n.value=!0,o.registerNewUser({...r.value}).then(a=>{const{response:h,error:ee}=a;h==="success"&&t.push({path:"/DependUsers"}),n.value=!1}))}return await o.fetchUserData(),await s.fetchContracts(),{formData:r,getUserInfo:_,getContract:f,isLoading:n,createReferral:i}}},ne=y($,[["render",Z],["__scopeId","data-v-7a5ace9a"]]);export{ne as default};

import{o as A,c as F,a as m,b as i,n as y,w as H,d as M,_ as N,u as T,e as q,r as u,f as D,g as E}from"./index-59dbe3d3.js";import{_ as B}from"./BasicInput-1e25083b.js";import{_ as O}from"./ButtonDefault-a62141a4.js";import j from"./IconGoogle-b37ca51f.js";import z from"./IconOk-dac58350.js";import G from"./IconVk-240a2ffc.js";import X from"./IconYandex-7913f737.js";import{u as Y}from"./promoStore-e0b1f805.js";import{B as S}from"./BlockAuthEnter-761e569e.js";import{f as J}from"./bootstrap-vue-next.es-faf1dad1.js";import"./component-ff695e34.js";const K={class:"sign-in"},Q={class:"section"},W={class:"container"},Z=m("div",{class:"section-header"},[m("h1",{class:"section-title"},"Войти в аккаунт")],-1),$={class:"form-offset_label"},ee={class:"form-offset_label"};function oe(c,o,s,e,d,v){const l=B,a=O,_=J,r=S;return A(),F("div",K,[m("div",Q,[m("div",W,[Z,i(l,{class:y({"form-field-error":e.submitStatus==="ERROR"}),label:"Email",name:"login",modelValue:e.mail,"onUpdate:modelValue":o[0]||(o[0]=t=>e.mail=t)},null,8,["class","modelValue"]),i(l,{class:y({"form-field-error":e.submitStatus==="ERROR"}),label:"Пароль",name:"password",modelValue:e.password,"onUpdate:modelValue":o[1]||(o[1]=t=>e.password=t),"is-password":""},null,8,["class","modelValue"]),m("div",$,[i(a,{class:"button-password-restore",label:"ЗАБЫЛ ПАРОЛЬ",onClick:o[2]||(o[2]=t=>e.navigateToPage("/UserPasswordRestore"))})]),m("div",ee,[i(a,{onClick:o[3]||(o[3]=t=>e.signIn()),"is-loading":e.isLoading,label:"Войти"},null,8,["is-loading"]),i(_,{id:"checkbox-1",modelValue:e.isRemember,"onUpdate:modelValue":o[4]||(o[4]=t=>e.isRemember=t),name:"checkbox-1"},{default:H(()=>[M("Запомнить меня")]),_:1},8,["modelValue"])])])]),i(r,{label:"Войти с помощью"})])}const te={components:{BlockAuthEnter:S,BasicInput:B,IconGoogle:j,IconOk:z,IconVk:G,IconYandex:X,ButtonDefault:O},setup(){const c=T(),o=Y(),s=q(),e=u(null),d=u(null),v=u(!0),l=u(null),a=u(!1),_=u(""),r=D();E(()=>r==null?void 0:r.userInfo);const t=E(()=>c==null?void 0:c.ConfigList);function U(){var p;e.value===null||((p=e.value)==null?void 0:p.length)<3||d.value===null||d.value<4?l.value="ERROR":(a.value=!0,r.signIn({Email:e.value,Password:d.value,IsRemember:v.value===!0?"1":"0",ReOpen:"yes",XMLHttpRequest:"yes"}).then(n=>{var g,b,R,h,C,I;if(a.value=!1,(n==null?void 0:n.result)!=="success")l.value="ERROR",_.value=n.errorMessage;else if(c.fetchConfig().then(()=>{var f,V,k;if((f=t.value)!=null&&f.Colors){let w=document.documentElement;Object.keys((V=t.value)==null?void 0:V.Colors).map(P=>{var x;w.style.setProperty(`--${P}`,(x=t.value)==null?void 0:x.Colors[P])}),w.style.setProperty("--btn-hover-transparent",`${(k=t.value)==null?void 0:k.Colors["btn-hover"]}33`)}}),((b=(g=n==null?void 0:n.data)==null?void 0:g.User)==null?void 0:b.InterfaceID)==="User")if((R=s.currentRoute.value.redirectedFrom)!=null&&R.fullPath){const f=(C=(h=s.currentRoute.value.redirectedFrom)==null?void 0:h.query)==null?void 0:C.PromoCode;f&&o.setPromoCode(f),s.push({path:(I=s.currentRoute.value.redirectedFrom)==null?void 0:I.fullPath})}else s.push({path:"/Home"});else window.location.replace("/Administrator/Home")}))}function L(p){s.push(p)}return{isRemember:v,submitStatus:l,errorMessage:_,mail:e,password:d,isLoading:a,navigateToPage:L,signIn:U}}},_e=N(te,[["render",oe]]);export{_e as default};

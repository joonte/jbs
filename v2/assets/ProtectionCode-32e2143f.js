import{_ as i,r as c,g as p,o as n,c as r,a as _,b as u,w as m,k as f,T as v,p as h,l as g}from"./index-90eb49f0.js";const C=e=>(h("data-v-169f71be"),e=e(),g(),e),P=["src"],k={key:0,class:"protection-code__loader-wrapper"},w=C(()=>_("div",{class:"protection-code__loader"},null,-1)),x=[w],I={__name:"ProtectionCode",setup(e){const o=c(!1),t=c(null);function a(){t.value=Math.floor(Math.random()*(9999-1e3)+1e3)}function d(){o.value=!0,a(),setTimeout(()=>{o.value=!1},1e3)}const l=p(()=>`/Protect?Rand=${t.value}`);return a(),(b,s)=>(n(),r("div",{class:"protection-code__wrapper",onClick:s[0]||(s[0]=V=>d())},[_("img",{class:"protection-code__image",src:l.value,alt:""},null,8,P),u(v,{name:"fade",mode:"out-in"},{default:m(()=>[o.value?(n(),r("div",k,x)):f("",!0)]),_:1})]))}},B=i(I,[["__scopeId","data-v-169f71be"]]);export{B as P};

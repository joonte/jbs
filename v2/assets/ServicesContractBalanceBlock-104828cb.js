import{_ as m,o as p,c as f,a as e,t as a,b as d,d as u,e as b,g as C,i as w}from"./index-00a0bf0d.js";import{u as B}from"./contracts-8ab155ab.js";import{I}from"./IconProfile-3ffb91e1.js";import{_ as $}from"./IconCard-30dada0d.js";const x={},D={fill:"none",height:"24",viewBox:"0 0 24 24",width:"24",xmlns:"http://www.w3.org/2000/svg"},k=e("path",{"clip-rule":"evenodd",d:"M15.864 5.718c-5.504-2.135-11.696.597-13.83 6.101a.5.5 0 0 0 0 .362 10.69 10.69 0 0 0 6.102 6.102c5.504 2.134 11.696-.598 13.83-6.102a.5.5 0 0 0 0-.362 10.69 10.69 0 0 0-6.102-6.101ZM20.962 12c-2 4.868-7.536 7.261-12.465 5.35A9.69 9.69 0 0 1 3.038 12a9.69 9.69 0 0 1 17.923 0ZM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm3-4a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z",fill:"currentColor","fill-rule":"evenodd"},null,-1),y=[k];function M(n,t){return p(),f("svg",D,y)}const F=m(x,[["render",M]]),N={class:"list-col list-col--md"},P={class:"list-item"},Z={class:"list-item__row"},S={class:"list-item__title"},T={class:"list-item__row"},V={class:"list-item__balance btn-default"},A={class:"list-item__row"};function E(n,t,c,o,i,s){var r,_,l;const v=$,g=I;return p(),f("div",N,[e("div",P,[e("div",Z,[e("div",S,"# "+a((r=o.getContractsData)==null?void 0:r.ID)+" / "+a((_=o.getContractsData)==null?void 0:_.Customer),1)]),e("div",T,[e("div",V,[d(v),u("Баланс "+a((l=o.getContractsData)==null?void 0:l.Balance)+" ₽",1)]),e("div",{class:"btn btn-default btn--blue",onClick:t[0]||(t[0]=h=>o.navigateToBalancePage())},"Пополнить баланс")]),e("div",A,[e("div",{class:"btn btn-default btn--border",onClick:t[1]||(t[1]=h=>n.$emit("transfer"))},[d(g),u("Передать на другой аккаунт")])])])])}const L={props:{contractId:{type:[String,Number],default:""}},emits:["transfer"],async setup(n){const t=B(),c=b(),o=C(()=>{var s;return(s=t==null?void 0:t.contractsList)==null?void 0:s[n.contractId]});await t.fetchContracts();function i(){c.push(`/Balance/${n.contractId}`)}return w(()=>{console.log(o)}),{getContractsData:o,navigateToBalancePage:i}}},G=m(L,[["render",E],["__scopeId","data-v-668bc543"]]);export{G as _,F as a};

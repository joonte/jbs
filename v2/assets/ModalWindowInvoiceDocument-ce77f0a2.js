import{o as v,c as f,a as h,b as I,_ as b,S as w,r as D,i as g,C as M}from"./index-00a0bf0d.js";import{R as _}from"./RecursiveComponent-c36e213b.js";import{u as S}from"./postings-81749241.js";const y={class:"modal__body"},x={class:"html__container",id:"html-container"};function W(c,i,s,o,l,n){const e=_;return v(),f("div",y,[h("div",x,[I(e,{data:o.htmlContent},null,8,["data"])])])}const B={props:{data:{type:Object,default:()=>{}}},components:{RecursiveComponent:_},setup(c){const i=w("emitter"),s=S(),o=D(null);function l(n,e){var a,m,d,r,u;if(n==="/InvoiceDocument"&&(e!=null&&e.Mobile))((a=e.Mobile)==null?void 0:a.length)!==11?i.emit("open-error-modal",{component:"ExclusionWindow",data:{message:{String:"Неверный номер телефона"}}}):s.InvoiceDocument({InvoiceID:(m=c.data)==null?void 0:m.ID,Mobile:e.Mobile}).then(t=>{o.value=t});else{let t=(d=document==null?void 0:document.forms[0])==null?void 0:d.querySelector('input[name="Mobile"]');n==="/InvoiceDocument"&&t.value&&(((r=t.value)==null?void 0:r.length)!==11?i.emit("open-error-modal",{component:"ExclusionWindow",data:{message:{String:"Неверный номер телефона"}}}):s.InvoiceDocument({InvoiceID:(u=c.data)==null?void 0:u.ID,Mobile:t.value}).then(p=>{o.value=p}))}}return window.ShowWindow=l.bind(this),g(()=>{o.value=c.data.html;const n=document.querySelector(".modal__close-modal-button");n&&(n.style.display="none")}),M(()=>{i.emit("close-modal")}),{htmlContent:o}}},j=b(B,[["render",W],["__scopeId","data-v-0201d923"]]);export{j as default};

import{o as i,c as p,b as _,a as o,F as l,p as m,l as u,_ as h,g as f}from"./index-eaeb1281.js";import{u as k}from"./content-dab41baa.js";import{B as r}from"./BlockBalanceAgreement-11afb494.js";import"./contracts-ac470ffa.js";import"./bootstrap-vue-next.es-f4c478af.js";import"./ButtonDefault-598fef28.js";import"./IconArrow-ce1bc7b6.js";const v=t=>(m("data-v-57c18d97"),t=t(),u(),t),B={class:"section"},C={class:"container"},g=v(()=>o("div",{class:"section-header"},[o("h1",{class:"section-title"},"Контакты")],-1)),x=["innerHTML"];function y(t,n,e,s,b,T){var c,a;const d=r;return i(),p(l,null,[_(d),o("div",B,[o("div",C,[g,o("div",{class:"contacts",innerHTML:(a=(c=s.getContacts)==null?void 0:c[0])==null?void 0:a.Text},null,8,x)])])],64)}const I={components:{BlockBalanceAgreement:r},data(){return{contacts:[{link:"support@host-food.ru",name:"Техническая поддержка"},{link:"buh@host-food.ru",name:"Бухгалтерия"},{link:"pr@host-food.ru",name:"Реклама и маркетинг"},{link:"domains@host-food.ru",name:"По вопросам связанным с доменами"}]}},async setup(){const t=k(),n=f(()=>{var e;return(e=Object.keys(t==null?void 0:t.contacts))==null?void 0:e.map(s=>t==null?void 0:t.contacts[s])});return await t.fetchContacts(),{getContacts:n}}},N=h(I,[["render",y],["__scopeId","data-v-57c18d97"]]);export{N as default};

import{a7 as n,S as c,r,i,o as l,c as u,a as _,b as d,w as m,d as p}from"./index-90eb49f0.js";import{S as f}from"./SlotPageTitle-39519e24.js";const b={class:"container"},h={class:"section"},B={__name:"UnsubscribePage",setup(v){const o=n(),s=c("emitter"),e=r({ContactID:null,TypeID:null,Code:null});return i(()=>{Object.keys(e.value).forEach(t=>{var a;e.value[t]=(a=o.query)==null?void 0:a[t]}),s.emit("open-modal",{component:"UnsubscribeConfirmation",data:e.value})}),(t,a)=>(l(),u("div",b,[_("div",h,[d(f,null,{title:m(()=>[p(" Отключение уведомлений ")]),_:1})])]))}};export{B as default};

import{_ as i}from"./IconDots-24f2aa45.js";import{_ as c}from"./IconCard-b302365f.js";import{S as d}from"./StatusBadge-52b4bbab.js";import{j as l,o as a,c as o,a as e,t as s,F as m,x as v,m as u,k as p,b as h,w as f,d as S,n as b,_ as w}from"./index-642cdac5.js";const g={class:"server-item__wrapper"},k={class:"server-item__header"},x={class:"server-item__name"},y={class:"server-item__price"},C={class:"server-item__content"},I={class:"server-item__row"},N={class:"server-item__list"},B={class:"server-item__row"},D={class:"server-item__note"};function V(r,F,t,O,z,E){const _=l("router-link");return a(),o("div",{class:b(["server-item",{"server-item--special":t.data.special}])},[e("div",g,[e("div",k,[e("div",x,s(t.data.Name),1),e("div",y,s(t.data.CostMonth)+" ₽ в месяц",1)]),e("div",C,[e("div",I,[e("ul",N,[(a(!0),o(m,null,v(t.specifications,n=>(a(),o("li",null,s(n),1))),256))])]),t.data.note?u(r.$slots,"default",{key:0},()=>[e("div",B,[e("div",D,s(t.data.note),1)])],!0):p("",!0),h(_,{class:"btn btn--wide btn--border btn--wide",to:`/DSSchemes/${t.data.ID}`},{default:f(()=>[S(" Заказать")]),_:1},8,["to"])])])],2)}const j={components:{IconDots:i,IconCard:c,StatusBadge:d},props:{data:{type:Object,default:()=>{}},specifications:{type:Object,default:()=>{}}}},A=w(j,[["render",V],["__scopeId","data-v-811d1936"]]);export{A as S};
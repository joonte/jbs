import{q as G,r as p,a2 as h,a3 as D,j as U,o as g,c as y,b as v,w as _,a,t as f,k as w,F as P,y as b,_ as C,g as k,Z as H,af as J,p as x,l as L,e as O,h as A,n as I,a7 as K}from"./index-642cdac5.js";import{u as Q}from"./promoStore-ddf74633.js";import{s as E}from"./multiselect-bb2ec7ab.js";import{a as M,f as R}from"./useTimeFunction-8602dd60.js";import{E as S}from"./EmptyStateBlock-1861cff5.js";import{C as N,F}from"./bootstrap-vue-next.es-9ff54498.js";import{_ as W}from"./ButtonDefault-eb076df2.js";import{B as j}from"./BlockBalanceAgreement-ccba2ab2.js";import{_ as X}from"./ClausesKeeper-12bc5d50.js";import"./contracts-16ef7000.js";import"./IconArrow-c0196cbe.js";import"./IconClose-f1b85910.js";const B=G("bonuses",()=>{const t=p(null),o=p(null);async function m(l){await h.post(""+D.fetchBonuses,l).then(r=>{r!=null&&r.data&&(t.value=r.data)})}async function e(l){await h.post(""+D.fetchPromos,l).then(r=>{r!=null&&r.data&&(o.value=r.data)})}async function c(l){let r="ERROR",i=null;return await h.post(""+D.PromoCodesActivate,l).then(d=>{var u;((u=d==null?void 0:d.data)==null?void 0:u.Status)==="Ok"&&(r="SUCCESS")}),{result:r,error:i}}return{bonusesList:t,promosList:o,fetchBonuses:m,fetchPromos:e,PromoCodesActivate:c}}),Y=[{key:"CreateDate",label:"Дата создания",variant:"td-gray",sortable:!0},{key:"ExpirationDate",label:"Дата окончания",variant:"td-gray",sortable:!0},{key:"Display",label:"Тариф/Группа",variant:"td-blue"},{key:"DaysRemainded",label:"Дней осталось",sortable:!0},{key:"Discont",variant:"td-controls",label:"Скидка"},{key:"Comment",label:"Комментарий",variant:"td-gray"}],$=a("span",null,null,-1),ee=a("span",null,null,-1),te={key:0,class:"table-controls"},oe=a("span",{class:"text-success"},"First",-1),se=a("span",{class:"text-info"},"Last",-1),ne={class:"multiselect-multiple-label"},ae={class:"multiselect-option"};function re(t,o,m,e,c,l){const r=N,i=F,d=U("Multiselect"),u=S;return e.getBonuses?(g(),y(P,{key:0},[$,ee,v(r,{class:"basic-table",items:e.getBonuses,fields:e.bonusesTable,"show-empty":!0,"per-page":e.perPage,"current-page":e.currentPage,"empty-text":"Бонусов пока нет.","empty-filtered-text":"Бонусов не найдены.","sort-desc":e.sortDesc,"onUpdate:sortDesc":o[0]||(o[0]=s=>e.sortDesc=s),"sort-by":e.sortBy,"onUpdate:sortBy":o[1]||(o[1]=s=>e.sortBy=s),"sort-direction":e.sortDirection},{"cell(CreateDate)":_(s=>[a("span",null,f(e.formNormalDate(e.secondToDate(s==null?void 0:s.value))),1)]),"cell(ExpirationDate)":_(s=>[a("span",null,f(e.formNormalDate(e.secondToDate(s==null?void 0:s.value))),1)]),_:1},8,["items","fields","per-page","current-page","sort-desc","sort-by","sort-direction"]),e.getBonusesLength>0?(g(),y("div",te,[v(i,{modelValue:e.currentPage,"onUpdate:modelValue":o[2]||(o[2]=s=>e.currentPage=s),"total-rows":e.getBonusesLength,"per-page":e.perPage,"first-number":"","last-number":""},{"first-text":_(()=>[oe]),"last-text":_(()=>[se]),page:_(({page:s,active:n})=>[a("span",null,f((s-1)*e.perPage+1)+"-"+f(s*e.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),v(d,{class:"multiselect--white",modelValue:e.perPage,"onUpdate:modelValue":o[3]||(o[3]=s=>e.perPage=s),options:e.pageOptions,label:"name",openDirection:"top",onInput:o[4]||(o[4]=s=>e.currentPage=1)},{singlelabel:_(({value:s})=>[a("div",ne,[a("span",null,"Отображать "+f(s.value)+" строк",1)])]),option:_(({option:s})=>[a("div",ae,"Отображать "+f(s.name)+" строк",1)]),_:1},8,["modelValue","options"])])):w("",!0)],64)):(g(),b(u,{key:1,label:"Бонусов не найдено"}))}const le={components:{Multiselect:E},setup(){const t=B(),o=p("CreateDate"),m=p(!0),e=p("desc"),c=p(1),l=p(10),r=p([10,25,50,100]),i=k(()=>{var s;return(s=Object.keys(t==null?void 0:t.bonusesList))==null?void 0:s.map(n=>t==null?void 0:t.bonusesList[n])}),d=k(()=>{var s;return(s=Object.keys(t==null?void 0:t.bonusesList))==null?void 0:s.length});function u(){c.value=1}return{bonusesTable:Y,sortBy:o,sortDesc:m,sortDirection:e,currentPage:c,perPage:l,pageOptions:r,setPageToZero:u,getBonuses:i,getBonusesLength:d,secondToDate:M,formNormalDate:R}}},q=C(le,[["render",re]]),ce=[{key:"CreateDate",label:"Дата активации",variant:"td-gray",sortable:!0},{key:"ApplyTo",label:"Тариф/Группа",variant:"td-blue"},{key:"PromoCode",label:"Промокод"}],ie=t=>(x("data-v-7d214ff9"),t=t(),L(),t),de={class:"promo-field"},me=ie(()=>a("div",{class:"promo-field-text"},"Промокод",-1)),pe={class:"promo-field-input form-field"};function ue(t,o,m,e,c,l){const r=W;return g(),y("div",de,[me,a("div",pe,[H(a("input",{type:"text",name:"promo-code","onUpdate:modelValue":o[0]||(o[0]=i=>e.promoInput=i)},null,512),[[J,e.promoInput]])]),v(r,{"is-loading":e.isLoading,label:"Применить",onClick:o[1]||(o[1]=i=>e.activatePromo())},null,8,["is-loading"])])}const _e={props:{promoCode:{type:String,default:""}},emits:["update"],setup(t,{emit:o}){const m=B(),e=O(),c=p(!1),l=p("");function r(){l.value.length>0&&(c.value=!0,m.PromoCodesActivate({Code:l.value}).then(i=>{const{result:d}=i;d==="SUCCESS"&&o("update"),c.value=!1}))}return A(()=>{e.currentRoute.value.query.PromoCode?(l.value=e.currentRoute.value.query.PromoCode,r()):t.promoCode&&(l.value=t.promoCode),console.log(e)}),{isLoading:c,promoInput:l,activatePromo:r,router:e}}},Z=C(_e,[["render",ue],["__scopeId","data-v-7d214ff9"]]),V=t=>(x("data-v-5e97d52d"),t=t(),L(),t),ge={class:"promo-block block--wrapper"},fe=V(()=>a("div",{class:"promo-text"},"Активировать промокод",-1)),ve={key:0,class:"table-controls"},ye=V(()=>a("span",{class:"text-success"},"First",-1)),be=V(()=>a("span",{class:"text-info"},"Last",-1)),Pe={class:"multiselect-multiple-label"},ke={class:"multiselect-option"};function Ce(t,o,m,e,c,l){const r=Z,i=N,d=F,u=U("Multiselect"),s=S;return g(),y(P,null,[a("div",ge,[fe,v(r,{onUpdate:o[0]||(o[0]=n=>e.updatePromos()),promoCode:m.promoCode},null,8,["promoCode"])]),e.getPromos?(g(),y(P,{key:0},[v(i,{class:"basic-table",items:e.getPromos,fields:e.promosTable,"show-empty":!0,"per-page":e.perPage,"current-page":e.currentPage,"empty-text":"Промокодов пока нет.","empty-filtered-text":"Промокоды не найдены.","sort-desc":e.sortDesc,"onUpdate:sortDesc":o[1]||(o[1]=n=>e.sortDesc=n),"sort-by":e.sortBy,"onUpdate:sortBy":o[2]||(o[2]=n=>e.sortBy=n),"sort-direction":e.sortDirection},{"cell(CreateDate)":_(n=>[a("span",null,f(e.formNormalDate(e.secondToDate(n==null?void 0:n.value))),1)]),_:1},8,["items","fields","per-page","current-page","sort-desc","sort-by","sort-direction"]),e.getPromosLength>0?(g(),y("div",ve,[v(d,{modelValue:e.currentPage,"onUpdate:modelValue":o[3]||(o[3]=n=>e.currentPage=n),"total-rows":e.getPromosLength,"per-page":e.perPage,"first-number":"","last-number":""},{"first-text":_(()=>[ye]),"last-text":_(()=>[be]),page:_(({page:n,active:T})=>[a("span",null,f((n-1)*e.perPage+1)+"-"+f(n*e.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),v(u,{class:"multiselect--white",modelValue:e.perPage,"onUpdate:modelValue":o[4]||(o[4]=n=>e.perPage=n),options:e.pageOptions,label:"name",openDirection:"top",onInput:o[5]||(o[5]=n=>e.currentPage=1)},{singlelabel:_(({value:n})=>[a("div",Pe,[a("span",null,"Отображать "+f(n.value)+" строк",1)])]),option:_(({option:n})=>[a("div",ke,"Отображать "+f(n.name)+" строк",1)]),_:1},8,["modelValue","options"])])):w("",!0)],64)):(g(),b(s,{key:1,label:"Промокоды не найдены"}))],64)}const Be={components:{ActivatePromo:Z,Multiselect:E},props:{promoCode:{type:String,default:""}},setup(){const t=B(),o=p("CreateDate"),m=p(!0),e=p("desc"),c=p(1),l=p(10),r=p([10,25,50,100]),i=k(()=>{var n;return(n=Object.keys(t==null?void 0:t.promosList))==null?void 0:n.map(T=>t==null?void 0:t.promosList[T])}),d=k(()=>{var n;return(n=Object.keys(t==null?void 0:t.promosList))==null?void 0:n.length});function u(){c.value=1}function s(){t.fetchBonuses(),t.fetchPromos()}return{promosTable:ce,sortBy:o,sortDesc:m,sortDirection:e,currentPage:c,perPage:l,pageOptions:r,setPageToZero:u,getPromos:i,getPromosLength:d,secondToDate:M,formNormalDate:R,updatePromos:s}}},z=C(Be,[["render",Ce],["__scopeId","data-v-5e97d52d"]]),he=t=>(x("data-v-8e58387d"),t=t(),L(),t),De={class:"section"},xe={class:"container"},Le=he(()=>a("div",{class:"section-header"},[a("h1",{class:"section-title"},"Бонусы и промокоды")],-1)),Se={class:"section-header"},Ve={class:"section-nav"};function Te(t,o,m,e,c,l){const r=j,i=X,d=q,u=z,s=S;return g(),y(P,null,[v(r),a("div",De,[a("div",xe,[Le,v(i),a("div",Se,[a("div",Ve,[a("div",{class:I(["section-nav__item",{"section-link":!0,"section-link-active":e.section==="bonuses"}]),onClick:o[0]||(o[0]=n=>e.switchToSection("bonuses"))},"Бонусы",2),a("div",{class:I(["section-nav__item",{"section-link":!0,"section-link-active":e.section==="promos"}]),onClick:o[1]||(o[1]=n=>e.switchToSection("promos"))},"Промокоды",2)])]),e.section==="bonuses"?(g(),b(d,{key:0})):e.section==="promos"?(g(),b(u,{key:1,promoCode:e.promoCode},null,8,["promoCode"])):(g(),b(s,{key:2,label:"Секция не найдена"}))])])],64)}const Ie={components:{BonusesBlock:q,PromosBlock:z,BlockBalanceAgreement:j},async setup(){const t=B(),o=Q(),m=p("bonuses"),e=O(),c=K(),l=o.promoCode;function r(i){e.replace(`/Bonuses?section=${i}`),m.value=i}return A(()=>{var i,d;l&&t.PromoCodesActivate({Code:l}).then(u=>{const{result:s}=u;s==="SUCCESS"&&emit("update"),e.replace("/Bonuses?section=promos"),m.value="promos"}),(i=c==null?void 0:c.query)!=null&&i.section?m.value=(d=c==null?void 0:c.query)==null?void 0:d.section:e.replace("/Bonuses?section=bonuses")}),await t.fetchBonuses(),await t.fetchPromos(),{section:m,switchToSection:r,promoCode:l}}},ze=C(Ie,[["render",Te],["__scopeId","data-v-8e58387d"]]);export{ze as default};

import{q as G,r as p,a2 as h,a3 as x,j as U,o as f,c as b,b as v,w as _,a,t as g,k as w,F as P,y as k,_ as B,g as C,Z as H,af as J,p as E,l as I,e as O,i as L,O as Q,n as T,a7 as W}from"./index-b5b5ce3d.js";import{u as X}from"./promoStore-d93e4160.js";import{s as A}from"./multiselect-4f7d14de.js";import{a as N,f as M}from"./useTimeFunction-8602dd60.js";import{E as S}from"./EmptyStateBlock-cd0c2748.js";import{C as R,F}from"./bootstrap-vue-next.es-37aa63ea.js";import{_ as Y}from"./ButtonDefault-0006f72a.js";import{B as j}from"./BlockBalanceAgreement-a016b94f.js";import{_ as $}from"./ClausesKeeper-3a1c11ba.js";import"./contracts-74f05bb7.js";import"./IconArrow-fddabae2.js";import"./IconClose-cd6d3917.js";const D=G("bonuses",()=>{const o=p(null),t=p(null);async function m(l){await h.post(""+x.fetchBonuses,l).then(r=>{r!=null&&r.data&&(o.value=r.data)})}async function e(l){await h.post(""+x.fetchPromos,l).then(r=>{r!=null&&r.data&&(t.value=r.data)})}async function c(l){let r="ERROR",d=null;return await h.post(""+x.PromoCodesActivate,l).then(i=>{var u;((u=i==null?void 0:i.data)==null?void 0:u.Status)==="Ok"&&(r="SUCCESS")}),{result:r,error:d}}return{bonusesList:o,promosList:t,fetchBonuses:m,fetchPromos:e,PromoCodesActivate:c}}),ee=[{key:"CreateDate",label:"Дата создания",variant:"td-gray",sortable:!0},{key:"ExpirationDate",label:"Дата окончания",variant:"td-gray",sortable:!0},{key:"Display",label:"Тариф/Группа",variant:"td-blue"},{key:"DaysRemainded",label:"Дней осталось",sortable:!0},{key:"Discont",variant:"td-controls",label:"Скидка"},{key:"Comment",label:"Комментарий",variant:"td-gray"}],te=a("span",null,null,-1),oe=a("span",null,null,-1),se={key:0,class:"table-controls"},ne=a("span",{class:"text-success"},"First",-1),ae=a("span",{class:"text-info"},"Last",-1),re={class:"multiselect-multiple-label"},le={class:"multiselect-option"};function ce(o,t,m,e,c,l){const r=R,d=F,i=U("Multiselect"),u=S;return e.getBonuses?(f(),b(P,{key:0},[te,oe,v(r,{class:"basic-table",items:e.getBonuses,fields:e.bonusesTable,"show-empty":!0,"per-page":e.perPage,"current-page":e.currentPage,"empty-text":"Бонусов пока нет.","empty-filtered-text":"Бонусов не найдены.","sort-desc":e.sortDesc,"onUpdate:sortDesc":t[0]||(t[0]=s=>e.sortDesc=s),"sort-by":e.sortBy,"onUpdate:sortBy":t[1]||(t[1]=s=>e.sortBy=s),"sort-direction":e.sortDirection},{"cell(CreateDate)":_(s=>[a("span",null,g(e.formNormalDate(e.secondToDate(s==null?void 0:s.value))),1)]),"cell(ExpirationDate)":_(s=>[a("span",null,g(e.formNormalDate(e.secondToDate(s==null?void 0:s.value))),1)]),_:1},8,["items","fields","per-page","current-page","sort-desc","sort-by","sort-direction"]),e.getBonusesLength>0?(f(),b("div",se,[v(d,{modelValue:e.currentPage,"onUpdate:modelValue":t[2]||(t[2]=s=>e.currentPage=s),"total-rows":e.getBonusesLength,"per-page":e.perPage,"first-number":"","last-number":""},{"first-text":_(()=>[ne]),"last-text":_(()=>[ae]),page:_(({page:s,active:n})=>[a("span",null,g((s-1)*e.perPage+1)+"-"+g(s*e.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),v(i,{class:"multiselect--white",modelValue:e.perPage,"onUpdate:modelValue":t[3]||(t[3]=s=>e.perPage=s),options:e.pageOptions,label:"name",openDirection:"top",onInput:t[4]||(t[4]=s=>e.currentPage=1)},{singlelabel:_(({value:s})=>[a("div",re,[a("span",null,"Отображать "+g(s.value)+" строк",1)])]),option:_(({option:s})=>[a("div",le,"Отображать "+g(s.name)+" строк",1)]),_:1},8,["modelValue","options"])])):w("",!0)],64)):(f(),k(u,{key:1,label:"Бонусов не найдено"}))}const ie={components:{Multiselect:A},setup(){const o=D(),t=p("CreateDate"),m=p(!0),e=p("desc"),c=p(1),l=p(10),r=p([10,25,50,100]),d=C(()=>{var s;return(s=Object.keys(o==null?void 0:o.bonusesList))==null?void 0:s.map(n=>o==null?void 0:o.bonusesList[n])}),i=C(()=>{var s;return(s=Object.keys(o==null?void 0:o.bonusesList))==null?void 0:s.length});function u(){c.value=1}return{bonusesTable:ee,sortBy:t,sortDesc:m,sortDirection:e,currentPage:c,perPage:l,pageOptions:r,setPageToZero:u,getBonuses:d,getBonusesLength:i,secondToDate:N,formNormalDate:M}}},q=B(ie,[["render",ce]]),de=[{key:"CreateDate",label:"Дата активации",variant:"td-gray",sortable:!0},{key:"ApplyTo",label:"Тариф/Группа",variant:"td-blue"},{key:"PromoCode",label:"Промокод"}],me=o=>(E("data-v-f522f070"),o=o(),I(),o),pe={class:"promo-field"},ue=me(()=>a("div",{class:"promo-field-text"},"Промокод",-1)),_e={class:"promo-field-input form-field"};function fe(o,t,m,e,c,l){const r=Y;return f(),b("div",pe,[ue,a("div",_e,[H(a("input",{type:"text",name:"promo-code","onUpdate:modelValue":t[0]||(t[0]=d=>e.promoInput=d)},null,512),[[J,e.promoInput]])]),v(r,{"is-loading":e.isLoading,label:"Применить",onClick:t[1]||(t[1]=d=>e.activatePromo())},null,8,["is-loading"])])}const ge={props:{promoCode:{type:String,default:""}},emits:["update"],setup(o,{emit:t}){const m=D(),e=O(),c=p(!1),l=p("");function r(){l.value.length>0&&(c.value=!0,m.PromoCodesActivate({Code:l.value}).then(d=>{const{result:i}=d;i==="SUCCESS"&&t("update"),c.value=!1}))}return L(()=>{e.currentRoute.value.query.PromoCode?(l.value=e.currentRoute.value.query.PromoCode,r()):o.promoCode&&(l.value=o.promoCode)}),{isLoading:c,promoInput:l,activatePromo:r,router:e}}},Z=B(ge,[["render",fe],["__scopeId","data-v-f522f070"]]),V=o=>(E("data-v-5617c0ed"),o=o(),I(),o),ve={class:"promo-block block--wrapper"},ye=V(()=>a("div",{class:"promo-text"},"Активировать промокод",-1)),be={key:0,class:"table-controls"},ke=V(()=>a("span",{class:"text-success"},"First",-1)),Pe=V(()=>a("span",{class:"text-info"},"Last",-1)),Ce={class:"multiselect-multiple-label"},Be={class:"multiselect-option"};function De(o,t,m,e,c,l){const r=Z,d=R,i=F,u=U("Multiselect"),s=S;return f(),b(P,null,[a("div",ve,[ye,v(r,{onUpdate:t[0]||(t[0]=n=>e.updatePromos()),promoCode:m.promoCode},null,8,["promoCode"])]),e.getPromos?(f(),b(P,{key:0},[v(d,{class:"basic-table",items:e.getPromos,fields:e.promosTable,"show-empty":!0,"per-page":e.perPage,"current-page":e.currentPage,"empty-text":"Промокодов пока нет.","empty-filtered-text":"Промокоды не найдены.","sort-desc":e.sortDesc,"onUpdate:sortDesc":t[1]||(t[1]=n=>e.sortDesc=n),"sort-by":e.sortBy,"onUpdate:sortBy":t[2]||(t[2]=n=>e.sortBy=n),"sort-direction":e.sortDirection},{"cell(CreateDate)":_(n=>[a("span",null,g(e.formNormalDate(e.secondToDate(n==null?void 0:n.value))),1)]),_:1},8,["items","fields","per-page","current-page","sort-desc","sort-by","sort-direction"]),e.getPromosLength>0?(f(),b("div",be,[v(i,{modelValue:e.currentPage,"onUpdate:modelValue":t[3]||(t[3]=n=>e.currentPage=n),"total-rows":e.getPromosLength,"per-page":e.perPage,"first-number":"","last-number":""},{"first-text":_(()=>[ke]),"last-text":_(()=>[Pe]),page:_(({page:n,active:y})=>[a("span",null,g((n-1)*e.perPage+1)+"-"+g(n*e.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),v(u,{class:"multiselect--white",modelValue:e.perPage,"onUpdate:modelValue":t[4]||(t[4]=n=>e.perPage=n),options:e.pageOptions,label:"name",openDirection:"top",onInput:t[5]||(t[5]=n=>e.currentPage=1)},{singlelabel:_(({value:n})=>[a("div",Ce,[a("span",null,"Отображать "+g(n.value)+" строк",1)])]),option:_(({option:n})=>[a("div",Be,"Отображать "+g(n.name)+" строк",1)]),_:1},8,["modelValue","options"])])):w("",!0)],64)):(f(),k(s,{key:1,label:"Промокоды не найдены"}))],64)}const he={components:{ActivatePromo:Z,Multiselect:A},props:{promoCode:{type:String,default:""}},setup(){const o=D(),t=p("CreateDate"),m=p(!0),e=p("desc"),c=p(1),l=p(10),r=p([10,25,50,100]),d=C(()=>{var y;return(y=Object.keys(o==null?void 0:o.promosList))==null?void 0:y.map(z=>o==null?void 0:o.promosList[z])}),i=C(()=>{var y;return(y=Object.keys(o==null?void 0:o.promosList))==null?void 0:y.length});function u(){c.value=1}function s(){o.fetchBonuses(),o.fetchPromos()}const n=y=>{y.key==="Enter"&&y.ctrlKey&&s()};return L(()=>{document.addEventListener("keyup",n)}),Q(()=>{document.removeEventListener("keyup",n)}),{promosTable:de,sortBy:t,sortDesc:m,sortDirection:e,currentPage:c,perPage:l,pageOptions:r,setPageToZero:u,getPromos:d,getPromosLength:i,secondToDate:N,formNormalDate:M,updatePromos:s}}},K=B(he,[["render",De],["__scopeId","data-v-5617c0ed"]]),xe={class:"section"},Le={class:"container"},Se={class:"section-header"},Ve={class:"section-header"},Te={class:"section-nav"};function Ue(o,t,m,e,c,l){const r=j,d=$,i=q,u=K,s=S;return f(),b(P,null,[v(r),a("div",xe,[a("div",Le,[a("div",Se,[a("h1",{class:"section-title",onClick:t[0]||(t[0]=n=>e.reloadNow())},"Бонусы и промокоды")]),v(d),a("div",Ve,[a("div",Te,[a("div",{class:T(["section-nav__item",{"section-link":!0,"section-link-active":e.section==="bonuses"}]),onClick:t[1]||(t[1]=n=>e.switchToSection("bonuses"))},"Бонусы",2),a("div",{class:T(["section-nav__item",{"section-link":!0,"section-link-active":e.section==="promos"}]),onClick:t[2]||(t[2]=n=>e.switchToSection("promos"))},"Промокоды",2)])]),e.section==="bonuses"?(f(),k(i,{key:0})):e.section==="promos"?(f(),k(u,{key:1,promoCode:e.promoCode},null,8,["promoCode"])):(f(),k(s,{key:2,label:"Секция не найдена"}))])])],64)}const we={components:{BonusesBlock:q,PromosBlock:K,BlockBalanceAgreement:j},async setup(){const o=D(),t=X(),m=p("bonuses"),e=O(),c=W(),l=t.promoCode;function r(){e.go(0)}function d(i){e.replace(`/Bonuses?section=${i}`),m.value=i}return L(()=>{var i,u;l&&o.PromoCodesActivate({Code:l}).then(s=>{const{result:n}=s;n==="SUCCESS"&&emit("update"),e.replace("/Bonuses?section=promos"),m.value="promos"}),(i=c==null?void 0:c.query)!=null&&i.section?m.value=(u=c==null?void 0:c.query)==null?void 0:u.section:e.replace("/Bonuses?section=bonuses")}),await o.fetchBonuses(),await o.fetchPromos(),{section:m,switchToSection:d,promoCode:l,reloadNow:r}}},ze=B(we,[["render",Ue],["__scopeId","data-v-01abdf22"]]);export{ze as default};

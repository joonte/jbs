import{a1 as Z,r as i,a2 as h,a3 as x,j as w,o as g,c as b,b as f,w as _,a,t as v,k as S,F as k,x as y,_ as B,g as P,Y as z,af as Y,p as C,l as L,n as U,e as G,a7 as H,i as J}from"./index-eaeb1281.js";import{s as O}from"./multiselect-92d0918e.js";import{a as A,f as E}from"./useTimeFunction-8602dd60.js";import{E as V}from"./EmptyStateBlock-5faddaec.js";import{C as M,F as N}from"./bootstrap-vue-next.es-f4c478af.js";import{_ as K}from"./ButtonDefault-598fef28.js";import{B as F}from"./BlockBalanceAgreement-11afb494.js";import{_ as Q}from"./ClausesKeeper-a152fc61.js";import"./contracts-ac470ffa.js";import"./IconArrow-ce1bc7b6.js";import"./IconClose-e8029cfe.js";const D=Z("bonuses",()=>{const o=i(null),e=i(null);async function m(c){await h.post(""+x.fetchBonuses,c).then(l=>{l!=null&&l.data&&(o.value=l.data)})}async function t(c){await h.post(""+x.fetchPromos,c).then(l=>{l!=null&&l.data&&(e.value=l.data)})}async function r(c){let l="ERROR",d=null;return await h.post(""+x.PromoCodesActivate,c).then(p=>{var u;((u=p==null?void 0:p.data)==null?void 0:u.Status)==="Ok"&&(l="SUCCESS")}),{result:l,error:d}}return{bonusesList:o,promosList:e,fetchBonuses:m,fetchPromos:t,PromoCodesActivate:r}}),W=[{key:"CreateDate",label:"Дата создания",variant:"td-gray",sortable:!0},{key:"ExpirationDate",label:"Дата окончания",variant:"td-gray",sortable:!0},{key:"Display",label:"Тариф/Группа",variant:"td-blue"},{key:"DaysRemainded",label:"Дней осталось",sortable:!0},{key:"Discont",variant:"td-controls",label:"Скидка"},{key:"Comment",label:"Комментарий",variant:"td-gray"}],X=a("span",null,null,-1),$=a("span",null,null,-1),tt={key:0,class:"table-controls"},et=a("span",{class:"text-success"},"First",-1),ot=a("span",{class:"text-info"},"Last",-1),st={class:"multiselect-multiple-label"},nt={class:"multiselect-option"};function at(o,e,m,t,r,c){const l=M,d=N,p=w("Multiselect"),u=V;return t.getBonuses?(g(),b(k,{key:0},[X,$,f(l,{class:"basic-table",items:t.getBonuses,fields:t.bonusesTable,"show-empty":!0,"per-page":t.perPage,"current-page":t.currentPage,"empty-text":"Бонусов пока нет.","empty-filtered-text":"Бонусов не найдены.","sort-desc":t.sortDesc,"onUpdate:sortDesc":e[0]||(e[0]=s=>t.sortDesc=s),"sort-by":t.sortBy,"onUpdate:sortBy":e[1]||(e[1]=s=>t.sortBy=s),"sort-direction":t.sortDirection},{"cell(CreateDate)":_(s=>[a("span",null,v(t.formNormalDate(t.secondToDate(s==null?void 0:s.value))),1)]),"cell(ExpirationDate)":_(s=>[a("span",null,v(t.formNormalDate(t.secondToDate(s==null?void 0:s.value))),1)]),_:1},8,["items","fields","per-page","current-page","sort-desc","sort-by","sort-direction"]),t.getBonusesLength>0?(g(),b("div",tt,[f(d,{modelValue:t.currentPage,"onUpdate:modelValue":e[2]||(e[2]=s=>t.currentPage=s),"total-rows":t.getBonusesLength,"per-page":t.perPage,"first-number":"","last-number":""},{"first-text":_(()=>[et]),"last-text":_(()=>[ot]),page:_(({page:s,active:n})=>[a("span",null,v((s-1)*t.perPage+1)+"-"+v(s*t.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),f(p,{class:"multiselect--white",modelValue:t.perPage,"onUpdate:modelValue":e[3]||(e[3]=s=>t.perPage=s),options:t.pageOptions,label:"name",openDirection:"top",onInput:e[4]||(e[4]=s=>t.currentPage=1)},{singlelabel:_(({value:s})=>[a("div",st,[a("span",null,"Отображать "+v(s.value)+" строк",1)])]),option:_(({option:s})=>[a("div",nt,"Отображать "+v(s.name)+" строк",1)]),_:1},8,["modelValue","options"])])):S("",!0)],64)):(g(),y(u,{key:1,label:"Бонусов не найдено"}))}const lt={components:{Multiselect:O},setup(){const o=D(),e=i("CreateDate"),m=i(!0),t=i("desc"),r=i(1),c=i(10),l=i([10,25,50,100]),d=P(()=>{var s;return(s=Object.keys(o==null?void 0:o.bonusesList))==null?void 0:s.map(n=>o==null?void 0:o.bonusesList[n])}),p=P(()=>{var s;return(s=Object.keys(o==null?void 0:o.bonusesList))==null?void 0:s.length});function u(){r.value=1}return{bonusesTable:W,sortBy:e,sortDesc:m,sortDirection:t,currentPage:r,perPage:c,pageOptions:l,setPageToZero:u,getBonuses:d,getBonusesLength:p,secondToDate:A,formNormalDate:E}}},R=B(lt,[["render",at]]),ct=[{key:"CreateDate",label:"Дата активации",variant:"td-gray",sortable:!0},{key:"ApplyTo",label:"Тариф/Группа",variant:"td-blue"},{key:"PromoCode",label:"Промокод"}],rt=o=>(C("data-v-b9a5421c"),o=o(),L(),o),it={class:"promo-field"},dt=rt(()=>a("div",{class:"promo-field-text"},"Промокод",-1)),mt={class:"promo-field-input form-field"};function pt(o,e,m,t,r,c){const l=K;return g(),b("div",it,[dt,a("div",mt,[z(a("input",{type:"text",name:"promo-code","onUpdate:modelValue":e[0]||(e[0]=d=>t.promoInput=d)},null,512),[[Y,t.promoInput]])]),f(l,{"is-loading":t.isLoading,label:"Применить",onClick:e[1]||(e[1]=d=>t.activatePromo())},null,8,["is-loading"])])}const _t={emits:["update"],setup(o,{emit:e}){const m=D(),t=i(!1),r=i("");function c(){r.value.length>0&&(t.value=!0,m.PromoCodesActivate({Code:r.value}).then(l=>{const{result:d}=l;d==="SUCCESS"&&e("update"),t.value=!1}))}return{isLoading:t,promoInput:r,activatePromo:c}}},j=B(_t,[["render",pt],["__scopeId","data-v-b9a5421c"]]),T=o=>(C("data-v-ed0ee183"),o=o(),L(),o),ut={class:"promo-block block--wrapper"},gt=T(()=>a("div",{class:"promo-text"},"Активировать промокод",-1)),vt={key:0,class:"table-controls"},ft=T(()=>a("span",{class:"text-success"},"First",-1)),bt=T(()=>a("span",{class:"text-info"},"Last",-1)),yt={class:"multiselect-multiple-label"},kt={class:"multiselect-option"};function Pt(o,e,m,t,r,c){const l=j,d=M,p=N,u=w("Multiselect"),s=V;return g(),b(k,null,[a("div",ut,[gt,f(l,{onUpdate:e[0]||(e[0]=n=>t.updatePromos())})]),t.getPromos?(g(),b(k,{key:0},[f(d,{class:"basic-table",items:t.getPromos,fields:t.promosTable,"show-empty":!0,"per-page":t.perPage,"current-page":t.currentPage,"empty-text":"Промокодов пока нет.","empty-filtered-text":"Промокоды не найдены.","sort-desc":t.sortDesc,"onUpdate:sortDesc":e[1]||(e[1]=n=>t.sortDesc=n),"sort-by":t.sortBy,"onUpdate:sortBy":e[2]||(e[2]=n=>t.sortBy=n),"sort-direction":t.sortDirection},{"cell(CreateDate)":_(n=>[a("span",null,v(t.formNormalDate(t.secondToDate(n==null?void 0:n.value))),1)]),_:1},8,["items","fields","per-page","current-page","sort-desc","sort-by","sort-direction"]),t.getPromosLength>0?(g(),b("div",vt,[f(p,{modelValue:t.currentPage,"onUpdate:modelValue":e[3]||(e[3]=n=>t.currentPage=n),"total-rows":t.getPromosLength,"per-page":t.perPage,"first-number":"","last-number":""},{"first-text":_(()=>[ft]),"last-text":_(()=>[bt]),page:_(({page:n,active:I})=>[a("span",null,v((n-1)*t.perPage+1)+"-"+v(n*t.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),f(u,{class:"multiselect--white",modelValue:t.perPage,"onUpdate:modelValue":e[4]||(e[4]=n=>t.perPage=n),options:t.pageOptions,label:"name",openDirection:"top",onInput:e[5]||(e[5]=n=>t.currentPage=1)},{singlelabel:_(({value:n})=>[a("div",yt,[a("span",null,"Отображать "+v(n.value)+" строк",1)])]),option:_(({option:n})=>[a("div",kt,"Отображать "+v(n.name)+" строк",1)]),_:1},8,["modelValue","options"])])):S("",!0)],64)):(g(),y(s,{key:1,label:"Промокоды не найдены"}))],64)}const Bt={components:{ActivatePromo:j,Multiselect:O},setup(){const o=D(),e=i("CreateDate"),m=i(!0),t=i("desc"),r=i(1),c=i(10),l=i([10,25,50,100]),d=P(()=>{var n;return(n=Object.keys(o==null?void 0:o.promosList))==null?void 0:n.map(I=>o==null?void 0:o.promosList[I])}),p=P(()=>{var n;return(n=Object.keys(o==null?void 0:o.promosList))==null?void 0:n.length});function u(){r.value=1}function s(){o.fetchBonuses(),o.fetchPromos()}return{promosTable:ct,sortBy:e,sortDesc:m,sortDirection:t,currentPage:r,perPage:c,pageOptions:l,setPageToZero:u,getPromos:d,getPromosLength:p,secondToDate:A,formNormalDate:E,updatePromos:s}}},q=B(Bt,[["render",Pt],["__scopeId","data-v-ed0ee183"]]),Dt=o=>(C("data-v-478df684"),o=o(),L(),o),ht={class:"section"},xt={class:"container"},Ct=Dt(()=>a("div",{class:"section-header"},[a("h1",{class:"section-title"},"Бонусы и промокоды")],-1)),Lt={class:"section-header"},Vt={class:"section-nav"};function Tt(o,e,m,t,r,c){const l=F,d=Q,p=R,u=q,s=V;return g(),b(k,null,[f(l),a("div",ht,[a("div",xt,[Ct,f(d),a("div",Lt,[a("div",Vt,[a("div",{class:U(["section-nav__item",{"section-link":!0,"section-link-active":t.section==="bonuses"}]),onClick:e[0]||(e[0]=n=>t.switchToSection("bonuses"))},"Бонусы",2),a("div",{class:U(["section-nav__item",{"section-link":!0,"section-link-active":t.section==="promos"}]),onClick:e[1]||(e[1]=n=>t.switchToSection("promos"))},"Промокоды",2)])]),t.section==="bonuses"?(g(),y(p,{key:0})):t.section==="promos"?(g(),y(u,{key:1})):(g(),y(s,{key:2,label:"Секция не найдена"}))])])],64)}const It={components:{BonusesBlock:R,PromosBlock:q,BlockBalanceAgreement:F},async setup(){const o=D(),e=i("bonuses"),m=G(),t=H();function r(c){m.replace(`/Bonuses?section=${c}`),e.value=c}return J(()=>{var c,l;(c=t==null?void 0:t.query)!=null&&c.section?e.value=(l=t==null?void 0:t.query)==null?void 0:l.section:m.replace("/Bonuses?section=bonuses")}),await o.fetchBonuses(),await o.fetchPromos(),{section:e,switchToSection:r}}},qt=B(It,[["render",Tt],["__scopeId","data-v-478df684"]]);export{qt as default};

import{j as U,o as y,c as L,b as u,a as o,w as r,t as l,F as w,n as A,k as S,x as E,p as M,l as O,_ as T,r as d,g}from"./index-4434f005.js";import{u as j}from"./postings-9b7fbb4e.js";import{u as Z}from"./contracts-d017d511.js";import{s as z}from"./multiselect-eb561fe9.js";import{a as q,f as G}from"./useTimeFunction-8602dd60.js";import{B as F}from"./BlockBalanceAgreement-4e844242.js";import{E as H}from"./EmptyStateBlock-927727c9.js";import{C as J,F as K}from"./bootstrap-vue-next.es-5d8af788.js";import{_ as Q}from"./FormInputSearch-29c276f6.js";import{_ as R}from"./ClausesKeeper-68df7f35.js";import"./ButtonDefault-f91c301e.js";import"./IconArrow-6847c901.js";import"./IconPlus-6c2cbbce.js";import"./IconSearch-d168a9f9.js";import"./IconClose-0cfbf563.js";const W=[{key:"CreateDate",label:"Дата Создания",variant:"td-gray td-order",sortable:!0},{key:"ContractID",label:"Договор",variant:"td-blue"},{key:"ServiceID",label:"Операция"},{key:"Comment",label:"Примечание"},{key:"Before",variant:"td-gray td-order",label:"До"},{key:"After",variant:"td-gray td-order",label:"После"},{key:"Change",variant:"td-gray td-order",label:"Сумма",sortable:!0}],i=m=>(M("data-v-3d824d06"),m=m(),O(),m),X={class:"section"},Y={class:"container"},$=i(()=>o("div",{class:"section-header"},[o("h1",{class:"section-title"},"История операций")],-1)),ee={class:"list-form"},te={class:"list-form_col list-form_col--sm"},se={class:"list-form_select"},oe={class:"multiselect-option"},ae=i(()=>o("span",{class:"td-mobile"},"Дата Создания",-1)),ne={class:"d-block"},le=i(()=>o("span",{class:"td-mobile"},"Договор",-1)),re=i(()=>o("span",{class:"td-mobile"},"До",-1)),ce={class:"d-block"},ie=i(()=>o("span",{class:"td-mobile"},"После",-1)),de={class:"d-block"},me=i(()=>o("span",{class:"td-mobile"},"Операция",-1)),_e=i(()=>o("span",{class:"td-mobile"},"Сумма",-1)),ue={key:0,class:"table-controls"},pe=i(()=>o("span",{class:"text-success"},"First",-1)),ge=i(()=>o("span",{class:"text-info"},"Last",-1)),ve={class:"multiselect-multiple-label"},fe={class:"multiselect-option"};function be(m,s,v,t,D,V){var C,h;const k=F,P=R,f=U("Multiselect"),B=Q,p=J,I=K,b=H;return y(),L(w,null,[u(k),o("div",X,[o("div",Y,[$,u(P),o("div",ee,[o("div",te,[o("div",se,[u(f,{modelValue:t.select,"onUpdate:modelValue":s[0]||(s[0]=e=>t.select=e),options:t.getUsers,disabled:((C=t.getUsers)==null?void 0:C.length)<=1,label:"name"},{option:r(({option:e})=>[o("div",oe,"# "+l(e.value.padStart(5,"0"))+" / "+l(e.name),1)]),_:1},8,["modelValue","options","disabled"])])]),u(B,{modelValue:t.search,"onUpdate:modelValue":s[1]||(s[1]=e=>t.search=e)},null,8,["modelValue"])]),t.getPostings&&((h=t.getPostings)==null?void 0:h.length)>0?(y(),L(w,{key:0},[u(p,{class:"basic-table",items:t.getPostings.filter(e=>e.ContractID.includes(t.select!=="*"?t.select:"")),fields:t.postingsFields,"show-empty":!0,filter:t.search,"per-page":t.perPage,"current-page":t.currentPage,filterable:["Customer","Comment"],"sort-by":t.sortBy,"onUpdate:sortBy":s[2]||(s[2]=e=>t.sortBy=e),"sort-desc":t.sortDesc,"onUpdate:sortDesc":s[3]||(s[3]=e=>t.sortDesc=e),"sort-direction":t.sortDirection,"sort-compare-options":{numeric:!0,sensitivity:"base"},"empty-text":"Операций пока нет.","empty-filtered-text":"Операции не найдены.",onFiltered:t.setPageToZero},{"cell(CreateDate)":r(e=>{var c;return[ae,o("span",ne,l(t.formNormalDate(t.secondToDate((c=e.item)==null?void 0:c.CreateDate))),1)]}),"cell(ContractID)":r(e=>{var c,a;return[le,o("span",null,l((c=e.item)==null?void 0:c.ContractID)+" - "+l((a=e.item)==null?void 0:a.Customer),1)]}),"cell(Before)":r(e=>[re,o("span",ce,l(e.value),1)]),"cell(After)":r(e=>[ie,o("span",de,l(e.value),1)]),"cell(ServiceID)":r(e=>{var c,a,n;return[me,o("span",null,l((n=(a=t.getServices)==null?void 0:a[(c=e.item)==null?void 0:c.ServiceID])==null?void 0:n.Name),1)]}),"cell(Change)":r(e=>[_e,o("span",{class:A(["d-block",{positive:e.value>0,negative:e.value<0}])},l(e.value>0?`+${e.value}`:e.value)+" ₽",3)]),_:1},8,["items","fields","filter","per-page","current-page","sort-by","sort-desc","sort-direction","onFiltered"]),t.filteredContractsLength>0?(y(),L("div",ue,[u(I,{modelValue:t.currentPage,"onUpdate:modelValue":s[4]||(s[4]=e=>t.currentPage=e),"total-rows":t.filteredContractsLength,"per-page":t.perPage,"first-number":"","last-number":""},{"first-text":r(()=>[pe]),"last-text":r(()=>[ge]),page:r(({page:e,active:c})=>[o("span",null,l((e-1)*t.perPage+1)+"-"+l(e*t.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),u(f,{class:"multiselect--white",modelValue:t.perPage,"onUpdate:modelValue":s[5]||(s[5]=e=>t.perPage=e),options:t.pageOptions,label:"name",openDirection:"top",onInput:s[6]||(s[6]=e=>t.currentPage=1)},{singlelabel:r(({value:e})=>[o("div",ve,[o("span",null,"Отображать "+l(e.value)+" строк",1)])]),option:r(({option:e})=>[o("div",fe,"Отображать "+l(e.name)+" строк",1)]),_:1},8,["modelValue","options"])])):S("",!0)],64)):(y(),E(b,{key:1,label:"Ваша история операций пуста"}))])])],64)}const Ce={components:{Multiselect:z,BlockBalanceAgreement:F},async setup(){const m=Z(),s=j(),v=d(""),t=d("*"),D=d(1),V=d(10),k=d([10,25,50,100]),P=d("CreateDate"),f=d(!0),B=d("desc"),p=g(()=>Object.keys(s==null?void 0:s.postingsList).map(a=>{var n,_,x,N;return{...s==null?void 0:s.postingsList[a],Customer:(_=(n=b.value)==null?void 0:n[s==null?void 0:s.postingsList[a].ContractID])==null?void 0:_.Customer,Change:e((x=s==null?void 0:s.postingsList[a])==null?void 0:x.Before,(N=s==null?void 0:s.postingsList[a])==null?void 0:N.After)}})),I=g(()=>s==null?void 0:s.servicesList),b=g(()=>m.contractsList),C=g(()=>{let a=[];return p.value.map(n=>{if(!a.find(_=>_===n.Customer))return a.push(n.Customer),{value:n.ContractID,name:n.Customer}}).filter(Boolean).concat([{value:"*",name:"Все договора"}]).reverse()}),h=g(()=>p.value.filter(a=>{var n,_;return a.Comment.toLowerCase().includes((n=v.value)==null?void 0:n.toLowerCase())||a.Customer.toLowerCase().includes((_=v.value)==null?void 0:_.toLowerCase())}).filter(a=>a.ContractID.includes(t.value!=="*"?t.value:"")).length);function e(a,n){return n-a>0,Number(Math.round((n-a+Number.EPSILON)*100)/100)}function c(){D.value=1}return await m.fetchContracts(),await s.fetchPostings(),await s.fetchServices(),{search:v,select:t,getContracts:b,getUsers:C,getPostings:p,getServices:I,currentPage:D,perPage:V,sortBy:P,sortDesc:f,sortDirection:B,pageOptions:k,filteredContractsLength:h,postingsFields:W,setPageToZero:c,secondToDate:q,formNormalDate:G,calculateChange:e}}},Se=T(Ce,[["render",be],["__scopeId","data-v-3d824d06"]]);export{Se as default};

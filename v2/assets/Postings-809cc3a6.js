import{j as S,o as y,c as w,b as p,a as o,w as r,t as n,F,n as A,k as M,y as E,p as O,l as T,_ as j,a7 as R,r as m,h as Z,g as b}from"./index-642cdac5.js";import{u as q}from"./postings-500009e8.js";import{u as z}from"./contracts-16ef7000.js";import{s as G}from"./multiselect-bb2ec7ab.js";import{a as H,f as J}from"./useTimeFunction-8602dd60.js";import{B as U}from"./BlockBalanceAgreement-ccba2ab2.js";import{E as K}from"./EmptyStateBlock-1861cff5.js";import{C as Q,F as W}from"./bootstrap-vue-next.es-9ff54498.js";import{_ as X}from"./FormInputSearch-7a23b402.js";import{_ as Y}from"./ClausesKeeper-12bc5d50.js";import"./ButtonDefault-eb076df2.js";import"./IconArrow-c0196cbe.js";import"./IconPlus-08c75518.js";import"./IconSearch-f354c728.js";import"./IconClose-f1b85910.js";const $=[{key:"CreateDate",label:"Дата Создания",variant:"td-gray td-order",sortable:!0},{key:"ContractID",label:"Договор",variant:"td-blue"},{key:"ServiceID",label:"Операция"},{key:"Comment",label:"Примечание"},{key:"Before",variant:"td-gray td-order",label:"До"},{key:"After",variant:"td-gray td-order",label:"После"},{key:"Change",variant:"td-gray td-order",label:"Сумма",sortable:!0}],i=d=>(O("data-v-91c3162b"),d=d(),T(),d),ee={class:"section"},te={class:"container"},se=i(()=>o("div",{class:"section-header"},[o("h1",{class:"section-title"},"История операций")],-1)),oe={class:"list-form"},ae={class:"list-form_col list-form_col--sm"},ne={class:"list-form_select"},le={class:"multiselect-option"},re=i(()=>o("span",{class:"td-mobile"},"Дата Создания",-1)),ce={class:"d-block"},ie=i(()=>o("span",{class:"td-mobile"},"Договор",-1)),de=i(()=>o("span",{class:"td-mobile"},"До",-1)),me={class:"d-block"},ue=i(()=>o("span",{class:"td-mobile"},"После",-1)),_e={class:"d-block"},pe=i(()=>o("span",{class:"td-mobile"},"Операция",-1)),ge=i(()=>o("span",{class:"td-mobile"},"Сумма",-1)),ve={key:0,class:"table-controls"},fe=i(()=>o("span",{class:"text-success"},"First",-1)),be=i(()=>o("span",{class:"text-info"},"Last",-1)),Ce={class:"multiselect-multiple-label"},he={class:"multiselect-option"};function ye(d,s,D,t,g,k){var f,h;const P=U,I=Y,C=S("Multiselect"),B=X,L=Q,v=W,V=K;return y(),w(F,null,[p(P),o("div",ee,[o("div",te,[se,p(I),o("div",oe,[o("div",ae,[o("div",ne,[p(C,{modelValue:t.select,"onUpdate:modelValue":s[0]||(s[0]=e=>t.select=e),options:t.getUsers,disabled:((f=t.getUsers)==null?void 0:f.length)<=1,label:"name","track-by":"value"},{singlelabel:r(({value:e})=>[o("span",null,"# "+n(e.value.padStart(5,"0"))+" / "+n(e.name),1)]),option:r(({option:e})=>[o("div",le,"# "+n(e.value.padStart(5,"0"))+" / "+n(e.name),1)]),_:1},8,["modelValue","options","disabled"])])]),p(B,{modelValue:t.search,"onUpdate:modelValue":s[1]||(s[1]=e=>t.search=e)},null,8,["modelValue"])]),t.getPostings&&((h=t.getPostings)==null?void 0:h.length)>0?(y(),w(F,{key:0},[p(L,{class:"basic-table",items:t.getPostings.filter(e=>e.ContractID.includes(t.select!=="*"?t.select:"")),fields:t.postingsFields,"show-empty":!0,filter:t.search,"per-page":t.perPage,"current-page":t.currentPage,filterable:["Customer","Comment"],"sort-by":t.sortBy,"onUpdate:sortBy":s[2]||(s[2]=e=>t.sortBy=e),"sort-desc":t.sortDesc,"onUpdate:sortDesc":s[3]||(s[3]=e=>t.sortDesc=e),"sort-direction":t.sortDirection,"sort-compare-options":{numeric:!0,sensitivity:"base"},"empty-text":"Операций пока нет.","empty-filtered-text":"Операции не найдены.",onFiltered:t.setPageToZero},{"cell(CreateDate)":r(e=>{var c;return[re,o("span",ce,n(t.formNormalDate(t.secondToDate((c=e.item)==null?void 0:c.CreateDate))),1)]}),"cell(ContractID)":r(e=>{var c,u;return[ie,o("span",null,n((c=e.item)==null?void 0:c.ContractID)+" - "+n((u=e.item)==null?void 0:u.Customer),1)]}),"cell(Before)":r(e=>[de,o("span",me,n(e.value),1)]),"cell(After)":r(e=>[ue,o("span",_e,n(e.value),1)]),"cell(ServiceID)":r(e=>{var c,u,a;return[pe,o("span",null,n((a=(u=t.getServices)==null?void 0:u[(c=e.item)==null?void 0:c.ServiceID])==null?void 0:a.Name),1)]}),"cell(Change)":r(e=>[ge,o("span",{class:A(["d-block",{positive:e.value>0,negative:e.value<0}])},n(e.value>0?`+${e.value}`:e.value)+" ₽",3)]),_:1},8,["items","fields","filter","per-page","current-page","sort-by","sort-desc","sort-direction","onFiltered"]),t.filteredContractsLength>0?(y(),w("div",ve,[p(v,{modelValue:t.currentPage,"onUpdate:modelValue":s[4]||(s[4]=e=>t.currentPage=e),"total-rows":t.filteredContractsLength,"per-page":t.perPage,"first-number":"","last-number":""},{"first-text":r(()=>[fe]),"last-text":r(()=>[be]),page:r(({page:e,active:c})=>[o("span",null,n((e-1)*t.perPage+1)+"-"+n(e*t.perPage),1)]),_:1},8,["modelValue","total-rows","per-page"]),p(C,{class:"multiselect--white",modelValue:t.perPage,"onUpdate:modelValue":s[5]||(s[5]=e=>t.perPage=e),options:t.pageOptions,label:"name",openDirection:"top",onInput:s[6]||(s[6]=e=>t.currentPage=1)},{singlelabel:r(({value:e})=>[o("div",Ce,[o("span",null,"Отображать "+n(e.value)+" строк",1)])]),option:r(({option:e})=>[o("div",he,"Отображать "+n(e.name)+" строк",1)]),_:1},8,["modelValue","options"])])):M("",!0)],64)):(y(),E(V,{key:1,label:"Ваша история операций пуста"}))])])],64)}const De={components:{Multiselect:G,BlockBalanceAgreement:U},async setup(){const d=z(),s=q(),D=R(),t=m(""),g=m("*"),k=m(1),P=m(10),I=m([10,25,50,100]),C=m("CreateDate"),B=m(!0),L=m("desc");Z(async()=>{await d.fetchContracts(),await s.fetchPostings(),await s.fetchServices(),console.log(D);const a=D.query.ContractID;a&&(g.value=a||"*")});const v=b(()=>Object.keys(s==null?void 0:s.postingsList).map(a=>{var l,_,N,x;return{...s==null?void 0:s.postingsList[a],Customer:(_=(l=f.value)==null?void 0:l[s==null?void 0:s.postingsList[a].ContractID])==null?void 0:_.Customer,Change:c((N=s==null?void 0:s.postingsList[a])==null?void 0:N.Before,(x=s==null?void 0:s.postingsList[a])==null?void 0:x.After)}})),V=b(()=>s==null?void 0:s.servicesList),f=b(()=>d.contractsList),h=b(()=>{let a=[];return v.value.map(l=>{if(!a.find(_=>_===l.Customer))return a.push(l.Customer),{value:l.ContractID,name:l.Customer}}).filter(Boolean).concat([{value:"*",name:"Все договора"}]).reverse()}),e=b(()=>v.value.filter(a=>{var l,_;return a.Comment.toLowerCase().includes((l=t.value)==null?void 0:l.toLowerCase())||a.Customer.toLowerCase().includes((_=t.value)==null?void 0:_.toLowerCase())}).filter(a=>a.ContractID.includes(g.value!=="*"?g.value:"")).length);function c(a,l){return l-a>0,Number(Math.round((l-a+Number.EPSILON)*100)/100)}function u(){k.value=1}return await d.fetchContracts(),await s.fetchPostings(),await s.fetchServices(),{search:t,select:g,getContracts:f,getUsers:h,getPostings:v,getServices:V,currentPage:k,perPage:P,sortBy:C,sortDesc:B,sortDirection:L,pageOptions:I,filteredContractsLength:e,postingsFields:$,setPageToZero:u,secondToDate:H,formNormalDate:J,calculateChange:c}}},Oe=j(De,[["render",ye],["__scopeId","data-v-91c3162b"]]);export{Oe as default};
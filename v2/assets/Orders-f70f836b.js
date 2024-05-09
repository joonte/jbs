import{C as ae,F as le}from"./bootstrap-vue-next.es-ee787e70.js";import{_ as ne}from"./IconCard-7dcad6e0.js";import{S as oe}from"./StatusBadge-d0f26ab2.js";import{_ as ue}from"./FormInputSearch-22adbbb3.js";import{_ as ce}from"./ClausesKeeper-9fc4bc59.js";import{_ as ie,e as re,a7 as pe,f as ve,u as de,R as me,r as _,g as d,ag as T,o as H,c as J,b as c,a,w as o,t as u,L as I,k as _e,F as fe,p as be,l as De}from"./index-1d7f6848.js";import{u as ge}from"./contracts-97246549.js";import{u as Se}from"./services-61ca0497.js";import{f as K}from"./useTimeFunction-8602dd60.js";import{_ as Ie}from"./HelperComponent-8df5ca49.js";import{s as x}from"./multiselect-3f954972.js";import{B as he}from"./BlockBalanceAgreement-fc8b62a5.js";import"./IconPlus-f101ee33.js";import"./IconSearch-f1afa701.js";import"./IconClose-b09002f7.js";import"./ButtonDefault-ceb0a858.js";import"./IconArrow-6ea40b14.js";const Ce=[{key:"ID",label:"Заказ",variant:"td-blue",sortable:!0},{key:"ServiceID",label:"Услуга",variant:"td-gray td-order bold"},{key:"ContractID",label:"Договор",sortable:!0,variant:"td-blue"},{key:"StatusID",label:"Статус",variant:"td-gray td-order"},{key:"ExpirationDate",label:"Дата окончания",variant:"td-gray td-order"},{key:"UserNotice",label:"Примечание",variant:"td-gray td-limited"},{key:"controls",label:"",variant:"td-controls"}];const r=h=>(be("data-v-c8798a6f"),h=h(),De(),h),ye={class:"section"},Ve={class:"container"},Oe=r(()=>a("div",{class:"section-header"},[a("h1",{class:"section-title"},"Мои заказы")],-1)),ke={class:"list-form"},xe={class:"list-form_col list-form_col--sm"},we={class:"list-form_select"},Le={class:"multiselect-option"},Re={class:"list-form_col list-form_col--sm"},Te={class:"list-form_select"},Ue={class:"multiselect-option"},Ee={class:"list-form_col list-form_col--sm"},Ne={class:"list-form_select"},Ae={class:"multiselect-option"},Be=r(()=>a("span",{class:"td-mobile"},"Заказ",-1)),$e=r(()=>a("span",{class:"td-mobile"},"Услуга",-1)),Fe=r(()=>a("span",{class:"td-mobile"},"Договор",-1)),Pe=r(()=>a("span",{class:"td-mobile"},"Комментарий",-1)),je=r(()=>a("span",{class:"td-mobile"},"Статус",-1)),qe=r(()=>a("span",{class:"td-mobile"},"Дата окончания",-1)),ze={class:"btn__group"},Ge=["onClick"],He=r(()=>a("p",null,"ОПЛАТИТЬ",-1)),Je={key:0,class:"table-controls"},Ke=r(()=>a("span",{class:"text-success"},"First",-1)),Me=r(()=>a("span",{class:"text-info"},"Last",-1)),Qe={class:"multiselect-multiple-label"},We={class:"multiselect-option"},Xe={__name:"Orders",async setup(h){let f,b;const C=re(),U=pe(),p=ve(),y=ge(),E=Se(),w=de();me("emitter").on("updateOrdersList",()=>{p.fetchUserOrders()});const L=_(""),D=_(10),S=_(1),M=_([10,25,50,100]),V=_("*"),O=_("*"),k=_("*"),v=d(()=>Object.keys(p==null?void 0:p.userOrders).map(s=>p==null?void 0:p.userOrders[s])),R=d(()=>{let s=v.value.filter(e=>e==null?void 0:e.ID.includes(L.value));return O.value!=="*"&&s!==null&&(s=s.filter(e=>e.ServiceID.includes(O.value))),V.value!=="*"&&s!==null&&(s=s.filter(e=>e.ContractID.includes(V.value))),k.value!=="*"&&s!==null&&(s=s.filter(e=>e.StatusID.includes(k.value))),S.value=1,s}),N=d(()=>y==null?void 0:y.contractsList);function Q(s){if(!s)return K(new Date);const e=new Date,l=new Date(e.getTime()+s*24*60*60*1e3);return K(l)}const m=d(()=>E.ServicesList),A=d(()=>{let s=[{value:"*",name:"Все договора"}];return v.value!==null?(v.value.map(e=>{var l;s.find(i=>i.value===(e==null?void 0:e.ContractID))||s.push({value:e==null?void 0:e.ContractID,name:(l=N.value[e==null?void 0:e.ContractID])==null?void 0:l.Customer})}),s):{value:"*",name:"Все договора"}}),B=d(()=>{let s=[{value:"*",name:"Все услуги"}];return v.value!==null?(v.value.map(e=>{var l;s.find(i=>i.value===(e==null?void 0:e.ServiceID))||s.push({value:e==null?void 0:e.ServiceID,name:(l=m.value[e==null?void 0:e.ServiceID])==null?void 0:l.NameShort})}),s):{value:"*",name:"Все услуги"}}),W=d(()=>{var s,e;return(e=(s=w==null?void 0:w.ConfigList)==null?void 0:s.Statuses)==null?void 0:e.Orders}),$=d(()=>{let s=[{value:"*",name:"Все статусы"}];return v.value!==null?(v.value.map(e=>{var l,i;s.find(g=>g.value===(e==null?void 0:e.StatusID))||s.push({value:e==null?void 0:e.StatusID,name:((i=(l=W.value)==null?void 0:l[e==null?void 0:e.StatusID])==null?void 0:i.Name)||(e==null?void 0:e.StatusID)})}),s):{value:"*",name:"Все статусы"}});function X(s){var e,l;return((e=m.value[s])==null?void 0:e.Code)!=="Default"?((l=m.value[s])==null?void 0:l.Code)+"Orders":"Orders"}function Y(s){var l;let e=(l=m.value[s.ServiceID])==null?void 0:l.Code;e==="Default"?C.push(`/AdditionalServices/${s.ID}`):C.push(`/${e}Orders/${s.ID}`)}function Z(s){var l;let e=(l=m.value[s.ServiceID])==null?void 0:l.Code;e==="Default"?C.push(`/ServiceOrderPay/${s.ID}`):C.push(`/${e}OrderPay/${s.ID}`)}return[f,b]=T(()=>p.fetchUserOrders().then(()=>{var s,e;if((s=U.params)!=null&&s.id){const l=(e=v.value)==null?void 0:e.find(i=>{var g;return i.ID===((g=U.params)==null?void 0:g.id)});console.log(l),console.log(m)}})),await f,b(),[f,b]=T(()=>y.fetchContracts()),await f,b(),[f,b]=T(()=>E.fetchServices()),await f,b(),(s,e)=>{var F,P,j,q,z;const l=ce,i=ue,g=oe,ee=ne,te=ae,se=le;return H(),J(fe,null,[c(he),a("div",ye,[a("div",Ve,[Oe,c(l),a("div",ke,[c(i,{modelValue:L.value,"onUpdate:modelValue":e[0]||(e[0]=t=>L.value=t)},null,8,["modelValue"]),a("div",xe,[a("div",we,[c(I(x),{modelValue:V.value,"onUpdate:modelValue":e[1]||(e[1]=t=>V.value=t),options:A.value,disabled:((F=A.value)==null?void 0:F.length)<=1,label:"name"},{singlelabel:o(({value:t})=>[a("span",null,"# "+u(t.value.padStart(5,"0"))+" / "+u(t.name),1)]),option:o(({option:t})=>[a("div",Le,"# "+u(t.value.padStart(5,"0"))+" / "+u(t.name),1)]),_:1},8,["modelValue","options","disabled"])])]),a("div",Re,[a("div",Te,[c(I(x),{modelValue:k.value,"onUpdate:modelValue":e[2]||(e[2]=t=>k.value=t),options:$.value,disabled:((P=$.value)==null?void 0:P.length)<=1,label:"name"},{option:o(({option:t})=>[a("div",Ue,u(t.name),1)]),_:1},8,["modelValue","options","disabled"])])]),a("div",Ee,[a("div",Ne,[c(I(x),{modelValue:O.value,"onUpdate:modelValue":e[3]||(e[3]=t=>O.value=t),options:B.value,disabled:((j=B.value)==null?void 0:j.length)<=1,label:"name"},{option:o(({option:t})=>[a("div",Ae,u(t.name),1)]),_:1},8,["modelValue","options","disabled"])])])]),c(te,{class:"basic-table",fields:I(Ce),items:R.value,"show-empty":!0,"per-page":D.value,responsive:"","current-page":S.value,"empty-text":"Заказов пока нет.","empty-filtered-text":"Заказы не найдены.",onRowClicked:Y},{"cell(ID)":o(t=>[Be,a("span",null,u(t.value),1)]),"cell(ServiceID)":o(t=>{var n;return[$e,a("span",null,u((n=m.value[t.value])==null?void 0:n.NameShort),1)]}),"cell(ContractID)":o(t=>{var n;return[Fe,a("span",null,u((n=N.value[t.value])==null?void 0:n.Customer),1)]}),"cell(UserNotice)":o(t=>{var n;return[Pe,c(Ie,{"user-notice":t.value,id:(n=t.item)==null?void 0:n.ID,emit:"updateOrdersList"},null,8,["user-notice","id"])]}),"cell(StatusID)":o(t=>{var n;return[je,c(g,{status:t.value,"status-table":X((n=t.item)==null?void 0:n.ServiceID)},null,8,["status","status-table"])]}),"cell(ExpirationDate)":o(t=>{var n,G;return[qe,a("span",null,u(Q((n=t.item)==null?void 0:n.DaysRemainded))+" | "+u(((G=t.item)==null?void 0:G.DaysRemainded)||0)+" дн.",1)]}),"cell(controls)":o(t=>[a("div",ze,[a("button",{class:"btn btn-default btn--border",onClick:n=>Z(t.item)},[c(ee),He],8,Ge)])]),_:1},8,["fields","items","per-page","current-page"]),((q=R.value)==null?void 0:q.length)>0?(H(),J("div",Je,[c(se,{modelValue:S.value,"onUpdate:modelValue":e[4]||(e[4]=t=>S.value=t),"total-rows":(z=R.value)==null?void 0:z.length,"per-page":D.value,"first-number":"","last-number":""},{"first-text":o(()=>[Ke]),"last-text":o(()=>[Me]),page:o(({page:t,active:n})=>[a("span",null,u((t-1)*D.value+1)+"-"+u(t*D.value),1)]),_:1},8,["modelValue","total-rows","per-page"]),c(I(x),{class:"multiselect--white",modelValue:D.value,"onUpdate:modelValue":e[5]||(e[5]=t=>D.value=t),options:M.value,label:"name",openDirection:"top",onInput:e[6]||(e[6]=t=>S.value=1)},{singlelabel:o(({value:t})=>[a("div",Qe,[a("span",null,"Отображать "+u(t.value)+" строк",1)])]),option:o(({option:t})=>[a("div",We,"Отображать "+u(t.name)+" строк",1)]),_:1},8,["modelValue","options"])])):_e("",!0)])])],64)}}},ft=ie(Xe,[["__scopeId","data-v-c8798a6f"]]);export{ft as default};

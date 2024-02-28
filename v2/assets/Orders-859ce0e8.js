import{C as ae,F as le}from"./bootstrap-vue-next.es-b0c2a08e.js";import{_ as ne}from"./IconCard-3c442370.js";import{S as oe}from"./StatusBadge-1111ad36.js";import{_ as ue}from"./FormInputSearch-ea7c08b5.js";import{_ as ce}from"./ClausesKeeper-54c941b5.js";import{_ as ie,e as re,a7 as pe,f as ve,u as de,R as me,r as _,g as d,ag as T,o as H,c as J,b as c,a,w as o,t as u,L as I,k as _e,F as fe,p as be,l as De}from"./index-d9e5d80f.js";import{u as ge}from"./contracts-0b0db280.js";import{u as Se}from"./services-311c9679.js";import{f as K}from"./useTimeFunction-8602dd60.js";import{_ as Ie}from"./HelperComponent-7f3019d3.js";import{s as x}from"./multiselect-547456cc.js";import{B as he}from"./BlockBalanceAgreement-bd5f9bec.js";import"./IconPlus-fe0f21cb.js";import"./IconSearch-e7fe9235.js";import"./IconClose-b8cba5ec.js";import"./ButtonDefault-cf4dc84b.js";import"./IconArrow-3cf18905.js";const Ce=[{key:"ID",label:"Заказ",variant:"td-blue",sortable:!0},{key:"ServiceID",label:"Услуга",variant:"td-gray td-order bold"},{key:"ContractID",label:"Договор",sortable:!0,variant:"td-blue"},{key:"StatusID",label:"Статус",variant:"td-gray td-order"},{key:"ExpirationDate",label:"Дата окончания",variant:"td-gray td-order"},{key:"UserNotice",label:"Примечание",variant:"td-gray td-limited"},{key:"controls",label:"",variant:"td-controls"}];const r=h=>(be("data-v-bc759f77"),h=h(),De(),h),ye={class:"section"},Ve={class:"container"},Oe=r(()=>a("div",{class:"section-header"},[a("h1",{class:"section-title"},"Мои заказы")],-1)),ke={class:"list-form"},xe={class:"list-form_col list-form_col--sm"},we={class:"list-form_select"},Le={class:"multiselect-option"},Re={class:"list-form_col list-form_col--sm"},Te={class:"list-form_select"},Ue={class:"multiselect-option"},Ee={class:"list-form_col list-form_col--sm"},Ne={class:"list-form_select"},Ae={class:"multiselect-option"},Be=r(()=>a("span",{class:"td-mobile"},"Заказ",-1)),$e=r(()=>a("span",{class:"td-mobile"},"Услуга",-1)),Fe=r(()=>a("span",{class:"td-mobile"},"Договор",-1)),Pe=r(()=>a("span",{class:"td-mobile"},"Комментарий",-1)),je=r(()=>a("span",{class:"td-mobile"},"Статус",-1)),qe=r(()=>a("span",{class:"td-mobile"},"Дата окончания",-1)),ze={class:"btn__group"},Ge=["onClick"],He=r(()=>a("p",null,"ОПЛАТИТЬ",-1)),Je={key:0,class:"table-controls"},Ke=r(()=>a("span",{class:"text-success"},"First",-1)),Me=r(()=>a("span",{class:"text-info"},"Last",-1)),Qe={class:"multiselect-multiple-label"},We={class:"multiselect-option"},Xe={__name:"Orders",async setup(h){let f,b;const C=re(),U=pe(),p=ve(),y=ge(),E=Se(),w=de();me("emitter").on("updateOrdersList",()=>{p.fetchUserOrders()});const L=_(""),D=_(10),S=_(1),M=_([10,25,50,100]),V=_("*"),O=_("*"),k=_("*"),v=d(()=>Object.keys(p==null?void 0:p.userOrders).map(t=>p==null?void 0:p.userOrders[t])),R=d(()=>{let t=v.value.filter(e=>e==null?void 0:e.ID.includes(L.value));return O.value!=="*"&&t!==null&&(t=t.filter(e=>e.ServiceID.includes(O.value))),V.value!=="*"&&t!==null&&(t=t.filter(e=>e.ContractID.includes(V.value))),k.value!=="*"&&t!==null&&(t=t.filter(e=>e.StatusID.includes(k.value))),S.value=1,t}),N=d(()=>y==null?void 0:y.contractsList);function Q(t){if(!t)return K(new Date);const e=new Date,l=new Date(e.getTime()+t*24*60*60*1e3);return K(l)}const m=d(()=>E.ServicesList),A=d(()=>{let t=[{value:"*",name:"Все договора"}];return v.value!==null?(v.value.map(e=>{var l;t.find(i=>i.value===(e==null?void 0:e.ContractID))||t.push({value:e==null?void 0:e.ContractID,name:(l=N.value[e==null?void 0:e.ContractID])==null?void 0:l.Customer})}),t):{value:"*",name:"Все договора"}}),B=d(()=>{let t=[{value:"*",name:"Все услуги"}];return v.value!==null?(v.value.map(e=>{var l;t.find(i=>i.value===(e==null?void 0:e.ServiceID))||t.push({value:e==null?void 0:e.ServiceID,name:(l=m.value[e==null?void 0:e.ServiceID])==null?void 0:l.NameShort})}),t):{value:"*",name:"Все услуги"}}),W=d(()=>{var t,e;return(e=(t=w==null?void 0:w.ConfigList)==null?void 0:t.Statuses)==null?void 0:e.Orders}),$=d(()=>{let t=[{value:"*",name:"Все статусы"}];return v.value!==null?(v.value.map(e=>{var l,i;t.find(g=>g.value===(e==null?void 0:e.StatusID))||t.push({value:e==null?void 0:e.StatusID,name:((i=(l=W.value)==null?void 0:l[e==null?void 0:e.StatusID])==null?void 0:i.Name)||(e==null?void 0:e.StatusID)})}),t):{value:"*",name:"Все статусы"}});function X(t){var e,l;return((e=m.value[t])==null?void 0:e.Code)!=="Default"?((l=m.value[t])==null?void 0:l.Code)+"Orders":"Orders"}function Y(t){var l;let e=(l=m.value[t.ServiceID])==null?void 0:l.Code;e==="Default"?C.push(`/AdditionalServices/${t.ID}`):C.push(`/${e}Orders/${t.ID}`)}function Z(t){var l;let e=(l=m.value[t.ServiceID])==null?void 0:l.Code;e==="Default"?C.push(`/ServiceOrderPay/${t.ID}`):C.push(`/${e}OrderPay/${t.ID}`)}return[f,b]=T(()=>p.fetchUserOrders().then(()=>{var t,e;if((t=U.params)!=null&&t.id){const l=(e=v.value)==null?void 0:e.find(i=>{var g;return i.ID===((g=U.params)==null?void 0:g.id)});console.log(l),console.log(m)}})),await f,b(),[f,b]=T(()=>y.fetchContracts()),await f,b(),[f,b]=T(()=>E.fetchServices()),await f,b(),(t,e)=>{var F,P,j,q,z;const l=ce,i=ue,g=oe,ee=ne,te=ae,se=le;return H(),J(fe,null,[c(he),a("div",ye,[a("div",Ve,[Oe,c(l),a("div",ke,[c(i,{modelValue:L.value,"onUpdate:modelValue":e[0]||(e[0]=s=>L.value=s)},null,8,["modelValue"]),a("div",xe,[a("div",we,[c(I(x),{modelValue:V.value,"onUpdate:modelValue":e[1]||(e[1]=s=>V.value=s),options:A.value,disabled:((F=A.value)==null?void 0:F.length)<=1,label:"name"},{option:o(({option:s})=>[a("div",Le,"# "+u(s.value.padStart(5,"0"))+" / "+u(s.name),1)]),_:1},8,["modelValue","options","disabled"])])]),a("div",Re,[a("div",Te,[c(I(x),{modelValue:k.value,"onUpdate:modelValue":e[2]||(e[2]=s=>k.value=s),options:$.value,disabled:((P=$.value)==null?void 0:P.length)<=1,label:"name"},{option:o(({option:s})=>[a("div",Ue,u(s.name),1)]),_:1},8,["modelValue","options","disabled"])])]),a("div",Ee,[a("div",Ne,[c(I(x),{modelValue:O.value,"onUpdate:modelValue":e[3]||(e[3]=s=>O.value=s),options:B.value,disabled:((j=B.value)==null?void 0:j.length)<=1,label:"name"},{option:o(({option:s})=>[a("div",Ae,u(s.name),1)]),_:1},8,["modelValue","options","disabled"])])])]),c(te,{class:"basic-table",fields:I(Ce),items:R.value,"show-empty":!0,"per-page":D.value,responsive:"","current-page":S.value,"empty-text":"Заказов пока нет.","empty-filtered-text":"Заказы не найдены.",onRowClicked:Y},{"cell(ID)":o(s=>[Be,a("span",null,u(s.value),1)]),"cell(ServiceID)":o(s=>{var n;return[$e,a("span",null,u((n=m.value[s.value])==null?void 0:n.NameShort),1)]}),"cell(ContractID)":o(s=>{var n;return[Fe,a("span",null,u((n=N.value[s.value])==null?void 0:n.Customer),1)]}),"cell(UserNotice)":o(s=>{var n;return[Pe,c(Ie,{"user-notice":s.value,id:(n=s.item)==null?void 0:n.ID,emit:"updateOrdersList"},null,8,["user-notice","id"])]}),"cell(StatusID)":o(s=>{var n;return[je,c(g,{status:s.value,"status-table":X((n=s.item)==null?void 0:n.ServiceID)},null,8,["status","status-table"])]}),"cell(ExpirationDate)":o(s=>{var n,G;return[qe,a("span",null,u(Q((n=s.item)==null?void 0:n.DaysRemainded))+" | "+u(((G=s.item)==null?void 0:G.DaysRemainded)||0)+" дн.",1)]}),"cell(controls)":o(s=>[a("div",ze,[a("button",{class:"btn btn-default btn--border",onClick:n=>Z(s.item)},[c(ee),He],8,Ge)])]),_:1},8,["fields","items","per-page","current-page"]),((q=R.value)==null?void 0:q.length)>0?(H(),J("div",Je,[c(se,{modelValue:S.value,"onUpdate:modelValue":e[4]||(e[4]=s=>S.value=s),"total-rows":(z=R.value)==null?void 0:z.length,"per-page":D.value,"first-number":"","last-number":""},{"first-text":o(()=>[Ke]),"last-text":o(()=>[Me]),page:o(({page:s,active:n})=>[a("span",null,u((s-1)*D.value+1)+"-"+u(s*D.value),1)]),_:1},8,["modelValue","total-rows","per-page"]),c(I(x),{class:"multiselect--white",modelValue:D.value,"onUpdate:modelValue":e[5]||(e[5]=s=>D.value=s),options:M.value,label:"name",openDirection:"top",onInput:e[6]||(e[6]=s=>S.value=1)},{singlelabel:o(({value:s})=>[a("div",Qe,[a("span",null,"Отображать "+u(s.value)+" строк",1)])]),option:o(({option:s})=>[a("div",We,"Отображать "+u(s.name)+" строк",1)]),_:1},8,["modelValue","options"])])):_e("",!0)])])],64)}}},ft=ie(Xe,[["__scopeId","data-v-bc759f77"]]);export{ft as default};

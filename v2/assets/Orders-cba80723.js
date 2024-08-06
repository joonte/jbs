import{C as ue,F as ce}from"./bootstrap-vue-next.es-8f3ae81a.js";import{_ as ie}from"./IconCard-2cc17a6b.js";import{S as re}from"./StatusBadge-0d170ac0.js";import{_ as pe}from"./FormInputSearch-48364fb5.js";import{_ as de}from"./ClausesKeeper-55c14b7f.js";import{_ as ve,e as me,a7 as _e,f as fe,u as be,S as De,r as f,g as m,ag as R,o as K,c as Q,b as r,a,w as o,t as u,M as I,k as Se,F as ge,p as Ie,l as he}from"./index-83b4b7ba.js";import{u as Ce}from"./contracts-623ec7a8.js";import{u as ye}from"./services-c044aff2.js";import{f as W}from"./useTimeFunction-8602dd60.js";import{_ as Oe}from"./HelperComponent-df0c894c.js";import{s as x}from"./multiselect-f2568231.js";import{B as ke}from"./BlockBalanceAgreement-c5fad736.js";import"./IconPlus-07b0a078.js";import"./IconSearch-30270af1.js";import"./IconClose-86aafc4e.js";import"./ButtonDefault-dc248ec6.js";import"./IconArrow-80c72216.js";const Ve=[{key:"ID",label:"Заказ",variant:"td-blue",sortable:!0},{key:"ServiceID",label:"Услуга",variant:"td-gray td-order bold"},{key:"SchemeName",label:"Тариф",variant:"td-gray td-order"},{key:"ExpirationDate",label:"Дата окончания",variant:"td-gray td-order"},{key:"StatusID",label:"Статус",variant:"td-gray td-order"},{key:"ContractID",label:"Договор",sortable:!0,variant:"td-blue"},{key:"UserNotice",label:"Примечание",variant:"td-gray td-limited"},{key:"controls",label:"",variant:"td-controls"}];const p=h=>(Ie("data-v-be636857"),h=h(),he(),h),xe={class:"section"},Ne={class:"container"},we=p(()=>a("div",{class:"section-header"},[a("h1",{class:"section-title"},"Мои заказы")],-1)),Ee={class:"list-form"},Le={class:"list-form_col list-form_col--sm"},Re={class:"list-form_select"},Te={class:"multiselect-option"},Ue={class:"list-form_col list-form_col--sm"},Ae={class:"list-form_select"},$e={class:"multiselect-option"},Be={class:"list-form_col list-form_col--sm"},Fe={class:"list-form_select"},Pe={class:"multiselect-option"},je=p(()=>a("span",{class:"td-mobile"},"Заказ",-1)),He=p(()=>a("span",{class:"td-mobile"},"Услуга",-1)),Me=p(()=>a("span",{class:"td-mobile"},"Тариф",-1)),qe=p(()=>a("span",{class:"td-mobile"},"Дата окончания",-1)),ze=p(()=>a("span",{class:"td-mobile"},"Статус",-1)),Ge=p(()=>a("span",{class:"td-mobile"},"Договор",-1)),Je=p(()=>a("span",{class:"td-mobile"},"Комментарий",-1)),Ke={class:"btn__group"},Qe=["onClick"],We=p(()=>a("p",null,"ОПЛАТИТЬ",-1)),Xe={key:0,class:"table-controls"},Ye=p(()=>a("span",{class:"text-success"},"First",-1)),Ze=p(()=>a("span",{class:"text-info"},"Last",-1)),et={class:"multiselect-multiple-label"},tt={class:"multiselect-option"},st={__name:"Orders",async setup(h){let b,D;const C=me(),T=_e(),d=fe(),y=Ce(),U=ye(),N=be(),A=De("emitter");A.on("updateOrdersList",()=>{d.fetchUserOrders()});const w=f(""),S=f(10),g=f(1),X=f([10,25,50,100]),O=f("*"),k=f("*"),V=f("*"),v=m(()=>Object.keys(d==null?void 0:d.userOrders).map(s=>d==null?void 0:d.userOrders[s])),E=m(()=>{let s=v.value.filter(e=>e==null?void 0:e.ID.includes(w.value));return k.value!=="*"&&s!==null&&(s=s.filter(e=>e.ServiceID.includes(k.value))),O.value!=="*"&&s!==null&&(s=s.filter(e=>e.ContractID.includes(O.value))),V.value!=="*"&&s!==null&&(s=s.filter(e=>e.StatusID.includes(V.value))),g.value=1,s}),$=m(()=>y==null?void 0:y.contractsList);function Y(s){if(!s)return W(new Date);const e=new Date,l=new Date(e.getTime()+s*24*60*60*1e3);return W(l)}const _=m(()=>U.ServicesList);function Z(s,e){let l="";e==="Default"?l="Orders":l=e+"Orders",A.emit("open-modal",{component:"StatusHistory",data:{modeID:l,rowID:s}})}const B=m(()=>{let s=[{value:"*",name:"Все договора"}];return v.value!==null?(v.value.map(e=>{var l;s.find(c=>c.value===(e==null?void 0:e.ContractID))||s.push({value:e==null?void 0:e.ContractID,name:(l=$.value[e==null?void 0:e.ContractID])==null?void 0:l.Customer})}),s):{value:"*",name:"Все договора"}}),F=m(()=>{let s=[{value:"*",name:"Все услуги"}];return v.value!==null?(v.value.map(e=>{var l;s.find(c=>c.value===(e==null?void 0:e.ServiceID))||s.push({value:e==null?void 0:e.ServiceID,name:(l=_.value[e==null?void 0:e.ServiceID])==null?void 0:l.NameShort})}),s):{value:"*",name:"Все услуги"}}),ee=m(()=>{var s,e;return(e=(s=N==null?void 0:N.ConfigList)==null?void 0:s.Statuses)==null?void 0:e.Orders}),P=m(()=>{let s=[{value:"*",name:"Все статусы"}];return v.value!==null?(v.value.map(e=>{var l,c;s.find(i=>i.value===(e==null?void 0:e.StatusID))||s.push({value:e==null?void 0:e.StatusID,name:((c=(l=ee.value)==null?void 0:l[e==null?void 0:e.StatusID])==null?void 0:c.Name)||(e==null?void 0:e.StatusID)})}),s):{value:"*",name:"Все статусы"}});function te(s){var e,l;return((e=_.value[s])==null?void 0:e.Code)!=="Default"?((l=_.value[s])==null?void 0:l.Code)+"Orders":"Orders"}function se(s,e,l){var c;if(!l.target.closest("button, a, .clickable-element")){let i=(c=_.value[s.ServiceID])==null?void 0:c.Code;i==="Default"||i==="ExtraIP"||i==="ISPsw"||i==="DNSmanager"?C.push(`/AdditionalServices/${s.ID}`):C.push(`/${i}Orders/${s.ID}`)}}function ae(s){var l;let e=(l=_.value[s.ServiceID])==null?void 0:l.Code;e==="Default"?C.push(`/ServiceOrderPay/${s.ID}`):C.push(`/${e}OrderPay/${s.ID}`)}return[b,D]=R(()=>d.fetchUserOrders().then(()=>{var s,e;if((s=T.params)!=null&&s.id){const l=(e=v.value)==null?void 0:e.find(c=>{var i;return c.ID===((i=T.params)==null?void 0:i.id)});console.log(l),console.log(_)}})),await b,D(),[b,D]=R(()=>y.fetchContracts()),await b,D(),[b,D]=R(()=>U.fetchServices()),await b,D(),(s,e)=>{var j,H,M,q,z;const l=de,c=pe,i=re,le=ie,ne=ue,oe=ce;return K(),Q(ge,null,[r(ke),a("div",xe,[a("div",Ne,[we,r(l),a("div",Ee,[r(c,{modelValue:w.value,"onUpdate:modelValue":e[0]||(e[0]=t=>w.value=t)},null,8,["modelValue"]),a("div",Le,[a("div",Re,[r(I(x),{modelValue:O.value,"onUpdate:modelValue":e[1]||(e[1]=t=>O.value=t),options:B.value,disabled:((j=B.value)==null?void 0:j.length)<=1,label:"name"},{singlelabel:o(({value:t})=>[a("span",null,"# "+u(t.value.padStart(5,"0"))+" / "+u(t.name),1)]),option:o(({option:t})=>[a("div",Te,"# "+u(t.value.padStart(5,"0"))+" / "+u(t.name),1)]),_:1},8,["modelValue","options","disabled"])])]),a("div",Ue,[a("div",Ae,[r(I(x),{modelValue:V.value,"onUpdate:modelValue":e[2]||(e[2]=t=>V.value=t),options:P.value,disabled:((H=P.value)==null?void 0:H.length)<=1,label:"name"},{option:o(({option:t})=>[a("div",$e,u(t.name),1)]),_:1},8,["modelValue","options","disabled"])])]),a("div",Be,[a("div",Fe,[r(I(x),{modelValue:k.value,"onUpdate:modelValue":e[3]||(e[3]=t=>k.value=t),options:F.value,disabled:((M=F.value)==null?void 0:M.length)<=1,label:"name"},{option:o(({option:t})=>[a("div",Pe,u(t.name),1)]),_:1},8,["modelValue","options","disabled"])])])]),r(ne,{class:"basic-table",fields:I(Ve),items:E.value,"show-empty":!0,"per-page":S.value,responsive:"","current-page":g.value,"empty-text":"Заказов пока нет.","empty-filtered-text":"Заказы не найдены.",onRowClicked:se},{"cell(ID)":o(t=>[je,a("span",null,u(t.value),1)]),"cell(ServiceID)":o(t=>{var n;return[He,a("span",null,u((n=_.value[t.value])==null?void 0:n.NameShort),1)]}),"cell(SchemeName)":o(t=>{var n;return[Me,a("span",null,u((n=t.item)==null?void 0:n.SchemeName),1)]}),"cell(ExpirationDate)":o(t=>{var n,L;return[qe,a("span",null,u(Y((n=t.item)==null?void 0:n.DaysRemainded))+" | "+u(((L=t.item)==null?void 0:L.DaysRemainded)||0)+" дн.",1)]}),"cell(StatusID)":o(t=>{var n;return[ze,r(i,{class:"clickable-element",status:t.value,"status-table":te((n=t.item)==null?void 0:n.ServiceID),onClick:L=>{var G,J;return Z((G=t.item)==null?void 0:G.ServiceOrderID,(J=t.item)==null?void 0:J.Code)}},null,8,["status","status-table","onClick"])]}),"cell(ContractID)":o(t=>{var n;return[Ge,a("span",null,u((n=$.value[t.value])==null?void 0:n.Customer),1)]}),"cell(UserNotice)":o(t=>{var n;return[Je,r(Oe,{"user-notice":t.value,id:(n=t.item)==null?void 0:n.ID,emit:"updateOrdersList"},null,8,["user-notice","id"])]}),"cell(controls)":o(t=>[a("div",Ke,[a("button",{class:"btn btn-default btn--border",onClick:n=>ae(t.item)},[r(le),We],8,Qe)])]),_:1},8,["fields","items","per-page","current-page"]),((q=E.value)==null?void 0:q.length)>0?(K(),Q("div",Xe,[r(oe,{modelValue:g.value,"onUpdate:modelValue":e[4]||(e[4]=t=>g.value=t),"total-rows":(z=E.value)==null?void 0:z.length,"per-page":S.value,"first-number":"","last-number":""},{"first-text":o(()=>[Ye]),"last-text":o(()=>[Ze]),page:o(({page:t,active:n})=>[a("span",null,u((t-1)*S.value+1)+"-"+u(t*S.value),1)]),_:1},8,["modelValue","total-rows","per-page"]),r(I(x),{class:"multiselect--white",modelValue:S.value,"onUpdate:modelValue":e[5]||(e[5]=t=>S.value=t),options:X.value,label:"name",openDirection:"top",onInput:e[6]||(e[6]=t=>g.value=1)},{singlelabel:o(({value:t})=>[a("div",et,[a("span",null,"Отображать "+u(t.value)+" строк",1)])]),option:o(({option:t})=>[a("div",tt,"Отображать "+u(t.name)+" строк",1)]),_:1},8,["modelValue","options"])])):Se("",!0)])])],64)}}},gt=ve(st,[["__scopeId","data-v-be636857"]]);export{gt as default};

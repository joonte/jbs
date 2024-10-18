import{_ as le}from"./FormInputSearch-3eea35fe.js";import{_ as re}from"./ClausesKeeper-b1ff97f6.js";import{_ as ce}from"./ButtonDefault-a62141a4.js";import{_ as O,o as L,c as R,b as l,a as e,m as ie,r as I,h as q,w as a,t as f,M as B,k as Q,p as G,l as z,e as Y,u as W,S as Z,g as P,j as _e,an as X,y as j,d as U,a7 as ue,ag as K,Z as de,a9 as pe,F as me}from"./index-59dbe3d3.js";import{u as J}from"./contracts-d90f6cbb.js";import{S as ve}from"./SlotPageTitle-8adee246.js";import{F as fe}from"./FormTabs-17be62e8.js";import{F as be,f as ge,z as ye,d as Ce,C as ee}from"./bootstrap-vue-next.es-faf1dad1.js";import{_ as he}from"./IconDots-29c2c1b9.js";import{S as te}from"./StatusBadge-13d0c588.js";import{f as Ie,a as ke}from"./useTimeFunction-8602dd60.js";/* empty css                                                                       */import{_ as De}from"./IconCard-4307e3b9.js";import{s as Te}from"./multiselect-49fa0a82.js";import{B as Se}from"./BlockBalanceAgreement-f093da58.js";import"./IconPlus-cfa19e86.js";import"./IconSearch-7115ed09.js";import"./IconClose-0bef5a37.js";import"./IconArrow-ad4e318c.js";const $e=[{key:"ID",label:"Номер",variant:"td-gray td-order",sortable:!0},{key:"CreateDate",label:"Дата Создания",variant:"td-gray td-order",sortable:!0},{key:"Customer",label:"Договор"},{key:"TypeID",label:"Тип",variant:"td-gray td-order"},{key:"Orders",label:"Количество подключенных услуг",sortable:!0,variant:"td-gray td-order"},{key:"Balance",label:"Баланс",sortable:!0,variant:"td-gray td-order"},{key:"IsHidden",label:"Скрыт ?",variant:"td-gray td-order"},{key:"Status",label:"Статус",variant:"td-gray td-order"},{key:"controls",label:"",variant:"td-controls"}];const Le={class:"button-payment"},Pe={class:"button-payment__text"},we={__name:"FormButtonPayment",setup(_){return(b,p)=>(L(),R("button",Le,[l(De,{class:"button-payment__icon"}),e("span",Pe,[ie(b.$slots,"default",{},void 0,!0)])]))}},xe=O(we,[["__scopeId","data-v-fcb8fa3c"]]);const se=_=>(G("data-v-64d83888"),_=_(),z(),_),Be={key:0,class:"table-controls"},Ve=se(()=>e("span",{class:"text-success"},"First",-1)),Ne=se(()=>e("span",{class:"text-info"},"Last",-1)),Fe={class:"multiselect-multiple-label"},Ee={class:"multiselect-option"},He={__name:"PaginationBlock",props:["modelValue","total_rows"],emits:["update:modelValue","updatePage"],setup(_,{emit:b}){const p=_,c=[10,25,50,100],i=I({current_page:1,per_page:10});return q(i.value,()=>{b("updatePage",i.value)}),(y,C)=>{const $=be;return p.total_rows>0?(L(),R("div",Be,[l($,{modelValue:i.value.current_page,"onUpdate:modelValue":C[0]||(C[0]=u=>i.value.current_page=u),"total-rows":p.total_rows,"per-page":i.value.per_page,"first-number":"","last-number":""},{"first-text":a(()=>[Ve]),"last-text":a(()=>[Ne]),page:a(({page:u,active:m})=>[e("span",null,f((u-1)*i.value.per_page+1)+"-"+f(u*i.value.per_page),1)]),_:1},8,["modelValue","total-rows","per-page"]),l(B(Te),{class:"multiselect--white",modelValue:i.value.per_page,"onUpdate:modelValue":C[1]||(C[1]=u=>i.value.per_page=u),options:c,label:"name",openDirection:"top",onInput:C[2]||(C[2]=u=>i.value.current_page=1)},{singlelabel:a(({value:u})=>[e("div",Fe,[e("span",null,"Отображать "+f(u.value)+" строк",1)])]),option:a(({option:u})=>[e("div",Ee,"Отображать "+f(u.name)+" строк",1)]),_:1},8,["modelValue"])])):Q("",!0)}}},oe=O(He,[["__scopeId","data-v-64d83888"]]);const w=_=>(G("data-v-39022284"),_=_(),z(),_),Ue={class:"contracts-list"},Oe=w(()=>e("span",{class:"td-mobile"},"Номер",-1)),Re={class:"td-column-heavy"},Ae={class:"table-td-gray"},Me=w(()=>e("span",{class:"td-mobile"},"Дата Создания",-1)),je={class:"d-block"},qe=w(()=>e("span",{class:"td-mobile"},"Договор",-1)),Ge={class:"td-column-heavy"},ze={class:"table-td-blue"},Ye=w(()=>e("span",{class:"td-mobile"},"Тип договора",-1)),Ze=w(()=>e("span",{class:"td-mobile"},"Баланс",-1)),Je=w(()=>e("span",{class:"td-mobile"},"Скрыт ?",-1)),Ke=w(()=>e("span",{class:"td-mobile"},"Статус",-1)),Qe=w(()=>e("span",{class:"td-mobile"},"Количество подключенных услуг",-1)),We={class:"btn__group"},Xe={class:"dropdown-button"},et={class:"btn btn--dots"},tt={__name:"ContractsList",props:{search:{type:String,default:""}},setup(_){const b=_,p=Y(),c=J(),i=W(),y=Z("emitter"),C=I("CreateDate"),$=I(!0),u=I("desc"),m=I(10),D=I(1);I(!1);const V=s=>{c.booleanEdit({TableID:"Contracts",ColumnID:"IsHidden",RowID:s.ID}).then(()=>{c.fetchContracts()})},N=P(()=>{var s,v;return(v=(s=Object.keys(c==null?void 0:c.contractsList))==null?void 0:s.map(k=>c==null?void 0:c.contractsList[k]))==null?void 0:v.reverse()}),H=P(()=>{var s,v;return(v=(s=i.ConfigList)==null?void 0:s.Contracts)==null?void 0:v.Types}),F=P(()=>{var s;return(s=N.value.filter(v=>{var k;return v.Customer.toLowerCase().includes(((k=b.search)==null?void 0:k.toLowerCase())||"")}))==null?void 0:s.length});function E(s){y.emit("open-modal",{component:"StatusHistory",data:{modeID:"Contracts",rowID:s}})}function x(s){p.push(`/Balance/${s}`)}function t(s={}){m.value=s==null?void 0:s.per_page,D.value=s==null?void 0:s.current_page}function r(s){c==null||c.contractDownload({ContractID:s})}return q(()=>b,()=>{D.value=1}),(s,v)=>{const k=ge,d=te,g=he,S=_e("router-link"),M=ye,ae=Ce,ne=ee;return L(),R("div",Ue,[l(ne,{class:"basic-table",items:N.value,fields:B($e),"show-empty":!0,filterable:[],filter:b.search,"per-page":m.value,"sort-desc":$.value,"onUpdate:sortDesc":v[0]||(v[0]=o=>$.value=o),"sort-by":C.value,"onUpdate:sortBy":v[1]||(v[1]=o=>C.value=o),"sort-direction":u.value,"current-page":D.value,"empty-text":"У вас пока нет договоров.","empty-filtered-text":"Договора не найдены."},{"cell(ID)":a(o=>{var n;return[Oe,e("div",Re,[e("div",Ae,f(B(X).transformContractNumber((n=o.item)==null?void 0:n.ID)),1)])]}),"cell(CreateDate)":a(o=>{var n;return[Me,e("span",je,f(B(Ie)(B(ke)((n=o.item)==null?void 0:n.CreateDate))),1)]}),"cell(Customer)":a(o=>{var n;return[qe,e("div",Ge,[e("div",ze,f((n=o.item)==null?void 0:n.Customer),1)])]}),"cell(TypeID)":a(o=>{var n,h;return[Ye,e("span",null,f((h=H.value[(n=o.item)==null?void 0:n.TypeID])==null?void 0:h.Name),1)]}),"cell(Balance)":a(o=>{var n;return[Ze,e("span",null,f((n=o.item)==null?void 0:n.Balance)+" ₽",1)]}),"cell(IsHidden)":a(o=>{var n,h;return[Je,l(k,{class:"rule-checkbox",name:"checkbox",checked:(n=o.item)==null?void 0:n.IsHidden,modelValue:o.item.IsChecked,"onUpdate:modelValue":T=>o.item.IsChecked=T,onChange:T=>V(o.item)},null,8,["checked","modelValue","onUpdate:modelValue","onChange"]),e("span",null,f(((h=o.item)==null?void 0:h.IsHidden)===!0?"Да":"Нет"),1)]}),"cell(Status)":a(o=>{var n;return[Ke,l(d,{status:(n=o.item)==null?void 0:n.StatusID,"status-table":"Contracts",onClick:h=>{var T;return E((T=o.item)==null?void 0:T.ID)}},null,8,["status","onClick"])]}),"cell(Orders)":a(o=>{var n;return[Qe,e("span",null,f((n=o.item)==null?void 0:n.Orders),1)]}),"cell(controls)":a(o=>{var n;return[e("div",We,[((n=o.item)==null?void 0:n.TypeID)!="NaturalPartner"?(L(),j(xe,{key:0,onClick:h=>{var T;return x((T=o.item)==null?void 0:T.ID)}},{default:a(()=>[U("Пополнить баланс")]),_:2},1032,["onClick"])):Q("",!0),e("div",Xe,[l(ae,{class:"dropdown-dots",right:""},{"button-content":a(()=>[e("div",et,[l(g)])]),default:a(()=>[l(M,null,{default:a(()=>{var h;return[l(S,{to:`/ContractScheme/${(h=o.item)==null?void 0:h.ID}`},{default:a(()=>[U("Открыть")]),_:2},1032,["to"])]}),_:2},1024),l(M,null,{default:a(()=>{var h;return[l(S,{to:`/ContractSettings/${(h=o.item)==null?void 0:h.ID}`},{default:a(()=>[U("Изменить тип учёта")]),_:2},1032,["to"])]}),_:2},1024),l(M,{onClick:h=>{var T;return r((T=o.item)==null?void 0:T.ID)}},{default:a(()=>[U("Загрузить договор")]),_:2},1032,["onClick"])]),_:2},1024)])])]}),_:1},8,["items","fields","filter","per-page","sort-desc","sort-by","sort-direction","current-page"]),l(oe,{total_rows:F.value,onUpdatePage:t},null,8,["total_rows"])])}}},st=O(tt,[["__scopeId","data-v-39022284"]]),ot=[{key:"ID",label:"Номер",variant:"td-gray td-order",sortable:!0},{key:"Name",label:"Профиль",variant:"td-blue"},{key:"TemplateID",label:"Тип",variant:"td-gray td-order"},{key:"StatusID",label:"Статус",sortable:!0,variant:"td-gray td-order"},{key:"StatusDate",label:"От статуса",sortable:!0,variant:"td-default td-order"}];const A=_=>(G("data-v-55858f0b"),_=_(),z(),_),at={class:"contracts-profile-list"},nt=A(()=>e("span",{class:"td-mobile"},"Номер",-1)),lt={class:"d-block"},rt=A(()=>e("span",{class:"td-mobile"},"Номер",-1)),ct=A(()=>e("span",{class:"td-mobile"},"От статуса",-1)),it={class:"d-block"},_t=A(()=>e("span",{class:"td-mobile"},"Тип",-1)),ut=A(()=>e("span",{class:"td-mobile"},"Статус",-1)),dt={__name:"ContractsProfileList",props:["search"],setup(_){const b=_;Y();const p=J(),c=W(),i=Z("emitter"),y=I("ID"),C=I(!0),$=I("desc"),u=I(10),m=I(1),D=P(()=>{var t,r;return(r=(t=Object==null?void 0:Object.keys(p==null?void 0:p.profileList))==null?void 0:t.map(s=>p==null?void 0:p.profileList[s]))==null?void 0:r.reverse()}),V=P(()=>{var t,r;return(r=(t=c.ConfigList)==null?void 0:t.Profiles)==null?void 0:r.Templates}),N=P(()=>{var t;return(t=D.value.filter(r=>{var s;return r.Name.toLowerCase().includes(((s=b.search)==null?void 0:s.toLowerCase())||"")}))==null?void 0:t.length});function H(t={}){u.value=t==null?void 0:t.per_page,m.value=t==null?void 0:t.current_page}function F(t,r){r.target.closest("button, a, .clickable-element")||i.emit("open-modal",{component:"ProfileInfo",data:{data:t,template:V.value[t.TemplateID]}})}function E(t){return Math.floor((new Date().getTime()-t*1e3)/(1e3*60*60*24))}function x(t){i.emit("open-modal",{component:"StatusHistory",data:{modeID:"Profiles",rowID:t}})}return(t,r)=>{const s=te,v=ee,k=oe;return L(),R("div",at,[l(v,{class:"basic-table",items:D.value,fields:B(ot),"show-empty":!0,filterable:[],filter:b.search,"per-page":u.value,"sort-desc":C.value,"onUpdate:sortDesc":r[0]||(r[0]=d=>C.value=d),"sort-by":y.value,"onUpdate:sortBy":r[1]||(r[1]=d=>y.value=d),"sort-direction":$.value,"current-page":m.value,"empty-text":"У вас пока нет профилей.","empty-filtered-text":"Профили не найдены.",onRowClicked:r[2]||(r[2]=(d,g,S)=>F(d,S))},{"cell(ID)":a(d=>{var g;return[nt,e("span",lt,f(B(X).transformContractNumber((g=d.item)==null?void 0:g.ID)),1)]}),"cell(Name)":a(d=>{var g;return[rt,e("span",null,f((g=d.item)==null?void 0:g.Name),1)]}),"cell(StatusDate)":a(d=>{var g;return[ct,e("span",it,f(E((g=d.item)==null?void 0:g.StatusDate))+" дн.",1)]}),"cell(TemplateID)":a(d=>{var g,S;return[_t,e("span",null,f((S=V.value[(g=d.item)==null?void 0:g.TemplateID])==null?void 0:S.Name),1)]}),"cell(StatusID)":a(d=>[ut,l(s,{class:"clickable-element",status:d.value,"status-table":"Profiles",onClick:g=>{var S;return x((S=d.item)==null?void 0:S.ID)}},null,8,["status","onClick"])]),_:1},8,["items","fields","filter","per-page","sort-desc","sort-by","sort-direction","current-page"]),l(k,{total_rows:N.value,onUpdatePage:H},null,8,["total_rows"])])}}},pt=O(dt,[["__scopeId","data-v-55858f0b"]]);const mt={class:"section"},vt={class:"container"},ft={class:"list-form"},bt={class:"contracts__page"},gt={__name:"Contracts",async setup(_){var F,E;let b,p;const c=ue(),i=Y(),y=J(),C=Z("emitter"),$=[{value:"Contracts",name:"Договора"},{value:"Profiles",name:"Профили"}],u=P(()=>m.value==="Contracts"?"Договора":"Профили"),m=I("Contracts"),D=I(""),V=P(()=>{var x,t;return(t=(x=Object.keys(y==null?void 0:y.contractsList))==null?void 0:x.map(r=>y==null?void 0:y.contractsList[r]))==null?void 0:t.reverse()});function N(){m.value==="Contracts"?i.push({path:"/ContractMake"}):i.push({path:"/ProfileMake"})}function H(){C.emit("open-modal",{component:"FundsTransfer"})}return(F=c==null?void 0:c.query)!=null&&F.section?m.value=(E=c.query)==null?void 0:E.section:i.replace({query:{section:"Contracts"}}),q(m,()=>{i.replace({query:{section:m.value}})}),[b,p]=K(()=>y.fetchContracts()),await b,p(),[b,p]=K(()=>y.fetchProfiles()),await b,p(),(x,t)=>{const r=ce,s=re,v=le;return L(),R(me,null,[l(Se),e("div",mt,[e("div",vt,[l(fe,{modelValue:m.value,"onUpdate:modelValue":t[0]||(t[0]=k=>m.value=k),tabs:$},null,8,["modelValue"]),l(ve,null,{title:a(()=>[U(f(u.value),1)]),buttons:a(()=>[de(l(r,{label:"Перевод средств",onClick:t[1]||(t[1]=k=>H())},null,512),[[pe,m.value==="Contracts"&&V.value.length>1]]),l(r,{label:m.value==="Contracts"?"Создать договор":"Создать профиль",onClick:N},null,8,["label"])]),_:1}),l(s),e("div",ft,[l(v,{modelValue:D.value,"onUpdate:modelValue":t[2]||(t[2]=k=>D.value=k)},null,8,["modelValue"])]),e("div",bt,[m.value==="Contracts"?(L(),j(st,{key:0,search:D.value},null,8,["search"])):(L(),j(pt,{key:1,search:D.value},null,8,["search"]))])])])],64)}}},Ut=O(gt,[["__scopeId","data-v-463d4c40"]]);export{Ut as default};

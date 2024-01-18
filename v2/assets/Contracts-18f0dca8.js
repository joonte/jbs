import{_ as Q}from"./FormInputSearch-de4e3004.js";import{_ as W}from"./ClausesKeeper-b9906182.js";import{_ as X}from"./ButtonDefault-cd183bf8.js";import{_ as w,o as D,c as L,a as e,m as U,b as c,r as g,h as z,w as a,t as v,L as k,k as Y,p as R,l as O,e as A,g as T,j as Z,ao as H,d as B,u as ee,R as te,a6 as se,af as j,x as G}from"./index-1c309346.js";import{u as M}from"./contracts-7b919c67.js";import{F as oe}from"./FormTabs-4e851a55.js";import{F as ae,z as ne,d as le,C as q}from"./bootstrap-vue-next.es-b799f76d.js";import{_ as re}from"./IconDots-391abd93.js";import{f as ce,a as _e}from"./useTimeFunction-8602dd60.js";import{_ as ie}from"./IconCard-ee273855.js";import{s as ue}from"./multiselect-b7ad6876.js";import{S as pe}from"./StatusBadge-a49537d5.js";import"./IconPlus-ea6f8c21.js";import"./IconSearch-373e7b4f.js";import"./IconClose-7cf50ca7.js";const de={},me={class:"page-title"},ve={class:"page-title__title"},fe={class:"page-title__buttons"};function be(l,u){return D(),L("div",me,[e("div",ve,[U(l.$slots,"title",{},void 0,!0)]),e("div",fe,[U(l.$slots,"buttons",{},void 0,!0)])])}const ge=w(de,[["render",be],["__scopeId","data-v-b876c21b"]]),ye=[{key:"ID",label:"Номер",variant:"td-gray td-order",sortable:!0},{key:"CreateDate",label:"Дата Создания",variant:"td-gray td-order",sortable:!0},{key:"Customer",label:"Договор"},{key:"Orders",label:"Количество подключенных услуг",sortable:!0,variant:"td-gray td-order"},{key:"Balance",label:"Баланс",sortable:!0,variant:"td-gray td-order"},{key:"controls",label:"",variant:"td-controls"}];const he={class:"button-payment"},Ce={class:"button-payment__text"},Ie={__name:"FormButtonPayment",setup(l){return(u,i)=>(D(),L("button",he,[c(ie,{class:"button-payment__icon"}),e("span",Ce,[U(u.$slots,"default",{},void 0,!0)])]))}},$e=w(Ie,[["__scopeId","data-v-fcb8fa3c"]]);const J=l=>(R("data-v-64d83888"),l=l(),O(),l),De={key:0,class:"table-controls"},ke=J(()=>e("span",{class:"text-success"},"First",-1)),Te=J(()=>e("span",{class:"text-info"},"Last",-1)),we={class:"multiselect-multiple-label"},Le={class:"multiselect-option"},Pe={__name:"PaginationBlock",props:["modelValue","total_rows"],emits:["update:modelValue","updatePage"],setup(l,{emit:u}){const i=l,p=[10,25,50,100],_=g({current_page:1,per_page:10});return z(_.value,()=>{u("updatePage",_.value)}),(C,f)=>{const b=ae;return i.total_rows>0?(D(),L("div",De,[c(b,{modelValue:_.value.current_page,"onUpdate:modelValue":f[0]||(f[0]=n=>_.value.current_page=n),"total-rows":i.total_rows,"per-page":_.value.per_page,"first-number":"","last-number":""},{"first-text":a(()=>[ke]),"last-text":a(()=>[Te]),page:a(({page:n,active:I})=>[e("span",null,v((n-1)*_.value.per_page+1)+"-"+v(n*_.value.per_page),1)]),_:1},8,["modelValue","total-rows","per-page"]),c(k(ue),{class:"multiselect--white",modelValue:_.value.per_page,"onUpdate:modelValue":f[1]||(f[1]=n=>_.value.per_page=n),options:p,label:"name",openDirection:"top",onInput:f[2]||(f[2]=n=>_.value.current_page=1)},{singlelabel:a(({value:n})=>[e("div",we,[e("span",null,"Отображать "+v(n.value)+" строк",1)])]),option:a(({option:n})=>[e("div",Le,"Отображать "+v(n.name)+" строк",1)]),_:1},8,["modelValue"])])):Y("",!0)}}},K=w(Pe,[["__scopeId","data-v-64d83888"]]);const V=l=>(R("data-v-3eda493e"),l=l(),O(),l),xe={class:"contracts-list"},Se=V(()=>e("span",{class:"td-mobile"},"Номер",-1)),Be={class:"td-column-heavy"},Ve={class:"table-td-gray"},Fe=V(()=>e("span",{class:"td-mobile"},"Дата Создания",-1)),Ne={class:"d-block"},Ee=V(()=>e("span",{class:"td-mobile"},"Договор",-1)),Ue={class:"td-column-heavy"},Re={class:"table-td-blue"},Oe=V(()=>e("span",{class:"td-mobile"},"Баланс",-1)),Ae=V(()=>e("span",{class:"td-mobile"},"Количество подключенных услуг",-1)),Me={class:"btn__group"},je={class:"dropdown-button"},Ge={class:"btn btn--dots"},ze={__name:"ContractsList",props:{search:{type:String,default:""}},setup(l){const u=l,i=A(),p=M(),_=g("CreateDate"),C=g(!0),f=g("desc"),b=g(10),n=g(1),I=T(()=>{var r,d;return(d=(r=Object.keys(p==null?void 0:p.contractsList))==null?void 0:r.map(o=>p==null?void 0:p.contractsList[o]))==null?void 0:d.reverse()}),P=T(()=>{var r;return(r=I.value.filter(d=>{var o;return d.Customer.toLowerCase().includes(((o=u.search)==null?void 0:o.toLowerCase())||"")}))==null?void 0:r.length});function h(r){i.push(`/Balance/${r}`)}function x(r={}){b.value=r==null?void 0:r.per_page,n.value=r==null?void 0:r.current_page}function S(r){p==null||p.contractDownload({ContractID:r})}return z(()=>u,()=>{console.log("123"),n.value=1}),(r,d)=>{const o=re,m=Z("router-link"),y=ne,N=le,E=q;return D(),L("div",xe,[c(E,{class:"basic-table",items:I.value,fields:k(ye),"show-empty":!0,filterable:[],filter:u.search,"per-page":b.value,"sort-desc":C.value,"onUpdate:sortDesc":d[0]||(d[0]=s=>C.value=s),"sort-by":_.value,"onUpdate:sortBy":d[1]||(d[1]=s=>_.value=s),"sort-direction":f.value,"current-page":n.value,"empty-text":"У вас пока нет договоров.","empty-filtered-text":"Договора не найдены."},{"cell(ID)":a(s=>{var t;return[Se,e("div",Be,[e("div",Ve,v(k(H).transformContractNumber((t=s.item)==null?void 0:t.ID)),1)])]}),"cell(CreateDate)":a(s=>{var t;return[Fe,e("span",Ne,v(k(ce)(k(_e)((t=s.item)==null?void 0:t.CreateDate))),1)]}),"cell(Customer)":a(s=>{var t;return[Ee,e("div",Ue,[e("div",Re,v((t=s.item)==null?void 0:t.Customer),1)])]}),"cell(Balance)":a(s=>{var t;return[Oe,e("span",null,v((t=s.item)==null?void 0:t.Balance)+" ₽",1)]}),"cell(Orders)":a(s=>{var t;return[Ae,e("span",null,v((t=s.item)==null?void 0:t.Orders),1)]}),"cell(controls)":a(s=>[e("div",Me,[c($e,{onClick:t=>{var $;return h(($=s.item)==null?void 0:$.ID)}},{default:a(()=>[B("Пополнить баланс")]),_:2},1032,["onClick"]),e("div",je,[c(N,{class:"dropdown-dots",right:""},{"button-content":a(()=>[e("div",Ge,[c(o)])]),default:a(()=>[c(y,null,{default:a(()=>{var t;return[c(m,{to:`/ContractScheme/${(t=s.item)==null?void 0:t.ID}`},{default:a(()=>[B("Открыть")]),_:2},1032,["to"])]}),_:2},1024),c(y,null,{default:a(()=>{var t;return[c(m,{to:`/ContractSettings/${(t=s.item)==null?void 0:t.ID}`},{default:a(()=>[B("Изменить тип учёта")]),_:2},1032,["to"])]}),_:2},1024),c(y,{onClick:t=>{var $;return S(($=s.item)==null?void 0:$.ID)}},{default:a(()=>[B("Загрузить договор")]),_:2},1032,["onClick"])]),_:2},1024)])])]),_:1},8,["items","fields","filter","per-page","sort-desc","sort-by","sort-direction","current-page"]),c(K,{total_rows:P.value,onUpdatePage:x},null,8,["total_rows"])])}}},He=w(ze,[["__scopeId","data-v-3eda493e"]]),qe=[{key:"ID",label:"Номер",variant:"td-gray td-order",sortable:!0},{key:"Name",label:"Профиль",variant:"td-blue"},{key:"TemplateID",label:"Тип",variant:"td-gray td-order"},{key:"StatusID",label:"Статус",sortable:!0,variant:"td-gray td-order"},{key:"StatusDate",label:"От статуса",sortable:!0,variant:"td-default td-order"}];const F=l=>(R("data-v-aab722c9"),l=l(),O(),l),Je={class:"contracts-profile-list"},Ke=F(()=>e("span",{class:"td-mobile"},"Номер",-1)),Qe={class:"d-block"},We=F(()=>e("span",{class:"td-mobile"},"Номер",-1)),Xe=F(()=>e("span",{class:"td-mobile"},"От статуса",-1)),Ye={class:"d-block"},Ze=F(()=>e("span",{class:"td-mobile"},"Тип",-1)),et=F(()=>e("span",{class:"td-mobile"},"Статус",-1)),tt={__name:"ContractsProfileList",props:["search"],setup(l){const u=l;A();const i=M(),p=ee(),_=te("emitter"),C=g("ID"),f=g(!0),b=g("desc"),n=g(10),I=g(1),P=T(()=>{var o,m;return(m=(o=Object==null?void 0:Object.keys(i==null?void 0:i.profileList))==null?void 0:o.map(y=>i==null?void 0:i.profileList[y]))==null?void 0:m.reverse()}),h=T(()=>{var o,m;return(m=(o=p.ConfigList)==null?void 0:o.Profiles)==null?void 0:m.Templates}),x=T(()=>{var o;return(o=P.value.filter(m=>{var y;return m.Name.toLowerCase().includes(((y=u.search)==null?void 0:y.toLowerCase())||"")}))==null?void 0:o.length});function S(o={}){n.value=o==null?void 0:o.per_page,I.value=o==null?void 0:o.current_page}function r(o){_.emit("open-modal",{component:"ProfileInfo",data:{data:o,template:h.value[o.TemplateID]}})}function d(o){return Math.floor((new Date().getTime()-o*1e3)/(1e3*60*60*24))}return(o,m)=>{const y=pe,N=q,E=K;return D(),L("div",Je,[c(N,{class:"basic-table",items:P.value,fields:k(qe),"show-empty":!0,filterable:[],filter:u.search,"per-page":n.value,"sort-desc":f.value,"onUpdate:sortDesc":m[0]||(m[0]=s=>f.value=s),"sort-by":C.value,"onUpdate:sortBy":m[1]||(m[1]=s=>C.value=s),"sort-direction":b.value,"current-page":I.value,"empty-text":"У вас пока нет профилей.","empty-filtered-text":"Профили не найдены.",onRowClicked:r},{"cell(ID)":a(s=>{var t;return[Ke,e("span",Qe,v(k(H).transformContractNumber((t=s.item)==null?void 0:t.ID)),1)]}),"cell(Name)":a(s=>{var t;return[We,e("span",null,v((t=s.item)==null?void 0:t.Name),1)]}),"cell(StatusDate)":a(s=>{var t;return[Xe,e("span",Ye,v(d((t=s.item)==null?void 0:t.StatusDate))+" дн.",1)]}),"cell(TemplateID)":a(s=>{var t,$;return[Ze,e("span",null,v(($=h.value[(t=s.item)==null?void 0:t.TemplateID])==null?void 0:$.Name),1)]}),"cell(StatusID)":a(s=>[et,c(y,{status:s.value,"status-table":"Profiles"},null,8,["status"])]),_:1},8,["items","fields","filter","per-page","sort-desc","sort-by","sort-direction","current-page"]),c(E,{total_rows:x.value,onUpdatePage:S},null,8,["total_rows"])])}}},st=w(tt,[["__scopeId","data-v-aab722c9"]]);const ot={class:"section"},at={class:"container"},nt={class:"list-form"},lt={class:"contracts__page"},rt={__name:"Contracts",async setup(l){let u,i;se();const p=A(),_=M(),C=[{value:"contracts",name:"Контрагенты"},{value:"Profiles",name:"Профили"}],f=T(()=>b.value==="contracts"?"Контрагенты":"Профили"),b=g("Contracts"),n=g("");function I(){b.value==="contracts"?p.push({path:"/ContractMake"}):p.push({path:"/ProfileMake"})}return[u,i]=j(()=>_.fetchContracts()),await u,i(),[u,i]=j(()=>_.fetchProfiles()),await u,i(),(P,h)=>{const x=X,S=W,r=Q;return D(),L("div",ot,[e("div",at,[c(oe,{tabs:C,modelValue:b.value,"onUpdate:modelValue":h[0]||(h[0]=d=>b.value=d)},null,8,["modelValue"]),c(ge,null,{title:a(()=>[B(v(f.value),1)]),buttons:a(()=>[c(x,{label:b.value==="contracts"?"Создать контрагента":"Создать профиль",onClick:I},null,8,["label"])]),_:1}),c(S),e("div",nt,[c(r,{modelValue:n.value,"onUpdate:modelValue":h[1]||(h[1]=d=>n.value=d)},null,8,["modelValue"])]),e("div",lt,[b.value==="Contracts"?(D(),G(He,{key:0,search:n.value},null,8,["search"])):(D(),G(st,{key:1,search:n.value},null,8,["search"]))])])])}}},$t=w(rt,[["__scopeId","data-v-04973f85"]]);export{$t as default};

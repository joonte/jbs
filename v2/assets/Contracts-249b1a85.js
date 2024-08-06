import{_ as at}from"./FormInputSearch-48364fb5.js";import{_ as nt}from"./ClausesKeeper-55c14b7f.js";import{_ as lt}from"./ButtonDefault-dc248ec6.js";import{_ as O,o as x,c as A,b as r,a as t,m as rt,e as q,u as Y,S as J,r as v,g as w,h as ct,i as K,j as it,w as n,t as y,M as E,ao as Q,y as j,d as H,k as _t,p as W,l as X,a7 as ut,ag as z,F as dt}from"./index-83b4b7ba.js";import{u as G}from"./contracts-623ec7a8.js";import{S as pt}from"./SlotPageTitle-d9f66ca4.js";import{F as mt}from"./FormTabs-ea57b2d9.js";import{f as ft,z as bt,d as vt,C as Z}from"./bootstrap-vue-next.es-8f3ae81a.js";import{_ as yt}from"./IconDots-a66f045d.js";import{S as tt}from"./StatusBadge-0d170ac0.js";import{f as Ct,a as gt}from"./useTimeFunction-8602dd60.js";/* empty css                                                                       */import{_ as ht}from"./IconCard-2cc17a6b.js";import{P as et}from"./PaginationBlock-e9f2bc51.js";import{B as It}from"./BlockBalanceAgreement-c5fad736.js";import"./IconPlus-07b0a078.js";import"./IconSearch-30270af1.js";import"./IconClose-86aafc4e.js";import"./multiselect-f2568231.js";import"./IconArrow-80c72216.js";const kt=[{key:"ID",label:"Номер",variant:"td-gray td-order",sortable:!0},{key:"CreateDate",label:"Дата Создания",variant:"td-gray td-order",sortable:!0},{key:"Customer",label:"Договор"},{key:"TypeID",label:"Тип",variant:"td-gray td-order"},{key:"Orders",label:"Количество подключенных услуг",sortable:!0,variant:"td-gray td-order"},{key:"Balance",label:"Баланс",sortable:!0,variant:"td-gray td-order"},{key:"IsHidden",label:"Скрыт ?",variant:"td-gray td-order"},{key:"Status",label:"Статус",variant:"td-gray td-order"},{key:"controls",label:"",variant:"td-controls"}];const Dt={class:"button-payment"},Tt={class:"button-payment__text"},St={__name:"FormButtonPayment",setup(f){return(b,u)=>(x(),A("button",Dt,[r(ht,{class:"button-payment__icon"}),t("span",Tt,[rt(b.$slots,"default",{},void 0,!0)])]))}},Pt=O(St,[["__scopeId","data-v-fcb8fa3c"]]);const S=f=>(W("data-v-a7c81d2e"),f=f(),X(),f),Lt={class:"contracts-list"},$t=S(()=>t("span",{class:"td-mobile"},"Номер",-1)),Bt={class:"td-column-heavy"},xt={class:"table-td-gray"},wt=S(()=>t("span",{class:"td-mobile"},"Дата Создания",-1)),Nt={class:"d-block"},Et=S(()=>t("span",{class:"td-mobile"},"Договор",-1)),Ft={class:"td-column-heavy"},Vt={class:"table-td-blue"},Ht=S(()=>t("span",{class:"td-mobile"},"Тип договора",-1)),Rt=S(()=>t("span",{class:"td-mobile"},"Баланс",-1)),Ut=S(()=>t("span",{class:"td-mobile"},"Скрыт ?",-1)),Ot=S(()=>t("span",{class:"td-mobile"},"Статус",-1)),At=S(()=>t("span",{class:"td-mobile"},"Количество подключенных услуг",-1)),Mt={class:"btn__group"},jt={class:"dropdown-button"},qt={class:"btn btn--dots"},Gt={__name:"ContractsList",props:{search:{type:String,default:""}},setup(f){const b=f,u=q(),c=G(),I=Y(),P=J("emitter"),L=v("CreateDate"),N=v(!0),d=v("desc"),g=v(10),k=v(1);v(!1);const $=e=>{c.booleanEdit({TableID:"Contracts",ColumnID:"IsHidden",RowID:e.ID}).then(()=>{c.fetchContracts()})},B=w(()=>{var e,p;return(p=(e=Object.keys(c==null?void 0:c.contractsList))==null?void 0:e.map(T=>c==null?void 0:c.contractsList[T]))==null?void 0:p.reverse()}),U=w(()=>{var e,p;return(p=(e=I.ConfigList)==null?void 0:e.Contracts)==null?void 0:p.Types}),D=w(()=>{var e;return(e=B.value.filter(p=>{var T;return p.Customer.toLowerCase().includes(((T=b.search)==null?void 0:T.toLowerCase())||"")}))==null?void 0:e.length});function F(e){P.emit("open-modal",{component:"StatusHistory",data:{modeID:"Contracts",rowID:e}})}function V(e){u.push(`/Balance/${e}`)}ct(()=>{});function s(e={}){g.value=e==null?void 0:e.per_page,k.value=e==null?void 0:e.current_page}function l(e){c==null||c.contractDownload({ContractID:e})}return K(()=>b,()=>{k.value=1}),(e,p)=>{const T=ft,i=tt,_=yt,h=it("router-link"),M=bt,ot=vt,st=Z;return x(),A("div",Lt,[r(st,{class:"basic-table",items:B.value,fields:E(kt),"show-empty":!0,filterable:[],filter:b.search,"per-page":g.value,"sort-desc":N.value,"onUpdate:sortDesc":p[0]||(p[0]=o=>N.value=o),"sort-by":L.value,"onUpdate:sortBy":p[1]||(p[1]=o=>L.value=o),"sort-direction":d.value,"current-page":k.value,"empty-text":"У вас пока нет договоров.","empty-filtered-text":"Договора не найдены."},{"cell(ID)":n(o=>{var a;return[$t,t("div",Bt,[t("div",xt,y(E(Q).transformContractNumber((a=o.item)==null?void 0:a.ID)),1)])]}),"cell(CreateDate)":n(o=>{var a;return[wt,t("span",Nt,y(E(Ct)(E(gt)((a=o.item)==null?void 0:a.CreateDate))),1)]}),"cell(Customer)":n(o=>{var a;return[Et,t("div",Ft,[t("div",Vt,y((a=o.item)==null?void 0:a.Customer),1)])]}),"cell(TypeID)":n(o=>{var a,m;return[Ht,t("span",null,y((m=U.value[(a=o.item)==null?void 0:a.TypeID])==null?void 0:m.Name),1)]}),"cell(Balance)":n(o=>{var a;return[Rt,t("span",null,y((a=o.item)==null?void 0:a.Balance)+" ₽",1)]}),"cell(IsHidden)":n(o=>{var a,m;return[Ut,r(T,{class:"rule-checkbox",name:"checkbox",checked:(a=o.item)==null?void 0:a.IsHidden,modelValue:o.item.IsChecked,"onUpdate:modelValue":C=>o.item.IsChecked=C,onChange:C=>$(o.item)},null,8,["checked","modelValue","onUpdate:modelValue","onChange"]),t("span",null,y(((m=o.item)==null?void 0:m.IsHidden)===!0?"Да":"Нет"),1)]}),"cell(Status)":n(o=>{var a;return[Ot,r(i,{status:(a=o.item)==null?void 0:a.StatusID,"status-table":"Contracts",onClick:m=>{var C;return F((C=o.item)==null?void 0:C.ID)}},null,8,["status","onClick"])]}),"cell(Orders)":n(o=>{var a;return[At,t("span",null,y((a=o.item)==null?void 0:a.Orders),1)]}),"cell(controls)":n(o=>{var a;return[t("div",Mt,[((a=o.item)==null?void 0:a.TypeID)!="NaturalPartner"?(x(),j(Pt,{key:0,onClick:m=>{var C;return V((C=o.item)==null?void 0:C.ID)}},{default:n(()=>[H("Пополнить баланс")]),_:2},1032,["onClick"])):_t("",!0),t("div",jt,[r(ot,{class:"dropdown-dots",right:""},{"button-content":n(()=>[t("div",qt,[r(_)])]),default:n(()=>[r(M,null,{default:n(()=>{var m;return[r(h,{to:`/ContractScheme/${(m=o.item)==null?void 0:m.ID}`},{default:n(()=>[H("Открыть")]),_:2},1032,["to"])]}),_:2},1024),r(M,null,{default:n(()=>{var m;return[r(h,{to:`/ContractSettings/${(m=o.item)==null?void 0:m.ID}`},{default:n(()=>[H("Изменить тип учёта")]),_:2},1032,["to"])]}),_:2},1024),r(M,{onClick:m=>{var C;return l((C=o.item)==null?void 0:C.ID)}},{default:n(()=>[H("Загрузить договор")]),_:2},1032,["onClick"])]),_:2},1024)])])]}),_:1},8,["items","fields","filter","per-page","sort-desc","sort-by","sort-direction","current-page"]),r(et,{total_rows:D.value,onUpdatePage:s},null,8,["total_rows"])])}}},zt=O(Gt,[["__scopeId","data-v-a7c81d2e"]]),Yt=[{key:"ID",label:"Номер",variant:"td-gray td-order",sortable:!0},{key:"Name",label:"Профиль",variant:"td-blue"},{key:"TemplateID",label:"Тип",variant:"td-gray td-order"},{key:"StatusID",label:"Статус",sortable:!0,variant:"td-gray td-order"},{key:"StatusDate",label:"От статуса",sortable:!0,variant:"td-default td-order"}];const R=f=>(W("data-v-55858f0b"),f=f(),X(),f),Jt={class:"contracts-profile-list"},Kt=R(()=>t("span",{class:"td-mobile"},"Номер",-1)),Qt={class:"d-block"},Wt=R(()=>t("span",{class:"td-mobile"},"Номер",-1)),Xt=R(()=>t("span",{class:"td-mobile"},"От статуса",-1)),Zt={class:"d-block"},te=R(()=>t("span",{class:"td-mobile"},"Тип",-1)),ee=R(()=>t("span",{class:"td-mobile"},"Статус",-1)),oe={__name:"ContractsProfileList",props:["search"],setup(f){const b=f;q();const u=G(),c=Y(),I=J("emitter"),P=v("ID"),L=v(!0),N=v("desc"),d=v(10),g=v(1),k=w(()=>{var s,l;return(l=(s=Object==null?void 0:Object.keys(u==null?void 0:u.profileList))==null?void 0:s.map(e=>u==null?void 0:u.profileList[e]))==null?void 0:l.reverse()}),$=w(()=>{var s,l;return(l=(s=c.ConfigList)==null?void 0:s.Profiles)==null?void 0:l.Templates}),B=w(()=>{var s;return(s=k.value.filter(l=>{var e;return l.Name.toLowerCase().includes(((e=b.search)==null?void 0:e.toLowerCase())||"")}))==null?void 0:s.length});function U(s={}){d.value=s==null?void 0:s.per_page,g.value=s==null?void 0:s.current_page}function D(s,l){l.target.closest("button, a, .clickable-element")||I.emit("open-modal",{component:"ProfileInfo",data:{data:s,template:$.value[s.TemplateID]}})}function F(s){return Math.floor((new Date().getTime()-s*1e3)/(1e3*60*60*24))}function V(s){I.emit("open-modal",{component:"StatusHistory",data:{modeID:"Profiles",rowID:s}})}return(s,l)=>{const e=tt,p=Z,T=et;return x(),A("div",Jt,[r(p,{class:"basic-table",items:k.value,fields:E(Yt),"show-empty":!0,filterable:[],filter:b.search,"per-page":d.value,"sort-desc":L.value,"onUpdate:sortDesc":l[0]||(l[0]=i=>L.value=i),"sort-by":P.value,"onUpdate:sortBy":l[1]||(l[1]=i=>P.value=i),"sort-direction":N.value,"current-page":g.value,"empty-text":"У вас пока нет профилей.","empty-filtered-text":"Профили не найдены.",onRowClicked:l[2]||(l[2]=(i,_,h)=>D(i,h))},{"cell(ID)":n(i=>{var _;return[Kt,t("span",Qt,y(E(Q).transformContractNumber((_=i.item)==null?void 0:_.ID)),1)]}),"cell(Name)":n(i=>{var _;return[Wt,t("span",null,y((_=i.item)==null?void 0:_.Name),1)]}),"cell(StatusDate)":n(i=>{var _;return[Xt,t("span",Zt,y(F((_=i.item)==null?void 0:_.StatusDate))+" дн.",1)]}),"cell(TemplateID)":n(i=>{var _,h;return[te,t("span",null,y((h=$.value[(_=i.item)==null?void 0:_.TemplateID])==null?void 0:h.Name),1)]}),"cell(StatusID)":n(i=>[ee,r(e,{class:"clickable-element",status:i.value,"status-table":"Profiles",onClick:_=>{var h;return V((h=i.item)==null?void 0:h.ID)}},null,8,["status","onClick"])]),_:1},8,["items","fields","filter","per-page","sort-desc","sort-by","sort-direction","current-page"]),r(T,{total_rows:B.value,onUpdatePage:U},null,8,["total_rows"])])}}},se=O(oe,[["__scopeId","data-v-55858f0b"]]);const ae={class:"section"},ne={class:"container"},le={class:"list-form"},re={class:"contracts__page"},ce={__name:"Contracts",async setup(f){var $,B;let b,u;const c=ut(),I=q(),P=G(),L=[{value:"Contracts",name:"Договора"},{value:"Profiles",name:"Профили"}],N=w(()=>d.value==="Contracts"?"Договора":"Профили"),d=v("Contracts"),g=v("");function k(){d.value==="Contracts"?I.push({path:"/ContractMake"}):I.push({path:"/ProfileMake"})}return($=c==null?void 0:c.query)!=null&&$.section?d.value=(B=c.query)==null?void 0:B.section:I.replace({query:{section:"Contracts"}}),K(d,()=>{I.replace({query:{section:d.value}})}),[b,u]=z(()=>P.fetchContracts()),await b,u(),[b,u]=z(()=>P.fetchProfiles()),await b,u(),(U,D)=>{const F=lt,V=nt,s=at;return x(),A(dt,null,[r(It),t("div",ae,[t("div",ne,[r(mt,{tabs:L,modelValue:d.value,"onUpdate:modelValue":D[0]||(D[0]=l=>d.value=l)},null,8,["modelValue"]),r(pt,null,{title:n(()=>[H(y(N.value),1)]),buttons:n(()=>[r(F,{label:d.value==="Contracts"?"Создать договор":"Создать профиль",onClick:k},null,8,["label"])]),_:1}),r(V),t("div",le,[r(s,{modelValue:g.value,"onUpdate:modelValue":D[1]||(D[1]=l=>g.value=l)},null,8,["modelValue"])]),t("div",re,[d.value==="Contracts"?(x(),j(zt,{key:0,search:g.value},null,8,["search"])):(x(),j(se,{key:1,search:g.value},null,8,["search"]))])])])],64)}}},$e=O(ce,[["__scopeId","data-v-2a04373f"]]);export{$e as default};

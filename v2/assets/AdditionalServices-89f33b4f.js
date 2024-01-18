import{j as U,o as D,c as B,a as i,t as k,b as d,x as h,w as _,d as T,F as H,p as G,l as K,_ as E,e as F,f as M,r as C,g as V,v as P,n as j,k as J,a6 as Q,R as X,i as Y}from"./index-1c309346.js";import{u as Z}from"./services-4f3539cc.js";import{u as R}from"./contracts-7b919c67.js";import"./postings-da2063b0.js";import{u as $}from"./globalActions-8efdfe11.js";import{_ as z}from"./IconCard-ee273855.js";import{E as N}from"./EmptyStateBlock-5a6a4cd4.js";import{S as q}from"./StatusBadge-a49537d5.js";import{s as ee}from"./servicesStatuses-6293b6ac.js";import{_ as te}from"./HelperComponent-3eb94393.js";import{z as se,k as oe,d as ne,C as ce}from"./bootstrap-vue-next.es-b799f76d.js";import{_ as re}from"./IconDots-391abd93.js";import{_ as ie}from"./ClausesKeeper-b9906182.js";import{_ as ae}from"./ButtonDefault-cd183bf8.js";import{s as le}from"./multiselect-b7ad6876.js";import{_ as de}from"./IconArrow-6927efe5.js";import"./IconClose-7cf50ca7.js";const _e=[{key:"ID",label:"Заказ",variant:"td-gray td-order",sortable:!0},{key:"ContractID",label:"Договор",variant:"td-blue"},{key:"Keys",label:"Ключевые поля",variant:"td-gray td-order"},{key:"StatusID",label:"Статус",variant:"",sortable:!0},{key:"UserNotice",label:"Примечание",variant:""},{key:"controls",label:"",variant:"td-controls"}],A=n=>(G("data-v-f2c37688"),n=n(),K(),n),ue={class:"section-header"},ve={class:"section-title__nowrap"},me={class:"section-title"},Ie={class:"btn__group"},pe=A(()=>i("span",{class:"td-mobile"},"Заказ",-1)),De=A(()=>i("span",{class:"td-mobile"},"Договор",-1)),fe=A(()=>i("span",{class:"td-mobile"},"Статус",-1)),Se=A(()=>i("span",{class:"td-mobile"},"Комментарий",-1)),be={class:"service__controls-wrapper"},ge=["onClick"],ye={class:"btn btn--dots"};function ke(n,l,r,e,y,f){var s,L,x;const v=ae,S=ie,m=q,u=U("Helper"),I=z,p=re,o=U("router-link"),t=se,c=oe,b=ne,O=ce,w=N;return D(),B(H,null,[i("div",ue,[i("div",ve,[i("h1",me,k((s=r.sectionData)==null?void 0:s.Item),1),i("div",Ie,[d(v,{label:"Заказать",onClick:l[0]||(l[0]=a=>e.navigateToOrder())})])]),d(S,{class:"additional-clauses",partition:`Header:${((L=r.sectionData)==null?void 0:L.Code)==="Default"?r.sectionID:`/${(x=r.sectionData)==null?void 0:x.Code}Orders`}`},null,8,["partition"])]),e.getOrders&&e.getOrders.length?(D(),h(O,{key:0,class:"basic-table",items:e.getOrders,fields:e.oneTimeWorksFields,"sort-by":e.sortBy,"onUpdate:sortBy":l[1]||(l[1]=a=>e.sortBy=a),"sort-desc":e.sortDesc,"onUpdate:sortDesc":l[2]||(l[2]=a=>e.sortDesc=a),"sort-direction":e.sortDirection},{"cell(ID)":_(a=>[pe,i("span",null,k(a.value),1)]),"cell(ContractID)":_(a=>{var g;return[De,i("span",null,k((g=e.getContracts[a.value])==null?void 0:g.Customer),1)]}),"cell(StatusID)":_(a=>[fe,d(m,{status:a.value,"status-table":"Orders"},null,8,["status"])]),"cell(UserNotice)":_(a=>{var g;return[Se,d(u,{"user-notice":a.value,id:(g=a.item)==null?void 0:g.ID,emit:"updateServicesList"},null,8,["user-notice","id"])]}),"cell(controls)":_(a=>[i("div",be,[i("div",{class:"btn btn--md btn--bold",onClick:g=>n.$router.push(`/ServiceOrderPay/${a.item.ID}`)},[d(I),T("Оплатить")],8,ge),d(b,{class:"dropdown-dots",right:""},{"button-content":_(()=>[i("div",ye,[d(p)])]),default:_(()=>[d(t,null,{default:_(()=>[d(o,{to:`/AdditionalServices/${a.item.ID}`},{default:_(()=>[T("Дополнительная информация")]),_:2},1032,["to"])]),_:2},1024),d(c),d(t,{class:"color-red",onClick:g=>n.$emit("deleteItem",a.item)},{default:_(()=>[T("Удалить")]),_:2},1032,["onClick"])]),_:2},1024)])]),_:1},8,["items","fields","sort-by","sort-desc","sort-direction"])):(D(),h(w,{key:1,class:"no-margin",label:"Вы еще не пользовались этой услугой"}))],64)}const he={components:{EmptyStateBlock:N,StatusBadge:q,IconCard:z,Helper:te},props:{sectionID:{type:String,default:""},sectionData:{type:Object,default:()=>({})}},emits:["deleteItem","updateServicesList"],setup(n){const l=F(),r=M(),e=C("ID"),y=C(!0),f=C("desc"),v=R(),S=V(()=>v==null?void 0:v.contractsList),m=V(()=>{var I,p;return(p=(I=Object.keys(r==null?void 0:r.userOrders).map(o=>{var t;return{...r==null?void 0:r.userOrders[o],ID:Number((t=r==null?void 0:r.userOrders[o])==null?void 0:t.ID)}}))==null?void 0:I.reverse())==null?void 0:p.filter(o=>o.ServiceID===(n==null?void 0:n.sectionID))});function u(){l.push(`/AdditionalServicesOrder?ServiceID=${n==null?void 0:n.sectionID}`)}return{authStore:r,getOrders:m,getContracts:S,sortBy:e,sortDesc:y,sortDirection:f,navigateToOrder:u,servicesStatuses:ee,oneTimeWorksFields:_e}}},W=E(he,[["render",ke],["__scopeId","data-v-f2c37688"]]),Ce={class:"section"},Oe={class:"container"},we={class:"section-header"},Be={class:"section-nav"},Ne=["onClick"],Ae={class:"multiselect-option"},Le={class:"section-body"};function Te(n,l,r,e,y,f){var I,p,o,t,c,b,O,w;const v=de,S=U("Multiselect"),m=W,u=N;return D(),B("div",Ce,[i("div",Oe,[i("div",we,[i("div",Be,[(D(!0),B(H,null,P((I=e.getServices)==null?void 0:I.slice(0,3),s=>(D(),B("div",{class:j(["section-nav__item",{"section-link":!0,"section-link-active":e.section===(s==null?void 0:s.ID)}]),onClick:L=>e.switchToSection(s==null?void 0:s.ID)},k(s==null?void 0:s.Item),11,Ne))),256)),((p=e.getServices)==null?void 0:p.length)>3?(D(),h(S,{key:0,modelValue:e.section,"onUpdate:modelValue":l[0]||(l[0]=s=>e.section=s),class:j({"multiselect--white additional-service__select":!0,"additional-service__select_active":(t=e.getServices)==null?void 0:t.slice(3,(o=e.getServices)==null?void 0:o.length).find(s=>(s==null?void 0:s.ID)===e.section)}),options:(b=e.getServices)==null?void 0:b.slice(3,(c=e.getServices)==null?void 0:c.length),label:"ID",onSelect:l[1]||(l[1]=s=>e.switchToSection(s))},{option:_(({option:s})=>[i("div",Ae,k(s==null?void 0:s.Item),1)]),caret:_(()=>[d(v,{class:"icon-arrow_caret"})]),_:1},8,["modelValue","class","options"])):J("",!0)])]),i("div",Le,[(O=e.getServices)!=null&&O.find(s=>s.ID===e.section)?(D(),h(m,{key:0,sectionID:e.section,sectionData:(w=e.getServices)==null?void 0:w.find(s=>s.ID===e.section),onDeleteItem:e.deleteItem},null,8,["sectionID","sectionData","onDeleteItem"])):(D(),h(u,{key:1,label:"Услуга не найдена"}))])])])}const Ue={components:{ServiceListSection:W,emptyStateBlock:N,Multiselect:le},async setup(){const n=Q(),l=F(),r=X("emitter"),e=Z(),y=$(),f=M(),v=R(),S=C(null),m=V(()=>{var o;return(o=Object.keys(e==null?void 0:e.ServicesList))==null?void 0:o.map(t=>{var c;return{...e==null?void 0:e.ServicesList[t],value:(c=e==null?void 0:e.ServicesList[t])==null?void 0:c.ID}}).filter(t=>(t==null?void 0:t.ServicesGroupID)!=="1000"&&(t==null?void 0:t.IsActive)&&!(t!=null&&t.IsHidden)).sort((t,c)=>Number(t==null?void 0:t.SortID)<Number(c==null?void 0:c.SortID)?-1:Number(t==null?void 0:t.SortID)>Number(c==null?void 0:c.SortID)?1:0)}),u=C(null);r.on("updateServicesList",()=>{f.fetchUserOrders()});function I(o){l.replace(`/AdditionalServices?ServiceID=${o}`),u.value=o}function p(o){y.deleteItem({TableID:"Orders",RowsIDs:o.ID}).then(({result:t,error:c})=>{t==="SUCCESS"&&f.fetchUserOrders()})}return Y(()=>{var o,t;(o=m.value)!=null&&o.find(c=>{var b;return(c==null?void 0:c.ID)===((b=n==null?void 0:n.query)==null?void 0:b.ServiceID)})?u.value=(t=n==null?void 0:n.query)==null?void 0:t.ServiceID:l.replace(`/AdditionalServices?ServiceID=${u.value}`)}),await e.fetchServices().then(()=>{var o,t;u.value=(t=(o=m.value)==null?void 0:o[0])==null?void 0:t.ID}),await v.fetchContracts(),await f.fetchUserOrders(),{section:u,deleteItem:p,getServices:m,additionalServicesValue:S,switchToSection:I}}},Ye=E(Ue,[["render",Te],["__scopeId","data-v-c6da02e5"]]);export{Ye as default};

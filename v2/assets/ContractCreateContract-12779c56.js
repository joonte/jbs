import{_ as $}from"./ButtonDefault-7609cc9e.js";import{s as U}from"./SimpleCheckbox-beea426c.js";import{_ as j}from"./ClausesKeeper-d8c25fea.js";import{_ as M,u as O,e as A,f as K,U as z,r as v,h as d,i as P,P as J,j as q,ag as G,k as H,o as p,c as f,b as r,a as s,w as Q,d as W,F as h,y as X,z as Y,l as Z,p as ee,m as te}from"./index-400feb76.js";import{u as oe}from"./contracts-d1f15c94.js";import{B as ae}from"./BlockSelectProfile-0afc4c30.js";import{B as se}from"./BlockBalanceAgreement-9b1029a8.js";/* empty css                                                                       */import"./IconClose-20ed8b3b.js";import"./multiselect-f4d64e06.js";import"./bootstrap-vue-next.es-03cca149.js";import"./IconArrow-10b76666.js";const V=u=>(ee("data-v-d304e6e3"),u=u(),te(),u),le={class:"section"},ne={class:"container"},ce={class:"section-header"},re=V(()=>s("h1",{class:"section-title"},"Создать договор",-1)),ue=V(()=>s("div",{class:"section-header"},[s("h2",{class:"section-block_title"},"Шаблон")],-1)),ie={class:"form"},de={class:"container-limited"},pe={__name:"ContractCreateContract",async setup(u){let C,k;const S=O(),n=oe(),c=A(),T=K(),B=z("emitter"),_=v(!1),i=v("Natural"),o=v(null),L=d(()=>S.ConfigList),a=d(()=>n==null?void 0:n.profileList),m=d(()=>Object.keys(a.value).map(e=>{if(a.value[e].TemplateID==="Natural"||a.value[e].TemplateID==="Juridical"||a.value[e].TemplateID==="Individual"||a.value[e].TemplateID==="NaturalPartner")return{value:a.value[e].ID,name:a.value[e].Name,contractID:a.value[e].ContractID}}).filter(e=>e.contractID===0)),I=e=>{e.key==="Enter"&&e.ctrlKey&&!x.value&&g()},x=d(()=>o.value===null||o.value===void 0);P(()=>{document.addEventListener("keyup",I)}),J(()=>{document.removeEventListener("keyup",I)});function g(){_.value=!0,n.contractMake({ProfileID:o.value,TypeID:i.value}).then(e=>{console.log(e),e.status==="success"&&(B.emit("open-modal",{component:"SuccessContract",data:"Договор успешно создан!"}),c.push("/Contracts")),_.value=!1})}function y(){var e,l;o.value=((l=(e=m.value)==null?void 0:e[0])==null?void 0:l.value)||null}return q(i,()=>{y()}),P(()=>{m.value.length<1&&c.push(`/ProfileMake?ProfileEmpty=true&Redirect=${c.options.history.state.back}`),console.log(T,"route contracts"),console.log(c.options,"options"),console.log(c.options.history.state.back,"full path"),y()}),[C,k]=G(()=>n.fetchProfiles()),await C,k(),(e,l)=>{var b,D;const E=H("router-link"),N=j,w=U,R=$;return p(),f(h,null,[r(se),s("div",le,[s("div",ne,[s("div",ce,[r(E,{class:"section-label",to:"/Contracts"},{default:Q(()=>[W("Мои договора")]),_:1}),re]),r(N),ue,s("div",ie,[(p(!0),f(h,null,X((D=(b=L.value)==null?void 0:b.Contracts)==null?void 0:D.Types,t=>(p(),f(h,null,[(t==null?void 0:t.ProfileTemplateID)!==""?(p(),Y(w,{key:0,label:t==null?void 0:t.Name,value:t==null?void 0:t.ProfileTemplateID,modelValue:i.value,"onUpdate:modelValue":l[0]||(l[0]=F=>i.value=F)},null,8,["label","value","modelValue"])):Z("",!0)],64))),256))]),s("div",de,[r(ae,{title:"Использовать профиль",items:m.value,modelValue:o.value,"onUpdate:modelValue":l[1]||(l[1]=t=>o.value=t)},null,8,["items","modelValue"]),r(R,{onClick:g,disabled:o.value===null||o.value===void 0,"is-loading":_.value,label:"Зарегистрировать"},null,8,["disabled","is-loading"])])])])],64)}}},Pe=M(pe,[["__scopeId","data-v-d304e6e3"]]);export{Pe as default};

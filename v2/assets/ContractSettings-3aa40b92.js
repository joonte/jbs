import{j as b,o as k,c as y,b as c,a as n,w as p,d as m,t as S,F as V,p as B,l as I,_ as x,r as u,a7 as T,g as w}from"./index-eaeb1281.js";import{u as U}from"./contracts-ac470ffa.js";import{s as f}from"./SimpleCheckbox-3ab55ce6.js";import{B as g}from"./BlockBalanceAgreement-11afb494.js";import{_ as D}from"./ButtonDefault-598fef28.js";import{_ as L}from"./ClausesKeeper-a152fc61.js";import"./bootstrap-vue-next.es-f4c478af.js";import"./IconArrow-ce1bc7b6.js";import"./IconClose-e8029cfe.js";const v=e=>(B("data-v-ff55ced9"),e=e(),I(),e),N={class:"section"},A={class:"container"},E={class:"section-header"},F={class:"breadcrumb"},j=v(()=>n("h1",{class:"section-title"},"Настройки договора",-1)),R=v(()=>n("div",{class:"section-header small-margin"},[n("h2",{class:"section-block_title"},"Способ отчетности")],-1)),$={class:"form"};function q(e,o,r,t,l,i){const s=g,d=b("router-link"),h=L,_=f,C=D;return k(),y(V,null,[c(s),n("div",N,[n("div",A,[n("div",E,[n("div",F,[c(d,{class:"section-label",to:"/Contracts"},{default:p(()=>[m("Договора /")]),_:1}),c(d,{class:"section-label",to:`/ContractScheme/${e.$route.params.id}`},{default:p(()=>{var a;return[m(S((a=t.getContract)==null?void 0:a.Customer),1)]}),_:1},8,["to"])]),j]),c(h),R,n("div",$,[c(_,{label:"По факту",value:"0",modelValue:t.reportType,"onUpdate:modelValue":o[0]||(o[0]=a=>t.reportType=a)},null,8,["modelValue"]),c(_,{label:"Ежемесечно",value:"1",modelValue:t.reportType,"onUpdate:modelValue":o[1]||(o[1]=a=>t.reportType=a)},null,8,["modelValue"]),c(C,{class:"settings-button","is-loading":t.isLoading,label:"Сохранить",onClick:o[2]||(o[2]=a=>t.changeSettings())},null,8,["is-loading"])])])])],64)}const z={components:{simpleCheckbox:f,BlockBalanceAgreement:g},async setup(){const e=U(),o=u(2),r=T(),t=u(!1),l=w(()=>{var s;return(s=e==null?void 0:e.contractsList)==null?void 0:s[r.params.id]});function i(){var s;t.value=!0,e.contractEdit({ContractID:(s=l.value)==null?void 0:s.ID,IsUponConsider:o.value}).then(()=>{t.value=!1})}return await e.fetchContracts().then(()=>{var s;o.value=(s=l.value)!=null&&s.IsUponConsider?1:0}),{reportType:o,getContract:l,isLoading:t,changeSettings:i}}},X=x(z,[["render",q],["__scopeId","data-v-ff55ced9"]]);export{X as default};

import{j as O,o as y,c as k,a as s,t as i,b as v,w as b,p as M,l as j,_ as w,r as C,R as x,g as f,i as V}from"./index-01c72474.js";import{u as B}from"./services-479ed473.js";import{s as L}from"./multiselect-b3bc32a2.js";import{_ as P}from"./ButtonDefault-6f60da99.js";const h=d=>(M("data-v-4f07e925"),d=d(),j(),d),E={class:"modal__body"},G=h(()=>s("div",{class:"list-item__title"},"Смена тарифного плана",-1)),H=h(()=>s("div",{class:"list-item__subtitle"},"Текущий тарифный план ",-1)),U=h(()=>s("div",{class:"modal__input-row"},null,-1)),W=h(()=>s("div",{class:"modal__input-label"},"Новый тарифный план",-1)),A={class:"list-form_col list-form_col--xl"},R=h(()=>s("div",{class:"search-field form-field"},null,-1)),q={class:"multiselect-multiple-label"},z={class:"multiselect-option"};function F(d,r,a,t,g,p){var _,D;const m=O("Multiselect"),S=P;return y(),k("div",E,[G,H,s("span",null,i((_=t.getDNSScheme[0])==null?void 0:_.name)+"/"+i((D=t.getDNSScheme[0])==null?void 0:D.cost),1),U,W,s("div",A,[R,v(m,{class:"multiselect--white",options:t.getDNSSchemes,type:"text",modelValue:t.selectedPackage,"onUpdate:modelValue":r[0]||(r[0]=e=>t.selectedPackage=e),placeholder:"Поиск"},{singlelabel:b(({value:e})=>[s("div",q,[s("span",null,i(e==null?void 0:e.Name)+"/"+i(e==null?void 0:e.CostMonth),1)])]),option:b(({option:e})=>[s("div",z,i(e==null?void 0:e.Name)+"/"+i(e==null?void 0:e.CostMonth),1)]),_:1},8,["options","modelValue"])]),v(S,{class:"modal__button",label:"Сменить","is-loading":t.isLoading,onClick:r[1]||(r[1]=e=>t.changeScheme())},null,8,["is-loading"])])}const J={components:{Multiselect:L},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(d,{emit:r}){const a=B(),t=C(!1),g=C(null);x("emitter");const p=f(()=>a==null?void 0:a.DNSmanagerOrdersList),m=f(()=>{const e=p.value;if(!(!e||typeof e!="object"))return Object.keys(e).map(n=>e[n]).find(n=>{var o;return n.OrderID===((o=d.data)==null?void 0:o.DNSID)})}),S=f(()=>Object.keys(a==null?void 0:a.additionalServicesScheme.DNSmanager).map(e=>{var n,o,c;return{...a==null?void 0:a.additionalServicesScheme.DNSmanager[e],value:(n=a==null?void 0:a.additionalServicesScheme.DNSmanager[e])==null?void 0:n.ID,name:(o=a==null?void 0:a.additionalServicesScheme.DNSmanager[e])==null?void 0:o.Name,cost:(c=a==null?void 0:a.additionalServicesScheme.DNSmanager[e])==null?void 0:c.CostMonth}}).filter(e=>{var n;return(e==null?void 0:e.ID)==((n=m.value)==null?void 0:n.SchemeID)})),_=f(()=>{var c;const e=S.value[0],n=(c=Object.keys(a==null?void 0:a.additionalServicesScheme.DNSmanager).map(l=>{var u;return{...a==null?void 0:a.additionalServicesScheme.DNSmanager[l],value:(u=a==null?void 0:a.additionalServicesScheme.DNSmanager[l])==null?void 0:u.ID}}))==null?void 0:c.filter(l=>{var u,N,I;return((u=l==null?void 0:l.value)==null?void 0:u.HardServerID)===(e==null?void 0:e.HardServerID)&&(l==null?void 0:l.IsSchemeChangeable)&&(l==null?void 0:l.ID)!==((N=m.value)==null?void 0:N.SchemeID)&&(l==null?void 0:l.ServersGroupID)===((I=d.data)==null?void 0:I.ServerGroup)});return[e,...n].map(l=>({...l,disabled:l.ID===e.ID}))});function D(){var e;t.value=!0,a.DNSmanagerOrderSchemeChange({NewSchemeID:g.value,DNSmanagerOrderID:(e=m.value)==null?void 0:e.ID}).then(n=>{let{result:o,error:c}=n;o==="SUCCESS"&&r("modalClose"),t.value=!1})}return V(()=>{var e,n,o,c;a.fetchAdditionalServiceScheme((e=d.data)==null?void 0:e.Code),a.fetchDNSmanagerOrders(),g.value=(o=(n=_.value)==null?void 0:n[1])==null?void 0:o.value,console.log((c=d.data)==null?void 0:c.Code),console.log(S.value[0]),console.log(_.value)}),{isLoading:t,getDNSSchemes:_,changeScheme:D,selectedPackage:g,getDNSScheme:S}}},Y=w(J,[["render",F],["__scopeId","data-v-4f07e925"]]);export{Y as default};

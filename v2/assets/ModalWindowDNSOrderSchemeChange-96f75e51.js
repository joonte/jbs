import{j as k,o as M,c as j,a as d,t as _,b,w as O,p as w,l as x,_ as E,r as C,S as L,g as f,i as y,O as V}from"./index-00a0bf0d.js";import{u as B}from"./services-1b7b90ba.js";import{s as P}from"./multiselect-9faec1a5.js";import{_ as U}from"./ButtonDefault-4233c21f.js";const D=c=>(w("data-v-8eeddfc2"),c=c(),x(),c),G={class:"modal__body"},H=D(()=>d("div",{class:"list-item__title"},"Смена тарифного плана",-1)),W=D(()=>d("div",{class:"list-item__subtitle"},"Текущий тарифный план ",-1)),A=D(()=>d("div",{class:"modal__input-row"},null,-1)),K=D(()=>d("div",{class:"modal__input-label"},"Новый тарифный план",-1)),q={class:"list-form_col list-form_col--xl"},z=D(()=>d("div",{class:"search-field form-field"},null,-1)),F={class:"multiselect-multiple-label"},J={class:"multiselect-option"};function Q(c,i,e,s,g,N){var S,h;const u=k("Multiselect"),p=U;return M(),j("div",G,[H,W,d("span",null,_((S=s.getDNSScheme[0])==null?void 0:S.name)+"/"+_((h=s.getDNSScheme[0])==null?void 0:h.cost),1),A,K,d("div",q,[z,b(u,{class:"multiselect--white",options:s.getDNSSchemes,type:"text",modelValue:s.selectedPackage,"onUpdate:modelValue":i[0]||(i[0]=t=>s.selectedPackage=t),placeholder:"Поиск"},{singlelabel:O(({value:t})=>[d("div",F,[d("span",null,_(t==null?void 0:t.Name)+"/"+_(t==null?void 0:t.CostMonth),1)])]),option:O(({option:t})=>[d("div",J,_(t==null?void 0:t.Name)+"/"+_(t==null?void 0:t.CostMonth),1)]),_:1},8,["options","modelValue"])]),b(p,{class:"modal__button",label:"Сменить","is-loading":s.isLoading,onClick:i[1]||(i[1]=t=>s.changeScheme())},null,8,["is-loading"])])}const R={components:{Multiselect:P},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(c,{emit:i}){const e=B(),s=C(!1),g=C(null);L("emitter");const N=f(()=>e==null?void 0:e.DNSmanagerOrdersList),u=f(()=>{const a=N.value;if(!(!a||typeof a!="object"))return Object.keys(a).map(l=>a[l]).find(l=>{var o;return l.OrderID===((o=c.data)==null?void 0:o.DNSID)})}),p=f(()=>Object.keys(e==null?void 0:e.additionalServicesScheme.DNSmanager).map(a=>{var l,o,m;return{...e==null?void 0:e.additionalServicesScheme.DNSmanager[a],value:(l=e==null?void 0:e.additionalServicesScheme.DNSmanager[a])==null?void 0:l.ID,name:(o=e==null?void 0:e.additionalServicesScheme.DNSmanager[a])==null?void 0:o.Name,cost:(m=e==null?void 0:e.additionalServicesScheme.DNSmanager[a])==null?void 0:m.CostMonth}}).filter(a=>{var l;return(a==null?void 0:a.ID)==((l=u.value)==null?void 0:l.SchemeID)})),S=f(()=>{var m;const a=p.value[0],l=(m=Object.keys(e==null?void 0:e.additionalServicesScheme.DNSmanager).map(n=>{var r;return{...e==null?void 0:e.additionalServicesScheme.DNSmanager[n],value:(r=e==null?void 0:e.additionalServicesScheme.DNSmanager[n])==null?void 0:r.ID}}))==null?void 0:m.filter(n=>{var r,I,v;return((r=n==null?void 0:n.value)==null?void 0:r.HardServerID)===(a==null?void 0:a.HardServerID)&&(n==null?void 0:n.IsSchemeChangeable)&&(n==null?void 0:n.ID)!==((I=u.value)==null?void 0:I.SchemeID)&&(n==null?void 0:n.ServersGroupID)===((v=c.data)==null?void 0:v.ServerGroup)});return[a,...l].sort((n,r)=>n.SortID-r.SortID).map(n=>({...n,disabled:n.ID===a.ID}))});function h(){var a;s.value=!0,e.DNSmanagerOrderSchemeChange({NewSchemeID:g.value,DNSmanagerOrderID:(a=u.value)==null?void 0:a.ID}).then(l=>{let{result:o,error:m}=l;o==="SUCCESS"&&i("modalClose"),s.value=!1})}y(()=>{var a,l,o;e.fetchAdditionalServiceScheme((a=c.data)==null?void 0:a.Code),e.fetchDNSmanagerOrders(),g.value=(o=(l=S.value)==null?void 0:l[1])==null?void 0:o.value});const t=a=>{a.key==="Enter"&&h()};return y(()=>{document.addEventListener("keyup",t)}),V(()=>{document.removeEventListener("keyup",t)}),{isLoading:s,getDNSSchemes:S,changeScheme:h,selectedPackage:g,getDNSScheme:p}}},$=E(R,[["render",Q],["__scopeId","data-v-8eeddfc2"]]);export{$ as default};

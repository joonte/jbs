import{o as l,c as d,t as _,a5 as g,k as i,_ as y,u as S,g as r}from"./index-59dbe3d3.js";function F(o,u,e,s,c,a){var t;return e.status?(l(),d("div",{key:0,class:"status-tag",style:g(s.getStyles)},_(((t=s.getConfigStatus)==null?void 0:t.Name)||e.status),5)):i("",!0)}const m={props:{status:{type:Object,default:()=>{}},daysRemained:{type:String,default:""},statusTable:{type:String,default:""}},setup(o,{emit:u}){const e=S(),s=r(()=>{var a,t,n;return(n=(t=(a=e==null?void 0:e.ConfigList)==null?void 0:a.Statuses)==null?void 0:t[o.statusTable])==null?void 0:n[o.status]});return{getStyles:r(()=>{var a,t;return{"background-color":`#${((a=s.value)==null?void 0:a.Color)||"#FFFFFF"}`,"border-color":`#${((t=s.value)==null?void 0:t.Color)||"#FFFFFF"}`}}),getConfigStatus:s}}},f=y(m,[["render",F],["__scopeId","data-v-9dc080cd"]]);export{f as S};

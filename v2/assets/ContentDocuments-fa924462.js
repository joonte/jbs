import{j as g,o as i,c as u,b as d,a as n,F as _,v,w as D,d as x,t as f,p as y,l as B,_ as k,e as T,g as h,i as A}from"./index-58c913f0.js";import{u as S}from"./content-949cede6.js";import"./contracts-5c545170.js";import"./bootstrap-vue-next.es-9015192b.js";import"./ButtonDefault-7a589010.js";import{B as w}from"./BlockBalanceAgreement-59500b1f.js";import"./IconArrow-bff54646.js";const C=s=>(y("data-v-214f0ee6"),s=s(),B(),s),I={class:"section"},L={class:"container"},$=C(()=>n("div",{class:"section-header"},[n("h1",{class:"section-title"},"Шаблоны документов")],-1)),b={class:"documents-list"};function N(s,e,a,c,l,r){const o=w,m=g("router-link");return i(),u(_,null,[d(o),n("div",I,[n("div",L,[$,n("div",b,[(i(!0),u(_,null,v(c.formattedDocuments,t=>(i(),u("div",{class:"documents-list__item",key:t.id},[d(m,{class:"document-item__name",to:`/Documents/${t.partition}`},{default:D(()=>[x(f(t.title),1)]),_:2},1032,["to"]),n("p",null,f(t.shortText),1)]))),128))])])])],64)}const V={async setup(){const s=T(),e=S(),a=h(()=>e.documentsList),c=r=>{s.push({name:"DocumentPage",params:{id:r}})},l=h(()=>(Array.isArray(e.documentsList)?e.documentsList:Object.values(e.documentsList)).map(o=>{const t=(p=>p?p.replace(/<[^>]*>/g,""):"")(o.Text);return{id:o.ID,partition:o.Partition,title:o.Title,shortText:t.length>170?t.substring(0,170)+"...":t}}));return A(async()=>{await e.fetchDocuments(),console.log("getDoc - ",a)}),await e.fetchDocuments(),{getDocuments:a,formattedDocuments:l,navigateToDocument:c}}},R=k(V,[["render",N],["__scopeId","data-v-214f0ee6"]]);export{R as default};

import{j as g,o as r,c as i,b as p,a as n,F as _,x as v,w as D,d as x,t as h,p as y,l as B,_ as k,e as T,g as f,i as A}from"./index-b5b5ce3d.js";import{u as S}from"./content-b926406b.js";import"./contracts-74f05bb7.js";import"./bootstrap-vue-next.es-37aa63ea.js";import"./ButtonDefault-0006f72a.js";import{B as w}from"./BlockBalanceAgreement-a016b94f.js";import"./IconArrow-fddabae2.js";const C=s=>(y("data-v-8dd08255"),s=s(),B(),s),I={class:"section"},L={class:"container"},$=C(()=>n("div",{class:"section-header"},[n("h1",{class:"section-title"},"Шаблоны документов")],-1)),b={class:"documents-list"};function N(s,e,d,a,u,c){const o=w,m=g("router-link");return r(),i(_,null,[p(o),n("div",I,[n("div",L,[$,n("div",b,[(r(!0),i(_,null,v(a.formattedDocuments,t=>(r(),i("div",{class:"documents-list__item",key:t.id},[p(m,{class:"document-item__name",to:`/Documents/${t.partition}`},{default:D(()=>[x(h(t.title),1)]),_:2},1032,["to"]),n("p",null,h(t.shortText),1)]))),128))])])])],64)}const V={async setup(){const s=T(),e=S(),d=f(()=>e.documentsList),a=c=>{s.push({name:"DocumentPage",params:{id:c}})},u=f(()=>(Array.isArray(e.documentsList)?e.documentsList:Object.values(e.documentsList)).map(o=>{const t=(l=>l?l.replace(/<[^>]*>/g,""):"")(o.Text);return{id:o.ID,partition:o.Partition,title:o.Title,shortText:t.length>170?t.substring(0,170)+"...":t}}));return A(async()=>{await e.fetchDocuments()}),await e.fetchDocuments(),{getDocuments:d,formattedDocuments:u,navigateToDocument:a}}},R=k(V,[["render",N],["__scopeId","data-v-8dd08255"]]);export{R as default};
import{o as h,c as y,a as f,b as O,_ as E,y as v,w as V,F as x,x as W,a8 as H,p as U,l as q,r as F,f as K,g as C,e as G,S as z,a0 as J,O as Q,a2 as X}from"./index-5e313302.js";import{_ as Y,u as Z,n as ee}from"./networkController-90f3418e.js";import{u as te}from"./postings-03760ecf.js";import{u as oe}from"./contracts-3351d68f.js";import{I as ne}from"./IconClose-1f889a2e.js";import{E as $}from"./EmptyStateBlock-9f752916.js";import{B as N}from"./BlockBalanceAgreement-bb87adc1.js";import{_ as se}from"./ClausesKeeper-eb954229.js";import"./bootstrap-vue-next.es-a19a8b41.js";import"./ButtonDefault-7c216fc6.js";import"./IconArrow-555f9d45.js";const ce={class:"notes__note-container"},ae=["innerHTML"];function ie(n,l,p,a,m,u){const _=ne;return h(),y("div",ce,[f("div",{class:"notes__content",innerHTML:p.item},null,8,ae),f("div",{class:"notes__close-button",onClick:l[0]||(l[0]=w=>a.closeNote(p.index))},[O(_,{class:"notes__close-button-cross"})])])}const re={props:{item:{type:String,default:""},index:{type:[String,Number],default:""}},emits:["delete"],setup(n,{emit:l}){function p(a){let m=new Date;m.setDate(30+m.getDate()),document.cookie=`${a}=hidden; path=/; expires=${m}`,l("delete",a)}return{closeNote:p}}},M=E(re,[["render",ie],["__scopeId","data-v-8295fda7"]]),le=n=>(U("data-v-9585c447"),n=n(),q(),n),de={class:"section"},me={class:"container"},pe=le(()=>f("div",{class:"section-header"},[f("h1",{class:"section-title"},"Уведомления")],-1)),fe={class:"notes"};function he(n,l,p,a,m,u){const _=N,w=se,r=M,s=$;return h(),y(x,null,[O(_),f("div",de,[f("div",me,[pe,O(w),f("div",fe,[a.notes&&Object.keys(a.notes).length>0?(h(),v(H,{key:0,class:"notes__transition-container",tag:"div",name:"list"},{default:V(()=>[(h(!0),y(x,null,W(a.notes,(g,I)=>(h(),v(r,{item:g,index:I,key:I,onDelete:a.deleteItem},null,8,["item","index","onDelete"]))),128))]),_:1})):(h(),v(s,{key:1,label:"Все уведомления прочитаны или они еще не пришли"}))])])])],64)}const ue={components:{IconCross:Y,Note:M,EmptyStateBlock:$,BlockBalanceAgreement:N},async setup(){const n=Z(),l=F(null),p=te(),a=oe(),m=K(),u=C(()=>m.userInfo),_=C(()=>{var e,t;return(t=Object.keys((e=u.value)==null?void 0:e.Contacts))==null?void 0:t.map(o=>{var i,c,S,d,b;if(!((S=(c=(i=u.value)==null?void 0:i.Contacts)==null?void 0:c[o])!=null&&S.IsHidden))return(b=(d=u.value)==null?void 0:d.Contacts)==null?void 0:b[o]})}),w=e=>_.value.find(t=>t.ID===e),r=G(),s=z("emitter"),g=C(()=>{let e={};return Object.keys(n==null?void 0:n.notesList).reverse().forEach(t=>{var o;e[t]=(o=n==null?void 0:n.notesList)==null?void 0:o[t]}),Object.keys(e).length>0?e:null});function I(){let e={};Object.keys(n==null?void 0:n.notesList).reverse().forEach(t=>{var o;A(t)&&(e[t]=(o=n==null?void 0:n.notesList)==null?void 0:o[t])}),l.value=e}function A(e){return document.cookie.match(RegExp(`(?:^|;\\s*)${e}=([^;]*)`))===null}function B(e){delete l.value[e],s.emit("notes-reduce")}const k=async(e,t)=>{try{return(await X.post(e,t)).data}catch(o){throw new Error(`Error sending confirm request: ${o.message}`)}};async function D(e,t){if(console.log("Route: ",e,"Data: ",t),(e.includes("ISPswSchemes")||e.includes("ExtraIPSchemes")||e.includes("DNSmanagerSchemes"))&&(e.includes("ISPswSchemes")&&r.push("/AdditionalServicesOrder?ServiceID=51000"),e.includes("ExtraIPSchemes")&&r.push("/AdditionalServicesOrder?ServiceID=50000"),e.includes("DNSmanagerSchemes")&&r.push("/AdditionalServicesOrder?ServiceID=52000")),e.includes("OrderPay")){let o=t[Object.keys(t).find(i=>i.includes("OrderID"))];e!=="/ExtraIPOrderPay"&&e!=="/ISPswOrderPay"&&e!=="/DNSmanagerOrderPay"?r.push(`${e}/${o}`):e==="/ExtraIPOrderPay"||e==="/ISPswOrderPay"||e==="/DNSmanagerOrderPay"?r.push(`${e}/${o}`):r.push(`/ServiceOrderPay/${o}`)}else if(e==="/InvoiceDocument")p.InvoiceDocument({InvoiceID:t.InvoiceID}).then(o=>{s.emit("open-modal",{component:"InvoiceDocument",data:{html:o,ID:t.InvoiceID}})});else if(e==="/API/OrdersTransfer")console.log(t),s.emit("open-modal",{component:"TakeOrder",data:{OrdersTransferID:t}});else if(e==="/ContractMake")r.push("ContractMake");else if(e==="/DomainSelectOwner")s.emit("open-modal",{component:"DomainSelectOwner",data:t});else if(e==="/ISPswOrders")r.push("AdditionalServices");else if(e==="/ContactEdit?MethodID=VKontakte")s.emit("open-modal",{component:"CreateVK"});else if(e==="/ContactEdit?MethodID=Telegram")s.emit("open-modal",{component:"CreateTelegram"});else if(e==="/ContactEdit?MethodID=Viber")s.emit("open-modal",{component:"CreateViber"});else if(e==="/ContactEdit?MethodID=SMS")s.emit("open-modal",{component:"CreatePhone"});else if(e.includes("/ContactEdit")){(t==null?void 0:t.MethodID)==="SMS"&&s.emit("open-modal",{component:"CreatePhone"});const o=e.match(/ContactID=(\d+)/),i=o?o[1]:null;if(console.log("contactId:",i),i){const c=w(i);if(c){const S={Method:c.MethodID,Value:c.Address,ContactID:c.ID};try{const d=await k("/API/Confirm",S);console.log("Response from server:",d),d.Status==="Ok"?s.emit("open-modal",{component:"AcceptContact",data:{contactId:i,sendConfirmRequest:k}}):console.error(`Error confirming code. Status: ${d.Status}, Message: ${d.Message}`)}catch(d){console.error("Error accepting contact:",d.message)}}}else console.error("ContactID is not present in the URL params.",e)}else console.log(e,t),s.emit("open-modal",{component:e.replace("/",""),data:t})}function j(){}function L(e,t,o,i){console.log(e,t,o,i),ee.post(e,t).then(c=>{console.log(c.data),c.data.Status==="Exception"?s.emit("open-modal",{component:"ExclusionWindow",data:c.data}):n.fetchNotes().then(()=>{I()})}).catch(c=>{console.error("Ошибка при выполнении запроса:",c)})}function R(e,t){s.emit("open-modal",{component:"DeleteItem",data:{id:null,callback:t,message:e,tableID:"HostingOrders",successEmit:"updateHostingList"}})}function T(){}function P(){window.ShowWindow=D.bind(this)}return J(()=>{P()}),Q(()=>{window.ShowWindow=null}),s.on("close-modal",()=>{P()}),window.ShowProgress=T.bind(this),window.ShowWindow=D.bind(this),window.PromptShow=j.bind(this),window.AjaxCall=L.bind(this),window.ShowConfirm=R.bind(this),await n.fetchNotes().then(()=>{I()}),await a.fetchContracts(),await a.fetchProfiles(),await m.fetchUserData(),{getNotesList:g,notes:l,ShowWindow:D,deleteItem:B}}},Pe=E(ue,[["render",he],["__scopeId","data-v-9585c447"]]);export{Pe as default};

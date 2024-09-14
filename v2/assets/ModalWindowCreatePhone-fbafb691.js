import{_ as b}from"./ButtonDefault-0006f72a.js";import{_ as E}from"./BasicInput-a3395017.js";import{f as k,u as P,S as B,e as D,r as w,g as u,i as A,O as U,o as $,c as x,a as C,b as f,a2 as R}from"./index-b5b5ce3d.js";import"./component-2368399e.js";const V={class:"modal__body"},q=C("div",{class:"list-item__title"},"Добавление номера телефона",-1),N={class:"modal__input-row"},H={__name:"ModalWindowCreatePhone",emits:["modalClose","buttonClicked"],setup(L,{emit:h}){const c=k(),g=P(),v=B("emitter"),d=D(),l=w(""),S=u(()=>{var t,e;return(e=(t=g.ConfigList)==null?void 0:t.Messages)==null?void 0:e.Prompts}),i=u(()=>c.userInfo),M=u(()=>{var t;return Object.keys((t=i.value)==null?void 0:t.Contacts).map(e=>{var o,n,s,a,r;if(!((s=(n=(o=i.value)==null?void 0:o.Contacts)==null?void 0:n[e])!=null&&s.IsHidden))return(r=(a=i.value)==null?void 0:a.Contacts)==null?void 0:r[e]}).filter(Boolean)});function y(){const t=S.value.SMS,e=/<B>Например:<\/B>\s*([^<]+)/,o=t.match(e);return o&&o[1]?`${o[1]}`:t}const m=async(t,e)=>{try{return(await R.post(t,e)).data}catch(o){throw new Error(`Error sending confirm request: ${o.message}`)}};function p(){const t={MethodID:"SMS",Address:l.value,TimeBegin:"00",TimeEnd:"00",IsActive:"yes",ContactID:"0"};c.userEditContact(t).then(e=>{e.response==="SUCCESS"&&c.fetchUserData().then(async()=>{var s;((s=d.currentRoute.value)==null?void 0:s.fullPath)!=="/profile/UserProfile"&&d.push("profile/UserProfile");const o=M.value,n=o[o.length-1];if(n){const a={Method:n.MethodID,Value:n.Address,ContactID:n.ID};try{const r=await m("/API/Confirm",a);r.Status==="Ok"?v.emit("open-modal",{component:"AcceptContact",data:{contactId:n.ID,sendConfirmRequest:m}}):console.error(`Error confirming code. Status: ${r.Status}, Message: ${r.Message}`)}catch(r){console.error("Error accepting contact:",r.message)}}})})}function I(){h("modalClose")}const _=t=>{t.key==="Enter"&&p()};return A(()=>{document.addEventListener("keyup",_)}),U(()=>{document.removeEventListener("keyup",_)}),(t,e)=>{const o=E,n=b;return $(),x("div",V,[q,C("div",N,[f(o,{label:"Номер телефона",name:"phoneNumberSMS",placeholder:y(),vertical:"",modelValue:l.value,"onUpdate:modelValue":e[0]||(e[0]=s=>l.value=s)},null,8,["placeholder","modelValue"]),f(n,{class:"profile-info__add-button",label:"Добавить контакт",onClick:p,onButtonClicked:e[1]||(e[1]=s=>I())})])])}}};export{H as default};
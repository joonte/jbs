import{_ as I}from"./ButtonDefault-0006f72a.js";import{_ as E}from"./BasicInput-a3395017.js";import{f as k,e as b,S as D,r as M,g as f,i as S,O as w,o as A,c as B,b as C,a as g,a2 as T}from"./index-b5b5ce3d.js";import"./component-2368399e.js";const U={class:"modal__body"},V=g("div",{class:"list-item__title"},"Добавление Telegram",-1),$=g("div",{class:"modal__input-row"},null,-1),j={__name:"ModalWindowCreateTelegram",emits:["modalClose","buttonClicked"],setup(R,{emit:v}){const c=k(),u=b(),h=D("emitter"),l=M(""),i=f(()=>c.userInfo),y=f(()=>{var n;return Object.keys((n=i.value)==null?void 0:n.Contacts).map(e=>{var t,o,s,r,a;if(!((s=(o=(t=i.value)==null?void 0:t.Contacts)==null?void 0:o[e])!=null&&s.IsHidden))return(a=(r=i.value)==null?void 0:r.Contacts)==null?void 0:a[e]}).filter(Boolean)}),d=async(n,e)=>{try{return(await T.post(n,e)).data}catch(t){throw new Error(`Error sending confirm request: ${t.message}`)}};function m(){const n={MethodID:"Telegram",Address:l.value,TimeBegin:"00",TimeEnd:"00",IsActive:"yes",ContactID:"0"};c.userEditContact(n).then(e=>{e.response==="SUCCESS"&&(c.fetchUserData().then(async()=>{var s;((s=u.currentRoute.value)==null?void 0:s.fullPath)!=="/profile/UserProfile"&&u.push("profile/UserProfile");const t=y.value,o=t[t.length-1];if(o){const r={Method:o.MethodID,Value:o.Address,ContactID:o.ID};try{const a=await d("/API/Confirm",r);a.Status==="Ok"?h.emit("open-modal",{component:"AcceptContact",data:{contactId:o.ID,sendConfirmRequest:d}}):console.error(`Error confirming code. Status: ${a.Status}, Message: ${a.Message}`)}catch(a){console.error("Error accepting contact:",a.message)}}}),_())})}function _(){v("modalClose")}const p=n=>{n.key==="Enter"&&m()};return S(()=>{document.addEventListener("keyup",p)}),w(()=>{document.removeEventListener("keyup",p)}),(n,e)=>{const t=E,o=I;return A(),B("div",U,[V,$,C(t,{label:"Telegram сообщения",name:"name",placeholder:"Ссылка или id",vertical:"",modelValue:l.value,"onUpdate:modelValue":e[0]||(e[0]=s=>l.value=s)},null,8,["modelValue"]),C(o,{class:"profile-info__add-button",label:"Добавить контакт",onClick:m,onButtonClicked:e[1]||(e[1]=s=>_())})])}}};export{j as default};

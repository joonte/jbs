import{_ as y}from"./ButtonDefault-7c216fc6.js";import{_ as I}from"./BasicInput-be1c88a0.js";import{f as E,e as V,S as b,r as D,g as f,h as M,O as S,o as w,c as A,b as C,a as v,a2 as B}from"./index-5e313302.js";import"./component-6a5a025c.js";const U={class:"modal__body"},K=v("div",{class:"list-item__title"},"Добавление VKontakte",-1),R=v("div",{class:"modal__input-row"},null,-1),j={__name:"ModalWindowCreateVK",emits:["modalClose","buttonClicked"],setup($,{emit:h}){const c=E(),u=V(),g=b("emitter"),l=D(""),i=f(()=>c.userInfo),k=f(()=>{var n;return Object.keys((n=i.value)==null?void 0:n.Contacts).map(e=>{var t,o,a,r,s;if(!((a=(o=(t=i.value)==null?void 0:t.Contacts)==null?void 0:o[e])!=null&&a.IsHidden))return(s=(r=i.value)==null?void 0:r.Contacts)==null?void 0:s[e]}).filter(Boolean)}),d=async(n,e)=>{try{return(await B.post(n,e)).data}catch(t){throw new Error(`Error sending confirm request: ${t.message}`)}};function m(){const n={MethodID:"VKontakte",Address:l.value,TimeBegin:"00",TimeEnd:"00",IsActive:"yes",ContactID:"0"};c.userEditContact(n).then(e=>{e.response==="SUCCESS"&&(c.fetchUserData().then(async()=>{var a;((a=u.currentRoute.value)==null?void 0:a.fullPath)!=="/profile/UserProfile"&&u.push("profile/UserProfile");const t=k.value,o=t[t.length-1];if(o){const r={Method:o.MethodID,Value:o.Address,ContactID:o.ID};try{const s=await d("/API/Confirm",r);console.log("Response from server:",s),s.Status==="Ok"?g.emit("open-modal",{component:"AcceptContact",data:{contactId:o.ID,sendConfirmRequest:d}}):console.error(`Error confirming code. Status: ${s.Status}, Message: ${s.Message}`)}catch(s){console.error("Error accepting contact:",s.message)}}}),_())})}function _(){h("modalClose")}const p=n=>{n.key==="Enter"&&m()};return M(()=>{document.addEventListener("keyup",p)}),S(()=>{document.removeEventListener("keyup",p)}),(n,e)=>{const t=I,o=y;return w(),A("div",U,[K,R,C(t,{label:"VKontakte сообщения",name:"name",placeholder:"Ссылка или id",vertical:"",modelValue:l.value,"onUpdate:modelValue":e[0]||(e[0]=a=>l.value=a)},null,8,["modelValue"]),C(o,{class:"profile-info__add-button",label:"Добавить контакт",onClick:m,onButtonClicked:e[1]||(e[1]=a=>_())})])}}};export{j as default};

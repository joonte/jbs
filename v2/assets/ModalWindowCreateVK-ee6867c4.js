import{_ as y}from"./ButtonDefault-0006f72a.js";import{_ as I}from"./BasicInput-a3395017.js";import{f as E,e as V,S as b,r as D,g as f,i as M,O as S,o as w,c as A,b as C,a as v,a2 as B}from"./index-b5b5ce3d.js";import"./component-2368399e.js";const U={class:"modal__body"},K=v("div",{class:"list-item__title"},"Добавление VKontakte",-1),$=v("div",{class:"modal__input-row"},null,-1),j={__name:"ModalWindowCreateVK",emits:["modalClose","buttonClicked"],setup(R,{emit:h}){const c=E(),u=V(),g=b("emitter"),l=D(""),i=f(()=>c.userInfo),k=f(()=>{var n;return Object.keys((n=i.value)==null?void 0:n.Contacts).map(e=>{var t,o,s,r,a;if(!((s=(o=(t=i.value)==null?void 0:t.Contacts)==null?void 0:o[e])!=null&&s.IsHidden))return(a=(r=i.value)==null?void 0:r.Contacts)==null?void 0:a[e]}).filter(Boolean)}),d=async(n,e)=>{try{return(await B.post(n,e)).data}catch(t){throw new Error(`Error sending confirm request: ${t.message}`)}};function m(){const n={MethodID:"VKontakte",Address:l.value,TimeBegin:"00",TimeEnd:"00",IsActive:"yes",ContactID:"0"};c.userEditContact(n).then(e=>{e.response==="SUCCESS"&&(c.fetchUserData().then(async()=>{var s;((s=u.currentRoute.value)==null?void 0:s.fullPath)!=="/profile/UserProfile"&&u.push("profile/UserProfile");const t=k.value,o=t[t.length-1];if(o){const r={Method:o.MethodID,Value:o.Address,ContactID:o.ID};try{const a=await d("/API/Confirm",r);a.Status==="Ok"?g.emit("open-modal",{component:"AcceptContact",data:{contactId:o.ID,sendConfirmRequest:d}}):console.error(`Error confirming code. Status: ${a.Status}, Message: ${a.Message}`)}catch(a){console.error("Error accepting contact:",a.message)}}}),_())})}function _(){h("modalClose")}const p=n=>{n.key==="Enter"&&m()};return M(()=>{document.addEventListener("keyup",p)}),S(()=>{document.removeEventListener("keyup",p)}),(n,e)=>{const t=I,o=y;return w(),A("div",U,[K,$,C(t,{label:"VKontakte сообщения",name:"name",placeholder:"Ссылка или id",vertical:"",modelValue:l.value,"onUpdate:modelValue":e[0]||(e[0]=s=>l.value=s)},null,8,["modelValue"]),C(o,{class:"profile-info__add-button",label:"Добавить контакт",onClick:m,onButtonClicked:e[1]||(e[1]=s=>_())})])}}};export{j as default};
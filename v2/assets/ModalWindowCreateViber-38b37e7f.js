import{_ as h}from"./ButtonDefault-eb076df2.js";import{_ as I}from"./BasicInput-e305f811.js";import{f as V,e as y,S as D,r as S,g as _,o as E,c as M,b as p,a as f,a2 as k}from"./index-642cdac5.js";import"./component-faf9e0c1.js";const w={class:"modal__body"},A=f("div",{class:"list-item__title"},"Добавление Viber",-1),B=f("div",{class:"modal__input-row"},null,-1),P={__name:"ModalWindowCreateViber",emits:["modalClose","buttonClicked"],setup(R,{emit:C}){const c=V(),u=y(),v=D("emitter"),l=S(""),i=_(()=>c.userInfo),g=_(()=>{var a;return Object.keys((a=i.value)==null?void 0:a.Contacts).map(e=>{var t,o,n,r,s;if(!((n=(o=(t=i.value)==null?void 0:t.Contacts)==null?void 0:o[e])!=null&&n.IsHidden))return(s=(r=i.value)==null?void 0:r.Contacts)==null?void 0:s[e]}).filter(Boolean)}),d=async(a,e)=>{try{return(await k.post(a,e)).data}catch(t){throw new Error(`Error sending confirm request: ${t.message}`)}};function b(){const a={MethodID:"Viber",Address:l.value,TimeBegin:"00",TimeEnd:"00",IsActive:"yes",ContactID:"0"};c.userEditContact(a).then(e=>{e.response==="SUCCESS"&&(c.fetchUserData().then(async()=>{var n;((n=u.currentRoute.value)==null?void 0:n.fullPath)!=="/profile/UserProfile"&&u.push("profile/UserProfile");const t=g.value,o=t[t.length-1];if(o){const r={Method:o.MethodID,Value:o.Address,ContactID:o.ID};try{const s=await d("/API/Confirm",r);console.log("Response from server:",s),s.Status==="Ok"?v.emit("open-modal",{component:"AcceptContact",data:{contactId:o.ID,sendConfirmRequest:d}}):console.error(`Error confirming code. Status: ${s.Status}, Message: ${s.Message}`)}catch(s){console.error("Error accepting contact:",s.message)}}}),m())})}function m(){C("modalClose")}return(a,e)=>{const t=I,o=h;return E(),M("div",w,[A,B,p(t,{label:"Viber сообщения",name:"name",placeholder:"Ссылка или id",vertical:"",modelValue:l.value,"onUpdate:modelValue":e[0]||(e[0]=n=>l.value=n)},null,8,["modelValue"]),p(o,{class:"profile-info__add-button",label:"Добавить контакт",onClick:b,onButtonClicked:e[1]||(e[1]=n=>m())})])}}};export{P as default};
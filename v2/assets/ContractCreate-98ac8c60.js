import{j as Ce,o as s,c,b as f,a as n,w as N,d as H,F as P,x as O,k as d,y as C,t as ue,n as L,p as be,l as Ve,_ as Ie,u as Te,f as me,e as Pe,a7 as Ee,r as p,g as de,i as fe,O as xe,h as Ue}from"./index-b5b5ce3d.js";import{u as De}from"./contracts-74f05bb7.js";import{s as pe}from"./SimpleCheckbox-d63f97dc.js";import{_ as _e}from"./BasicInput-a3395017.js";import{P as he,T as ve,S as ie}from"./PhoneInput-3d83b6d5.js";import{B as ge}from"./BlockBalanceAgreement-a016b94f.js";import{u as Re}from"./files-542d845a.js";import{I as ke}from"./IconFile-411e597d.js";import{I as ye}from"./IconClose-cd6d3917.js";import{_ as we}from"./ButtonDefault-0006f72a.js";import{f as Fe}from"./bootstrap-vue-next.es-37aa63ea.js";import{_ as Se}from"./IconClip-5f3d9e99.js";import{_ as Ae}from"./ClausesKeeper-3a1c11ba.js";/* empty css                                                                       */import"./component-2368399e.js";import"./multiselect-4f7d14de.js";import"./IconArrow-fddabae2.js";const j=i=>(be("data-v-58f6f9d9"),i=i(),Ve(),i),Be={class:"section"},Le={class:"container"},Me={class:"section-header"},qe=j(()=>n("h1",{class:"section-title"},"Создать профиль",-1)),Ne=j(()=>n("div",{class:"section-header"},[n("h2",{class:"section-block_title"},"Шаблон")],-1)),He={class:"form"},Oe={class:"form"},je={class:"form-row-wrapper"},Ke={class:"special-form-row"},ze={key:0,class:"section-header inputs-block"},Ge={class:"section-block_title"},Je={key:0,class:"form width-limit"},Qe={key:0,class:"form-field form-field--file form-field--basic"},We={class:"chat-files"},Xe={class:"chat-files__item"},Ye=["onClick"],Ze={class:"form-field form-field--file form-field--basic"},$e={class:"form-field-input__file-wrapper"},el={class:"form-field-input__file"},ll=j(()=>n("span",null,"Прикрепить документ подтверждающий личность",-1)),ol=["innerHTML"];function al(i,r,E,o,x,_){var w,I,F,T,S,m,A;const b=ge,V=Ce("router-link"),h=Ae,U=pe,v=_e,g=he,D=ve,t=ie,u=ke,M=ye,R=Se,k=Fe,q=we;return s(),c(P,null,[f(b),n("div",Be,[n("div",Le,[n("div",Me,[f(V,{class:"section-label",to:"/Contracts"},{default:N(()=>[H("Мои договора")]),_:1}),qe]),f(h),Ne,n("div",He,[(s(!0),c(P,null,O((I=(w=o.getConfig)==null?void 0:w.Contracts)==null?void 0:I.Types,e=>(s(),c(P,null,[(e==null?void 0:e.ProfileTemplateID)!==""?(s(),C(U,{key:0,label:e==null?void 0:e.Name,value:e==null?void 0:e.ProfileTemplateID,modelValue:o.templateValue,"onUpdate:modelValue":r[0]||(r[0]=l=>o.templateValue=l)},null,8,["label","value","modelValue"])):d("",!0)],64))),256))]),n("div",Oe,[(s(!0),c(P,null,O((A=(m=(S=(T=(F=o.getConfig)==null?void 0:F.Profiles)==null?void 0:T.Templates)==null?void 0:S[o.templateValue])==null?void 0:m.Template)==null?void 0:A.Attribs,(e,l)=>(s(),c("div",je,[n("div",Ke,[e!=null&&e.Title?(s(),c("div",ze,[n("h2",Ge,ue(e==null?void 0:e.Title),1)])):d("",!0),(e==null?void 0:e.Type)==="Input"&&(e==null?void 0:e.Check)!=="Phone"?(s(),C(v,{key:1,class:L({"special-form-input":!0,"form-field-error":o.form[l].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:o.form[l].value,"onUpdate:modelValue":a=>o.form[l].value=a,pattern:o.getFieldRegex(e==null?void 0:e.Check)},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue","pattern"])):d("",!0),(e==null?void 0:e.Type)==="Input"&&(e==null?void 0:e.Check)==="Phone"?(s(),C(g,{key:2,class:L({"special-form-input":!0,"form-field-error":o.form[l].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:o.form[l].value,"onUpdate:modelValue":a=>o.form[l].value=a,pattern:o.getPhoneRegex()},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue","pattern"])):d("",!0),(e==null?void 0:e.Type)==="TextArea"?(s(),C(D,{key:3,class:L({"special-form-input":!0,"form-field-error":o.form[l].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:o.form[l].value,"onUpdate:modelValue":a=>o.form[l].value=a},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue"])):(e==null?void 0:e.Type)==="Select"?(s(),C(t,{key:4,class:L({"special-form-input":!0,"form-field-error":o.form[l].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,options:e==null?void 0:e.Options,modelValue:o.form[l].value,"onUpdate:modelValue":a=>o.form[l].value=a},null,8,["class","label","placeholder","necessarily","name","options","modelValue","onUpdate:modelValue"])):d("",!0)])]))),256))]),o.templateValue!==0?(s(),c("div",Je,[o.fileList.length?(s(),c("div",Qe,[n("div",We,[(s(!0),c(P,null,O(o.fileList,(e,l)=>(s(),c("div",Xe,[n("span",null,[f(u,{class:"file-icon"})]),n("span",null,ue(e==null?void 0:e.name),1),n("button",{class:"close",onClick:a=>o.removeFile(l)},[f(M)],8,Ye)]))),256))])])):d("",!0),n("label",Ze,[n("div",$e,[n("input",{ref:"fileInput",type:"file",onChange:r[1]||(r[1]=(...e)=>o.fileUpload&&o.fileUpload(...e))},null,544),n("div",el,[f(R),ll])])]),f(k,{class:"rule-checkbox",name:"checkbox",checked:"",modelValue:o.agree,"onUpdate:modelValue":r[2]||(r[2]=e=>o.agree=e)},{default:N(()=>[H("Подтверждаю свое согласие на передачу информации в электроной форме (в том числе персональных данных) по открытым каналам связи сети Интернет")]),_:1},8,["modelValue"]),f(k,{class:"rule-checkbox",name:"checkbox",checked:"",modelValue:o.agreeHandle,"onUpdate:modelValue":r[3]||(r[3]=e=>o.agreeHandle=e)},{default:N(()=>[H("Я разрешаю передачу своих персональных данных третьим лицам, для выполнения заказанных мною услуг (регистрация доменто и т.п.)")]),_:1},8,["modelValue"])])):d("",!0),o.createProfileErrorMessage?(s(),c("div",{key:1,class:"error-message width-limit",innerHTML:o.formErrorMessage(o.createProfileErrorMessage)},null,8,ol)):d("",!0),o.templateValue!==0?(s(),C(q,{key:2,class:"width-limit","is-loading":o.createProfileResult==="pending",label:"Зарегистрировать",onClick:r[4]||(r[4]=e=>o.checkForm())},null,8,["is-loading"])):d("",!0)])])],64)}const nl={components:{TextAreaLine:ve,simpleCheckbox:pe,basicInput:_e,SelectLine:ie,PhoneInput:he,IconFile:ke,IconClose:ye,BlockBalanceAgreement:ge},setup(){const i=Te(),r=De(),E=me(),o=Re(),x=Pe(),_=Ee(),b=p(!1),V=p(!1),h=p({}),U=p(null),v=p([]),g=p(null),D=p(null),t=p(0),u=de(()=>i.ConfigList),M=de(()=>{var e;return(e=E==null?void 0:E.userInfo)==null?void 0:e.Email}),R=u.value,k={};for(const e in R.Regulars)k[e]=R.Regulars[e];const q=e=>k[e]||null,w=()=>{const e=u.value;return e&&e.Regulars&&e.Regulars.Phone?new RegExp(e.Regulars.Phone):/^[0-9]{10}$/},I=e=>{e.key==="Enter"&&e.ctrlKey&&T()};fe(()=>{document.addEventListener("keyup",I)}),xe(()=>{document.removeEventListener("keyup",I)});function F(e){v.value=v.value.filter((l,a)=>a!==e)}function T(){if(b.value&&V.value){let e=new FormData;Object.keys(h.value).forEach(l=>{var a;e.append(l,(a=h.value[l])==null?void 0:a.value)}),e.append("TemplateID",t.value),e.append("Agree",b.value),e.append("AgreeHandle",V.value),v.value.forEach((l,a)=>{e.append("Document[]",l==null?void 0:l.value)}),g.value="pending",r.createNewContractFormData(e).then(l=>{var a;l.status==="success"?(g.value="success",_.query.Contract?x.push(`/Balance/${_.query.Contract}`):!_.query.Contract&&_.query.TemplateID?x.push("/Basket"):x.push("/Contracts")):(g.value="error",D.value=(a=l.data)==null?void 0:a.Exception)})}}function S(e){return""+m(e)}function m(e){return e?"<div>"+(e==null?void 0:e.String)+"</div>"+m(e==null?void 0:e.Parent):""}function A(e){var a;let l=((a=e.target.files)==null?void 0:a[0])||null;l&&(U.value.value="",o.sendFile(l).then(y=>{v.value.push({name:l==null?void 0:l.name,value:y.slice(y.lastIndexOf("^")+1,y.length)})}))}return Ue(t,()=>{var e,l,a,y,K;h.value={},Object.keys((K=(y=(a=(l=(e=u.value)==null?void 0:e.Profiles)==null?void 0:l.Templates)==null?void 0:a[t.value])==null?void 0:y.Template)==null?void 0:K.Attribs).forEach(B=>{var z,G,J,Q,W,X,Y,Z,$,ee,le,oe,ae,ne,se,re,ce,te;h.value[B]={error:!1,value:(((X=(W=(Q=(J=(G=(z=u.value)==null?void 0:z.Profiles)==null?void 0:G.Templates)==null?void 0:J[t.value])==null?void 0:Q.Template)==null?void 0:W.Attribs[B])==null?void 0:X.Value)==="%Email%"?M.value:(oe=(le=(ee=($=(Z=(Y=u.value)==null?void 0:Y.Profiles)==null?void 0:Z.Templates)==null?void 0:$[t.value])==null?void 0:ee.Template)==null?void 0:le.Attribs[B])==null?void 0:oe.Value)||null,required:((te=(ce=(re=(se=(ne=(ae=u.value)==null?void 0:ae.Profiles)==null?void 0:ne.Templates)==null?void 0:se[t.value])==null?void 0:re.Template)==null?void 0:ce.Attribs[B])==null?void 0:te.IsDuty)==="1"}})}),fe(()=>{var e,l;(l=(e=u.value)==null?void 0:e.Contracts)!=null&&l.Types&&_.query.TemplateID&&(t.value=_.query.TemplateID)}),{getConfig:u,templateValue:t,agreeHandle:V,agree:b,createProfileResult:g,createProfileErrorMessage:D,checkForm:T,formErrorMessage:S,form:h,fileInput:U,fileList:v,removeFile:F,fileUpload:A,getPhoneRegex:w,getFieldRegex:q,fieldRegexMap:k}}},Vl=Ie(nl,[["render",al],["__scopeId","data-v-58f6f9d9"]]);export{Vl as default};

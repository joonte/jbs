import{j as ke,o as s,c as t,b as _,a as n,w as q,d as N,F as T,x as H,k as f,y as C,t as te,n as B,p as ye,l as Ce,_ as be,u as Ve,f as Ie,e as Te,a7 as Pe,r as h,g as ue,i as me,h as xe}from"./index-83b4b7ba.js";import{u as Ee}from"./contracts-623ec7a8.js";import{s as de}from"./SimpleCheckbox-87a2cd8f.js";import{_ as fe}from"./BasicInput-3f1e9fd6.js";import{P as pe,T as _e,S as he}from"./PhoneInput-67d3406d.js";import{B as ve}from"./BlockBalanceAgreement-c5fad736.js";import{u as De}from"./files-5751fe30.js";import{I as ie}from"./IconFile-7147d816.js";import{I as ge}from"./IconClose-86aafc4e.js";import{_ as Re}from"./ButtonDefault-dc248ec6.js";import{f as Ue}from"./bootstrap-vue-next.es-8f3ae81a.js";import{_ as we}from"./IconClip-2305b29b.js";import{_ as Fe}from"./ClausesKeeper-55c14b7f.js";/* empty css                                                                       */import"./component-abb1f5a9.js";import"./multiselect-f2568231.js";import"./IconArrow-80c72216.js";const O=g=>(ye("data-v-ac4e889e"),g=g(),Ce(),g),Se={class:"section"},Ae={class:"container"},Be={class:"section-header"},Me=O(()=>n("h1",{class:"section-title"},"Создать профиль",-1)),Le=O(()=>n("div",{class:"section-header"},[n("h2",{class:"section-block_title"},"Шаблон")],-1)),qe={class:"form"},Ne={class:"form"},He={class:"form-row-wrapper"},Oe={class:"special-form-row"},je={key:0,class:"section-header inputs-block"},ze={class:"section-block_title"},Ge={key:0,class:"form width-limit"},Je={key:0,class:"form-field form-field--file form-field--basic"},Ke={class:"chat-files"},Qe={class:"chat-files__item"},We=["onClick"],Xe={class:"form-field form-field--file form-field--basic"},Ye={class:"form-field-input__file-wrapper"},Ze={class:"form-field-input__file"},$e=O(()=>n("span",null,"Прикрепить документ подтверждающий личность",-1)),el=["innerHTML"];function ll(g,c,P,o,m,p){var R,U,w,F,I,S,l;const b=ve,V=ke("router-link"),v=Fe,x=de,i=fe,k=pe,E=_e,u=he,d=ie,M=ge,D=we,y=Ue,L=Re;return s(),t(T,null,[_(b),n("div",Se,[n("div",Ae,[n("div",Be,[_(V,{class:"section-label",to:"/Contracts"},{default:q(()=>[N("Мои договора")]),_:1}),Me]),_(v),Le,n("div",qe,[(s(!0),t(T,null,H((U=(R=o.getConfig)==null?void 0:R.Contracts)==null?void 0:U.Types,e=>(s(),t(T,null,[(e==null?void 0:e.ProfileTemplateID)!==""?(s(),C(x,{key:0,label:e==null?void 0:e.Name,value:e==null?void 0:e.ProfileTemplateID,modelValue:o.templateValue,"onUpdate:modelValue":c[0]||(c[0]=a=>o.templateValue=a)},null,8,["label","value","modelValue"])):f("",!0)],64))),256))]),n("div",Ne,[(s(!0),t(T,null,H((l=(S=(I=(F=(w=o.getConfig)==null?void 0:w.Profiles)==null?void 0:F.Templates)==null?void 0:I[o.templateValue])==null?void 0:S.Template)==null?void 0:l.Attribs,(e,a)=>(s(),t("div",He,[n("div",Oe,[e!=null&&e.Title?(s(),t("div",je,[n("h2",ze,te(e==null?void 0:e.Title),1)])):f("",!0),(e==null?void 0:e.Type)==="Input"&&(e==null?void 0:e.Check)!=="Phone"?(s(),C(i,{key:1,class:B({"special-form-input":!0,"form-field-error":o.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:o.form[a].value,"onUpdate:modelValue":r=>o.form[a].value=r,pattern:o.getFieldRegex(e==null?void 0:e.Check)},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue","pattern"])):f("",!0),(e==null?void 0:e.Type)==="Input"&&(e==null?void 0:e.Check)==="Phone"?(s(),C(k,{key:2,class:B({"special-form-input":!0,"form-field-error":o.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:o.form[a].value,"onUpdate:modelValue":r=>o.form[a].value=r,pattern:o.getPhoneRegex()},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue","pattern"])):f("",!0),(e==null?void 0:e.Type)==="TextArea"?(s(),C(E,{key:3,class:B({"special-form-input":!0,"form-field-error":o.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:o.form[a].value,"onUpdate:modelValue":r=>o.form[a].value=r},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue"])):(e==null?void 0:e.Type)==="Select"?(s(),C(u,{key:4,class:B({"special-form-input":!0,"form-field-error":o.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,options:e==null?void 0:e.Options,modelValue:o.form[a].value,"onUpdate:modelValue":r=>o.form[a].value=r},null,8,["class","label","placeholder","necessarily","name","options","modelValue","onUpdate:modelValue"])):f("",!0)])]))),256))]),o.templateValue!==0?(s(),t("div",Ge,[o.fileList.length?(s(),t("div",Je,[n("div",Ke,[(s(!0),t(T,null,H(o.fileList,(e,a)=>(s(),t("div",Qe,[n("span",null,[_(d,{class:"file-icon"})]),n("span",null,te(e==null?void 0:e.name),1),n("button",{class:"close",onClick:r=>o.removeFile(a)},[_(M)],8,We)]))),256))])])):f("",!0),n("label",Xe,[n("div",Ye,[n("input",{ref:"fileInput",type:"file",onChange:c[1]||(c[1]=(...e)=>o.fileUpload&&o.fileUpload(...e))},null,544),n("div",Ze,[_(D),$e])])]),_(y,{class:"rule-checkbox",name:"checkbox",checked:"",modelValue:o.agree,"onUpdate:modelValue":c[2]||(c[2]=e=>o.agree=e)},{default:q(()=>[N("Подтверждаю свое согласие на передачу информации в электроной форме (в том числе персональных данных) по открытым каналам связи сети Интернет")]),_:1},8,["modelValue"]),_(y,{class:"rule-checkbox",name:"checkbox",checked:"",modelValue:o.agreeHandle,"onUpdate:modelValue":c[3]||(c[3]=e=>o.agreeHandle=e)},{default:q(()=>[N("Я разрешаю передачу своих персональных данных третьим лицам, для выполнения заказанных мною услуг (регистрация доменто и т.п.)")]),_:1},8,["modelValue"])])):f("",!0),o.createProfileErrorMessage?(s(),t("div",{key:1,class:"error-message width-limit",innerHTML:o.formErrorMessage(o.createProfileErrorMessage)},null,8,el)):f("",!0),o.templateValue!==0?(s(),C(L,{key:2,class:"width-limit","is-loading":o.createProfileResult==="pending",label:"Зарегистрировать",onClick:c[4]||(c[4]=e=>o.checkForm())},null,8,["is-loading"])):f("",!0)])])],64)}const ol={components:{TextAreaLine:_e,simpleCheckbox:de,basicInput:fe,SelectLine:he,PhoneInput:pe,IconFile:ie,IconClose:ge,BlockBalanceAgreement:ve},setup(){const g=Ve(),c=Ee(),P=Ie(),o=De(),m=Te(),p=Pe(),b=h(!1),V=h(!1),v=h({}),x=h(null),i=h([]),k=h(null),E=h(null),u=h(0),d=ue(()=>g.ConfigList),M=ue(()=>{var l;return(l=P==null?void 0:P.userInfo)==null?void 0:l.Email}),D=d.value,y={};for(const l in D.Regulars)y[l]=D.Regulars[l];const L=l=>y[l]||null,R=()=>{const l=d.value;return l&&l.Regulars&&l.Regulars.Phone?new RegExp(l.Regulars.Phone):/^[0-9]{10}$/};function U(l){i.value=i.value.filter((e,a)=>a!==l)}function w(){if(b.value&&V.value){let l=new FormData;Object.keys(v.value).forEach(e=>{var a;l.append(e,(a=v.value[e])==null?void 0:a.value)}),l.append("TemplateID",u.value),l.append("Agree",b.value),l.append("AgreeHandle",V.value),i.value.forEach((e,a)=>{l.append("Document[]",e==null?void 0:e.value)}),k.value="pending",c.createNewContractFormData(l).then(e=>{var a;e.status==="success"?(k.value="success",p.query.Contract?m.push(`/Balance/${p.query.Contract}`):!p.query.Contract&&p.query.TemplateID?m.push("/Basket"):m.push("/Contracts")):(k.value="error",E.value=(a=e.data)==null?void 0:a.Exception)})}}function F(l){return""+I(l)}function I(l){return l?"<div>"+(l==null?void 0:l.String)+"</div>"+I(l==null?void 0:l.Parent):""}function S(l){var a;let e=((a=l.target.files)==null?void 0:a[0])||null;e&&(x.value.value="",o.sendFile(e).then(r=>{i.value.push({name:e==null?void 0:e.name,value:r.slice(r.lastIndexOf("^")+1,r.length)})}))}return me(u,()=>{var l,e,a,r,j;v.value={},Object.keys((j=(r=(a=(e=(l=d.value)==null?void 0:l.Profiles)==null?void 0:e.Templates)==null?void 0:a[u.value])==null?void 0:r.Template)==null?void 0:j.Attribs).forEach(A=>{var z,G,J,K,Q,W,X,Y,Z,$,ee,le,oe,ae,ne,se,re,ce;v.value[A]={error:!1,value:(((W=(Q=(K=(J=(G=(z=d.value)==null?void 0:z.Profiles)==null?void 0:G.Templates)==null?void 0:J[u.value])==null?void 0:K.Template)==null?void 0:Q.Attribs[A])==null?void 0:W.Value)==="%Email%"?M.value:(le=(ee=($=(Z=(Y=(X=d.value)==null?void 0:X.Profiles)==null?void 0:Y.Templates)==null?void 0:Z[u.value])==null?void 0:$.Template)==null?void 0:ee.Attribs[A])==null?void 0:le.Value)||null,required:((ce=(re=(se=(ne=(ae=(oe=d.value)==null?void 0:oe.Profiles)==null?void 0:ae.Templates)==null?void 0:ne[u.value])==null?void 0:se.Template)==null?void 0:re.Attribs[A])==null?void 0:ce.IsDuty)==="1"}})}),xe(()=>{var l,e;console.log(p),(e=(l=d.value)==null?void 0:l.Contracts)!=null&&e.Types&&p.query.TemplateID&&(u.value=p.query.TemplateID)}),{getConfig:d,templateValue:u,agreeHandle:V,agree:b,createProfileResult:k,createProfileErrorMessage:E,checkForm:w,formErrorMessage:F,form:v,fileInput:x,fileList:i,removeFile:U,fileUpload:S,getPhoneRegex:R,getFieldRegex:L,fieldRegexMap:y}}},Cl=be(ol,[["render",ll],["__scopeId","data-v-ac4e889e"]]);export{Cl as default};

import{j as ge,o as n,c as t,a as s,b as h,w as m,d as N,F as D,v as H,k as p,x as b,t as ce,n as A,p as ie,l as ke,_ as be,u as Ce,f as Ve,e as ye,r as _,g as ue,h as Ie}from"./index-1c309346.js";import{u as Te}from"./contracts-7b919c67.js";import{s as te}from"./SimpleCheckbox-5ed39d0e.js";import{_ as de}from"./BasicInput-33bf464f.js";import{P as fe,T as pe,S as _e}from"./PhoneInput-6d09e8c6.js";import{u as Pe}from"./files-9e0f878c.js";import{I as ve}from"./IconFile-079fb40d.js";import{I as he}from"./IconClose-7cf50ca7.js";import{_ as xe}from"./ButtonDefault-cd183bf8.js";import{f as Ee}from"./bootstrap-vue-next.es-b799f76d.js";import{_ as Ue}from"./IconClip-fc8c1c22.js";import{_ as we}from"./ClausesKeeper-b9906182.js";import"./component2-139d1cad.js";import"./component-a2c8255f.js";import"./multiselect-b7ad6876.js";const O=g=>(ie("data-v-37d235f2"),g=g(),ke(),g),Fe={class:"section"},Re={class:"container"},Se={class:"section-header"},De=O(()=>s("h1",{class:"section-title"},"Создать профиль",-1)),Ae=O(()=>s("div",{class:"section-header"},[s("h2",{class:"section-block_title"},"Шаблон")],-1)),Le={class:"form"},Me={class:"form"},me={class:"form-row-wrapper"},Ne={class:"special-form-row"},He={key:0,class:"section-header inputs-block"},Oe={class:"section-block_title"},je={key:0,class:"form width-limit"},Be={key:0,class:"form-field form-field--file form-field--basic"},qe={class:"chat-files"},ze={class:"chat-files__item"},Ge=["onClick"],Je={class:"form-field form-field--file form-field--basic"},Ke={class:"form-field-input__file-wrapper"},Qe={class:"form-field-input__file"},We=O(()=>s("span",null,"Прикрепить документ подтверждающий личность",-1)),Xe=["innerHTML"];function Ye(g,c,y,l,j,I){var E,U,w,F,V,R,o;const C=ge("router-link"),u=we,T=te,v=de,i=fe,P=pe,d=_e,f=ve,L=he,x=Ue,k=Ee,M=xe;return n(),t("div",Fe,[s("div",Re,[s("div",Se,[h(C,{class:"section-label",to:"/Contracts"},{default:m(()=>[N("Мои договора")]),_:1}),De]),h(u),Ae,s("div",Le,[(n(!0),t(D,null,H((U=(E=l.getConfig)==null?void 0:E.Contracts)==null?void 0:U.Types,e=>(n(),t(D,null,[(e==null?void 0:e.ProfileTemplateID)!==""?(n(),b(T,{key:0,label:e==null?void 0:e.Name,value:e==null?void 0:e.ProfileTemplateID,modelValue:l.templateValue,"onUpdate:modelValue":c[0]||(c[0]=a=>l.templateValue=a)},null,8,["label","value","modelValue"])):p("",!0)],64))),256))]),s("div",Me,[(n(!0),t(D,null,H((o=(R=(V=(F=(w=l.getConfig)==null?void 0:w.Profiles)==null?void 0:F.Templates)==null?void 0:V[l.templateValue])==null?void 0:R.Template)==null?void 0:o.Attribs,(e,a)=>(n(),t("div",me,[s("div",Ne,[e!=null&&e.Title?(n(),t("div",He,[s("h2",Oe,ce(e==null?void 0:e.Title),1)])):p("",!0),(e==null?void 0:e.Type)==="Input"&&(e==null?void 0:e.Check)!=="Phone"?(n(),b(v,{key:1,class:A({"special-form-input":!0,"form-field-error":l.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:l.form[a].value,"onUpdate:modelValue":r=>l.form[a].value=r,pattern:l.getFieldRegex(e==null?void 0:e.Check)},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue","pattern"])):p("",!0),(e==null?void 0:e.Type)==="Input"&&(e==null?void 0:e.Check)==="Phone"?(n(),b(i,{key:2,class:A({"special-form-input":!0,"form-field-error":l.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:l.form[a].value,"onUpdate:modelValue":r=>l.form[a].value=r,pattern:l.getPhoneRegex()},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue","pattern"])):p("",!0),(e==null?void 0:e.Type)==="TextArea"?(n(),b(P,{key:3,class:A({"special-form-input":!0,"form-field-error":l.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,modelValue:l.form[a].value,"onUpdate:modelValue":r=>l.form[a].value=r},null,8,["class","label","placeholder","necessarily","name","modelValue","onUpdate:modelValue"])):(e==null?void 0:e.Type)==="Select"?(n(),b(d,{key:4,class:A({"special-form-input":!0,"form-field-error":l.form[a].error}),label:e==null?void 0:e.Comment,placeholder:e==null?void 0:e.Example,necessarily:(e==null?void 0:e.IsDuty)==="1",name:e==null?void 0:e.Check,options:e==null?void 0:e.Options,modelValue:l.form[a].value,"onUpdate:modelValue":r=>l.form[a].value=r},null,8,["class","label","placeholder","necessarily","name","options","modelValue","onUpdate:modelValue"])):p("",!0)])]))),256))]),l.templateValue!==0?(n(),t("div",je,[l.fileList.length?(n(),t("div",Be,[s("div",qe,[(n(!0),t(D,null,H(l.fileList,(e,a)=>(n(),t("div",ze,[s("span",null,[h(f,{class:"file-icon"})]),s("span",null,ce(e==null?void 0:e.name),1),s("button",{class:"close",onClick:r=>l.removeFile(a)},[h(L)],8,Ge)]))),256))])])):p("",!0),s("label",Je,[s("div",Ke,[s("input",{ref:"fileInput",type:"file",onChange:c[1]||(c[1]=(...e)=>l.fileUpload&&l.fileUpload(...e))},null,544),s("div",Qe,[h(x),We])])]),h(k,{class:"rule-checkbox",name:"checkbox",checked:"",modelValue:l.agree,"onUpdate:modelValue":c[2]||(c[2]=e=>l.agree=e)},{default:m(()=>[N("Подтверждаю свое согласие на передачу информации в электроной форме (в том числе персональных данных) по открытым каналам связи сети Интернет")]),_:1},8,["modelValue"]),h(k,{class:"rule-checkbox",name:"checkbox",checked:"",modelValue:l.agreeHandle,"onUpdate:modelValue":c[3]||(c[3]=e=>l.agreeHandle=e)},{default:m(()=>[N("Я разрешаю передачу своих персональных данных третьим лицам, для выполнения заказанных мною услуг (регистрация доменто и т.п.)")]),_:1},8,["modelValue"])])):p("",!0),l.createProfileErrorMessage?(n(),t("div",{key:1,class:"error-message width-limit",innerHTML:l.formErrorMessage(l.createProfileErrorMessage)},null,8,Xe)):p("",!0),l.templateValue!==0?(n(),b(M,{key:2,class:"width-limit","is-loading":l.createProfileResult==="pending",label:"Зарегистрировать",onClick:c[4]||(c[4]=e=>l.checkForm())},null,8,["is-loading"])):p("",!0)])])}const Ze={components:{TextAreaLine:pe,simpleCheckbox:te,basicInput:de,SelectLine:_e,PhoneInput:fe,IconFile:ve,IconClose:he},setup(){const g=Ce(),c=Te(),y=Ve(),l=Pe(),j=ye(),I=_(!1),C=_(!1),u=_({}),T=_(null),v=_([]),i=_(null),P=_(null),d=_(0),f=ue(()=>g.ConfigList),L=ue(()=>{var o;return(o=y==null?void 0:y.userInfo)==null?void 0:o.Email}),x=f.value,k={};for(const o in x.Regulars)k[o]=x.Regulars[o];const M=o=>k[o]||null,E=()=>{const o=f.value;return o&&o.Regulars&&o.Regulars.Phone?new RegExp(o.Regulars.Phone):/^[0-9]{10}$/};function U(o){v.value=v.value.filter((e,a)=>a!==o)}function w(){let o=!1;if(Object.keys(u.value).forEach(e=>{u.value[e].value===null&&u.value[e].required?(u.value[e].error=!0,o=!0):u.value[e].error=!1}),!o&&I.value&&C.value){let e=new FormData;Object.keys(u.value).forEach(a=>{var r;e.append(a,(r=u.value[a])==null?void 0:r.value)}),e.append("TemplateID",d.value),e.append("Agree",I.value),e.append("AgreeHandle",C.value),v.value.forEach((a,r)=>{e.append("Document[]",a==null?void 0:a.value)}),i.value="pending",c.createNewContractFormData(e).then(a=>{var r;a.status==="success"?(i.value="success",j.push("/Contracts")):(i.value="error",P.value=(r=a.data)==null?void 0:r.Exception)})}}function F(o){return""+V(o)}function V(o){return o?"<div>"+(o==null?void 0:o.String)+"</div>"+V(o==null?void 0:o.Parent):""}function R(o){var a;let e=((a=o.target.files)==null?void 0:a[0])||null;e&&(T.value.value="",l.sendFile(e).then(r=>{v.value.push({name:e==null?void 0:e.name,value:r.slice(r.lastIndexOf("^")+1,r.length)})}))}return Ie(d,()=>{var o,e,a,r,B;u.value={},Object.keys((B=(r=(a=(e=(o=f.value)==null?void 0:o.Profiles)==null?void 0:e.Templates)==null?void 0:a[d.value])==null?void 0:r.Template)==null?void 0:B.Attribs).forEach(S=>{var q,z,G,J,K,Q,W,X,Y,Z,$,ee,le,oe,ae,re,se,ne;u.value[S]={error:!1,value:(((Q=(K=(J=(G=(z=(q=f.value)==null?void 0:q.Profiles)==null?void 0:z.Templates)==null?void 0:G[d.value])==null?void 0:J.Template)==null?void 0:K.Attribs[S])==null?void 0:Q.Value)==="%Email%"?L.value:(ee=($=(Z=(Y=(X=(W=f.value)==null?void 0:W.Profiles)==null?void 0:X.Templates)==null?void 0:Y[d.value])==null?void 0:Z.Template)==null?void 0:$.Attribs[S])==null?void 0:ee.Value)||null,required:((ne=(se=(re=(ae=(oe=(le=f.value)==null?void 0:le.Profiles)==null?void 0:oe.Templates)==null?void 0:ae[d.value])==null?void 0:re.Template)==null?void 0:se.Attribs[S])==null?void 0:ne.IsDuty)==="1"}})}),{getConfig:f,templateValue:d,agreeHandle:C,agree:I,createProfileResult:i,createProfileErrorMessage:P,checkForm:w,formErrorMessage:F,form:u,fileInput:T,fileList:v,removeFile:U,fileUpload:R,getPhoneRegex:E,getFieldRegex:M,fieldRegexMap:k}}},vl=be(Ze,[["render",Ye],["__scopeId","data-v-37d235f2"]]);export{vl as default};

import{j as x,o as b,c as k,b as u,a as l,w as y,d as L,n as g,t as w,Y as j,af as O,F as P,v as A,k as S,p as z,l as K,_ as R,u as Y,e as H,r as C,g as B}from"./index-eaeb1281.js";import{u as J}from"./edesks-e3a5f2cc.js";import{u as Q}from"./files-10cb011d.js";import{_ as F}from"./BasicInput-f68fac7f.js";import{s as W}from"./multiselect-92d0918e.js";import{I as X}from"./IconUpload-3f009013.js";import{I as U}from"./IconFile-d1504561.js";import{I as N}from"./IconClose-e8029cfe.js";import{_ as q}from"./ClausesKeeper-a152fc61.js";import{B as M}from"./BlockBalanceAgreement-11afb494.js";import{_ as Z}from"./IconClip-7d6e08b2.js";import"./component2-c4162fc9.js";import"./component-92ce667a.js";import"./contracts-ac470ffa.js";import"./bootstrap-vue-next.es-f4c478af.js";import"./ButtonDefault-598fef28.js";import"./IconArrow-ce1bc7b6.js";const d=a=>(z("data-v-c358dbd9"),a=a(),K(),a),$={class:"section"},ee={class:"container"},le={class:"section-header"},oe=d(()=>l("h1",{class:"section-title"},"Создать тикет",-1)),se={class:"form-field form-field--basic"},te=d(()=>l("div",{class:"form-field__label"},"Отдел",-1)),ae={class:"form-field-input__wrapper"},ie={class:"multiselect-option"},re={class:"form-field form-field--basic"},ne=d(()=>l("div",{class:"form-field__label"},"Приоритет",-1)),ce={class:"form-field-input__wrapper"},ue={class:"multiselect-option"},de={class:"form-field form-field--textarea form-field--basic"},fe=d(()=>l("div",{class:"form-field__label"},"Сообщение",-1)),_e={class:"form-field-input__wrapper"},me={key:0,class:"form-field form-field--file form-field--basic"},pe=d(()=>l("div",{class:"form-field__label"},null,-1)),ve={class:"form-field-input__wrapper"},he={class:"chat-files"},be={class:"chat-files__item"},ke=["onClick"],ge={class:"form-field form-field--file form-field--basic"},Ie=d(()=>l("div",{class:"form-field__label"},null,-1)),De={class:"form-field-input__wrapper"},Ee={class:"form-field-input__file"},ye=d(()=>l("span",null,"Прикрепить файл",-1)),we={class:"form-offset_label"},Ce=d(()=>l("span",null,"Отправить",-1)),Te=[Ce];function Ve(a,i,T,o,r,I){const f=M,p=x("router-link"),v=q,D=F,h=x("Multiselect"),E=U,s=N,t=Z;return b(),k(P,null,[u(f),l("div",$,[l("div",ee,[l("div",le,[u(p,{class:"section-label",to:"/Tickets"},{default:y(()=>[L("Служба поддержки")]),_:1}),oe]),u(v),u(D,{class:g({"form-error":o.formData.theme.isError}),label:"Тема запроса",placeholder:"Заголовок",name:"login",modelValue:o.formData.theme.value,"onUpdate:modelValue":i[0]||(i[0]=e=>o.formData.theme.value=e)},null,8,["class","modelValue"]),l("label",se,[te,l("div",ae,[u(h,{class:g({"multiselect--white":!0,"form-error":o.formData.selectGroup.isError}),modelValue:o.formData.selectGroup.value,"onUpdate:modelValue":i[1]||(i[1]=e=>o.formData.selectGroup.value=e),options:o.getTicketGroups,label:"name"},{option:y(({option:e})=>[l("div",ie,w(e.name),1)]),_:1},8,["class","modelValue","options"])])]),l("label",re,[ne,l("div",ce,[u(h,{class:g({"multiselect--white":!0,"form-error":o.formData.selectPriority.isError}),modelValue:o.formData.selectPriority.value,"onUpdate:modelValue":i[2]||(i[2]=e=>o.formData.selectPriority.value=e),options:o.getEDeskPriorities,label:"name"},{option:y(({option:e})=>[l("div",ue,w(e.name),1)]),_:1},8,["class","modelValue","options"])])]),l("label",de,[fe,l("div",_e,[j(l("textarea",{class:g({"form-error":o.formData.message.isError}),placeholder:"Опишите проблему, указав сервер размещения, вашу учётную запись на сервере, имя сайта, УРЛ ошибки и способ её воспроизведения.","onUpdate:modelValue":i[3]||(i[3]=e=>o.formData.message.value=e)},null,2),[[O,o.formData.message.value]])])]),o.fileList.length?(b(),k("div",me,[pe,l("div",ve,[l("div",he,[(b(!0),k(P,null,A(o.fileList,(e,n)=>(b(),k("div",be,[l("span",null,[u(E,{class:"file-icon"})]),l("span",null,w(e==null?void 0:e.name),1),l("button",{class:"close",onClick:_=>o.removeFile(n)},[u(s)],8,ke)]))),256))])])])):S("",!0),l("label",ge,[Ie,l("div",De,[l("input",{ref:"fileInput",type:"file",onChange:i[4]||(i[4]=(...e)=>o.fileUpload&&o.fileUpload(...e))},null,544),l("div",Ee,[u(t),ye])])]),l("div",we,[l("button",{class:"btn btn--blue btn-default",onClick:i[5]||(i[5]=e=>o.createTicket())},Te)])])])],64)}const Ge={components:{IconUpload:X,IconFile:U,IconClose:N,BasicInput:F,Multiselect:W,ClausesKeeper:q,BlockBalanceAgreement:M},async setup(){const a=J(),i=Y(),T=Q(),o=H(),r=C({theme:{value:null,required:!0,isError:!1},selectGroup:{value:null,required:!0,isError:!1},selectPriority:{value:null,required:!0,isError:!1},message:{value:null,required:!0,isError:!1},file:{value:null,required:!1,isError:!1}}),I=C(null),f=C([]),p=B(()=>{var s;return(s=Object.keys((a==null?void 0:a.eDeskGroups)||[]))==null?void 0:s.map(t=>{var e,n,_,m;return{value:(n=(e=a==null?void 0:a.eDeskGroups)==null?void 0:e[t])==null?void 0:n.ID,name:(m=(_=a==null?void 0:a.eDeskGroups)==null?void 0:_[t])==null?void 0:m.Name}})}),v=B(()=>{var s,t;return(t=(s=i==null?void 0:i.ConfigList)==null?void 0:s.Edesks)==null?void 0:t.Priorities});function D(s){var t;r.value.file.value=((t=s.target.files)==null?void 0:t[0])||null,r.value.file.value&&(I.value.value="",T.sendFile(r.value.file.value).then(e=>{var n;f.value.push({name:(n=r.value.file.value)==null?void 0:n.name,value:e.slice(e.lastIndexOf("^")+1,e.length)})}))}function h(){var t,e,n,_,m;let s=!1;Object.keys(r.value).map(c=>{var V,G;(V=r.value)!=null&&V[c].required&&((G=r.value)==null?void 0:G[c].value)===null&&(r.value[c].isError=!0,s=!0)}),s||a.ticketEdit({Theme:((t=r.value)==null?void 0:t.theme.value)||null,TargetGroupID:((e=r.value)==null?void 0:e.selectGroup.value)||null,PriorityID:((n=r.value)==null?void 0:n.selectPriority.value)||null,Message:((_=r.value)==null?void 0:_.message.value)||null,TicketMessageFile:((m=f.value)==null?void 0:m.map(c=>c==null?void 0:c.value))||null}).then(c=>{c==="success"&&o.push({name:"default.Tickets"})})}function E(s){f.value=f.value.filter((t,e)=>e!==s)}return await a.fetchTicketGroups().then(()=>{var s,t,e;r.value.selectGroup.value=(t=(s=p.value)==null?void 0:s[0])==null?void 0:t.value,r.value.selectPriority.value=(e=Object.keys(v.value))==null?void 0:e[0]}),{getTicketGroups:p,getEDeskPriorities:v,formData:r,fileList:f,fileInput:I,removeFile:E,fileUpload:D,createTicket:h}}},He=R(Ge,[["render",Ve],["__scopeId","data-v-c358dbd9"]]);export{He as default};

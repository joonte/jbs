import{j as B,o as b,c as g,b as u,a as l,w,d as O,n as y,t as C,Z as j,af as K,F as P,x as A,k as S,p as z,l as R,_ as Z,u as H,e as J,r as T,g as F,h as Q,O as W}from"./index-5e313302.js";import{u as X}from"./edesks-38ed672b.js";import{u as Y}from"./files-1da1c955.js";import{_ as U}from"./BasicInput-be1c88a0.js";import{s as $}from"./multiselect-bfc29db8.js";import{I as ee}from"./IconUpload-ea1d54f5.js";import{I as L}from"./IconFile-44cc57ed.js";import{I as M}from"./IconClose-1f889a2e.js";import{_ as N}from"./ClausesKeeper-eb954229.js";import{B as q}from"./BlockBalanceAgreement-bb87adc1.js";import{_ as le}from"./IconClip-25c35fb1.js";import"./component-6a5a025c.js";import"./contracts-3351d68f.js";import"./bootstrap-vue-next.es-a19a8b41.js";import"./ButtonDefault-7c216fc6.js";import"./IconArrow-555f9d45.js";const d=t=>(z("data-v-a531697c"),t=t(),R(),t),oe={class:"section"},se={class:"container"},te={class:"section-header"},ae=d(()=>l("h1",{class:"section-title"},"Создать тикет",-1)),ie={class:"form-field form-field--basic"},re=d(()=>l("div",{class:"form-field__label"},"Отдел",-1)),ne={class:"form-field-input__wrapper"},ce={class:"multiselect-option"},ue={class:"form-field form-field--basic"},de=d(()=>l("div",{class:"form-field__label"},"Приоритет",-1)),fe={class:"form-field-input__wrapper"},me={class:"multiselect-option"},_e={class:"form-field form-field--textarea form-field--basic"},pe=d(()=>l("div",{class:"form-field__label"},"Сообщение",-1)),ve={class:"form-field-input__wrapper"},he={key:0,class:"form-field form-field--file form-field--basic"},ke=d(()=>l("div",{class:"form-field__label"},null,-1)),be={class:"form-field-input__wrapper"},ge={class:"chat-files"},ye={class:"chat-files__item"},Ee=["onClick"],Ie={class:"form-field form-field--file form-field--basic"},De=d(()=>l("div",{class:"form-field__label"},null,-1)),we={class:"form-field-input__wrapper"},Ce={class:"form-field-input__file"},Te=d(()=>l("span",null,"Прикрепить файл",-1)),Ve={class:"form-offset_label"},Ge=d(()=>l("span",null,"Отправить",-1)),xe=[Ge];function Be(t,a,V,o,i,E){const f=q,v=B("router-link"),h=N,I=U,m=B("Multiselect"),k=L,D=M,s=le;return b(),g(P,null,[u(f),l("div",oe,[l("div",se,[l("div",te,[u(v,{class:"section-label",to:"/Tickets"},{default:w(()=>[O("Служба поддержки")]),_:1}),ae]),u(h),u(I,{class:y({"form-error":o.formData.theme.isError}),label:"Тема запроса",placeholder:"Заголовок",name:"login",modelValue:o.formData.theme.value,"onUpdate:modelValue":a[0]||(a[0]=e=>o.formData.theme.value=e)},null,8,["class","modelValue"]),l("label",ie,[re,l("div",ne,[u(m,{class:y({"multiselect--white":!0,"form-error":o.formData.selectGroup.isError}),modelValue:o.formData.selectGroup.value,"onUpdate:modelValue":a[1]||(a[1]=e=>o.formData.selectGroup.value=e),options:o.getTicketGroups,label:"name"},{option:w(({option:e})=>[l("div",ce,C(e.name),1)]),_:1},8,["class","modelValue","options"])])]),l("label",ue,[de,l("div",fe,[u(m,{class:y({"multiselect--white":!0,"form-error":o.formData.selectPriority.isError}),modelValue:o.formData.selectPriority.value,"onUpdate:modelValue":a[2]||(a[2]=e=>o.formData.selectPriority.value=e),options:o.getEDeskPriorities,label:"name"},{option:w(({option:e})=>[l("div",me,C(e.name),1)]),_:1},8,["class","modelValue","options"])])]),l("label",_e,[pe,l("div",ve,[j(l("textarea",{class:y({"form-error":o.formData.message.isError}),placeholder:"Опишите проблему, указав сервер размещения, вашу учётную запись на сервере, имя сайта, УРЛ ошибки и способ её воспроизведения.","onUpdate:modelValue":a[3]||(a[3]=e=>o.formData.message.value=e)},null,2),[[K,o.formData.message.value]])])]),o.fileList.length?(b(),g("div",he,[ke,l("div",be,[l("div",ge,[(b(!0),g(P,null,A(o.fileList,(e,r)=>(b(),g("div",ye,[l("span",null,[u(k,{class:"file-icon"})]),l("span",null,C(e==null?void 0:e.name),1),l("button",{class:"close",onClick:n=>o.removeFile(r)},[u(D)],8,Ee)]))),256))])])])):S("",!0),l("label",Ie,[De,l("div",we,[l("input",{ref:"fileInput",type:"file",onChange:a[4]||(a[4]=(...e)=>o.fileUpload&&o.fileUpload(...e))},null,544),l("div",Ce,[u(s),Te])])]),l("div",Ve,[l("button",{class:"btn btn--blue btn-default",onClick:a[5]||(a[5]=e=>o.createTicket())},xe)])])])],64)}const Pe={components:{IconUpload:ee,IconFile:L,IconClose:M,BasicInput:U,Multiselect:$,ClausesKeeper:N,BlockBalanceAgreement:q},async setup(){const t=X(),a=H(),V=Y(),o=J(),i=T({theme:{value:null,required:!0,isError:!1},selectGroup:{value:null,required:!0,isError:!1},selectPriority:{value:null,required:!0,isError:!1},message:{value:null,required:!0,isError:!1},file:{value:null,required:!1,isError:!1}}),E=T(null),f=T([]),v=F(()=>{var s;return(s=Object.keys((t==null?void 0:t.eDeskGroups)||[]))==null?void 0:s.map(e=>{var r,n,_,p;return{value:(n=(r=t==null?void 0:t.eDeskGroups)==null?void 0:r[e])==null?void 0:n.ID,name:(p=(_=t==null?void 0:t.eDeskGroups)==null?void 0:_[e])==null?void 0:p.Name}})}),h=F(()=>{var s,e;return(e=(s=a==null?void 0:a.ConfigList)==null?void 0:s.Edesks)==null?void 0:e.Priorities});function I(s){var e;i.value.file.value=((e=s.target.files)==null?void 0:e[0])||null,i.value.file.value&&(E.value.value="",V.sendFile(i.value.file.value).then(r=>{var n;f.value.push({name:(n=i.value.file.value)==null?void 0:n.name,value:r.slice(r.lastIndexOf("^")+1,r.length)})}))}const m=s=>{s.key==="Enter"&&s.ctrlKey&&k()};Q(()=>{document.addEventListener("keyup",m)}),W(()=>{document.removeEventListener("keyup",m)});function k(){var e,r,n,_,p;let s=!1;Object.keys(i.value).map(c=>{var G,x;(G=i.value)!=null&&G[c].required&&((x=i.value)==null?void 0:x[c].value)===null&&(i.value[c].isError=!0,s=!0)}),s||t.ticketEdit({Theme:((e=i.value)==null?void 0:e.theme.value)||null,TargetGroupID:((r=i.value)==null?void 0:r.selectGroup.value)||null,PriorityID:((n=i.value)==null?void 0:n.selectPriority.value)||null,Message:((_=i.value)==null?void 0:_.message.value)||null,TicketMessageFile:((p=f.value)==null?void 0:p.map(c=>c==null?void 0:c.value))||null}).then(c=>{c==="success"&&o.push({name:"default.Tickets"})})}function D(s){f.value=f.value.filter((e,r)=>r!==s)}return await t.fetchTicketGroups().then(()=>{var s,e,r;i.value.selectGroup.value=(e=(s=v.value)==null?void 0:s[0])==null?void 0:e.value,i.value.selectPriority.value=(r=Object.keys(h.value))==null?void 0:r[0]}),{getTicketGroups:v,getEDeskPriorities:h,formData:i,fileList:f,fileInput:E,removeFile:D,fileUpload:I,createTicket:k}}},Qe=Z(Pe,[["render",Be],["__scopeId","data-v-a531697c"]]);export{Qe as default};

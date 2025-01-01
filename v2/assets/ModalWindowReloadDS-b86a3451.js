import{o as I,c as T,a as e,n as d,t as k,Z as l,ao as i,b as C,p as R,l as O,_ as M,r as m,S as V,i as g,O as U}from"./index-6f846e0d.js";import{u as x}from"./resetServer-008fdf83.js";import{u as B}from"./dedicatedServer-6e120d8e.js";import{_ as E}from"./ButtonDefault-40efcd87.js";const t=n=>(R("data-v-30326c2a"),n=n(),O(),n),j={class:"modal__body"},L=t(()=>e("div",{class:"list-item__title"},"Питание сервера",-1)),N={class:"list-item__status"},W=t(()=>e("div",{class:"list-item__status-text"},"Текущий статус",-1)),z=t(()=>e("div",{class:"list-item__subtitle"},"Выбор команды",-1)),K={class:"form"},Z=["disabled"],q=t(()=>e("span",{class:"form-input_border-content"},[e("span",{class:"form-input_border-mark"}),e("span",{class:"form-input_border-info"},[e("span",{class:"form-input_border-address"},"Включить")])],-1)),A=["disabled"],F=t(()=>e("span",{class:"form-input_border-content"},[e("span",{class:"form-input_border-mark"}),e("span",{class:"form-input_border-info"},[e("span",{class:"form-input_border-address"},"Выключить")])],-1)),G=["disabled"],H=t(()=>e("span",{class:"form-input_border-content"},[e("span",{class:"form-input_border-mark"}),e("span",{class:"form-input_border-info"},[e("span",{class:"form-input_border-address"},"Корректно выключить")])],-1)),J=["disabled"],Q=t(()=>e("span",{class:"form-input_border-content"},[e("span",{class:"form-input_border-mark"}),e("span",{class:"form-input_border-info"},[e("span",{class:"form-input_border-address"},"Выключить/включить")])],-1)),X=["disabled"],Y=t(()=>e("span",{class:"form-input_border-content"},[e("span",{class:"form-input_border-mark"}),e("span",{class:"form-input_border-info"},[e("span",{class:"form-input_border-address"},"Reset")])],-1)),$={class:"form-input_border"},ee=t(()=>e("span",{class:"form-input_border-content"},[e("span",{class:"form-input_border-mark"}),e("span",{class:"form-input_border-info"},[e("span",{class:"form-input_border-address"},"Перезагрузить IPMI")])],-1)),oe={class:"modal__buttons-wrap"};function se(n,s,p,o,u,_){const c=E;return I(),T("div",j,[L,e("div",N,[W,e("div",{class:d(["server-status",{"server-status-on":o.SystemPower==="on","server-status-off":o.SystemPower!=="on"}])},k(o.SystemPower==="on"?"Включен":"Выключен"),3)]),z,e("div",K,[e("label",{class:d(["form-input_border",{"disabled-label":o.SystemPower==="on"}])},[l(e("input",{type:"radio",name:"Command",value:"on","onUpdate:modelValue":s[0]||(s[0]=a=>o.commandType=a),disabled:o.SystemPower==="on"},null,8,Z),[[i,o.commandType]]),q],2),e("label",{class:d(["form-input_border",{"disabled-label":o.SystemPower!=="on"}])},[l(e("input",{type:"radio",name:"Command",value:"off","onUpdate:modelValue":s[1]||(s[1]=a=>o.commandType=a),disabled:o.SystemPower!=="on"},null,8,A),[[i,o.commandType]]),F],2),e("label",{class:d(["form-input_border",{"disabled-label":o.SystemPower!=="on"}])},[l(e("input",{type:"radio",name:"Command",value:"soft","onUpdate:modelValue":s[2]||(s[2]=a=>o.commandType=a),disabled:o.SystemPower!=="on"},null,8,G),[[i,o.commandType]]),H],2),e("label",{class:d(["form-input_border",{"disabled-label":o.SystemPower!=="on"}])},[l(e("input",{type:"radio",name:"Command",value:"cycle","onUpdate:modelValue":s[3]||(s[3]=a=>o.commandType=a),disabled:o.SystemPower!=="on"},null,8,J),[[i,o.commandType]]),Q],2),e("label",{class:d(["form-input_border",{"disabled-label":o.SystemPower!=="on"}])},[l(e("input",{type:"radio",name:"Command",value:"reset","onUpdate:modelValue":s[4]||(s[4]=a=>o.commandType=a),disabled:o.SystemPower!=="on"},null,8,X),[[i,o.commandType]]),Y],2),e("label",$,[l(e("input",{type:"radio",name:"Command",value:"mc","onUpdate:modelValue":s[5]||(s[5]=a=>o.commandType=a)},null,512),[[i,o.commandType]]),ee])]),e("div",oe,[C(c,{class:"modal__button yes",label:"Выполнить",onClick:s[6]||(s[6]=a=>o.acceptReload())})])])}const ae={props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(n,{emit:s}){var S,v,h,w;const p=x(),o=m(""),u=V("emitter"),_=B(),c=m(((S=n.data)==null?void 0:S.isReloading)||""),a=m(((v=n.data)==null?void 0:v.DSOrderID)||""),P=m(((h=n.data)==null?void 0:h.OrderID)||""),D=m((w=n.data)==null?void 0:w.SystemPower);function b(){c.value=!0,n.data.onReloadStatusChange(!0),p.ServerReset(a.value,o.value).then(()=>{u.emit("open-modal",{component:"Suspense"}),setTimeout(()=>{f(),c.value=!1,n.data.onReloadStatusChange(!1),_.fetchDSOrders().then(()=>{_.fetchDSOrderIPMI(P.value).then(r=>{}).catch(r=>{console.error("fetchDSOrderIPMI error:",r)})}).catch(r=>{console.error("fetchDSOrders error:",r)})},1e4)})}function f(){s("modalClose")}const y=r=>{r.key==="Enter"&&b()};return g(()=>{document.addEventListener("keyup",y)}),U(()=>{document.removeEventListener("keyup",y)}),{acceptReload:b,closeModal:f,commandType:o,SystemPower:D}}},le=M(ae,[["render",se],["__scopeId","data-v-30326c2a"]]);export{le as default};
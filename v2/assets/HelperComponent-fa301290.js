import{_ as w}from"./ButtonDefault-0006f72a.js";import{_ as N,o as s,c as r,a as o,S,g as c,m,Z as p,ae as v,b as u,d as f,t as h,n as d,k as H,ab as k}from"./index-b5b5ce3d.js";import{_ as V}from"./IconArrow-fddabae2.js";const x={},T={width:"20",height:"20",viewBox:"0 0 20 20",fill:"none",xmlns:"http://www.w3.org/2000/svg"},M=o("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M13.75 2.5H6.25C4.17893 2.5 2.5 4.17893 2.5 6.25V13.75C2.5 15.8211 4.17893 17.5 6.25 17.5H10.4167C14.3287 17.5 17.5 14.3287 17.5 10.4167V6.25C17.5 4.17893 15.8211 2.5 13.75 2.5ZM16.6667 9.58333V6.25C16.6667 4.63917 15.3608 3.33333 13.75 3.33333H6.25C4.63917 3.33333 3.33333 4.63917 3.33333 6.25V13.75C3.33333 15.3608 4.63917 16.6667 6.25 16.6667H10.4167C11.107 16.6667 11.6667 16.107 11.6667 15.4167V13.75C11.6667 12.1392 12.9725 10.8333 14.5833 10.8333H15.4167C16.107 10.8333 16.6667 10.2737 16.6667 9.58333ZM16.6055 11.2944C16.2684 11.5291 15.8586 11.6667 15.4167 11.6667H14.5833C13.4327 11.6667 12.5 12.5994 12.5 13.75V15.4167C12.5 15.7686 12.4127 16.1001 12.2587 16.3908C14.5408 15.688 16.2656 13.7134 16.6055 11.2944Z",fill:"currentColor"},null,-1),B=[M];function I(l,e){return s(),r("svg",T,B)}const Z=N(x,[["render",I]]);const D={class:"helper-title"},E={class:"helper__content-window"},L=["innerHTML"],O={class:"helper__content-button-wrapper"},U={__name:"HelperComponent",props:{userNotice:{type:String,default:null},title:{type:String,default:"Примечание"},secondaryTitle:{type:String,default:"Добавить примечание"},id:{type:[String,Number],default:null},isInline:{type:Boolean,default:!1},emit:{type:String,default:null},position:{type:String,default:"top"},noteTable:{type:String,default:"Orders"}},setup(l){const e=l,C=S("emitter"),t=c(()=>e.userNotice!==null&&e.userNotice!==""),y=c(()=>e==null?void 0:e.position),_=c(()=>{var n;return((n=e.userNotice)==null?void 0:n.replace(/\n/g,"<br>"))||""});function a(n=!0){n&&C.emit("open-modal",{component:"NoticeEdit",data:{OrderID:e==null?void 0:e.id,UserNotice:e==null?void 0:e.userNotice,noteTable:e==null?void 0:e.noteTable,emit:e==null?void 0:e.emit}})}return(n,i)=>{const g=w;return l.isInline?(s(),r("div",{key:1,class:d(["helper__inline",[t.value?"":"empty"]]),onClickCapture:i[1]||(i[1]=k(b=>a(!0),["prevent","stop"]))},[m(n.$slots,"default",{},()=>[f(h(_.value||"Добавить примечание"),1)])],34)):(s(),r("button",{key:0,class:d(["helper",{helper__active:t.value,"helper--arrow":l.title}]),onClick:i[0]||(i[0]=b=>a(!t.value))},[m(n.$slots,"default",{},()=>[p(u(Z,{class:"helper__inactive-icon"},null,512),[[v,!t.value]]),o("div",D,[f(h(t.value?l.title:l.secondaryTitle),1),p(u(V,null,null,512),[[v,t.value]])])]),t.value?(s(),r("div",{key:0,class:d(["helper__content-window-box",y.value])},[o("div",E,[o("div",{class:"helper__content-text",innerHTML:_.value},null,8,L),o("div",O,[u(g,{class:"helper__content-button",type:"btn--border",label:"Редактировать",onClick:a})])])],2)):H("",!0)],2))}}};export{U as _};
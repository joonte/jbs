import{_ as M,o as n,c as a,a as s,b as C,t as w,k,i as L,O as B}from"./index-59dbe3d3.js";import{_ as H}from"./ButtonDefault-a62141a4.js";const S={},W={width:"32",height:"30",viewBox:"0 0 32 30",fill:"none",xmlns:"http://www.w3.org/2000/svg"},Z=s("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M15.097 0.443427C15.41 -0.147809 16.257 -0.147809 16.57 0.443427L31.57 28.7768C31.8638 29.3318 31.4615 30 30.8335 30H0.833499C0.20552 30 -0.196815 29.3318 0.0970092 28.7768L15.097 0.443427ZM15.8335 18.3333C16.2937 18.3333 16.6668 17.9602 16.6668 17.5V10.8333C16.6668 10.3731 16.2937 10 15.8335 10C15.3733 10 15.0002 10.3731 15.0002 10.8333V17.5C15.0002 17.9602 15.3733 18.3333 15.8335 18.3333ZM16.6668 23.3333V21.6667H15.0002V23.3333H16.6668ZM29.4494 28.3333H2.21759L15.8335 2.61439L29.4494 28.3333Z",fill:"#DD0035"},null,-1),D=[Z];function I(d,t){return n(),a("svg",W,D)}const E=M(S,[["render",I]]),N={class:"modal__body"},P={class:"modal__info"},O={class:"modal__alert-text"},j={key:0,class:"modal__alert-text"},A=["src"];function K(d,t,e,o,c,q){var _,l,i,r,m,u,f,g,v,h,p,x,y;const V=E,b=H;return n(),a("div",N,[C(V,{class:"modal__alert-icon"}),s("div",P,[s("div",O,w(((l=(_=e.data)==null?void 0:_.message)==null?void 0:l.String)||((r=(i=e.data)==null?void 0:i.Exception)==null?void 0:r.String)||((m=e.data)==null?void 0:m.ErrorIP)||((u=e.data)==null?void 0:u.msg)),1),(g=(f=e.data)==null?void 0:f.message)!=null&&g.Parent?(n(),a("div",j,w((p=(h=(v=e.data)==null?void 0:v.message)==null?void 0:h.Parent)==null?void 0:p.String),1)):k("",!0),(x=e.data)!=null&&x.additional_image?(n(),a("img",{key:1,class:"modal__alert-image",src:(y=e.data)==null?void 0:y.additional_image,alt:""},null,8,A)):k("",!0)]),C(b,{class:"modal__alert-button",label:"ОК",onClick:t[0]||(t[0]=z=>o.closeModal())})])}const U={components:{IconAlert:E},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(d,{emit:t}){function e(){t("modalClose")}const o=c=>{c.key==="Enter"&&e()};return L(()=>{document.addEventListener("keyup",o)}),B(()=>{document.removeEventListener("keyup",o)}),{closeModal:e}}},J=M(U,[["render",K],["__scopeId","data-v-2c3c8c8c"]]);export{J as default};

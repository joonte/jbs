import{o as b,c as v,a as d,b as r,p as f,l as S,_ as y,r as u,h as R,O as V}from"./index-5e313302.js";import{u as h}from"./vps-80bde7d9.js";import{_ as k}from"./ButtonDefault-7c216fc6.js";const p=e=>(f("data-v-ab57a1ab"),e=e(),S(),e),I={class:"modal__body"},g=p(()=>d("div",{class:"list-item__title"},"Перезагрузка выделенного сервера",-1)),C=p(()=>d("div",{class:"list-item__subtitle"},"Вы уверены, что хотите выполнить перезагрузку выделенного сервера?",-1)),P={class:"modal__buttons-wrap"};function M(e,t,l,o,c,n){const a=k;return b(),v("div",I,[g,C,d("div",P,[r(a,{class:"modal__button yes",label:"Да",onClick:t[0]||(t[0]=s=>o.acceptReload())}),r(a,{class:"modal__button",label:"Нет",onClick:t[1]||(t[1]=s=>o.closeModal())})])])}const O={props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(e,{emit:t}){var _,i;const l=h(),o=u(((_=e.data)==null?void 0:_.isReloading)||""),c=u(((i=e.data)==null?void 0:i.VPSOrderID)||"");function n(){o.value=!0,e.data.onReloadStatusChange(!0),l.VPSReboot({VPSOrderID:c.value}).then(()=>{setTimeout(()=>{a(),o.value=!1,e.data.onReloadStatusChange(!1)},1e3)})}function a(){t("modalClose")}const s=m=>{m.key==="Enter"&&n()};return R(()=>{document.addEventListener("keyup",s)}),V(()=>{document.removeEventListener("keyup",s)}),{acceptReload:n,closeModal:a}}},E=y(O,[["render",M],["__scopeId","data-v-ab57a1ab"]]);export{E as default};

import{o as d,c as r,a as s,b as m,F as A,x as P,n as O,ab as R,t as _,k as g,w as W,T as H,y as K,p as Q,l as j,_ as G,r as t,h as J,e as X,a7 as Y,g as $,i as L,O as oo,C as T}from"./index-b5b5ce3d.js";import{u as no}from"./domain-24e2548a.js";import{s as eo}from"./multiselect-4f7d14de.js";import{E as ao}from"./EmptyStateBlock-cd0c2748.js";import{b as so,f as co}from"./bootstrap-vue-next.es-37aa63ea.js";import{I as lo}from"./IconClose-cd6d3917.js";import{_ as io}from"./ButtonDefault-0006f72a.js";const ro=u=>(Q("data-v-3144f310"),u=u(),j(),u),_o={class:"section"},to={class:"container"},uo={class:"domain-block"},fo=ro(()=>s("div",{class:"section-title"},[s("h2",{class:"section-block_title"},"Проверить домен")],-1)),vo={class:"domain-field"},ho={key:0,class:"domain__grid"},po=["onClick"],ko={class:"domain__item-wrapper"},mo={class:"domain__item-info__title"},yo={class:"domain__item-info__text"},Do={class:"domain__info-wrapper"},No={class:"domain__info-wrapper-vertical"},wo={class:"domain__info-wrapper"},Io={class:"domain__info-title"},Co={class:"domain__info-title"},bo={class:"domain__package-name"},go={key:1,class:"domain__loader"},Lo={key:1,class:"domain__info-wrapper"},xo={class:"domain__price"},Eo={class:"domain__price-order"},So={class:"domain__price-prolong"};function Fo(u,l,I,a,x,C){var k;const i=so,b=io,p=lo,y=co,f=ao;return d(),r("div",_o,[s("div",to,[s("div",uo,[fo,s("div",vo,[m(i,{modelValue:a.domainName,"onUpdate:modelValue":l[0]||(l[0]=o=>a.domainName=o),placeholder:"Например, yourName",type:"text"},null,8,["modelValue"]),m(b,{label:"Подобрать",disabled:!a.domainName,onClick:l[1]||(l[1]=o=>a.checkDomain())},null,8,["disabled"])]),a.isListLoaded?(d(),r(A,{key:0},[((k=a.domainsArray)==null?void 0:k.length)>0?(d(),r("div",ho,[(d(!0),r(A,null,P(a.domainsArray,o=>(d(),r("div",{class:O(["domain__item",{domain__item_inactive:(o==null?void 0:o.isFree)===!1||(o==null?void 0:o.isFree)===null,domain__item_active:(o==null?void 0:o.ID)===a.selectedDomain}]),onClick:v=>{a.selectDomain(o),a.checkInfo(o==null?void 0:o.info)}},[o!=null&&o.info&&a.selectedDomainInfo===(o==null?void 0:o.info)?(d(),r("div",{key:0,class:"domain__item-info",onClick:l[2]||(l[2]=R(()=>{},["stop"]))},[s("div",ko,[s("div",mo,"Данные домена "+_(a.checkedDomain)+"."+_(o==null?void 0:o.Name),1),m(p,{class:"domain__item-icon",onClick:v=>a.checkInfo(o==null?void 0:o.info)},null,8,["onClick"])]),s("pre",yo,_(o==null?void 0:o.info),1)])):g("",!0),s("div",Do,[s("div",No,[s("div",wo,[s("div",Io,_(a.checkedDomain),1),s("div",Co,"."+_(o==null?void 0:o.Name),1)]),s("div",bo,_(o==null?void 0:o.PackageID),1)]),m(H,{name:"fade",mode:"out-in"},{default:W(()=>[(o==null?void 0:o.isFree)!==null?(d(),r("div",{key:0,class:O(["domain__info-status",{available:(o==null?void 0:o.isFree)===!0}])},_(o!=null&&o.isFree?"Свободен":"Занят"),3)):(d(),r("div",go))]),_:2},1024)]),o!=null&&o.isFree?(d(),r("div",Lo,[s("div",xo,[s("div",Eo,_(o==null?void 0:o.CostOrder)+" ₽ / год",1),s("div",So,"Продление: "+_(o==null?void 0:o.CostProlong)+" ₽",1)]),m(y,{class:"domain__checkbox",value:o==null?void 0:o.ID,"unchecked-value":"null",modelValue:a.selectedDomain,"onUpdate:modelValue":l[3]||(l[3]=v=>a.selectedDomain=v)},null,8,["value","modelValue"])])):g("",!0)],10,po))),256))])):(d(),K(f,{key:1,class:"domain__empty",label:"Неправильное доменное имя"}))],64)):g("",!0)])])])}const Vo={components:{Multiselect:eo},props:{search:{type:String,default:""},domainList:{type:Array,default:()=>[]}},emits:["select"],setup(u,{emit:l}){const I=no(),a=t(u.search);J(()=>u.search,n=>{a.value=n,n&&N()});const x=X(),C=Y(),i=t(null);C.query.DomainName&&(i.value=C.query.DomainName,N());const b=t(null),p=t(null),y=t(!1),f=t(null),k=t(!1),o=t([]),v=t(null),E=n=>{n.key==="Enter"&&!S.value&&N()},S=$(()=>!i.value);L(()=>{document.addEventListener("keyup",E)}),oo(()=>{document.removeEventListener("keyup",E)});let F=n=>{n.key==="Escape"&&(f.value=null)};function U(){x.replace({query:{DomainName:i.value}})}function Z(n){f.value===n?f.value=null:f.value=n}function D(){const n=document.querySelector(".domain__info-title");n&&(k.value=n.scrollWidth>n.clientWidth)}function N(){U(),I.DomainNameCheck({DomainName:i.value}).then(n=>{var V;const{result:w,resultData:e}=n;if(e!=null&&e.DomainName&&(i.value=e.DomainName,p.value=e.DomainName),e!=null&&e.Zones){o.value=u.domainList.filter(c=>{var h;return((h=e==null?void 0:e.Zones)==null?void 0:h.includes(c==null?void 0:c.Name))&&(c==null?void 0:c.IsActive)}).sort((c,h)=>(e==null?void 0:e.Zones.indexOf(c==null?void 0:c.Name))-(e==null?void 0:e.Zones.indexOf(h==null?void 0:h.Name))).map(c=>({...c,isFree:null,info:w.Info})),y.value=!0;let B=0;const M=setInterval(()=>{q(B),B++},100);setTimeout(()=>{clearInterval(M)},((V=o.value)==null?void 0:V.length)*100)}else o.value=[]})}function q(n){var w;I.DomainCheck({DomainName:p.value,DomainZone:(w=o.value[n])==null?void 0:w.Name}).then(e=>{o.value[n]={...o.value[n],isFree:e.status==="SUCCESS",info:e.info}}).catch(e=>{console.error("An error occurred:",e)})}function z(n){v.value=n==null?void 0:n.ID,l("select",{...n,line:i.value})}return L(()=>{D(),window.addEventListener("resize",D)}),T(()=>{window.removeEventListener("resize",D)}),L(()=>{window.addEventListener("keydown",F)}),T(()=>{window.removeEventListener("keydown",F)}),{domainName:i,isListLoaded:y,checkedDomain:p,domain:b,domainsArray:o,selectedDomain:v,selectDomain:z,checkDomain:N,isTextOverflow:k,checkTextOverflow:D,buttonDisabled:S,checkInfo:Z,selectedDomainInfo:f}}},zo=G(Vo,[["render",Fo],["__scopeId","data-v-3144f310"]]);export{zo as D};
import{o as l,c as f,a as C,b as $,t as b,k as d,n as D,_ as L,r as p,h as S,q as B,a2 as A,a3 as N,f as T,a7 as E,g as H,i as M,y as g,w as R,F as V,x as j,a8 as q}from"./index-642cdac5.js";import{I as w}from"./IconClose-f1b85910.js";const F={key:0},K={key:0,class:"notify-title"},O=["innerHTML"];function P(c,o,t,a,y,n){var i,u;const h=w;return a.show?(l(),f("div",F,[C("div",{class:D(["notify","notify--"+t.notify.class])},[C("button",{class:"notify-close",onClick:o[0]||(o[0]=m=>a.closeAlert())},[$(h)]),(i=t.notify)!=null&&i.title?(l(),f("div",K,b(t.notify.title),1)):d("",!0),(u=t.notify)!=null&&u.text?(l(),f("p",{key:1,innerHTML:t.notify.text},null,8,O)):d("",!0)],2)])):d("",!0)}const z={components:{IconClose:w},props:{show:{type:Boolean,default:!1},notify:{type:Object,default:()=>{}}},emits:["close"],setup(c,{emit:o}){const t=p(!1);function a(){t.value=!1,o("close")}return S(()=>{t.value=c.show}),{show:t,closeAlert:a}}},G=L(z,[["render",P],["__scopeId","data-v-5af090cb"]]),U=B("clauses",()=>{const c=p(null);async function o(t){return await A.post(""+N.fetchClauses,{Partition:t}).then(a=>a==null?void 0:a.data)}return{clausesList:c,fetchClauses:o}});const X={__name:"ClausesKeeper",props:["partition"],setup(c){const o=c,t=U(),a=T(),y=E();p("");const n=p([]),h=H(()=>a.userInfo);function i(e){var s;return`clause_${(s=h.value)==null?void 0:s.ID}_${e==null?void 0:e.ID}`}function u(e){return document.cookie.match(RegExp(`(?:^|;\\s*)${i(e)}=([^;]*)`))===null}function m(e){let s=new Date;s.setDate(7+s.getDate()),document.cookie=`${i(e)}=hidden; path=/; expires=${s}`,r()}function r(){let e=[];return Object.keys((n==null?void 0:n.value)||{}).forEach((s,x)=>{u(n==null?void 0:n.value[s])&&e.push(n==null?void 0:n.value[s])}),e}function v(){t.fetchClauses((o==null?void 0:o.partition)||`Header:${y.fullPath}`).then(e=>{e&&(n.value=e,r())})}return v(),M(o,()=>{v()}),(e,s)=>{var k;const x=G;return((k=r())==null?void 0:k.length)>0?(l(),g(q,{key:0,class:"clauses-keeper",name:"list",tag:"div"},{default:R(()=>[(l(!0),f(V,null,j(r(),(_,I)=>(l(),g(x,{class:"clauses-alert",key:I,notify:{text:_==null?void 0:_.Text,class:"basic"},show:!0,onClose:J=>m(_)},null,8,["notify","onClose"]))),128))]),_:1})):d("",!0)}}};export{X as _};
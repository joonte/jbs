import{o as l,c as f,a as C,b as $,t as b,l as d,n as D,_ as L,r as p,i as S,s as B,a3 as A,a4 as N,g as T,f as j,h as E,j as H,z as g,w as M,F as R,y as V,a8 as z}from"./index-400feb76.js";import{I as w}from"./IconClose-20ed8b3b.js";const F={key:0},K={key:0,class:"notify-title"},O=["innerHTML"];function P(c,o,t,a,y,n){var i,u;const h=w;return a.show?(l(),f("div",F,[C("div",{class:D(["notify","notify--"+t.notify.class])},[C("button",{class:"notify-close",onClick:o[0]||(o[0]=m=>a.closeAlert())},[$(h)]),(i=t.notify)!=null&&i.title?(l(),f("div",K,b(t.notify.title),1)):d("",!0),(u=t.notify)!=null&&u.text?(l(),f("p",{key:1,innerHTML:t.notify.text},null,8,O)):d("",!0)],2)])):d("",!0)}const q={components:{IconClose:w},props:{show:{type:Boolean,default:!1},notify:{type:Object,default:()=>{}}},emits:["close"],setup(c,{emit:o}){const t=p(!1);function a(){t.value=!1,o("close")}return S(()=>{t.value=c.show}),{show:t,closeAlert:a}}},G=L(q,[["render",P],["__scopeId","data-v-5af090cb"]]),U=B("clauses",()=>{const c=p(null);async function o(t){return await A.post(""+N.fetchClauses,{Partition:t}).then(a=>a==null?void 0:a.data)}return{clausesList:c,fetchClauses:o}});const X={__name:"ClausesKeeper",props:["partition"],setup(c){const o=c,t=U(),a=T(),y=j();p("");const n=p([]),h=E(()=>a.userInfo);function i(e){var s;return`clause_${(s=h.value)==null?void 0:s.ID}_${e==null?void 0:e.ID}`}function u(e){return document.cookie.match(RegExp(`(?:^|;\\s*)${i(e)}=([^;]*)`))===null}function m(e){let s=new Date;s.setDate(7+s.getDate()),document.cookie=`${i(e)}=hidden; path=/; expires=${s}`,r()}function r(){let e=[];return Object.keys((n==null?void 0:n.value)||{}).forEach((s,x)=>{u(n==null?void 0:n.value[s])&&e.push(n==null?void 0:n.value[s])}),e}function v(){t.fetchClauses((o==null?void 0:o.partition)||`Header:${y.fullPath}`).then(e=>{e&&(n.value=e,r())})}return v(),H(o,()=>{v()}),(e,s)=>{var k;const x=G;return((k=r())==null?void 0:k.length)>0?(l(),g(z,{key:0,class:"clauses-keeper",name:"list",tag:"div"},{default:M(()=>[(l(!0),f(R,null,V(r(),(_,I)=>(l(),g(x,{class:"clauses-alert",key:I,notify:{text:_==null?void 0:_.Text,class:"basic"},show:!0,onClose:J=>m(_)},null,8,["notify","onClose"]))),128))]),_:1})):d("",!0)}}};export{X as _};

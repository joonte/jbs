import{a1 as U,r as f,a2 as c,a3 as m}from"./index-58c913f0.js";const g=U("domains",()=>{const n=f(null),D=f(null);async function E(){let l="error";return await c.post(""+m.fetchDomains).then(a=>{n.value=a==null?void 0:a.data}).catch(()=>{l="error"}),l}async function h(){await c.post(""+m.fetchDomainSchemes).then(l=>{D.value=l.data})}async function O(l){let a="ERROR",u=null,t=null;return await c.post(""+m.DomainOrder,l).then(i=>{var R,S;((R=i==null?void 0:i.data)==null?void 0:R.Status)==="Ok"?(a="SUCCESS",t=i==null?void 0:i.data):u=(S=i==null?void 0:i.data)==null?void 0:S.Exception}),{result:a,info:t,error:u}}async function C(l){let a="ERROR",u=null;return await c.post(""+m.DomainOrderPay,l).then(t=>{var i,R,S;((i=t==null?void 0:t.data)==null?void 0:i.Status)==="Ok"?a="SUCCESS":((R=t==null?void 0:t.data)==null?void 0:R.Status)==="UseBasket"?a="BASKET":u=(S=t==null?void 0:t.data)==null?void 0:S.Exception}),{result:a,error:u}}async function d(l){let a="ERROR",u=null;return await c.get(""+m.DomainNameCheck+`?DomainName=${l==null?void 0:l.DomainName}&JSON=1`).then(t=>{var i;(i=t==null?void 0:t.data)!=null&&i.DomainName?(a="SUCCESS",u=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:u}}async function w(l){let a="ERROR";return await c.get(""+m.DomainCheck+`?DomainName=${l==null?void 0:l.DomainName}&DomainZone=${l==null?void 0:l.DomainZone}`).then(u=>{var t,i;((t=u==null?void 0:u.data)==null?void 0:t.Status)==="Free"?a="SUCCESS":((i=u==null?void 0:u.data)==null?void 0:i.Status)==="Exception"?a=null:a="ERROR"}),a}async function y(l){let a="ERROR",u=null;return await c.post(""+m.CheckDomainTransfer,l).then(t=>{var i;((i=t==null?void 0:t.data)==null?void 0:i.Status)!=="Exception"?a="SUCCESS":a="ERROR"}),{result:a,resultData:u}}async function N(l){let a="ERROR",u=null;return await c.post(""+m.DomainTransfer,l).then(t=>{var i;((i=t==null?void 0:t.data)==null?void 0:i.Status)!=="Exception"?(a="SUCCESS",u=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:u}}async function k(l){let a="ERROR",u=null;return await c.post(""+m.DomainSelectOwner,l).then(t=>{var i;((i=t==null?void 0:t.data)==null?void 0:i.Status)!=="Exception"?(a="SUCCESS",u=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:u}}async function x(l){let a="ERROR",u=null;return await c.post(""+m.DomainOrderNsChange,l).then(t=>{var i;((i=t==null?void 0:t.data)==null?void 0:i.Status)!=="Exception"?(a="SUCCESS",u=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:u}}return{domainsList:n,domainSchemes:D,DomainNameCheck:d,DomainCheck:w,DomainOrder:O,DomainTransfer:N,CheckDomainTransfer:y,DomainOrderPay:C,fetchDomains:E,fetchDomainSchemes:h,DomainSelectOwner:k,DomainOrderNsChange:x}});export{g as u};
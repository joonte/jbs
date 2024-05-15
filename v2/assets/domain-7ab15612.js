import{a1 as U,r as D,a2 as c,a3 as m}from"./index-b105c350.js";const g=U("domains",()=>{const f=D(null),n=D(null);async function E(){let u="error";return await c.post(""+m.fetchDomains).then(a=>{f.value=a==null?void 0:a.data}).catch(()=>{u="error"}),u}async function h(){await c.post(""+m.fetchDomainSchemes).then(u=>{n.value=u.data})}async function O(u){let a="ERROR",i=null,t=null;return await c.post(""+m.DomainOrder,u).then(l=>{var R,S;((R=l==null?void 0:l.data)==null?void 0:R.Status)==="Ok"?(a="SUCCESS",t=l==null?void 0:l.data):i=(S=l==null?void 0:l.data)==null?void 0:S.Exception}),{result:a,info:t,error:i}}async function C(u){let a="ERROR",i=null;return await c.post(""+m.DomainOrderPay,u).then(t=>{var l,R,S;((l=t==null?void 0:t.data)==null?void 0:l.Status)==="Ok"?a="SUCCESS":((R=t==null?void 0:t.data)==null?void 0:R.Status)==="UseBasket"?a="BASKET":i=(S=t==null?void 0:t.data)==null?void 0:S.Exception}),{result:a,error:i}}async function d(u){let a="ERROR",i=null;return await c.get(""+m.DomainNameCheck+`?DomainName=${u==null?void 0:u.DomainName}&JSON=1`).then(t=>{var l;(l=t==null?void 0:t.data)!=null&&l.DomainName?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}async function w(u){let a={status:"ERROR",info:null};return await c.get(""+m.DomainCheck+`?DomainName=${u==null?void 0:u.DomainName}&DomainZone=${u==null?void 0:u.DomainZone}`).then(i=>{var t,l,R,S;((t=i==null?void 0:i.data)==null?void 0:t.Status)==="Free"?a.status="SUCCESS":((l=i==null?void 0:i.data)==null?void 0:l.Status)==="Exception"?(a.status=null,a.info=(R=i==null?void 0:i.data)==null?void 0:R.Info):(a.status="ERROR",a.info=(S=i==null?void 0:i.data)==null?void 0:S.Info)}),a}async function y(u){let a="ERROR",i=null;return await c.post(""+m.CheckDomainTransfer,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?a="SUCCESS":a="ERROR"}),{result:a,resultData:i}}async function N(u){let a="ERROR",i=null;return await c.post(""+m.DomainTransfer,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}async function k(u){let a="ERROR",i=null;return await c.post(""+m.DomainSelectOwner,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}async function x(u){let a="ERROR",i=null;return await c.post(""+m.DomainOrderNsChange,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}return{domainsList:f,domainSchemes:n,DomainNameCheck:d,DomainCheck:w,DomainOrder:O,DomainTransfer:N,CheckDomainTransfer:y,DomainOrderPay:C,fetchDomains:E,fetchDomainSchemes:h,DomainSelectOwner:k,DomainOrderNsChange:x}});export{g as u};

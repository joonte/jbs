import{a1 as I,r as D,a2 as c,a3 as R}from"./index-d4c8f165.js";const g=I("domains",()=>{const n=D(null),f=D(null);async function E(){let u="error";return await c.post(""+R.fetchDomains).then(a=>{n.value=a==null?void 0:a.data}).catch(()=>{u="error"}),u}async function h(){await c.post(""+R.fetchDomainSchemes).then(u=>{f.value=u.data})}async function O(u){let a="ERROR",i=null,t=null;return await c.post(""+R.DomainOrder,u).then(l=>{var S,m;((S=l==null?void 0:l.data)==null?void 0:S.Status)==="Ok"?(a="SUCCESS",t=l==null?void 0:l.data):i=(m=l==null?void 0:l.data)==null?void 0:m.Exception}),{result:a,info:t,error:i}}async function C(u){let a="ERROR",i=null;return await c.post(""+R.DomainOrderPay,u).then(t=>{var l,S,m;((l=t==null?void 0:t.data)==null?void 0:l.Status)==="Ok"?a="SUCCESS":((S=t==null?void 0:t.data)==null?void 0:S.Status)==="UseBasket"?a="BASKET":i=(m=t==null?void 0:t.data)==null?void 0:m.Exception}),{result:a,error:i}}async function d(u){let a="ERROR",i=null;return await c.get(""+R.DomainNameCheck+`?DomainName=${u==null?void 0:u.DomainName}&JSON=1`).then(t=>{var l;(l=t==null?void 0:t.data)!=null&&l.DomainName?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}async function w(u){let a={status:"ERROR",info:null};return await c.get(""+R.DomainCheck+`?DomainName=${u==null?void 0:u.DomainName}&DomainZone=${u==null?void 0:u.DomainZone}`).then(i=>{var t,l,S,m;((t=i==null?void 0:i.data)==null?void 0:t.Status)==="Free"?a.status="SUCCESS":((l=i==null?void 0:i.data)==null?void 0:l.Status)==="Exception"?(a.status=null,a.info=(S=i==null?void 0:i.data)==null?void 0:S.Info):(a.status="ERROR",a.info=(m=i==null?void 0:i.data)==null?void 0:m.Info)}),a}async function y(u){let a="ERROR",i=null;return await c.post(""+R.CheckDomainTransfer,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?a="SUCCESS":a="ERROR"}),{result:a,resultData:i}}async function x(u){let a="ERROR",i=null;return await c.post(""+R.DomainTransfer,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}async function N(u){let a="ERROR",i=null;return await c.post(""+R.DomainSelectOwner,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}async function U(u){let a="ERROR";return await c.post(""+R.DomainAuthInfoInput,u).then(i=>{var t;((t=i==null?void 0:i.data)==null?void 0:t.Status)!=="Exception"?a="SUCCESS":a="ERROR"}),a}async function k(u){let a="ERROR",i=null;return await c.post(""+R.DomainOrderNsChange,u).then(t=>{var l;((l=t==null?void 0:t.data)==null?void 0:l.Status)!=="Exception"?(a="SUCCESS",i=t==null?void 0:t.data):a="ERROR"}),{result:a,resultData:i}}return{domainsList:n,domainSchemes:f,DomainNameCheck:d,DomainCheck:w,DomainOrder:O,DomainTransfer:x,CheckDomainTransfer:y,DomainOrderPay:C,fetchDomains:E,fetchDomainSchemes:h,DomainSelectOwner:N,DomainOrderNsChange:k,DomainAuthInfoInput:U}});export{g as u};

import{q as M,r as n,a2 as c,a3 as d}from"./index-5f8fd0e9.js";const m=M("DSOrders",()=>{const D=n(null),f=n(null),O=n(null),e=n(null);async function h(){let t="error";return await c.post(""+d.fetchDSOrders).then(a=>{D.value=a==null?void 0:a.data}).catch(()=>{t="error"}),t}async function I(){await c.post(""+d.fetchDSSchemes).then(t=>{f.value=t.data})}async function y(t){await c.post(""+d.DSOrderIPMI,{OrderID:t}).then(a=>{O.value=a.data})}async function P(){await c.get(""+d.DSOrderIPMI+"?IsSensors=true&OrderID=1033").then(t=>{e.value=t.data})}async function E(t){let a="ERROR",l=null,S=null;return await c.post(""+d.DSOrder,t).then(r=>{var u,i;((u=r==null?void 0:r.data)==null?void 0:u.Status)==="Ok"?(a="SUCCESS",S=r==null?void 0:r.data):l=(i=r==null?void 0:r.data)==null?void 0:i.Exception}),{result:a,info:S,error:l}}async function w(t){let a="ERROR",l=null;return await c.post(""+d.DSOrderPay,t).then(S=>{var r,u,i;((r=S==null?void 0:S.data)==null?void 0:r.Status)==="Ok"?a="SUCCESS":((u=S==null?void 0:S.data)==null?void 0:u.Status)==="UseBasket"?a="BASKET":l=(i=S==null?void 0:S.data)==null?void 0:i.Exception}),{result:a,error:l}}return{DSList:D,DSSchemes:f,DSOrderIPMI:O,DSOrderIPMISensors:e,fetchDSOrders:h,fetchDSSchemes:I,fetchDSOrderIPMISensors:P,fetchDSOrderIPMI:y,DSOrder:E,DSOrderPay:w}});export{m as u};

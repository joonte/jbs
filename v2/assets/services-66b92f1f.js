import{a1 as A,r as O,a2 as v,a3 as R}from"./index-7c70979b.js";const L=A("Services",()=>{const E=O(null),h=O({}),C=O(null);async function y(){await v.post(""+R.fetchServices).then(i=>{E.value=i.data})}async function D(i){let S="ERROR",u=null,t=null,l=null,{Code:c="Default"}=i;return c==="Default"?l=R.ServiceOrder:l="/API/"+c+"Order",await v.post(""+l,i).then(a=>{var d,f;((d=a==null?void 0:a.data)==null?void 0:d.Status)==="Ok"?(S="SUCCESS",t=a==null?void 0:a.data):u=(f=a==null?void 0:a.data)==null?void 0:f.Exception}),{result:S,info:t,error:u}}async function U(i){let S="ERROR",u=null,t=null,l=null,{Code:c="Default"}=i;return c==="Default"?l=R.ISPswOrder:l="/API/"+c+"Order",await v.post(""+l,i).then(a=>{var d,f;((d=a==null?void 0:a.data)==null?void 0:d.Status)==="Ok"?(S="SUCCESS",t=a==null?void 0:a.data):u=(f=a==null?void 0:a.data)==null?void 0:f.Exception}),{result:S,info:t,error:u}}async function w(i="Default",S){let u="ERROR",t=null,l=null;return i==="Default"?l=R.ServiceOrderPay:l="/"+i+"OrderPay",await v.post(""+l,S).then(c=>{var a,d,f;((a=c==null?void 0:c.data)==null?void 0:a.Status)==="Ok"?u="SUCCESS":((d=c==null?void 0:c.data)==null?void 0:d.Status)==="UseBasket"?u="BASKET":t=(f=c==null?void 0:c.data)==null?void 0:f.Exception}),{result:u,error:t}}async function P(i){let S="ERROR",u=null;return await v.post("/API/v2/"+i+"Schemes",{}).then(t=>{var l;t!=null&&t.data?(S="SUCCESS",h.value[i]=t==null?void 0:t.data):u=(l=t==null?void 0:t.data)==null?void 0:l.Exception}),{result:S,error:u}}async function x(i){let S="ERROR",u=null;return await v.post(""+R.DependServices,i).then(t=>{var l;t!=null&&t.data?(S="SUCCESS",C.value=t==null?void 0:t.data):u=(l=t==null?void 0:t.data)==null?void 0:l.Exception}),{result:S,error:u}}return{ServicesList:E,additionalServicesScheme:h,DependServicesList:C,fetchAdditionalServiceScheme:P,fetchDependServices:x,ServiceOrderPay:w,fetchServices:y,ServiceOrder:D,ISPswOrder:U}});export{L as u};

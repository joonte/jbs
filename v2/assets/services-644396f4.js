import{a1 as L,r as E,a2 as O,a3 as R}from"./index-58c913f0.js";const g=L("Services",()=>{const v=E(null),D=E({}),h=E(null),C=E(null);async function P(){await O.post(""+R.fetchServices).then(i=>{v.value=i.data})}async function U(){await O.post(""+R.DNSmanagerOrders).then(i=>{C.value=i.data})}async function y(i){let c="ERROR",u=null,a=null,l=null,{Code:S="Default"}=i;return S==="Default"?l=R.ServiceOrder:l="/API/"+S+"Order",await O.post(""+l,i).then(t=>{var d,f;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Ok"?(c="SUCCESS",a=t==null?void 0:t.data):u=(f=t==null?void 0:t.data)==null?void 0:f.Exception}),{result:c,info:a,error:u}}async function w(i){let c="ERROR",u=null,a=null,l=null,{Code:S="Default"}=i;return S==="Default"?l="/API/DNSmanagerOrder":l="/API/"+S+"Order",await O.post(""+l,i).then(t=>{var d,f;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Ok"?(c="SUCCESS",a=t==null?void 0:t.data):u=(f=t==null?void 0:t.data)==null?void 0:f.Exception}),{result:c,info:a,error:u}}async function x(i){let c="ERROR",u=null,a=null,l=null,{Code:S="Default"}=i;return S==="Default"?l=R.ExtraIPOrder:l="/API/"+S+"Order",await O.post(""+l,i).then(t=>{var d,f;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Ok"?(c="SUCCESS",a=t==null?void 0:t.data):u=(f=t==null?void 0:t.data)==null?void 0:f.Exception}),{result:c,info:a,error:u}}async function r(i){let c="ERROR",u=null,a=null,l=null,{Code:S="Default"}=i;return S==="Default"?l=R.ISPswOrder:l="/API/"+S+"Order",await O.post(""+l,i).then(t=>{var d,f;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Ok"?(c="SUCCESS",a=t==null?void 0:t.data):u=(f=t==null?void 0:t.data)==null?void 0:f.Exception}),{result:c,info:a,error:u}}async function I(i="Default",c){let u="ERROR",a=null,l=null;return i==="Default"?l=R.ServiceOrderPay:l="/"+i+"OrderPay",await O.post(""+l,c).then(S=>{var t,d,f;((t=S==null?void 0:S.data)==null?void 0:t.Status)==="Ok"?u="SUCCESS":((d=S==null?void 0:S.data)==null?void 0:d.Status)==="UseBasket"?u="BASKET":a=(f=S==null?void 0:S.data)==null?void 0:f.Exception}),{result:u,error:a}}async function m(i){let c="ERROR",u=null;return await O.post("/API/v2/"+i+"Schemes",{}).then(a=>{var l;a!=null&&a.data?(c="SUCCESS",D.value[i]=a==null?void 0:a.data):u=(l=a==null?void 0:a.data)==null?void 0:l.Exception}),{result:c,error:u}}async function A(i){let c="ERROR",u=null;return await O.post(""+R.DependServices,i).then(a=>{var l;a!=null&&a.data?(c="SUCCESS",h.value=a==null?void 0:a.data):u=(l=a==null?void 0:a.data)==null?void 0:l.Exception}),{result:c,error:u}}return{ServicesList:v,additionalServicesScheme:D,DependServicesList:h,fetchAdditionalServiceScheme:m,fetchDependServices:A,ServiceOrderPay:I,fetchServices:P,ServiceOrder:y,DNSmanagerOrder:w,ISPswOrder:r,ExtraIPOrder:x,fetchDNSmanagerOrders:U,DNSmanagerOrdersList:C}});export{g as u};
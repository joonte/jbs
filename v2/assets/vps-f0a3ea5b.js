import{q as C,r as V,a2 as l,a3 as u}from"./index-00a0bf0d.js";const w=C("vps",()=>{const d=V(null),P=V(null);async function n(){let c="error";return await l.post(""+u.fetchVPS).then(S=>{d.value=S==null?void 0:S.data}).catch(()=>{c="error"}),c}async function r(){await l.post(""+u.fetchVPSSchemes).then(c=>{P.value=c.data})}async function O(c){let S="ERROR",i=null,t=null;return await l.post(""+u.VPSOrder,c).then(a=>{var f,h;((f=a==null?void 0:a.data)==null?void 0:f.Status)==="Ok"?(S="SUCCESS",t=a==null?void 0:a.data):i=(h=a==null?void 0:a.data)==null?void 0:h.Exception}),{result:S,info:t,error:i}}async function R(c){let S="ERROR",i=null;return await l.post(""+u.VPSOrderPay,c).then(t=>{var a,f,h;((a=t==null?void 0:t.data)==null?void 0:a.Status)==="Ok"?S="SUCCESS":((f=t==null?void 0:t.data)==null?void 0:f.Status)==="UseBasket"?S="BASKET":i=(h=t==null?void 0:t.data)==null?void 0:h.Exception}),{result:S,error:i}}async function E(c){let S="ERROR",i=null;return await l.post(""+u.VPSOrderSchemeChange,c).then(t=>{var a;((a=t==null?void 0:t.data)==null?void 0:a.Status)==="Ok"&&(S="SUCCESS")}),{result:S,error:i}}async function y(c){await l.post(""+u.VPSReboot,c)}return{vpsList:d,vpsSchemes:P,fetchVPS:n,fetchVPSSchemes:r,VPSOrderPay:R,VPSReboot:y,VPSOrder:O,VPSOrderSchemeChange:E}});export{w as u};

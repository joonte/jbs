import{q as s,a2 as r,a3 as l}from"./index-145d9cde.js";const p=s("files",()=>{async function o(n){const e=new FormData;e.append("Upload",n);let t=null;return await r.post(""+l.uploadFile,e,{headers:{"Content-Type":"multipart/form-data"}}).then(a=>{t=a.data}).catch(a=>{console.log(a)}),t}return{sendFile:o}});export{p as u};

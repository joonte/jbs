import{q as D,r as s,a2 as c,a3 as d}from"./index-3af25d4c.js";const N=D("contracts",()=>{const i=s(null),r=s(null);let e={headers:{"Content-Type":"multipart/form-data"}};async function f(){let o="error";return await c.post(""+d.fetchContracts).then(t=>{i.value=t==null?void 0:t.data}).catch(()=>{o="error"}),o}async function p(o){await c.post(""+d.contracts.download,o).then(t=>{var a,n;((a=t.data)==null?void 0:a.Status)==="Ok"&&(document.location=""+((n=t.data)==null?void 0:n.Location))}).catch(t=>{console.log(t,"error")})}async function h(){let o="error";return await c.post(""+d.fetchProfiles).then(t=>{r.value=t==null?void 0:t.data}).catch(()=>{o="error"}),o}async function w(o){let t={status:"error",data:null};return await c.post(""+d.profileEdit,{...o}).then(a=>{var n;(n=a==null?void 0:a.data)!=null&&n.Exception?(t.status="error",t.data=a==null?void 0:a.data):(t.status="success",t.data=a==null?void 0:a.data)}).catch(()=>{t.status="error"}),t}async function y(o){let t={status:"error",data:null};return await c.post(""+d.ContractFundsTransfer,{...o}).then(a=>{var n;(n=a==null?void 0:a.data)!=null&&n.Exception?(t.status="error",t.data=a==null?void 0:a.data):(t.status="success",t.data=a==null?void 0:a.data)}).catch(()=>{t.status="error"}),t}async function E(o){let t={status:"error",data:null};return await c.post(""+d.profileEdit,o,e).then(a=>{var n;(n=a==null?void 0:a.data)!=null&&n.Exception?(t.status="error",t.data=a==null?void 0:a.data):(t.status="success",t.data=a==null?void 0:a.data)}).catch(()=>{t.status="error"}),t}async function C(o){let t={status:"error",data:null};return await c.post(""+d.contractMake,{...o}).then(a=>{var n;(n=a==null?void 0:a.data)!=null&&n.Exception?(t.status="error",t.data=a==null?void 0:a.data):(t.status="success",t.data=a==null?void 0:a.data)}).catch(()=>{t.status="error"}),t}async function x(o){let t=null;return await c.get(""+d.DaysCalculate+`?${o}`).then(a=>{var n;t=((n=a==null?void 0:a.data)==null?void 0:n.DaysFromBallance)||null}),t}async function m(o){let t={status:"error",data:null};return await c.post(""+d.contractEdit,{...o}).then(a=>{var n;(n=a==null?void 0:a.data)!=null&&n.Exception?(t.status="error",t.data=a==null?void 0:a.data):(t.status="success",t.data=a==null?void 0:a.data)}).catch(()=>{t.status="error"}),t}async function F(o){let t={status:"error",data:null};return await c.post(""+d.BooleanEdit,{...o}).then(a=>{var n;(n=a==null?void 0:a.data)!=null&&n.Exception?(t.status="error",t.data=a==null?void 0:a.data):(t.status="success",t.data=a==null?void 0:a.data)}).catch(()=>{t.status="error"}),t}return{contractsList:i,profileList:r,fetchProfiles:h,fetchContracts:f,checkContractBalance:x,contractMake:C,contractEdit:m,createNewContract:w,createNewContractFormData:E,contractDownload:p,booleanEdit:F,FundsTransfer:y}});export{N as u};

import{a1 as n,r as s,a2 as o,a3 as r}from"./index-58c913f0.js";const S=n("reset",()=>{const e=s(null);async function c(){await o.post(""+r.serverGroups).then(t=>{t!=null&&t.data&&(e.value=t==null?void 0:t.data)}).catch(t=>{console.log(t)})}async function u(t,i){let l;return await o.post(""+r.DSReboot,{DSOrderID:t,XMLHttpRequest:"yes",Command:i}).then(a=>{console.log(a)}).catch(a=>{console.log(a)}),l}return{serverGroupsList:e,fetchServerGroups:c,ServerReset:u}});export{S as u};
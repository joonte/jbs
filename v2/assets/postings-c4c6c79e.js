import{s as d,r as o,a3 as e,a4 as a}from"./index-400feb76.js";const g=d("postings",()=>{const c=o(null),u=o(null),r=o(null);async function f(){await e.post(""+a.postings).then(t=>{c.value=t.data})}async function v(){await e.post(""+a.services).then(t=>{u.value=t.data})}async function p(){await e.post(""+a.fetchInvoices).then(t=>{r.value=t.data})}async function h(t){let s=null;return await e.post(""+a.InvoiceDocument,t).then(i=>{var n,l;((n=i.data)==null?void 0:n.Status)==="Ok"&&(s=(l=i.data)==null?void 0:l.DOM)}),s}async function S(t){let s="ERROR";return await e.post(""+a.InvoicesReject,t).then(i=>{var n;((n=i.data)==null?void 0:n.Status)!=="Exception"&&(s="SUCCESS")}),s}return{postingsList:c,servicesList:u,invoicesList:r,fetchPostings:f,fetchServices:v,fetchInvoices:p,InvoiceDocument:h,InvoicesReject:S}});export{g as u};

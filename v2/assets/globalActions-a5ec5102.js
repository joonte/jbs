import{q as b,a2 as c,a3 as S}from"./index-90eb49f0.js";const P=b("delete",()=>{async function O(l){let i="ERROR",a=null;return await c.post(""+S.delete,{...l}).then(t=>{var d,u;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Exception"?a=(u=t==null?void 0:t.data)==null?void 0:u.Exception:(i="SUCCESS",console.log(i))}),{result:i,error:a}}async function h(l){let i="ERROR",a=null;return await c.post(""+S.OrdersTransfer,{...l}).then(t=>{var d,u;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Exception"?a=(u=t==null?void 0:t.data)==null?void 0:u.Exception:i="SUCCESS"}),{result:i,error:a}}async function w(l){let i="ERROR",a=null;return await c.post(""+S.OrderManage,{...l}).then(t=>{var d,u,f;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Exception"?a=(u=t==null?void 0:t.data)==null?void 0:u.Exception:(i="SUCCESS",window.open((f=t==null?void 0:t.data)==null?void 0:f.Url,"_blank"))}),{result:i,error:a}}async function m(l){let i="ERROR",a=null;return await c.post(""+S.OrderManage,{...l}).then(t=>{var d,u,f;if(((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Ok"){const E=window.open("","_blank");let R=`<html>
                            <head><title>Управление заказом</title></head>
                            <body>
                              <form id="OrderManageForm" action="${t.data.Url}" method="post">`;Object.entries(t.data.Args||{}).forEach(([x,k])=>{R+=`<input type="hidden" name="${x}" value="${k}">`}),R+=`</form>
                          <script>
                            document.getElementById('OrderManageForm').submit();
                          <\/script>
                        </body>
                        </html>`,E.document.write(R),E.document.close(),i="SUCCESS"}else((u=t==null?void 0:t.data)==null?void 0:u.Status)==="Exception"?a=(f=t==null?void 0:t.data)==null?void 0:f.Exception:a="Неизвестный статус ответа"}).catch(t=>{a=t}),{result:i,error:a}}async function r(l){let i="ERROR",a=null;return await c.post(""+S.OrderChangePassword,{...l}).then(t=>{var d;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Ok"&&(i="SUCCESS")}),{result:i,error:a}}async function C(l){let i="ERROR",a=null;return await c.post(""+S.OrderRestore,{...l}).then(t=>{var d;((d=t==null?void 0:t.data)==null?void 0:d.Status)==="Ok"&&(i="SUCCESS")}),{result:i,error:a}}async function y(l){let i="ERROR";return await c.get(""+S.Discount+l).then(a=>{a!=null&&a.data&&(i=a==null?void 0:a.data)}),i}async function g(){let l="ERROR";return await c.get(""+S.Generator).then(i=>{i!=null&&i.data&&(l=i==null?void 0:i.data)}),l}async function U(l){let i="ERROR";return await c.post(""+S.NoticeEdit,l).then(a=>{var t;((t=a==null?void 0:a.data)==null?void 0:t.Status)==="Ok"&&(i="SUCCESS")}),i}async function n(l){let i="ERROR";return await c.post("https://manager.host-food.ru/API/OrdersPay",l).then(a=>{var t,d,u;((t=a==null?void 0:a.data)==null?void 0:t.Status)==="Url"&&((d=a==null?void 0:a.data)==null?void 0:d.Location)==="/Basket"&&(i="Basket"),((u=a==null?void 0:a.data)==null?void 0:u.Status)==="Ok"&&(i="SUCCESS")}),i}return{deleteItem:O,OrdersTransfer:h,OrderChangePassword:r,OrderRestore:C,OrderManage:w,fetchDiscount:y,NoticeEdit:U,OrderManageHosting:m,fetchPassword:g,OrdersPay:n}});export{P as u};

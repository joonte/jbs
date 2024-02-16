import{o as openBlock,c as createElementBlock,b as createVNode,a as createBaseVNode,t as toDisplayString,_ as _export_sfc,R as inject,r as ref}from"./index-c7c24b76.js";import{I as IconHelpCircle}from"./IconHelpCircle-c2612903.js";import{u as useDeleteStore}from"./globalActions-306d29c7.js";import{_ as __unplugin_components_0}from"./ButtonDefault-626291b9.js";const _hoisted_1={class:"modal__body"},_hoisted_2={class:"modal__info"},_hoisted_3={class:"modal__alert-title"};function render(i,t,s,o,n,l){var a;const e=IconHelpCircle,d=__unplugin_components_0;return openBlock(),createElementBlock("div",_hoisted_1,[createVNode(e,{class:"modal__alert-icon"}),createBaseVNode("div",_hoisted_2,[createBaseVNode("div",_hoisted_3,toDisplayString((a=s.data)==null?void 0:a.message),1)]),createVNode(d,{class:"modal__alert-button",onClick:t[0]||(t[0]=c=>o.deleteHosting()),label:"УДАЛИТЬ","is-loading":o.isLoading},null,8,["is-loading"])])}const ModalWindowDeleteItem_vue_vue_type_style_index_0_scoped_4f267926_lang="",_sfc_main={components:{IconHelpCircle},props:{data:{type:Object,default:()=>{}}},emits:["modalClose"],setup(props,{emit}){const deleteStore=useDeleteStore(),emitter=inject("emitter"),isLoading=ref(!1);function deleteHosting(){var i,t,s,o,n;if(isLoading.value=!0,props.data&&((i=props.data)!=null&&i.callback)){const l=props.data.callback.match(/RowsIDs:(\d+)/);deleteStore.deleteItem({TableID:(t=props.data)==null?void 0:t.tableID,RowsIDs:l[1]}).then(({result:e,error:d})=>{var a;e==="SUCCESS"&&(emitter.emit((a=props.data)==null?void 0:a.successEmit),emit("modalClose")),isLoading.value=!1})}else props.data?deleteStore.deleteItem({TableID:(s=props.data)==null?void 0:s.tableID,RowsIDs:(o=props.data)==null?void 0:o.id}).then(({result:l})=>{var e;l==="SUCCESS"&&(emitter.emit((e=props.data)==null?void 0:e.successEmit),emit("modalClose")),isLoading.value=!1}):(eval((n=props.data)==null?void 0:n.callback),emit("modalClose"))}return{deleteHosting,isLoading}}},ModalWindowDeleteItem=_export_sfc(_sfc_main,[["render",render],["__scopeId","data-v-4f267926"]]);export{ModalWindowDeleteItem as default};

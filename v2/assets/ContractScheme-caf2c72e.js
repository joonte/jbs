import{j as z,o as _,c as y,b as e,a as t,w as d,d as r,t as T,y as S,k as j,F as A,x as G,p as J,l as K,_ as M,u as Q,a7 as U,e as W,g as v}from"./index-90eb49f0.js";import{u as X}from"./contracts-0cbe8270.js";import{u as Y}from"./hosting-606725d4.js";import{H as F}from"./HostingItem-bc0cff9f.js";import{I as R}from"./IconPlus-855c0bea.js";import{B as q}from"./BlockBalanceAgreement-267acfcf.js";import{E as Z}from"./EmptyStateBlock-2d70ce64.js";import{_ as $}from"./ButtonDefault-5aebc1f7.js";import{_ as tt}from"./ClausesKeeper-313482ce.js";import"./IconDots-9788fdcb.js";import"./IconCard-95aab40c.js";import"./StatusBadge-0d2c62ad.js";import"./hostStatuses-94d00f20.js";import"./HelperComponent-f1dd2245.js";import"./IconArrow-380053a0.js";import"./useTimeFunction-8602dd60.js";import"./bootstrap-vue-next.es-788a757a.js";import"./OrderIDBadge-b66a7555.js";import"./IconClose-c838a5bc.js";const h=n=>(J("data-v-9fc72117"),n=n(),K(),n),st={class:"section"},ot={class:"container"},et={class:"section-header column"},it={class:"section-label-wrapper"},nt={class:"section-title"},ct={class:"section-label"},at={class:"list grid-list-three"},lt={class:"list-col list-col--sm"},rt={class:"list-item"},_t=h(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Профиль")],-1)),dt={class:"list-item__row flex flex-column"},mt={class:"list-item__row"},ft={class:"list-col list-col--sm"},gt={class:"list-item"},ut={class:"list-item__row flex space-between"},pt=h(()=>t("div",{class:"list-item__title"},"Баланс",-1)),vt={class:"list-item__simple-balance"},ht={class:"list-item__row flex flex-column"},Ct={key:0,class:"list-item__row"},kt={class:"list-col list-col--sm"},It={class:"list-item"},bt=h(()=>t("div",{class:"list-item__row"},[t("div",{class:"list-item__title"},"Поддержка")],-1)),wt={class:"list-item__row flex flex-column"},yt={class:"list-item__row"},Tt={class:"contracts__section"},St={class:"contracts__section-header"},xt=h(()=>t("div",{class:"contracts__section-title"},"Хостинги",-1)),Bt={class:"contracts__section-list"};function Pt(n,i,u,s,m,p){var o,g,x,B,P,D,H,L,N,E,V,O;const C=q,c=z("router-link"),k=tt,f=$,I=R,b=F,w=Z;return _(),y(A,null,[e(C),t("div",st,[t("div",ot,[t("div",et,[t("div",it,[e(c,{class:"section-label",to:"/Contracts"},{default:d(()=>[r("Договора")]),_:1})]),t("h1",nt,T(((o=s.getProfile)==null?void 0:o.Name)||((g=s.getContract)==null?void 0:g.Customer)),1),t("div",ct,T((H=(D=(B=(x=s.getConfig)==null?void 0:x.Contracts)==null?void 0:B.Types)==null?void 0:D[(P=s.getContract)==null?void 0:P.TypeID])==null?void 0:H.Name),1)]),e(k),t("div",at,[t("div",lt,[t("div",rt,[_t,t("div",dt,[e(c,{class:"item-link",to:`/ContractSettings/${(L=s.getContract)==null?void 0:L.ID}`},{default:d(()=>[r("Настройки")]),_:1},8,["to"]),e(c,{class:"item-link",to:`/ContractDocuments/${(N=s.getContract)==null?void 0:N.ID}`},{default:d(()=>[r("Документы")]),_:1},8,["to"])]),t("div",mt,[(E=s.getContract)!=null&&E.ProfileID?(_(),S(f,{key:0,label:"Редактировать данные",onClick:i[0]||(i[0]=a=>{var l;return s.navigateToEditPage((l=s.getContract)==null?void 0:l.ProfileID)})})):j("",!0)])])]),t("div",ft,[t("div",gt,[t("div",ut,[pt,t("div",vt,T((V=s.getContract)==null?void 0:V.Balance)+" ₽",1)]),t("div",ht,[e(c,{class:"item-link",to:"/Postings"},{default:d(()=>[r("История операций")]),_:1}),e(c,{class:"item-link",to:"/Invoices"},{default:d(()=>[r("Отчетные документы (Счета)")]),_:1})]),((O=s.getContract)==null?void 0:O.TypeID)!="NaturalPartner"?(_(),y("div",Ct,[e(f,{label:"Пополнить баланс",onClick:i[1]||(i[1]=a=>{var l;return s.navigateToBalancePage((l=s.getContract)==null?void 0:l.ID)})})])):j("",!0)])]),t("div",kt,[t("div",It,[bt,t("div",wt,[e(c,{class:"item-link",to:"/Tickets"},{default:d(()=>[r("Тикеты")]),_:1})]),t("div",yt,[e(f,{label:"Создать тикет",onClick:i[2]||(i[2]=a=>s.navigateToTicketsCreation())})])])])]),t("div",Tt,[t("div",St,[xt,t("button",{class:"btn btn-default btn--border small",onClick:i[3]||(i[3]=a=>s.navigateToHostingList())},[e(I),r("Купить еще хостинг")])]),t("div",Bt,[s.getHostingList.length>0?(_(!0),y(A,{key:0},G(s.getHostingList,a=>{var l;return _(),S(b,{"item-data":a,"user-name":(l=s.getContract)==null?void 0:l.Customer,"info-link":`/HostingOrders/${a==null?void 0:a.ID}`},null,8,["item-data","user-name","info-link"])}),256)):(_(),S(w,{key:1,class:"contracts__empty-state",label:"У Вас нет купленного хостинга"}))])])])])],64)}const Dt={components:{HostingItem:F,IconPlus:R,BlockBalanceAgreement:q},async setup(){const n=X(),i=Q(),u=Y(),s=U(),m=W(),p=v(()=>{var o;return(o=n.contractsList)==null?void 0:o[s.params.id]}),C=v(()=>{var o;return n.profileList[(o=p.value)==null?void 0:o.ProfileID]}),c=v(()=>i.ConfigList),k=v(()=>Object.keys(u.hostingList).map(o=>u.hostingList[o]).filter(o=>{var g;return(o==null?void 0:o.ContractID)===((g=p.value)==null?void 0:g.ID)}).reverse());function f(o){m.push(`/Balance/${o}`)}function I(){m.push({name:"default.NewTicket"})}function b(o){m.push(`/ContractEdit/${o}`)}function w(){m.push("/HostingSchemes")}return await n.fetchContracts(),await n.fetchProfiles(),await u.fetchHostingOrders(),{getConfig:c,getProfile:C,getContract:p,getHostingList:k,navigateToBalancePage:f,navigateToHostingList:w,navigateToEditPage:b,navigateToTicketsCreation:I}}},Xt=M(Dt,[["render",Pt],["__scopeId","data-v-9fc72117"]]);export{Xt as default};

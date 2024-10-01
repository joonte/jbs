import{_ as B,o as c,c as h,a as t,b as l,F as E,t as m,y as x,w as u,p as L,l as V,f as A,r as v,e as P,g as k}from"./index-90eb49f0.js";import{u as S}from"./contracts-0cbe8270.js";import{_ as I}from"./ClausesKeeper-313482ce.js";import{f as j,a as F}from"./useTimeFunction-8602dd60.js";import{B as T}from"./BlockBalanceAgreement-267acfcf.js";import{E as M}from"./EmptyStateBlock-2d70ce64.js";import{C as O}from"./bootstrap-vue-next.es-788a757a.js";import{_ as Z}from"./FormInputSearch-e1779cda.js";import{_ as K}from"./IconCopy-3177f184.js";import{_ as q}from"./ButtonDefault-5aebc1f7.js";import"./IconClose-c838a5bc.js";import"./IconArrow-380053a0.js";import"./IconPlus-855c0bea.js";import"./IconSearch-373a4f18.js";const z=[{key:"RegisterDate",label:"Дата Создания",variant:"td-gray",sortable:!0},{key:"Name",label:"Реферал",variant:"td-blue"},{key:"EnterDate",label:"Дата Входа",variant:"td-gray",sortable:!0},{key:"controls",label:"",variant:"td-controls"}],G={},J={width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},Q=t("path",{d:"M14.2929 12H3.5C3.22386 12 3 11.7761 3 11.5C3 11.2239 3.22386 11 3.5 11H14.2929L11.1464 7.85355C10.9512 7.65829 10.9512 7.34171 11.1464 7.14645C11.3417 6.95118 11.6583 6.95118 11.8536 7.14645L15.8536 11.1464C16.0488 11.3417 16.0488 11.6583 15.8536 11.8536L11.8536 15.8536C11.6583 16.0488 11.3417 16.0488 11.1464 15.8536C10.9512 15.6583 10.9512 15.3417 11.1464 15.1464L14.2929 12ZM10.5 4C10.2239 4 10 3.77614 10 3.5C10 3.22386 10.2239 3 10.5 3H18.5C19.8807 3 21 4.11929 21 5.5V18.5C21 19.8807 19.8807 21 18.5 21H10.5C10.2239 21 10 20.7761 10 20.5C10 20.2239 10.2239 20 10.5 20H18.5C19.3284 20 20 19.3284 20 18.5V5.5C20 4.67157 19.3284 4 18.5 4H10.5Z"},null,-1),W=[Q];function X(r,s){return c(),h("svg",J,W)}const N=B(G,[["render",X]]),a=r=>(L("data-v-d21e3ca7"),r=r(),V(),r),Y={class:"section"},$={class:"container"},ee=a(()=>t("div",{class:"section-header"},[t("h1",{class:"section-title"},"Реферальная программа"),t("div",{class:"section-text"},[t("p",null,"Получайте 5% от суммы оказанных услуг привлечённых пользователей. Вознаграждение можно использовать для оплаты услуг Host-food.")])],-1)),te={key:0,class:"btn__group"},oe=a(()=>t("h2",{class:"section-title"},"Добавить реферала",-1)),se={class:"profile_referral-wrapper"},re={class:"profile_referral-col"},ne={class:"profile_referral-item"},ae=a(()=>t("div",{class:"profile_referral-item__name"},"С помощью реферальной ссылки",-1)),le=a(()=>t("div",{class:"profile_referral-item__text"},"Для каждого вашего реферала доступна информация о дате регистрации, времени последнего входа, потраченных средствах. Однако просмотр дополнительной информации и управление пользователем при этом способе приглашения недоступны.",-1)),ce={class:"profile_referral-value"},ie={class:"profile_referral-col"},_e={class:"profile_referral-item"},de=a(()=>t("div",{class:"profile_referral-item__name"},"Создать аккаунт реферала",-1)),pe=a(()=>t("div",{class:"profile_referral-item__text"},"У вас будет доступ к управлению сайтами своих клиентов, так как при таком способе приглашения пользователя партнёр получает совместный с ним контроль над его аккаунтом",-1)),fe={class:"list-form"},me=a(()=>t("span",{class:"td-mobile"},"Дата создания",-1)),ue={class:"d-block"},ve=a(()=>t("span",{class:"td-mobile"},"Реферал",-1)),he=a(()=>t("span",{class:"td-mobile"},"Дата входа",-1));function Ce(r,s,U,o,w,i){var f;const _=T,d=I,p=q,C=K,g=Z,y=N,D=O,b=M;return c(),h("div",Y,[l(_),t("div",$,[ee,l(d,{class:"user-referral__clauses",partition:"Header:/DependUsers"}),!o.getPartnerContract&&o.getDependUsers.length<1?(c(),h("div",te,[l(p,{label:"Стать участником",onClick:s[0]||(s[0]=e=>o.navigateToContracts())})])):(c(),h(E,{key:1},[oe,t("div",se,[t("div",re,[t("div",ne,[ae,le,t("div",ce,[t("div",{class:"profile-info__item-referral",ref:"message"},"http://www.host-food.ru/p/"+m((f=o.getUserInfo)==null?void 0:f.ID)+"/",513),t("button",{class:"btn profile-info__item-referral_copy",onClick:s[1]||(s[1]=(...e)=>o.copyText&&o.copyText(...e))},[l(C)])])])]),t("div",ie,[t("div",_e,[de,pe,l(p,{label:"Создать реферала",onClick:s[2]||(s[2]=e=>o.createDependUser())})])])]),t("div",fe,[l(g,{modelValue:o.filter,"onUpdate:modelValue":s[3]||(s[3]=e=>o.filter=e)},null,8,["modelValue"])]),o.getDependUsers?(c(),x(D,{key:0,class:"basic-table",fields:o.referralTable,items:o.filterReferrals(o.getDependUsers),"show-empty":!0,"sort-by":o.sortBy,"onUpdate:sortBy":s[4]||(s[4]=e=>o.sortBy=e),"sort-desc":o.sortDesc,"onUpdate:sortDesc":s[5]||(s[5]=e=>o.sortDesc=e),"sort-direction":o.sortDirection,"empty-text":"У Вас нет зарегистрированных пользователей.","empty-filtered-text":"Пользователи не найдены."},{"cell(RegisterDate)":u(e=>[me,t("span",ue,m(o.formNormalDate(o.secondToDate(e.value))),1)]),"cell(Name)":u(e=>[ve,t("span",null,m(e.value),1)]),"cell(EnterDate)":u(e=>[he,t("span",null,m(o.formNormalDate(o.secondToDate(e.value))),1)]),"cell(controls)":u(e=>[l(y,{class:"icon-enter",onClick:n=>o.changeToUser(e)},null,8,["onClick"])]),_:1},8,["fields","items","sort-by","sort-desc","sort-direction"])):(c(),x(b,{key:1,label:"Вы пока не добавили рефералов"}))],64))])])}const ge={components:{ClausesKeeper:I,IconEnter:N,BlockBalanceAgreement:T},async setup(){const r=A(),s=S(),U=v("RegisterDate"),o=v(!0),w=v("desc"),i=P(),_=v(""),d=k(()=>r.userInfo),p=k(()=>Object.keys(r==null?void 0:r.dependUsers).map(e=>{var n;return(n=r==null?void 0:r.dependUsers)==null?void 0:n[e]})),C=k(()=>s==null?void 0:s.contractsList[Object.keys(s==null?void 0:s.contractsList).find(e=>{var n;return((n=s==null?void 0:s.contractsList[e])==null?void 0:n.TypeID)==="NaturalPartner"})]);function g(){i.push("/Contracts")}function y(){var e;navigator.clipboard.writeText(`http://www.host-food.ru/p/${(e=d.value)==null?void 0:e.ID}/`)}function D(){i.push("/profile/DependUsersCreate")}function b(e){var n;r.userSwitch({UserID:(n=e==null?void 0:e.item)==null?void 0:n.ID}).then(H=>{const{result:R}=H;R==="SUCCESS"&&i.push("/Contracts")})}function f(e){return e.filter(n=>n==null?void 0:n.Name.includes(_.value))}return await r.fetchUserData(),await r.fetchDependUsers(),await s.fetchContracts(),{filter:_,referralTable:z,getUserInfo:d,getPartnerContract:C,getDependUsers:p,filterReferrals:f,sortBy:U,sortDesc:o,sortDirection:w,formNormalDate:j,secondToDate:F,navigateToContracts:g,copyText:y,createDependUser:D,changeToUser:b}}},Le=B(ge,[["render",Ce],["__scopeId","data-v-d21e3ca7"]]);export{Le as default};

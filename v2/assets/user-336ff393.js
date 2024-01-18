import{_ as u,r as f,h as n1,j as l1,o as a,x as M,w as P,m as r1,c,a as e,k as D,b as d,t as x,F as j,v as s1,d as k,p as f1,l as u1,f as p1,u as m1,R as h1,g as $}from"./index-1c309346.js";import{s as i1}from"./multiselect-b7ad6876.js";import{_ as a1}from"./BasicInput-33bf464f.js";import{I as c1}from"./IconUpload-1f349e03.js";import{u as v1}from"./files-9e0f878c.js";import{u as C1}from"./globalActions-8efdfe11.js";import{_ as _1}from"./ClausesKeeper-b9906182.js";import{f as g1}from"./bootstrap-vue-next.es-b799f76d.js";import{I as w1}from"./IconSettings-f951b6bf.js";import{_ as I1}from"./HelperComponent-3eb94393.js";import{_ as V1}from"./IconArrow-6927efe5.js";import{_ as b1}from"./IconCopy-b67ecf08.js";import{_ as D1}from"./ButtonDefault-cd183bf8.js";import{_ as x1}from"./user-placeholder-6c59613c.js";import"./component2-139d1cad.js";import"./component-a2c8255f.js";import"./IconClose-7cf50ca7.js";const M1={components:{VueSelect:i1},props:{modelValue:{type:String,required:!0},placeholder:{type:String,default:"Select"},options:{type:Array,required:!0,default:()=>[]},taggable:{type:Boolean,default:!1}},emits:["update:modelValue"],setup(i,{emit:s}){const p=f(i.modelValue);return n1(p,()=>{s("update:modelValue",p.value)}),n1(i,()=>{p.value=i.modelValue}),{newValue:p}}};function k1(i,s,p,t,K,R){const h=l1("vue-select");return a(),M(h,{modelValue:t.newValue,"onUpdate:modelValue":s[0]||(s[0]=C=>t.newValue=C),"clear-on-select":!1,options:p.options,placeholder:p.placeholder,searchable:!0,taggable:p.taggable,label:"name","track-by":"value"},{default:P(()=>[r1(i.$slots,"default",{},void 0,!0)]),_:3},8,["modelValue","options","placeholder","taggable"])}const y1=u(M1,[["render",k1],["__scopeId","data-v-f3c9e691"]]),S1={},E1={width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},U1=e("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M19 6V18.5C19 19.8807 17.8807 21 16.5 21H7.5C6.11929 21 5 19.8807 5 18.5V6H4.5C4.22386 6 4 5.77614 4 5.5C4 5.22386 4.22386 5 4.5 5H9V4.5C9 3.67157 9.67157 3 10.5 3H13.5C14.3284 3 15 3.67157 15 4.5V5H19.5C19.7761 5 20 5.22386 20 5.5C20 5.77614 19.7761 6 19.5 6H19ZM6 6V18.5C6 19.3284 6.67157 20 7.5 20H16.5C17.3284 20 18 19.3284 18 18.5V6H6ZM14 4.5V5H10V4.5C10 4.22386 10.2239 4 10.5 4H13.5C13.7761 4 14 4.22386 14 4.5ZM14 9.5C14 9.22386 14.2239 9 14.5 9C14.7761 9 15 9.22386 15 9.5V16.5C15 16.7761 14.7761 17 14.5 17C14.2239 17 14 16.7761 14 16.5V9.5ZM9.5 9C9.22386 9 9 9.22386 9 9.5V16.5C9 16.7761 9.22386 17 9.5 17C9.77614 17 10 16.7761 10 16.5V9.5C10 9.22386 9.77614 9 9.5 9Z",fill:"#B92519"},null,-1),L1=[U1];function Z1(i,s){return a(),c("svg",E1,L1)}const d1=u(S1,[["render",Z1]]),$1={},A1={xmlns:"http://www.w3.org/2000/svg",width:"20",height:"20",viewBox:"0 0 20 20",fill:"none"},B1=e("path",{d:"M14.4442 6.12906C14.4442 8.4053 12.4464 10.2569 9.98869 10.2569C7.53154 10.2569 5.53319 8.4053 5.53319 6.12906C5.53319 3.85207 7.53154 2 9.98869 2C12.4464 2 14.4442 3.85207 14.4442 6.12906ZM11.8335 6.12904C11.8335 5.18633 11.006 4.41976 9.98869 4.41976C8.97219 4.41976 8.1439 5.18633 8.1439 6.12904C8.1439 7.07101 8.97219 7.83808 9.98869 7.83808C11.006 7.83808 11.8335 7.07101 11.8335 6.12904Z",fill:"#EE8208"},null,-1),F1=e("path",{d:"M11.8028 13.6269C12.7097 13.4354 13.5844 13.1029 14.39 12.634C14.9997 12.2778 15.1834 11.531 14.7992 10.966C14.4153 10.3998 13.61 10.2293 12.9992 10.5855C11.1741 11.6491 8.82442 11.6488 7.00042 10.5855C6.38966 10.2293 5.58403 10.3998 5.20094 10.966C4.81679 11.5315 4.99993 12.2778 5.60962 12.634C6.41525 13.1024 7.28993 13.4354 8.19687 13.6269L5.70586 15.9352C5.19668 16.4076 5.19668 17.1734 5.70639 17.6457C5.96152 17.8817 6.29529 17.9998 6.62906 17.9998C6.96336 17.9998 7.29766 17.8817 7.55279 17.6457L9.99954 15.3774L12.4484 17.6457C12.9576 18.1181 13.7838 18.1181 14.2935 17.6457C14.8037 17.1734 14.8037 16.4071 14.2935 15.9352L11.8028 13.6269Z",fill:"#EE8208"},null,-1),H1=[B1,F1];function N1(i,s){return a(),c("svg",A1,H1)}const T1=u($1,[["render",N1]]),O1={},j1={xmlns:"http://www.w3.org/2000/svg",width:"20",height:"20",viewBox:"0 0 20 20",fill:"none"},P1=e("path",{d:"M16.3848 11.3341C16.2017 11.0418 16.1607 10.7459 16.2608 10.4461C16.5104 9.64627 20.147 4.56612 18.6321 4.56612C17.5911 4.5824 16.3391 4.56612 15.3411 4.56612C15.1558 4.61918 15.036 4.73357 14.961 4.96631C14.3774 6.54946 13.6632 8.48147 12.6166 9.72315C12.4789 9.85376 12.393 9.84819 12.2361 9.79989C11.502 8.89054 12.2848 5.77508 11.8383 4.54405C11.7503 4.30309 11.5577 4.19425 11.3607 4.13296C10.3639 3.85349 8.08743 4.01984 7.716 4.5824C7.60982 4.74322 7.59501 4.83098 7.67166 4.84542C8.02556 4.9111 8.27609 5.06824 8.42354 5.31666C8.70172 6.03737 8.90673 9.8764 8.08743 9.8764C7.26814 9.8764 5.947 6.1088 5.58406 5.06436C5.48748 4.74988 5.24161 4.56959 4.98254 4.51637L2.5587 4.53818C2.13265 4.53818 1.89454 4.78543 2.04555 5.28357C3.30798 8.97029 6.07063 16.1164 10.3078 15.9375C10.7323 15.9375 11.4625 16.1292 11.794 15.6963C12.2496 14.9852 11.694 13.7188 12.4752 13.2359C12.6722 13.1126 12.8764 13.2159 13.0457 13.3676C13.9146 14.146 14.3759 15.5122 15.4475 15.927C15.6359 16 15.801 16.0183 15.9427 15.9818L18.2073 15.938C18.6277 15.938 19.0418 15.7179 18.9548 15.1266C18.6247 13.8585 17.3092 12.555 16.3848 11.3341Z",fill:"#2789F6"},null,-1),K1=[P1];function R1(i,s){return a(),c("svg",j1,K1)}const q1=u(O1,[["render",R1]]),G1={},W1={xmlns:"http://www.w3.org/2000/svg",width:"20",height:"20",viewBox:"0 0 20 20",fill:"none"},Y1=e("g",{"clip-path":"url(#clip0_2871_29099)"},[e("path",{d:"M14 17H11.6428V5.75836H10.5909C8.66254 5.75836 7.64954 6.69326 7.64954 8.07766C7.64954 9.64858 8.35078 10.3783 9.7921 11.3137L10.9804 12.0807L7.55193 17H5L8.07786 12.6043C6.30534 11.3883 5.31177 10.21 5.31177 8.20868C5.31177 5.70194 7.12361 4 10.5715 4H14V17Z",fill:"#FC3F1D"})],-1),z1=e("defs",null,[e("clipPath",{id:"clip0_2871_29099"},[e("rect",{width:"20",height:"20",fill:"white"})])],-1),J1=[Y1,z1];function Q1(i,s){return a(),c("svg",W1,J1)}const X1=u(G1,[["render",Q1]]),ee={},oe={xmlns:"http://www.w3.org/2000/svg",width:"20",height:"20",viewBox:"0 0 20 20",fill:"none"},te=e("path",{d:"M18.7511 10.1943C18.7511 9.47489 18.6915 8.94989 18.5626 8.40546H10.1797V11.6526H15.1003C15.0011 12.4596 14.4654 13.6749 13.2749 14.4915L13.2582 14.6002L15.9087 16.6125L16.0924 16.6305C17.7788 15.104 18.7511 12.8582 18.7511 10.1943Z",fill:"#4285F4"},null,-1),ne=e("path",{d:"M10.1793 18.75C12.59 18.75 14.6138 17.9722 16.092 16.6305L13.2745 14.4916C12.5206 15.0068 11.5086 15.3666 10.1793 15.3666C7.81822 15.3666 5.81427 13.8402 5.09992 11.7305L4.99522 11.7392L2.23917 13.8295L2.20312 13.9277C3.67136 16.786 6.68723 18.75 10.1793 18.75Z",fill:"#34A853"},null,-1),se=e("path",{d:"M5.09916 11.7305C4.91068 11.1861 4.80159 10.6027 4.80159 9.99998C4.80159 9.39716 4.91068 8.81385 5.08925 8.26941L5.08425 8.15346L2.29366 6.0296L2.20236 6.07216C1.59723 7.25829 1.25 8.59026 1.25 9.99998C1.25 11.4097 1.59723 12.7416 2.20236 13.9277L5.09916 11.7305Z",fill:"#FBBC05"},null,-1),le=e("path",{d:"M10.1794 4.63331C11.8559 4.63331 12.9868 5.34303 13.6317 5.93612L16.1516 3.525C14.604 2.11528 12.59 1.25 10.1794 1.25C6.68725 1.25 3.67137 3.21387 2.20312 6.07218L5.09002 8.26943C5.8143 6.15972 7.81825 4.63331 10.1794 4.63331Z",fill:"#EB4335"},null,-1),ie=[te,ne,se,le];function ae(i,s){return a(),c("svg",oe,ie)}const ce=u(ee,[["render",ae]]),_e={},de={fill:"none",height:"34",viewBox:"0 0 34 34",width:"34",xmlns:"http://www.w3.org/2000/svg"},re=e("path",{"clip-rule":"evenodd",d:"M17.227 10.012c3.053.013 4.516.902 4.855 1.2 1.126.932 1.7 3.164 1.28 6.433-.365 2.855-2.345 3.301-3.06 3.463-.08.017-.144.032-.188.046-.189.06-1.938.479-4.137.34 0 0-1.639 1.912-2.15 2.409-.08.077-.174.108-.237.094-.087-.021-.111-.122-.111-.27l.014-2.61c-3.095-.832-3.023-3.881-2.982-5.612 0-.06.002-.118.004-.174.036-1.692.365-3.079 1.341-4.011 1.755-1.538 5.37-1.308 5.37-1.308Zm-.336 1.684c-.11 0-.2.085-.201.192 0 .107.088.194.198.194 1.088.008 1.987.354 2.71 1.036.718.677 1.091 1.609 1.1 2.82a.198.198 0 0 0 .202.192c.11 0 .2-.088.198-.194-.01-1.291-.41-2.33-1.22-3.094-.803-.757-1.803-1.138-2.987-1.146Zm.679 2.084a.198.198 0 0 0-.21.182.196.196 0 0 0 .189.203c.353.018.594.118.752.276.159.159.26.403.279.76a.197.197 0 0 0 .209.184.196.196 0 0 0 .19-.203c-.022-.416-.143-.762-.39-1.01-.25-.248-.599-.371-1.02-.392Zm-.38-1.021a.187.187 0 0 0-.19.183c0 .101.082.185.188.186.704.005 1.283.232 1.75.68.461.442.704 1.052.71 1.855 0 .101.085.184.19.183a.187.187 0 0 0 .188-.186c-.006-.878-.275-1.59-.823-2.116-.541-.519-1.217-.78-2.013-.785Zm.851 4.916c-.154.184-.441.16-.441.16-2.098-.517-2.659-2.571-2.659-2.571s-.025-.277.166-.427l.38-.291c.187-.14.307-.48.116-.812a8.585 8.585 0 0 0-.478-.724c-.167-.22-.556-.671-.557-.673-.188-.214-.463-.263-.754-.117h-.003a3.116 3.116 0 0 0-.78.612c-.179.21-.281.415-.307.617a.796.796 0 0 0 .036.352l.01.006c.09.31.317.829.81 1.692.281.5.606.975.97 1.422a7.7 7.7 0 0 0 .587.639l.007.007.015.015.067.064c.209.201.43.39.661.567.462.353.954.667 1.47.94.893.476 1.43.695 1.75.782l.007.01a.874.874 0 0 0 .364.035c.208-.024.42-.124.636-.298.002 0 .002-.001.003-.002l.003-.002a3.06 3.06 0 0 0 .627-.747v-.002l.001-.003c.151-.281.1-.548-.122-.73l-.05-.038c-.125-.1-.463-.37-.646-.5a8.926 8.926 0 0 0-.748-.462c-.344-.185-.694-.07-.84.113l-.3.366Z",fill:"#2992CD","fill-rule":"evenodd"},null,-1),fe=[re];function ue(i,s){return a(),c("svg",de,fe)}const pe=u(_e,[["render",ue]]),me={},he={width:"34",height:"34",viewBox:"0 0 34 34",fill:"none",xmlns:"http://www.w3.org/2000/svg"},ve=e("path",{d:"M22.25 21.0833V20.1232C22.25 19.6461 21.9596 19.2172 21.5166 19.04L20.33 18.5653C19.7667 18.34 19.1246 18.5841 18.8533 19.1268L18.75 19.3333C18.75 19.3333 17.2917 19.0417 16.125 17.875C14.9583 16.7083 14.6667 15.25 14.6667 15.25L14.8732 15.1467C15.4159 14.8754 15.66 14.2333 15.4346 13.67L14.96 12.4834C14.7828 12.0404 14.3539 11.75 13.8768 11.75H12.9167C12.2723 11.75 11.75 12.2723 11.75 12.9167C11.75 18.0713 15.9287 22.25 21.0833 22.25C21.7277 22.25 22.25 21.7277 22.25 21.0833Z",fill:"#2992CD"},null,-1),Ce=[ve];function ge(i,s){return a(),c("svg",he,Ce)}const we=u(me,[["render",ge]]),Ie={},Ve={fill:"none",height:"34",viewBox:"0 0 34 34",width:"34",xmlns:"http://www.w3.org/2000/svg"},be=e("path",{"clip-rule":"evenodd",d:"M17 10.438A6.57 6.57 0 0 0 10.437 17 6.57 6.57 0 0 0 17 23.563a6.526 6.526 0 0 0 3.697-1.142h.001l.15-.106-1.05-1.222-.122.076h-.001a4.93 4.93 0 0 1-2.675.786A4.961 4.961 0 0 1 12.045 17 4.961 4.961 0 0 1 17 12.045a4.961 4.961 0 0 1 4.84 6.019c-.075.3-.213.485-.361.592a.687.687 0 0 1-.88-.052c-.116-.112-.206-.288-.208-.558V17A3.395 3.395 0 0 0 17 13.609 3.395 3.395 0 0 0 13.609 17 3.395 3.395 0 0 0 17 20.391a3.36 3.36 0 0 0 2.277-.88 2.23 2.23 0 0 0 1.798.888c.5 0 .993-.167 1.39-.47a2.55 2.55 0 0 0 .882-1.294 4.698 4.698 0 0 0 .067-.247l.006-.024.001-.007.001-.003.001-.004.001-.005c.096-.42.139-.835.139-1.345A6.57 6.57 0 0 0 17 10.437Zm2.277 9.072c-.03-.038.027.042 0 0-.035.035.036-.032 0 0 .027.042-.029-.039 0 0 .037-.033-.035.036 0 0Zm2.563-1.448-.001.003v-.003ZM15.216 17c0-.984.8-1.784 1.784-1.784s1.784.8 1.784 1.784-.8 1.784-1.784 1.784-1.784-.8-1.784-1.784Z",fill:"#2992CD","fill-rule":"evenodd"},null,-1),De=[be];function xe(i,s){return a(),c("svg",Ve,De)}const Me=u(Ie,[["render",xe]]),ke={},ye={fill:"none",height:"34",viewBox:"0 0 34 34",width:"34",xmlns:"http://www.w3.org/2000/svg"},Se=e("path",{d:"M22.081 12.132s1.133-.442 1.038.631c-.03.442-.314 1.989-.534 3.661l-.756 4.956s-.063.726-.63.852c-.566.126-1.416-.442-1.573-.568-.126-.095-2.361-1.515-3.148-2.21-.22-.19-.473-.568.031-1.01l3.305-3.156c.378-.379.756-1.262-.818-.19l-4.407 2.999s-.504.316-1.448.032l-2.046-.632s-.756-.473.535-.946c3.148-1.484 7.02-2.999 10.45-4.42h.001Z",fill:"#2992CD"},null,-1),Ee=[Se];function Ue(i,s){return a(),c("svg",ye,Ee)}const Le=u(ke,[["render",Ue]]),Ze={},$e={fill:"none",height:"34",viewBox:"0 0 34 34",width:"34",xmlns:"http://www.w3.org/2000/svg"},Ae=e("path",{"clip-rule":"evenodd",d:"M23.68 13.47c.096-.319 0-.553-.465-.553h-1.53c-.39 0-.57.202-.668.426 0 0-.778 1.864-1.881 3.075-.357.351-.52.463-.714.463-.098 0-.244-.112-.244-.431v-2.98c0-.383-.108-.553-.432-.553h-2.408c-.243 0-.39.177-.39.346 0 .362.552.446.609 1.466v2.215c0 .486-.09.574-.284.574-.52 0-1.782-1.873-2.532-4.016-.145-.417-.292-.585-.684-.585h-1.532c-.438 0-.525.202-.525.426 0 .397.52 2.374 2.418 4.988 1.266 1.785 3.048 2.752 4.671 2.752.974 0 1.094-.214 1.094-.585V19.15c0-.43.092-.516.4-.516.228 0 .617.112 1.526.973 1.038 1.02 1.21 1.477 1.794 1.477h1.531c.438 0 .657-.214.531-.639-.139-.422-.635-1.035-1.292-1.763-.357-.414-.893-.86-1.055-1.084-.227-.286-.162-.414 0-.669 0 0 1.867-2.582 2.061-3.458Z",fill:"#2992CD","fill-rule":"evenodd"},null,-1),Be=[Ae];function Fe(i,s){return a(),c("svg",$e,Be)}const He=u(Ze,[["render",Fe]]),Ne="/v2/assets/warning-polygon-2df7feaf.svg",m=i=>(f1("data-v-3b7de96d"),i=i(),u1(),i),Te={class:"section"},Oe={class:"container"},je={class:"section-header"},Pe=m(()=>e("h1",{class:"section-title"},"Профиль",-1)),Ke={class:"btn__group"},Re={class:"profile"},qe={class:"profile-rows"},Ge={class:"profile-col"},We={class:"profile-avatar img"},Ye=["src"],ze={key:1,src:x1,alt:""},Je={key:0,class:"profile-avatar_upload"},Qe={key:0,class:"profile-col col-max-width"},Xe={class:"profile-info"},eo={class:"profile-info__wrap-e"},oo={class:"profile-info__item"},to=m(()=>e("div",{class:"profile-info__item-label"},"ФИО",-1)),no={class:"profile-info__item-value"},so={class:"profile-info__item"},lo=m(()=>e("div",{class:"profile-info__item-label"},"Почта",-1)),io={class:"profile-info__item-value"},ao={class:"profile-info__item"},co=m(()=>e("div",{class:"profile-info__item-label"},"Подпись",-1)),_o={class:"profile-info__item-value"},ro={class:"profile-info__item"},fo=m(()=>e("div",{class:"profile-info__item-label"},"Реферальная ссылка",-1)),uo={class:"profile-info__item-value"},po={key:0,class:"profile-info__item"},mo=m(()=>e("div",{class:"profile-info__item-label"},"Контактные данные",-1)),ho={class:"profile-info__item-contacts"},vo={class:"profile-info__item-contacts-item"},Co={class:"profile-info__item-info-column"},go={class:"profile-info__item-contacts-ico"},wo={class:"profile-info__item-contacts-val"},Io={class:"profile-info__item-info-column"},Vo=m(()=>e("div",{class:"profile-info__item-warning"},[e("img",{src:Ne,alt:""}),k("не подтвержден")],-1)),bo={class:"profile-info__item-helper-column"},Do={class:"profile-info__note-wrapper"},xo=m(()=>e("div",{class:"profile-info__note-label"},"Заметка",-1)),Mo={key:1,class:"profile-col"},ko={class:"profile-info"},yo={class:"profile-info__wrap-e"},So={class:"profile-info__item"},Eo={class:"profile-info__item"},Uo={class:"profile-info__item"},Lo=m(()=>e("div",{class:"profile-info__item"},[e("div",{class:"profile-info__item-title"},"Новый контакт")],-1)),Zo={class:"profile-info__wrap contact-item"},$o={class:"profile-info__item"},Ao={class:"profile-info__item wrap-item"},Bo={class:"profile-info__item"},Fo={class:"profile-info__wrap"},Ho={class:"profile-info__item"},No={class:"profile-info__link",href:"#"},To={class:"profile-info__item"},Oo={class:"profile-info__link",href:"#"},jo={class:"profile-info__item"},Po={class:"profile-info__link",href:"#"},Ko={class:"profile-info__item"},Ro={class:"profile-info__link",href:"#"},qo=m(()=>e("div",{class:"profile-info__item"},[e("div",{class:"profile-info__item-title"},"Контактные данные")],-1)),Go={class:"profile-info__contact-label"},Wo={class:"profile-info__wrap edited-items"},Yo={class:"profile-info__item profile-info__contact"},zo={class:"profile-info__item"},Jo=["onClick"],Qo={class:"profile-info__item mark"},Xo={class:"profile-info__note-wrapper"},et=m(()=>e("div",{class:"profile-info__note-label"},"Заметка",-1)),ot={class:"profile-info__item"},tt=["onClick"];function nt(i,s,p,t,K,R){var F,H,N,T,O,Z;const h=D1,C=_1,A=c1,y=b1,U=He,S=Le,r=Me,q=we,L=pe,g=V1,V=I1,w=a1,B=l1("Multiselect"),G=ce,W=X1,E=q1,b=T1,Y=w1,z=d1,J=g1;return a(),c("div",Te,[e("div",Oe,[e("div",je,[Pe,e("div",Ke,[t.isEditMode?(a(),c("button",{key:0,class:"btn btn--blue btn-default static-button delete",onClick:s[0]||(s[0]=(...o)=>t.cancelEdit&&t.cancelEdit(...o))},"Отменить")):D("",!0),d(h,{class:"static-button","is-loading":t.isLoading,label:t.isEditMode?"Сохранить изменения":"Редактировать профиль",onClick:s[1]||(s[1]=o=>t.switchEdit().then(()=>t.stopEdit()))},null,8,["is-loading","label"])])]),d(C),e("div",Re,[e("div",qe,[e("div",Ge,[e("div",We,[t.getAvatarName?(a(),c("img",{key:0,src:`/UserFoto?UserID=${(F=t.getUserInfo)==null?void 0:F.ID}?${t.randomSuf}`,alt:""},null,8,Ye)):(a(),c("img",ze))]),t.isEditMode?(a(),c("label",Je,[e("input",{type:"file",onInput:s[2]||(s[2]=(...o)=>t.inputFile&&t.inputFile(...o)),title:" "},null,32),d(A),e("span",null,x(((H=t.file)==null?void 0:H.name)||"Загрузить аватарку"),1)])):D("",!0)]),t.isEditMode?D("",!0):(a(),c("div",Qe,[e("div",Xe,[e("div",eo,[e("div",oo,[to,e("div",no,x((N=t.getUserInfo)==null?void 0:N.Name),1)]),e("div",so,[lo,e("div",io,x((T=t.getUserInfo)==null?void 0:T.Email),1)]),e("div",ao,[co,e("div",_o,x((O=t.getUserInfo)==null?void 0:O.Sign),1)])]),e("div",ro,[fo,e("div",uo,[e("div",{class:"profile-info__item-referral",ref:"message"},x(`http://www.host-food.ru/p/${(Z=t.getUserInfo)==null?void 0:Z.ID}/`),513),e("button",{class:"btn profile-info__item-referral_copy",onClick:s[3]||(s[3]=(...o)=>t.copyText&&t.copyText(...o))},[d(y)])])]),t.getContacts&&t.getContacts.length>0?(a(),c("div",po,[mo,e("div",ho,[(a(!0),c(j,null,s1(t.getContacts,o=>(a(),c("div",vo,[e("div",Co,[e("div",go,[(o==null?void 0:o.MethodID)==="VKontakte"?(a(),M(U,{key:0})):(o==null?void 0:o.MethodID)==="Telegram"?(a(),M(S,{key:1})):(o==null?void 0:o.MethodID)==="Email"?(a(),M(r,{key:2})):(o==null?void 0:o.MethodID)==="SMS"?(a(),M(q,{key:3})):(a(),M(L,{key:4}))]),e("div",wo,x(o==null?void 0:o.Address),1)]),e("div",Io,[(o==null?void 0:o.Confirmed)>0?D("",!0):(a(),c(j,{key:0},[Vo,d(h,{class:"profile-info__button",label:"ПОДТВЕРДИТЬ",onClick:n=>t.openConfirmationModal(o)},null,8,["onClick"])],64)),e("div",bo,[o.UserNotice?(a(),M(V,{key:0,"user-notice":o.UserNotice,id:o.ID,noteTable:"Contacts",emit:"updateContacts"},{default:P(()=>[e("div",Do,[xo,d(g,{class:"profile-info__note-icon"})])]),_:2},1032,["user-notice","id"])):D("",!0)])])]))),256))])])):D("",!0)])])),t.isEditMode?(a(),c("div",Mo,[e("div",ko,[e("div",yo,[e("div",So,[d(w,{label:"ФИО",name:"name",placeholder:"Крылов Евгений",vertical:"",modelValue:t.formData.Name,"onUpdate:modelValue":s[4]||(s[4]=o=>t.formData.Name=o)},null,8,["modelValue"])]),e("div",Eo,[d(w,{label:"Почта",name:"email",placeholder:"hosting@nopreset.ru",vertical:"",disabled:"",modelValue:t.formData.Email,"onUpdate:modelValue":s[5]||(s[5]=o=>t.formData.Email=o)},null,8,["modelValue"])]),e("div",Uo,[d(w,{label:"Подпись",name:"name",placeholder:"С уважением, Евгений Крылов",vertical:"",modelValue:t.formData.Sign,"onUpdate:modelValue":s[6]||(s[6]=o=>t.formData.Sign=o)},null,8,["modelValue"])])]),Lo,e("div",Zo,[e("div",$o,[d(B,{modelValue:t.select,"onUpdate:modelValue":s[7]||(s[7]=o=>t.select=o),options:t.activeContactOptions,disabled:t.activeContactOptions.length<=1,label:"name",placeholder:"Тип адреса"},null,8,["modelValue","options","disabled"])]),e("div",Ao,[d(w,{name:"name",placeholder:"Ссылка или id",vertical:"",modelValue:t.newContactValue,"onUpdate:modelValue":s[8]||(s[8]=o=>t.newContactValue=o)},null,8,["modelValue"])]),e("div",Bo,[d(h,{class:"profile-info__add-button",label:"Добавить контакт",onClick:t.addContact},null,8,["onClick"])])]),e("div",Fo,[e("div",Ho,[e("a",No,[k("Google"),d(G)])]),e("div",To,[e("a",Oo,[k("Яндекс"),d(W)])]),e("div",jo,[e("a",Po,[k("ВК"),d(E)])]),e("div",Ko,[e("a",Ro,[k("Одноклассники"),d(b)])])])]),qo,(a(!0),c(j,null,s1(t.getContacts,(o,n)=>(a(),c(j,{key:n},[e("div",Go,x(o==null?void 0:o.MethodID),1),e("div",Wo,[e("div",Yo,[d(w,{name:i.name,disabled:!(o!=null&&o.IsActive),modelValue:t.formContactsData[n].Address,"onUpdate:modelValue":l=>t.formContactsData[n].Address=l,vertical:""},null,8,["name","disabled","modelValue","onUpdate:modelValue"])]),e("div",zo,[e("div",{class:"profile-info__item-settings",onClick:l=>t.editContact(o)},[d(Y),k("Настройки")],8,Jo)]),e("div",Qo,[d(V,{"user-notice":o.UserNotice,id:o.ID,noteTable:"Contacts",emit:"updateContacts"},{default:P(()=>[e("div",Xo,[et,d(g,{class:"profile-info__note-icon"})])]),_:2},1032,["user-notice","id"])]),e("div",ot,[e("a",{class:"profile-info__item-close",onClick:l=>t.closeContact(n)},[d(z)],8,tt)])])],64))),128)),d(J,{id:"checkbox-1",modelValue:t.IsClear,"onUpdate:modelValue":s[9]||(s[9]=o=>t.IsClear=o),name:"checkbox-1",value:!0,"unchecked-value":!1},{default:P(()=>[k("Удалить аватарку")]),_:1},8,["modelValue"])])):D("",!0)])])])])}const st={components:{IconTrash:d1,IconUpload:c1,BasicInput:a1,ClausesKeeper:_1,Multiselect:i1,SelectComponent:y1},async setup(){const i=p1(),s=v1(),p=m1(),t=C1(),K=f(null),R=f(""),h=h1("emitter");h.on("updateContacts",()=>{i.fetchUserData().then(()=>{b()})});const C=f(!1),A=f(null),y=f(!1),U=f(!1),S=$(()=>{var n,l;return(l=(n=p.ConfigList)==null?void 0:n.Notifies)==null?void 0:l.Methods}),r=$(()=>i.userInfo),q=$(()=>{var n,l,_,I,v;return(v=(I=(n=r.value)==null?void 0:n.Files)==null?void 0:I[(_=Object.keys((l=r.value)==null?void 0:l.Files))==null?void 0:_[0]])==null?void 0:v.Name}),L=$(()=>{var n;return Object.keys((n=r.value)==null?void 0:n.Contacts).map(l=>{var _,I,v,Q,X,e1,o1,t1;if(!((v=(I=(_=r.value)==null?void 0:_.Contacts)==null?void 0:I[l])!=null&&v.IsPrimary)&&!((e1=(X=(Q=r.value)==null?void 0:Q.Contacts)==null?void 0:X[l])!=null&&e1.IsHidden))return(t1=(o1=r.value)==null?void 0:o1.Contacts)==null?void 0:t1[l]}).filter(Boolean)}),g=f(null),V=f({Name:"",Sign:""}),w=f(""),B=f(""),G=f([]),W=$(()=>Object.keys(S.value).filter(n=>S.value[n].IsActive==="1").map(n=>({name:S.value[n].Name,value:n}))),E=f({});function b(){let n={};L.value.forEach((l,_)=>{n[_]={...l,oldValue:l.Address}}),E.value=n,Y()}function Y(){var n,l,_;V.value={Name:(n=r.value)==null?void 0:n.Name,Email:(l=r.value)==null?void 0:l.Email,Sign:(_=r.value)==null?void 0:_.Sign}}b();function z(){const n={MethodID:w.value,Address:B.value,TimeBegin:"00",TimeEnd:"00",IsActive:"yes",ContactID:"0"};i.userEditContact(n).then(l=>{l.response==="SUCCESS"&&i.fetchUserData().then(()=>{b()})})}const J=async n=>{var v;const l=(v=L.value[n])==null?void 0:v.ID,{result:_,error:I}=await t.deleteItem({TableID:"Contacts",RowsIDs:l});_==="SUCCESS"?i.removeContact(l):console.error("Error deleting contact on server:",I)};async function F(){var n;if(U.value=!0,C.value){for(const l of Object.keys(E.value)){let _=(n=E.value)==null?void 0:n[l];_.Address!==_.oldValue&&await i.userEditContact({MethodID:_.MethodID,Address:_.Address,TimeBegin:"00",TimeEnd:"00",IsActive:"yes",IsImmediately:"yes",IsSendFiles:"yes",ContactID:_.ID}).then(({responseText:I,response:v})=>{v==="SUCCESS"&&t.deleteItem({TableID:"Contacts",RowsIDs:_.ID})})}g.value?await s.sendFile(g.value).then(l=>{i.UserPersonalDataChange({...V.value,IsClear:y.value?"yes":"","UserFoto[]":l.slice(l.lastIndexOf("^")+1,l.length)}).then(()=>{i.fetchUserData().then(()=>{Z(),A.value=new Date().getMilliseconds()})})}):await i.UserPersonalDataChange({...V.value,IsClear:y.value?"yes":""}).then(()=>{i.fetchUserData().then(()=>{g.value=null,b()})})}}function H(){var n;navigator.clipboard.writeText(`http://www.host-food.ru/p/${(n=r.value)==null?void 0:n.ID}/`)}function N(n){i.confirmContact({Method:n.MethodID,Value:n.Address,ContactID:n.ID}).then(l=>{var _;((_=l==null?void 0:l.data)==null?void 0:_.Status)==="Ok"&&h.emit("open-modal",{component:"ConfirmContact",data:{Method:n.MethodID,Value:n.Address,ContactID:n.ID}})})}function T(n){var l;g.value=(l=n.target.files)==null?void 0:l[0]}function O(n){n!=null&&n.IsActive?h.emit("open-modal",{component:"EditContact",data:n}):h.emit("open-error-modal",{component:"ExclusionWindow",data:{message:{String:'Уведомления для этого адреса отключены. Для включения уведомлений подтвердите адрес и поставьте галочку "Использовать для уведомлений"'}}})}function Z(){C.value=!1,g.value=null,y.value=!1,b()}function o(){C.value=!C.value,U.value=!1}return{file:g,isLoading:U,getAvatarName:q,getUserInfo:r,getContacts:L,isEditMode:C,formData:V,formContactsData:E,randomSuf:A,IsClear:y,getConfigMethods:S,stopEdit:o,switchEdit:F,copyText:H,inputFile:T,cancelEdit:Z,editContact:O,openConfirmationModal:N,select:w,activeContactOptions:W,addContact:z,newContactValue:B,closeContact:J,contactsData:G,selectedContact:K,selectedContactAddress:R}}},Vt=u(st,[["render",nt],["__scopeId","data-v-3b7de96d"]]);export{Vt as default};

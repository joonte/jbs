{* Smarty *}
<HTML>
 <HEAD id="Head">
  <TITLE>{block name=title}Default Page Title{/block}</TITLE>
  <META http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <LINK rel="alternate" type="application/rss+xml" title="Новости компании" href="/Rss/News" />
  <LINK rel="alternate" type="application/rss+xml" title="JBs версии и изменения" href="/Rss/Features" />
  <!-- JS -->
  <SCRIPT type="text/javascript" src="/styles/root/Js/Standard.js"></SCRIPT>
  <SCRIPT type="text/javascript" src="/styles/root/Js/DOM.js"></SCRIPT>
  <SCRIPT type="text/javascript" src="/styles/root/Js/HTTP.js"></SCRIPT>
  <SCRIPT type="text/javascript" src="/styles/root/Js/Ajax/Window.js"></SCRIPT>
  <SCRIPT type="text/javascript" src="/styles/root/Js/Ajax/AutoComplite.js"></SCRIPT>
  <SCRIPT type="text/javascript" src="/styles/root/others/jQuery/core.js"></SCRIPT>
  {block name=js}{/block}
  <!-- CSS -->
  <LINK href="/styles/root/Css/Standard.css" rel="stylesheet" type="text/css" />
  <LINK href="/styles/billing/Css/Standard.css" rel="stylesheet" type="text/css" />
  <LINK href="/styles/billing/Css/TableSuper.css" rel="stylesheet" type="text/css" />
  <LINK rel="stylesheet" type="text/css" href="/styles/root/others/jQuery/smoothness/jquery-ui-custom.css" />
  {block name=css}{/block}
  <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-28933191-1']);
      _gaq.push(['_setDomainName', 'joonte.com']);
      _gaq.push(['_setAllowLinker', true]);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
  </script>
 </HEAD>
 <BODY id="Body">
  <TABLE cellspacing="0" align="center" cellpadding="0" width="1000">
   <TR>
    <TD>
     <TABLE cellspacing="0" height="100%" cellpadding="0">
      <TR id="Top">
       <TD>
        <IMG src="/styles/joonte/Images/TopLogo.png" />
       </TD>
      </TR>
     </TABLE>
    </TD>
   </TR>
   <TR>
     <TD>
     {top_panel}
     </TD>
   </TR>
   <TR height="10">
    <TD></TD>
   </TR>
   <TR>
    <TD id="Context">

     <TABLE cellspacing="0" cellpadding="0" width="100%" height="100%">
      <TR>
       {block name=menuLeft}{/block}
       <TD valign="top">
        <DIV class="Title" id="Title">{block name=title}{/block}</DIV>
        <TABLE width="100%" cellspacing="5" cellpadding="0">
         <TR>
          <TD id="Into">
            {block name=into}{/block}
          </TD>
         </TR>
        </TABLE>
       </TD>
      </TR>
     </TABLE>
    </TD>
   </TR>
    <TR>
    <TD style="border-top:2px solid #DCDCDC;">
     <TABLE cellspacing="5" width="100%" cellpadding="0">
      <TR>
       <TD id="Copyright">{copyright}</TD>
       <TD align="right">
         <!--LiveInternet counter-->
           <script type="text/javascript">document.write("<a href='http://www.liveinternet.ru/click' target=_blank><img src='//counter.yadro.ru/hit?t26.6;r" + escape(document.referrer) + ((typeof(screen)=="undefined")?"":";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?screen.colorDepth:screen.pixelDepth)) + ";u" + escape(document.URL) + ";" + Math.random() + "' border=0 width=88 height=15 alt='' title='LiveInternet: number of visitors for today is shown'><\/a>")</script>
         <!--/LiveInternet-->
       </TD>
      </TR>
     </TABLE>
    </TD>
    </TR>
  </TABLE>
  <DIV id="Floating" />
 </BODY>
</HTML>

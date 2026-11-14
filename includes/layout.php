<?php
require_once __DIR__.'/../config/app.php';
function head($title,$minimal=false){ $u=user(); $f=flash(); $b=BASE;
$links=[['index.php','Home'],['menu.php','Menu'],['index.php#about','About Us'],['index.php#contact','Contact Us'],['orders.php','My Orders']];
if($u&&$u['role']!=='customer') $links=[];
if($u&&in_array($u['role'],['server','admin'])) $links[]=['server/board.php','Order Board'];
if($u&&$u['role']==='admin'){ $links[]=['admin/dashboard.php','Dashboard']; $links[]=['admin/products.php','Products']; $links[]=['admin/employees.php','Employees']; $links[]=['admin/categories.php','Categories']; $links[]=['admin/customers.php','Customers']; $links[]=['admin/orders.php','Orders']; $links[]=['admin/reports.php','Reports']; $links[]=['admin/settings.php','Settings']; $links[]=['admin/activity.php','Activity']; }
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> · Galleta Café</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:'#EFA1C4',soft:'#FCEAF2',light:'#FFF8FB',ink:'#40343D',mute:'#82747D',line:'#F2D5E3'}}}}</script>
<style>
.site-header{transition:background-color .3s ease,box-shadow .3s ease,border-color .3s ease}
.site-header.is-scrolled{background-color:rgba(255,255,255,.98);box-shadow:0 10px 28px rgba(110,62,87,.09)}
.nav-link{position:relative;transition:color .25s ease,background-color .25s ease,transform .25s ease}
.nav-link::after{content:"";position:absolute;left:1rem;right:1rem;bottom:.22rem;height:2px;border-radius:999px;background:#EFA1C4;transform:scaleX(0);transform-origin:center;transition:transform .28s ease}
.nav-link:hover,.nav-link:focus-visible{background:#FCEAF2;color:#40343D;transform:translateY(-1px)}
.nav-link:hover::after,.nav-link:focus-visible::after{transform:scaleX(1)}
.nav-action{transition:background-color .25s ease,border-color .25s ease,color .25s ease,box-shadow .25s ease,transform .25s ease}
.nav-action:hover,.nav-action:focus-visible{transform:translateY(-1px);box-shadow:0 7px 16px rgba(110,62,87,.12)}
.cart-action svg{transition:transform .3s ease}
.cart-action:hover svg,.cart-action:focus-visible svg{transform:translateY(-1px) rotate(-6deg)}
.mobile-menu-button svg{transition:transform .3s ease}
.mobile-menu-button.is-open svg{transform:rotate(90deg)}
@media (max-width:767px){
  #site-nav.nav-open{display:flex !important;flex:0 0 100%;width:100%;flex-direction:column;gap:.35rem;overflow:visible;border-top:1px solid #F2D5E3;padding:.55rem 0 .15rem;animation:nav-open .25s ease both}
  #site-nav.nav-open .nav-link{width:100%;justify-content:flex-start}
  #site-nav.nav-open .nav-link{animation:nav-link-in .25s ease both}
  #site-nav.nav-open .nav-link:nth-child(2){animation-delay:.03s}
  #site-nav.nav-open .nav-link:nth-child(3){animation-delay:.06s}
  #site-nav.nav-open .nav-link:nth-child(4){animation-delay:.09s}
  #site-nav.nav-open .nav-link:nth-child(5){animation-delay:.12s}
}
@keyframes nav-open{from{opacity:0;transform:translateY(-.5rem)}to{opacity:1;transform:translateY(0)}}
@keyframes nav-link-in{from{opacity:0;transform:translateX(-.5rem)}to{opacity:1;transform:translateX(0)}}
@media (prefers-reduced-motion:reduce){
  .site-header,.nav-link,.nav-link::after,.nav-action,.cart-action svg,.mobile-menu-button svg{transition:none}
  #site-nav.nav-open,.nav-link{animation:none}
}
</style></head>
<body class="bg-light text-ink min-h-screen overflow-x-hidden">
<header id="site-header" class="site-header bg-white/95 backdrop-blur border-b border-line sticky top-0 z-20"><div class="max-w-6xl mx-auto px-3 sm:px-4 py-2 flex items-center gap-2 sm:gap-3 flex-wrap">
<a href="<?=$b?>/index.php" class="flex items-center gap-2 shrink-0"><img src="<?=$b?>/assets/images/logo/logo.jpg" class="h-10 w-10 sm:h-12 sm:w-12 rounded-full object-cover" alt="Galleta Café"><span class="font-semibold hidden sm:block"><?=e(setting('cafe_name','Galleta Café'))?></span></a>
<?php if($minimal): ?><a href="<?=$b?>/menu.php" class="ml-auto rounded-full border border-line px-3 py-2 text-sm hover:bg-soft">Back to menu</a><?php else: ?>
<button id="nav-toggle" type="button" class="mobile-menu-button md:hidden ml-auto p-2 rounded-full border border-line hover:bg-soft" aria-label="Toggle navigation" aria-expanded="false"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
<nav id="site-nav" class="hidden md:flex order-3 md:order-none w-full md:w-auto gap-1 overflow-x-auto flex-1 min-w-0 text-sm"><?php foreach($links as $l): ?><a class="nav-link px-2.5 sm:px-3 py-2 rounded-full whitespace-nowrap inline-flex items-center gap-1.5" href="<?=$b.'/'.$l[0]?>"><?=$l[1]?><?php if($l[0]==='cart.php' && ($cartCount=array_sum($_SESSION['cart']??[]))>0): ?><span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[11px] leading-none text-center font-semibold shrink-0"><?=$cartCount?></span><?php endif ?></a><?php endforeach ?></nav>
<div class="text-sm flex items-center gap-1.5 sm:gap-2 shrink-0"><?php if(!$u || $u['role']==='customer'): ?><a href="<?=$b?>/cart.php" aria-label="Shopping cart" class="nav-action cart-action relative inline-flex items-center gap-1.5 px-3 py-2 rounded-full border border-line hover:bg-soft"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6M10 20h.01M18 20h.01"/></svg><span class="hidden sm:inline">Cart</span><?php $cartCount=array_sum($_SESSION['cart']??[]); ?><span data-cart-count class="absolute -right-1 -top-2 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] leading-none font-semibold text-white <?=$cartCount?'':'hidden'?>"><?=$cartCount?></span></a><?php endif ?><?php if($u): ?><details class="relative"><summary class="nav-action list-none cursor-pointer px-3 py-2 rounded-full hover:bg-soft"><?=e($u['name'])?> <span class="text-mute">⌄</span></summary><div class="absolute right-0 mt-1 w-44 rounded-2xl bg-white border border-line shadow-lg p-2 z-30"><p class="px-3 py-2 text-xs text-mute"><?=e($u['role'])?></p><a class="block px-3 py-2 rounded-xl hover:bg-soft" href="<?=$b?>/logout.php">Logout</a></div></details><?php else: ?><a class="nav-action px-3 sm:px-4 py-2 rounded-full border border-line hover:bg-soft" href="<?=$b?>/login.php">Login</a><a class="nav-action px-3 sm:px-4 py-2 rounded-full bg-brand text-ink font-medium hover:bg-[#E58EB7]" href="<?=$b?>/register.php">Sign up</a><?php endif ?></div></div></header>
<?php endif ?></div></header>
<?php if(!$minimal): ?><script>var navToggle=document.getElementById('nav-toggle'),siteNav=document.getElementById('site-nav'),siteHeader=document.getElementById('site-header');if(navToggle&&siteNav){navToggle.addEventListener('click',function(){var open=!siteNav.classList.contains('nav-open');siteNav.classList.toggle('hidden',!open);siteNav.classList.toggle('nav-open',open);navToggle.classList.toggle('is-open',open);navToggle.setAttribute('aria-expanded',String(open));});}if(siteHeader){var updateHeader=function(){siteHeader.classList.toggle('is-scrolled',window.scrollY>8)};updateHeader();window.addEventListener('scroll',updateHeader,{passive:true});}</script><?php endif ?><script>document.addEventListener('submit',function(e){var f=e.target;if(!(f instanceof HTMLFormElement)||!f.action.endsWith('/cart.php')||(!f.querySelector('input[name="return"]')&&!f.querySelector('input[name="act"][value="add"]')))return;e.preventDefault();var submit=f.querySelector('button[type="submit"],button:not([type])');if(submit)submit.disabled=true;fetch(f.action,{method:'POST',body:new FormData(f),headers:{'X-Requested-With':'XMLHttpRequest'}}).then(function(r){if(!r.ok)throw new Error('Cart update failed');return r.json()}).then(function(data){document.querySelectorAll('[data-cart-qty="'+data.id+'"]').forEach(function(el){el.textContent=data.qty});document.querySelectorAll('[data-cart-count]').forEach(function(el){el.textContent=data.count;el.classList.toggle('hidden',!data.count)});document.querySelectorAll('[data-product-id="'+data.id+'"] button[aria-label^="Decrease"]').forEach(function(button){button.disabled=data.qty===0});}).catch(function(){window.location.reload()}).finally(function(){if(submit)submit.disabled=false});});</script>
<main class="max-w-6xl mx-auto px-3 sm:px-4 py-4 sm:py-6 min-w-0">
<?php if($f): ?><div class="mb-4 px-4 py-3 rounded-2xl text-sm <?=$f[1]==='ok'?'bg-soft':'bg-red-100 text-red-800'?>"><?=e($f[0])?></div><?php endif;
}
function foot(){ echo '</main></body></html>'; }
function card($c=''){ return 'bg-white border border-line rounded-3xl shadow-sm '.$c; }
const BTN='px-5 py-2.5 rounded-full bg-brand text-ink font-medium hover:opacity-90 transition';
const INP='w-full border border-line rounded-2xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-brand';

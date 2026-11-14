<?php
require_once __DIR__.'/../config/app.php';
function head($title,$minimal=false){ $u=user(); $f=flash(); $b=BASE;
$links=[['index.php','Home'],['menu.php','Menu'],['index.php#about','About Us'],['index.php#contact','Contact Us'],['orders.php','My Orders']];
if($u&&$u['role']!=='customer') $links=[];
if($u&&in_array($u['role'],['server','admin'])) $links[]=['server/board.php','Order Board'];
if($u&&$u['role']==='admin'){ $links[]=['admin/dashboard.php','Dashboard']; $links[]=['admin/products.php','Products']; }
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> · Galleta Café</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{brand:'#EFA6C8',soft:'#FCE4EF',light:'#FFF8FB',ink:'#352B32',mute:'#82747D',line:'#F2D5E3'}}}}</script></head>
<body class="bg-light text-ink min-h-screen overflow-x-hidden">
<header class="bg-white/95 backdrop-blur border-b border-line sticky top-0 z-20"><div class="max-w-6xl mx-auto px-3 sm:px-4 py-2 flex items-center gap-2 sm:gap-3 flex-wrap">
<a href="<?=$b?>/index.php" class="flex items-center gap-2 shrink-0"><img src="<?=$b?>/assets/images/logo/logo.jpg" class="h-10 w-10 sm:h-12 sm:w-12 rounded-full object-cover" alt="Galleta Café"><span class="font-semibold hidden sm:block"><?=e(setting('cafe_name','Galleta Café'))?></span></a>
<?php if($minimal): ?><a href="<?=$b?>/menu.php" class="ml-auto rounded-full border border-line px-3 py-2 text-sm hover:bg-soft">Back to menu</a><?php else: ?>
<button id="nav-toggle" type="button" class="md:hidden ml-auto p-2 rounded-full hover:bg-soft" aria-label="Toggle navigation" aria-expanded="false"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
<nav id="site-nav" class="hidden md:flex order-3 md:order-none w-full md:w-auto gap-1 overflow-x-auto flex-1 min-w-0 text-sm"><?php foreach($links as $l): ?><a class="px-2.5 sm:px-3 py-2 rounded-full whitespace-nowrap hover:bg-soft inline-flex items-center gap-1.5" href="<?=$b.'/'.$l[0]?>"><?=$l[1]?><?php if($l[0]==='cart.php' && ($cartCount=array_sum($_SESSION['cart']??[]))>0): ?><span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-[11px] leading-none text-center font-semibold shrink-0"><?=$cartCount?></span><?php endif ?></a><?php endforeach ?></nav>
<div class="text-sm flex items-center gap-1.5 sm:gap-2 shrink-0"><?php if(!$u || $u['role']==='customer'): ?><a href="<?=$b?>/cart.php" aria-label="Shopping cart" class="relative inline-flex items-center gap-1.5 px-3 py-2 rounded-full border border-line hover:bg-soft"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6M10 20h.01M18 20h.01"/></svg><span class="hidden sm:inline">Cart</span><?php if(($cartCount=array_sum($_SESSION['cart']??[]))>0): ?><span class="absolute -right-1 -top-2 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] leading-none font-semibold text-white"><?=$cartCount?></span><?php endif ?></a><?php endif ?><?php if($u): ?><details class="relative"><summary class="list-none cursor-pointer px-3 py-2 rounded-full hover:bg-soft"><?=e($u['name'])?> <span class="text-mute">⌄</span></summary><div class="absolute right-0 mt-1 w-44 rounded-2xl bg-white border border-line shadow-lg p-2 z-30"><p class="px-3 py-2 text-xs text-mute"><?=e($u['role'])?></p><a class="block px-3 py-2 rounded-xl hover:bg-soft" href="<?=$b?>/logout.php">Logout</a></div></details><?php else: ?><a class="px-3 sm:px-4 py-2 rounded-full border border-line" href="<?=$b?>/login.php">Login</a><a class="px-3 sm:px-4 py-2 rounded-full bg-brand text-ink font-medium" href="<?=$b?>/register.php">Sign up</a><?php endif ?></div></div></header>
<?php endif ?></div></header>
<?php if(!$minimal): ?><script>var navToggle=document.getElementById('nav-toggle');if(navToggle){navToggle.addEventListener('click',function(){var n=document.getElementById('site-nav');var open=!n.classList.contains('hidden');n.classList.toggle('hidden',open);this.setAttribute('aria-expanded',String(!open));});}</script><?php endif ?>
<main class="max-w-6xl mx-auto px-3 sm:px-4 py-4 sm:py-6 min-w-0">
<?php if($f): ?><div class="mb-4 px-4 py-3 rounded-2xl text-sm <?=$f[1]==='ok'?'bg-soft':'bg-red-100 text-red-800'?>"><?=e($f[0])?></div><?php endif;
}
function foot(){ echo '</main></body></html>'; }
function card($c=''){ return 'bg-white border border-line rounded-3xl shadow-sm '.$c; }
const BTN='px-5 py-2.5 rounded-full bg-brand text-ink font-medium hover:opacity-90 transition';
const INP='w-full border border-line rounded-2xl px-4 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-brand';

<?php require 'includes/layout.php';
if(user() && user()['role']!=='customer') go(user()['role']==='admin'?'admin/dashboard.php':'server/board.php');
$q=trim($_GET['q']??''); $c=(int)($_GET['c']??0);
$cats=db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$sql='SELECT p.*,c.name cat FROM products p JOIN categories c ON c.id=p.category_id WHERE 1'; $a=[];
if($q){$sql.=' AND (p.name LIKE ? OR p.description LIKE ?)';$a[]="%$q%";$a[]="%$q%";} if($c){$sql.=' AND p.category_id=?';$a[]=$c;}
$s=db()->prepare($sql.' ORDER BY c.sort_order,p.featured DESC,p.name'); $s->execute($a); $prods=$s->fetchAll();
$grouped=[]; foreach($prods as $p) $grouped[$p['cat']][]=$p;
$cart=$_SESSION['cart']??[];
head('Menu'); ?>
<style>
.menu-filter{position:relative;transition:background-color .25s ease,color .25s ease,border-color .25s ease,transform .25s ease}
.menu-filter::after{content:"";position:absolute;left:1rem;right:1rem;bottom:.28rem;height:2px;border-radius:999px;background:#EFA1C4;transform:scaleX(0);transition:transform .28s ease}
.menu-filter:hover,.menu-filter:focus-visible{background:#FCEAF2;border-color:#EFA1C4;transform:translateY(-1px)}
.menu-filter:hover::after,.menu-filter:focus-visible::after{transform:scaleX(1)}
.menu-card{transition:transform .3s ease,box-shadow .3s ease,border-color .3s ease}
.menu-card:hover{transform:translateY(-3px);border-color:#EFA1C4;box-shadow:0 14px 28px rgba(110,62,87,.1)}
.menu-search{transition:border-color .25s ease,box-shadow .25s ease}
.menu-search:focus{border-color:#EFA1C4;box-shadow:0 0 0 4px rgba(239,161,196,.2)}
@media (prefers-reduced-motion:reduce){
  .menu-filter,.menu-filter::after,.menu-card,.menu-search{transition:none}
  .menu-card:hover,.menu-filter:hover{transform:none}
}
</style>
<section class="rounded-3xl bg-gradient-to-br from-soft to-white p-6 sm:p-8 mb-6">
<p class="text-sm font-semibold uppercase tracking-[.2em] text-mute">Fresh from the café</p><h1 class="text-3xl sm:text-4xl font-semibold mt-2">Choose something you’ll love</h1>
<p class="text-mute mt-2">Keep browsing, or add your favorites and order as a guest or signed-in customer.</p>
<div class="flex flex-wrap gap-3 mt-5"><a href="#menu-items" class="<?=BTN?>">Order now</a></div>
</section>
<form class="flex flex-col sm:flex-row gap-2 mb-4"><input class="menu-search <?=INP?>" name="q" value="<?=e($q)?>" placeholder="Search drinks, meals, desserts…"><button class="<?=BTN?> hover:-translate-y-0.5 transition-transform">Search</button></form>
<div class="flex gap-2 overflow-x-auto pb-3 mb-6 text-sm"><a href="menu.php" class="menu-filter px-4 py-2 rounded-full whitespace-nowrap <?=!$c?'bg-brand':'bg-white border border-line'?>">All</a><?php foreach($cats as $k): ?><a href="?c=<?=$k['id']?>" class="menu-filter px-4 py-2 rounded-full whitespace-nowrap <?=$c==$k['id']?'bg-brand':'bg-white border border-line'?>"><?=e($k['name'])?></a><?php endforeach ?></div>
<div id="menu-items" class="scroll-mt-24"><?php if(!$prods): ?><p class="text-center text-mute py-16">No items match your search.</p><?php endif ?>
<?php foreach($grouped as $name=>$items): ?><section class="mb-8"><div class="flex items-end justify-between mb-3"><h2 class="text-2xl font-semibold"><?=e($name)?></h2><span class="text-sm text-mute"><?=count($items)?> item<?=count($items)!==1?'s':''?></span></div>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4"><?php foreach($items as $p): $qty=(int)($cart[$p['id']]??0); ?><div data-product-id="<?=$p['id']?>" class="<?=card('menu-card overflow-hidden flex flex-col')?>"><?=img($p)?><div class="p-3 flex flex-col flex-1"><div class="flex gap-1 flex-wrap text-[10px] mb-1"><?php if($p['featured']): ?><span class="bg-soft px-2 rounded-full">★ Featured</span><?php endif; if($p['best_seller']): ?><span class="bg-soft px-2 rounded-full">Best seller</span><?php endif ?></div><h3 class="font-medium leading-tight"><?=e($p['name'])?></h3><p class="text-xs text-mute flex-1 mt-1"><?=e($p['description'])?></p><div class="flex items-center justify-between gap-2 mt-3"><b><?=money($p['price'])?></b><?php if($p['available']): ?><div class="flex items-center gap-2"><div class="inline-flex items-center rounded-full border border-line bg-light p-1"><form method="post" action="cart.php"><?=csrf()?><input type="hidden" name="return" value="menu.php"><input type="hidden" name="act" value="dec"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="h-8 w-8 rounded-full text-lg leading-none hover:bg-white disabled:cursor-not-allowed disabled:opacity-40" aria-label="Decrease <?=e($p['name'])?> quantity" <?=$qty?'':'disabled'?>>−</button></form><span data-cart-qty="<?=$p['id']?>" class="w-7 text-center text-sm font-semibold"><?=$qty?></span><form method="post" action="cart.php"><?=csrf()?><input type="hidden" name="return" value="menu.php"><input type="hidden" name="act" value="inc"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="h-8 w-8 rounded-full text-lg leading-none hover:bg-white" aria-label="Increase <?=e($p['name'])?> quantity">+</button></form></div><form method="post" action="cart.php"><?=csrf()?><input type="hidden" name="act" value="add"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="<?=BTN?> !px-3 !py-1.5 text-sm">Add</button></form></div><?php else: ?><span class="text-xs text-mute">Sold out</span><?php endif ?></div></div></div><?php endforeach ?></div></section><?php endforeach ?></div>
<?php foot();

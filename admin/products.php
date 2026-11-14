<?php require '../includes/layout.php'; require_role(['admin']); $d=db();
if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf();
 if(isset($_POST['del'])){ try{ $d->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['del']]); flash('Product deleted.'); }catch(PDOException $x){ flash('Product has past orders; untick Available instead.','err'); } go('admin/products.php'); }
 $id=(int)$_POST['id']; $f=[trim($_POST['name']),(int)$_POST['category_id'],trim($_POST['description']),max(0,(float)$_POST['price']),isset($_POST['available'])?1:0,isset($_POST['featured'])?1:0,isset($_POST['best_seller'])?1:0];
 $image=null; if(!empty($_FILES['image']['tmp_name'])){ $m=(new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']); $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$m]??null;
  if(!$ext||$_FILES['image']['size']>2*1024*1024){ flash('Image must be JPG/PNG/WEBP under 2MB.','err'); go('admin/products.php'); }
  $image=bin2hex(random_bytes(8)).".$ext"; move_uploaded_file($_FILES['image']['tmp_name'],'../assets/uploads/'.$image); }
 if($id){ $d->prepare('UPDATE products SET name=?,category_id=?,description=?,price=?,available=?,featured=?,best_seller=?'.($image?',image=?':'').' WHERE id=?')->execute(array_merge($f,$image?[$image]:[],[$id])); }
 else { $d->prepare('INSERT INTO products(name,category_id,description,price,available,featured,best_seller,image) VALUES(?,?,?,?,?,?,?,?)')->execute(array_merge($f,[$image])); }
 log_act('product saved'); flash('Product saved.'); go('admin/products.php'); }
$cats=$d->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll(); $ps=$d->query('SELECT p.*,c.name cat FROM products p JOIN categories c ON c.id=p.category_id ORDER BY c.sort_order,p.name')->fetchAll();
$ed=['id'=>0,'name'=>'','category_id'=>$cats[0]['id'],'description'=>'','price'=>'','available'=>1,'featured'=>0,'best_seller'=>0];
if(isset($_GET['edit'])) foreach($ps as $p) if($p['id']==(int)$_GET['edit']) $ed=$p;
head('Products'); ?><h1 class="text-xl font-semibold mb-4">Products</h1>
<form method="post" enctype="multipart/form-data" class="<?=card('p-4 mb-4 grid sm:grid-cols-2 gap-3')?>"><?=csrf()?><input type="hidden" name="id" value="<?=$ed['id']?>">
<input class="<?=INP?>" name="name" value="<?=e($ed['name'])?>" placeholder="Name" required><select class="<?=INP?>" name="category_id"><?php foreach($cats as $c): ?><option value="<?=$c['id']?>" <?=$c['id']==$ed['category_id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach ?></select>
<input class="<?=INP?>" name="price" type="number" step="0.01" value="<?=e($ed['price'])?>" placeholder="Price ₱" required><input class="<?=INP?>" type="file" name="image" accept="image/*">
<textarea class="<?=INP?> sm:col-span-2" name="description" placeholder="Description"><?=e($ed['description'])?></textarea>
<div class="flex gap-4 text-sm flex-wrap items-center sm:col-span-2"><label><input type="checkbox" name="available" <?=$ed['available']?'checked':''?>> Available</label><label><input type="checkbox" name="featured" <?=$ed['featured']?'checked':''?>> Featured</label><label><input type="checkbox" name="best_seller" <?=$ed['best_seller']?'checked':''?>> Best seller</label>
<button class="<?=BTN?> ml-auto"><?=$ed['id']?'Update':'Add'?> product</button></div></form>
<div class="<?=card('overflow-x-auto')?>"><table class="w-full text-sm min-w-[36rem]"><?php foreach($ps as $p): ?><tr class="border-t border-line first:border-0"><td class="p-3"><?=e($p['name'])?><div class="text-xs text-mute"><?=e($p['cat'])?></div></td><td><?=money($p['price'])?></td><td><?=$p['available']?'Available':'Hidden'?></td>
<td class="text-right p-3 whitespace-nowrap"><a class="underline mr-3" href="?edit=<?=$p['id']?>">Edit</a><form method="post" class="inline" onsubmit="return confirm('Delete?')"><?=csrf()?><button name="del" value="<?=$p['id']?>" class="text-red-700 underline">Delete</button></form></td></tr><?php endforeach ?></table></div><?php foot();

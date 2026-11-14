<?php require '../includes/layout.php'; require_role(['admin']); $d=db(); $current=user();
if($_SERVER['REQUEST_METHOD']==='POST'){ check_csrf(); $action=$_POST['action']??'save'; $id=(int)($_POST['id']??0);
 if($action==='delete'){
  if($id===$current['id']){ flash('You cannot delete your own account.','err'); go('admin/employees.php'); }
  try{ $d->prepare("DELETE FROM users WHERE id=? AND role IN('server','admin')")->execute([$id]); flash('Employee deleted.'); }
  catch(PDOException $x){ flash('This employee has linked orders or activity records. Deactivate the account instead.','err'); }
  go('admin/employees.php');
 }
 $name=trim($_POST['name']??''); $email=filter_var(trim($_POST['email']??''),FILTER_VALIDATE_EMAIL); $phone=trim($_POST['phone']??'');
 $role=in_array($_POST['role']??'', ['server','admin'], true)?$_POST['role']:'server'; $password=$_POST['password']??''; $active=isset($_POST['active'])?1:0;
 if(!$name||!$email||($id===0&&strlen($password)<6)||($id>0&&$password!==''&&strlen($password)<6)){ flash($id?'Enter valid employee details.':'Enter a name, valid email and a password of 6+ characters.','err'); go($id?'admin/employees.php?edit='.$id:'admin/employees.php'); }
 try{
  if($id){ $params=[$name,$email,$phone,$role,$active]; $sql='UPDATE users SET name=?,email=?,phone=?,role=?,active=?'; if($password!==''){ $sql.=',password=?';$params[]=password_hash($password,PASSWORD_DEFAULT); } $sql.=' WHERE id=? AND role IN(\'server\',\'admin\')'; $params[]=$id; $d->prepare($sql)->execute($params); }
  else $d->prepare("INSERT INTO users(name,email,phone,password,role,active) VALUES(?,?,?,?,?,?)")->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT),$role,$active]);
  log_act($id?'employee updated':'employee created'); flash('Employee saved.');
 }catch(PDOException $x){ flash('That email is already in use.','err'); }
 go('admin/employees.php');
}
$employees=$d->query("SELECT id,name,email,phone,role,active,created_at FROM users WHERE role IN('server','admin') ORDER BY role,name")->fetchAll();
$ed=['id'=>0,'name'=>'','email'=>'','phone'=>'','role'=>'server','active'=>1]; if(isset($_GET['edit'])) foreach($employees as $employee) if($employee['id']==(int)$_GET['edit']) $ed=$employee;
head('Employees'); ?>
<div class="flex flex-wrap items-end justify-between gap-3 mb-4"><div><p class="text-xs font-semibold uppercase tracking-[.2em] text-mute">Staff management</p><h1 class="text-2xl font-semibold mt-1">Employees</h1><p class="text-sm text-mute mt-1">Create and manage the people handling café operations.</p></div><?php if($ed['id']): ?><a href="employees.php" class="text-sm text-[#C978A1] hover:underline">Add new employee</a><?php endif ?></div>
<form method="post" class="<?=card('p-4 mb-5 grid gap-3 sm:grid-cols-2')?>"><?=csrf()?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=$ed['id']?>">
<label class="text-sm">Name<input class="<?=INP?> mt-1" name="name" value="<?=e($ed['name'])?>" placeholder="Employee name" required></label><label class="text-sm">Email<input class="<?=INP?> mt-1" name="email" type="email" value="<?=e($ed['email'])?>" placeholder="employee@galleta.cafe" required></label>
<label class="text-sm">Phone <span class="text-mute">(optional)</span><input class="<?=INP?> mt-1" name="phone" value="<?=e($ed['phone'])?>" placeholder="09xx xxx xxxx"></label><label class="text-sm">Role<select class="<?=INP?> mt-1" name="role"><option value="server" <?=$ed['role']==='server'?'selected':''?>>Server</option><option value="admin" <?=$ed['role']==='admin'?'selected':''?>>Admin</option></select></label>
<label class="text-sm sm:col-span-2">Password <span class="text-mute">(<?= $ed['id']?'leave blank to keep current':'6+ characters' ?>)</span><input class="<?=INP?> mt-1" name="password" type="password" minlength="6" placeholder="<?=$ed['id']?'Leave blank to keep current':'Password'?>" <?=$ed['id']?'':'required'?>></label>
<div class="flex flex-wrap items-center gap-4 text-sm sm:col-span-2"><label><input type="checkbox" name="active" <?=$ed['active']?'checked':''?>> Active account</label><button class="<?=BTN?> sm:ml-auto"><?=$ed['id']?'Update':'Add'?> employee</button></div></form>
<div class="<?=card('overflow-x-auto')?>"><table class="w-full min-w-[42rem] text-sm"><thead><tr class="text-left text-xs uppercase tracking-wide text-mute"><th class="p-3">Employee</th><th>Role</th><th>Phone</th><th>Status</th><th class="p-3 text-right">Actions</th></tr></thead><tbody><?php foreach($employees as $employee): ?><tr class="border-t border-line"><td class="p-3"><b><?=e($employee['name'])?></b><div class="text-xs text-mute"><?=e($employee['email'])?></div></td><td><?=ucfirst(e($employee['role']))?></td><td><?=e($employee['phone']?:'—')?></td><td><span class="rounded-full px-2 py-1 text-xs <?=$employee['active']?'bg-soft':'bg-red-100 text-red-700'?>"><?=$employee['active']?'Active':'Inactive'?></span></td><td class="p-3 text-right whitespace-nowrap"><a class="mr-3 underline" href="?edit=<?=$employee['id']?>">Edit</a><?php if($employee['id']!==$current['id']): ?><form method="post" class="inline" onsubmit="return confirm('Delete this employee?')"><?=csrf()?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$employee['id']?>"><button class="text-red-700 underline">Delete</button></form><?php endif ?></td></tr><?php endforeach ?></tbody></table></div>
<?php foot();

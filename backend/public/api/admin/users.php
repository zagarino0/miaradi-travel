<?php
declare(strict_types=1);
require_once dirname(__DIR__, 3) . '/src/http.php';
header('Cache-Control: no-store');
api_method('GET','POST','PATCH');
$admin = api_require_role('super_admin');
$pdo = database_connection();
$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($method === 'GET') {
  $rows = $pdo->query('SELECT id, display_name, email, role, is_active, last_login_at, created_at FROM miaradi_admin_users ORDER BY created_at DESC, id DESC')->fetchAll();
  api_json(['data'=>['users'=>$rows]]);
}
api_require_csrf();
$input = api_read_json();
if ($method === 'POST') {
  $name=trim((string)($input['display_name']??''));
  $email=strtolower(trim((string)($input['email']??'')));
  $password=$input['password']??null;
  $role=(string)($input['role']??'editor');
  if ($name==='' || mb_strlen($name)>180) api_json(['error'=>['code'=>'invalid_name','message'=>'Nom complet obligatoire (180 caractères maximum).']],422);
  if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>254) api_json(['error'=>['code'=>'invalid_email','message'=>'Adresse e-mail invalide.']],422);
  if (!is_string($password) || strlen($password)<12 || strlen($password)>72) api_json(['error'=>['code'=>'invalid_password','message'=>'Le mot de passe doit contenir entre 12 et 72 octets.']],422);
  if (!in_array($role,['super_admin','manager','editor'],true)) api_json(['error'=>['code'=>'invalid_role','message'=>'Rôle invalide.']],422);
  try {
    $stmt=$pdo->prepare('INSERT INTO miaradi_admin_users (display_name,email,password_hash,role,is_active,auth_version) VALUES (:name,:email,:hash,:role,1,1)');
    $stmt->execute(['name'=>$name,'email'=>$email,'hash'=>password_hash($password,PASSWORD_DEFAULT),'role'=>$role]);
    api_json(['data'=>['id'=>(int)$pdo->lastInsertId(),'created'=>true]],201);
  } catch (PDOException $e) {
    if ($e->getCode()==='23000') api_json(['error'=>['code'=>'email_exists','message'=>'Cette adresse e-mail est déjà utilisée.']],409);
    error_log('[miaradi-api] User creation failed: '.$e->getMessage());
    api_json(['error'=>['code'=>'user_create_failed','message'=>'Impossible de créer le compte. Vérifiez les migrations.']],503);
  }
}
$id=filter_var($input['id']??null,FILTER_VALIDATE_INT);
if (!$id || $id===(int)$admin['id']) api_json(['error'=>['code'=>'invalid_user','message'=>'Compte invalide. Vous ne pouvez pas modifier votre propre compte ici.']],422);
$target=$pdo->prepare('SELECT id, role, is_active FROM miaradi_admin_users WHERE id=:id');
$target->execute(['id'=>$id]); $row=$target->fetch();
if (!$row) api_json(['error'=>['code'=>'not_found','message'=>'Compte introuvable.']],404);
if (array_key_exists('role',$input)) {
  if (!in_array($input['role'],['super_admin','manager','editor'],true)) api_json(['error'=>['code'=>'invalid_role','message'=>'Rôle invalide.']],422);
  if ($row['role']==='super_admin' && $input['role']!=='super_admin') {
    $count=(int)$pdo->query("SELECT COUNT(*) FROM miaradi_admin_users WHERE role='super_admin' AND is_active=1")->fetchColumn();
    if ($count<=1) api_json(['error'=>['code'=>'last_super_admin','message'=>'Conservez au moins un super administrateur actif.']],409);
  }
  $stmt=$pdo->prepare('UPDATE miaradi_admin_users SET role=:role, auth_version=auth_version+1 WHERE id=:id');
  $stmt->execute(['role'=>$input['role'],'id'=>$id]);
}
if (array_key_exists('is_active',$input)) {
  $active=(bool)$input['is_active'];
  if (!$active && $row['role']==='super_admin') {
    $count=(int)$pdo->query("SELECT COUNT(*) FROM miaradi_admin_users WHERE role='super_admin' AND is_active=1")->fetchColumn();
    if ($count<=1) api_json(['error'=>['code'=>'last_super_admin','message'=>'Conservez au moins un super administrateur actif.']],409);
  }
  $stmt=$pdo->prepare('UPDATE miaradi_admin_users SET is_active=:active, auth_version=auth_version+1 WHERE id=:id');
  $stmt->execute(['active'=>$active?1:0,'id'=>$id]);
}
api_json(['data'=>['updated'=>true]]);

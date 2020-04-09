<!DOCTYPE html>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body align="center">

<?php
// 定义变量并设置为空值
$start_dateErr = $stop_dateErr = $nameErr = $departmentErr = $contractErr = $positionErr = "";
$d1 = $d2 = $start_date = $stop_date = $name = $department = $contract = $position = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["start_date"])) {
     //$start_dateErr = "开始日期是必填的";
   } else {
     $start_date = test_input($_POST["start_date"]);
	 
	 
	 $d1 =  str_replace('T', ' ', $start_date) . ":00";
	 
   }
   
   if (empty($_POST["stop_date"])) {
     //$stop_dateErr = "结束日期是必填的";
   } else {
     $stop_date = test_input($_POST["stop_date"]);
	 
	 
	 $d2 =  str_replace('T', ' ', $stop_date) . ":00";
	 
   }  
   
	if (!empty($_POST["name"])) {
		$name = test_input($_POST["name"]);
		// 检查姓名是否全是汉字
		if (!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$name)) {
			$nameErr = "只允许汉字"; 
		}
	}
	
	if (!empty($_POST["department"])) {
		$department = test_input($_POST["department"]);
		// 检查部门是否全是汉字
		if (!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$department)) {
			$departmentErr = "只允许汉字"; 
		}
	}
	
	if (!empty($_POST["contract"])) {
		$contract = test_input($_POST["contract"]);
	}
	
	if (!empty($_POST["position"])) {
		$position = test_input($_POST["position"]);
		// 检查部门是否全是汉字
		if (!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$position)) {
			$positionErr = "只允许汉字"; 
		}
	}
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<a href="/index.php">数据查询</a>
<a href="/statis.php">数据统计</a>
<h1>人员实名管理</h1>
<h2>数据统计</h2><br>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   开始日期：<input type="datetime-local" name="start_date" value="<?php echo $start_date;?>">
   <span class="error"> <?php echo $start_dateErr;?></span>
   <!-- <?php echo $start_date;?> -->
   <br><br>
   结束日期：<input type="datetime-local" name="stop_date" value="<?php echo $stop_date;?>">
   <span class="error"> <?php echo $stop_dateErr;?></span>
   <!-- <?php echo $stop_date;?> -->
   <br><br> 
   姓名：<input type="text" name="name" value="<?php echo $name;?>">
   <span class="error"> <?php echo $nameErr;?></span>
   <br><br>
   单位：<input type="text" name="department" value="<?php echo $department;?>">
   <span class="error"> <?php echo $departmentErr;?></span>
   <br><br>
   合同号：<input type="text" name="contract" value="<?php echo $contract;?>">
   <span class="error"> <?php echo $contractErr;?></span>
   <br><br>
   轨迹：<input type="text" name="position" value="<?php echo $position;?>">
   <span class="error"> <?php echo $positionErr;?></span>
   <br><br>
   <input type="submit" name="submit" value="统计"> 
</form>

<br>

<?php
	if (isset($_GET["page"])) { $page_ys = $_GET["page"]; } else { $page_ys = "1"; }
	
	class MyDB extends SQLite3
   {
      function __construct()
      {
         //$this->open('htdocs\faces.db');
		 $this->open('D:\lighttpd\htdocs\data.db');
      }
   }
   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } 
                                                
	 	
	$condition1 = " 1 = 1 ";
	$condition2 = " 1 = 1 ";
	$condition3 = " 1 = 1 ";
	$condition4 = " 1 = 1 ";
	$condition5 = " 1 = 1 ";
	$condition6 = " 1 = 1 ";
	
	if(!empty($d1))
	{
		$condition1 = "出现时间 >= '$d1'";
	}
	
	if(!empty($d2))
	{
		$condition2 = "出现时间 <= '$d2'";
	}
	
	if(!empty($name))
	{
		$condition3 = "姓名 LIKE '%{$name}%'";
	}
	
	if(!empty($department))
	{
		$condition4 = "单位 LIKE '%{$department}%'";
	}
	
	if(!empty($position))
	{
		$condition5 = "轨迹 = '{$position}'";
	}
	
	if(!empty($contract))
	{
		$condition6 = "合同号 LIKE '%{$contract}%'";
	}
	
	$sql = "select COUNT(身份证号) as 人数 from personnel WHERE {$condition1} AND {$condition2} AND {$condition3} AND {$condition4} AND {$condition5} AND {$condition6}";	
    $ret = $db->query($sql);   
    $row = $ret->fetchArray();   
    $totalrecord = $row[0];   //记录总数  	
	
	$sql = "select *, COUNT(身份证号) as 人数 from personnel WHERE {$condition1} AND {$condition2} AND {$condition3} AND {$condition4} AND {$condition5}  AND {$condition6}GROUP BY 合同号 ORDER BY 合同号";


   $ret = $db->query($sql);
   
	echo '<table width="600" align="center">';
	echo '<tr align="center">';
	echo "<td>单位</td> ";
	echo "<td>合同号</td> ";
	echo "<td>人数</td> ";
	echo "</tr>";	
   
	while($row = $ret->fetchArray(SQLITE3_ASSOC) )
	{
	   echo '<tr align="center">';
	   echo "<td>" . $row['简称'] . "</td>";
	   echo "<td>" . $row['合同号'] . "</td>";
	   echo "<td>" . $row['人数'] . "</td>";
	   echo "</tr>"; 
	   
	}
	echo"</table>";
	
   $db->close();

	echo "<br>";
	echo "总人数：&nbsp;";
	echo "$totalrecord";  
	echo "<br>";

?>

<br><br>
<a>83892369@qq.com</a>

</body>
</html>






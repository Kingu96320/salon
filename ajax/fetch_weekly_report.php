<?php
	include_once '../includes/db_include.php'; 
	$months = array(1=>"January", 2=>"February", 3=>"March", 4=>"April", 5=>"May", 6=>"June", 7=>"July", 8=>"August", 9=>"September", 10=>"October", 11=>"November", 12=>"December");
	$week = array(1=>"Sunday", 2=>"Monday", 3=>"Tuesday", 4=>"Wednesday", 5=>"Thursday", 6=>"Friday", 7=>"Saturday")
?>
<table class="table table-striped table-bordered">
	<?php
		echo "<tr><td></td>";
		if(date('Y') == $_GET['month']){
			
			for($i=1; $i<= date('m'); $i++){
				echo "<td><strong>".$months[$i]."</strong></td>";
			}
			echo "</tr>";
			for($j=1; $j<=7; $j++){
				echo "<tr><td><strong>".$week[$j]."</strong></td>";
				for($i=1; $i<= date('m'); $i++){					
					$clients = query("SELECT count(t) as t from( SELECT count(*) as t from invoice_".$_SESSION['branch_id']." i inner join client c on i.client = c.id where MONTH(i.doa) ='".$i."' and YEAR(i.doa) = ".$_GET['month']." and DAYOFWEEK(i.doa) = '".$j."' UNION SELECT count(distinct(a.client)) as t from app_invoice_".$_SESSION['branch_id']." a  inner join client c on a.client = c.id where MONTH(a.doa) ='".$i."'  and YEAR(a.doa) = ".$_GET['month']." and DAYOFWEEK(a.doa) = '".$j."') as t" , [], $conn)[0]['t'];
					echo "<td>".($clients - 1)."</td>";
				}
				echo "</tr>";
			}
		}
		else{
			for($i=1; $i<= 12; $i++){
				echo "<td><strong>".$months[$i]."</strong></td>";
			}
			echo "</tr>";	
			for($j=1; $j<=7; $j++){
				echo "<tr><td><strong>".$week[$j]."</strong></td>";						
				for($i=1; $i<= 12; $i++){							
					$clients = query("SELECT count(t) as t from( SELECT count(*) as t from invoice_".$_SESSION['branch_id']." i inner join client c on i.client = c.id where MONTH(i.doa) ='".$i."' and YEAR(i.doa) = ".$_GET['month']." and DAYOFWEEK(i.doa) = '".$j."' UNION SELECT count(distinct(a.client)) as t from app_invoice_".$_SESSION['branch_id']." a  inner join client c on a.client = c.id where MONTH(a.doa) ='".$i."'  and YEAR(a.doa) = ".$_GET['month']." and DAYOFWEEK(a.doa) = '".$j."') as t" , [], $conn)[0]['t'];

					echo "<td>".($clients - 1)."</td>";
				}
				echo "</tr>";						
			}
		}
	?>
</table>

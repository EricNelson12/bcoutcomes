@extends('default')
@section('body')
<div class="container">
	<div>
	   
		<h2>Query History</h2>
	   
	
		<table>
			<?php foreach ($test as $q): ?>
				<?php echo "<tr><td>"; ?>
			    <?php echo link_to_route('getquery',$q->date_of_query, array("query_id"=>$q->id)); ?>
			    <?php echo "</td></tr>"; ?>
			<?php endforeach; ?>
		</table>
	</div>

</div>


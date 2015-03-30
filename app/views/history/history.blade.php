@extends('default')
@section('body')
<div class="container">
	<div>
	   
		<h2>Query History</h2>
	   
		<table>
			<tr>
				<td style = "vertical-align: top;">
					<h3>Named Queries </h3>
					<table>
						<?php foreach ($test1 as $q): ?>
							<?php echo "<tr><td>"; ?>
						    <?php echo link_to_route('getquery',$q->query_name." ".$q->date_of_query, array("query_id"=>$q->id)); ?>
						    <?php echo "</td></tr>"; ?>
						<?php endforeach; ?>
					</table>
				
					<h3>Other Queries </h3>
					<table>
						<?php foreach ($test as $q): ?>
							<?php echo "<tr><td>"; ?>
						    <?php echo link_to_route('getquery',$q->date_of_query, array("query_id"=>$q->id)); ?>
						    <?php echo "</td></tr>"; ?>
						<?php endforeach; ?>
					</table>
				</td>
			</tr>
		</table>
	</div>

</div>


<?php include'class.php'; ?>
<article class="module width_full">
			<header><h3>LSUK Stats</h3></header>
			<div class="module_content">
				<article class="stats_graph">
                

             <div id="graph"><?php include'graph.php'; ?></div>
					
				</article>
				
				<!--<article class="stats_overview">-->
				<!--	<div class="overview_today">-->
				<!--		<p class="overview_day">Total cases</p>-->
				<!--		<p class="overview_count"><?php //echo $intrTotal=$statsObj->mainStat('interpreter');?></p>-->
				<!--		<p class="overview_type">Interpreter</p>-->
				<!--		<p class="overview_count"><?php //echo $telepTotal=$statsObj->mainStat('telephone');?></p>-->
				<!--		<p class="overview_type">Telephone</p>-->
				<!--	</div>-->
				<!--	<div class="overview_previous">-->
				<!--		<p class="overview_day">Registered</p>-->
				<!--		<p class="overview_count"><?php //echo $transTotal=$statsObj->mainStat('translation');?></p>-->
				<!--		<p class="overview_type">Translation</p>-->
				<!--	  <p class="overview_count"><?php //echo $intrTotal + $telepTotal + $transTotal; ?></p>-->
				<!--		<p class="overview_type">Grand Total</p>-->
				<!--	</div>-->
				<!--</article>-->
				<div class="clear"></div>
			</div>
		</article>
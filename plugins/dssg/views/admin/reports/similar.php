<?php if ( ! empty($reports)): ?>
<div class="similar-items">
	<span class="similar-reports-header">
	<?php 
		echo html::anchor("#", Kohana::lang('dssg.ui.similar_reports'), 
			array('id' => 'toggle-similar-items', 'title' => Kohana::lang('dssg.ui.show_similar_reports'))); 
	?>
	</span>
	<div class="item-listing">
		<?php foreach ($reports as $report):?>
		<div class="row similar">
			<h4><?php echo $report['title']; ?></h4>
			<span>
				<?php echo text::limit_chars($report['description'], 80, '...', TRUE); ?>
				<?php 
					echo html::anchor("admin/reports/edit/".$report['origin_report_id'], 
					Kohana::lang('ui_main.more'), array('class' => 'more'));
				?>
			</span>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#toggle-similar-items").toggle(
		// Show listing
		function(e){
			$(".similar-items .item-listing").slideDown();
		},
		// Hide listing
		function(e){
			$(".similar-items .item-listing").slideUp();
		});
});
</script>
<?php endif; ?>
<?php if ( ! empty($locations) OR ! empty($tags)): ?>
<div class="report-meta">
	<?php if ( ! empty($tags)): ?>
	<div class="report-meta-section">
		<span class="section-label">
			<?php echo Kohana::lang('dssg.ui.tags'); ?>
		</span>
		<?php foreach ($tags as $tag): ?>
			<span class="report-tag"><?php echo $tag; ?></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<?php if ( ! empty($locations)): ?>
	<div class="report-meta-section">
		<span class="section-label">
			<?php echo Kohana::lang('dssg.ui.location_mentions'); ?>
		</span>
		<?php foreach ($locations as $location): ?>
		<span class="r_location"><?php echo $location; ?></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
</div>
<?php endif; ?>
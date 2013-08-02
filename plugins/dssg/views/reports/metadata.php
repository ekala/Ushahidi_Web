<?php if ( ! empty($locations) OR ! empty($tags)  OR ! empty($language)): ?>
<div class="report-meta">
	
	<?php if (! empty($language)): ?>
	<div class="report-meta-section">
		<?php echo Kohana::lang('dssg.ui.report_language'); ?>
		<span class="report-language">
			<?php echo locale_get_display_language($language['language'], 'en'); ?>
		</span>
	</div>
	<?php endif; ?>

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
<?php if ( ! empty($language)): ?>
<div class="row report-meta">
	<?php echo Kohana::lang('dssg.ui.report_language')?>
	<span class="report-language">
		<?php echo locale_get_display_language($language['language'], 'en'); ?>
	</span>
</div>
<?php endif; ?>

<?php if ( ! empty($tags)): ?>
<div class="row report-meta">
	<h4><?php echo Kohana::lang('dssg.ui.tags'); ?></h4>
	<?php foreach ($tags as $tag): ?>
		<span class="report-tag"><?php echo $tag; ?></span>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ( ! empty($locations)): ?>
	<div class="row report-meta">
		<h4><?php echo Kohana::lang('dssg.ui.location_mentions'); ?></h4>
		<?php foreach ($locations as $location): ?>
			<span class="r_location"><?php echo $location; ?></span>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
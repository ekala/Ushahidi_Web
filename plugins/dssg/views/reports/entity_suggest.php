<div class="report-meta">
	<!-- Display the entities according to type -->
	<?php if ( ! empty($entities)): ?>
		<h5><?php Kohana::lang('dssg.ui.useful_info'); ?></h5>

		<?php if(array_has_key('gpe', $entities)): ?>
			<!-- Show location entities -->
			<?php echo Kohana::lang('dssg.ui.location_mentions'); ?>
			<span class="r_location"></span>
		<?php endif;?>

		<?php if (array_has_key('person', $entities)): ?>
			<!-- Show person entities -->
			<?php echo Kohana::lang('dssg.ui.person_mentions'); ?>
		<?php endif; ?>
	
		<?php if (array_has_key('organization', $entities)): ?>
			<?php echo Kohana::lang('dssg.ui.organization_mentions'); ?>
		<?php endif; ?>
	<?php endif;?>
</div>
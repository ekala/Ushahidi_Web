<div class="report-meta">
	<!-- Display the entities according to type -->
	<?php if ( ! empty($entities)): ?>
		<?php if(array_key_exists('gpe', $entities)): ?>
			<!-- Show location entities -->
			<div class="report-meta-section">
				<span class="section-label">
					<?php echo Kohana::lang('dssg.ui.location_mentions'); ?>
				</span>
				<?php foreach ($entities['gpe'] as $location): ?>
					<span class="r_location"><?php echo $location; ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif;?>

		<?php if (array_key_exists('person', $entities)): ?>
			<!-- Show person entities -->
			<div class="report-meta-section">
				<span class="section-label">
					<?php echo Kohana::lang('dssg.ui.people_mentions'); ?>
				</span>
				<?php foreach ($entities['person'] as $person): ?>
					<span><?php echo $person; ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	
		<?php if (array_key_exists('organization', $entities)): ?>
			<div class="report-meta-section">
				<span class="section-label">
					<?php echo Kohana::lang('dssg.ui.organization_mentions'); ?>
				</span>
				<?php foreach ($entities['organization'] as $organization): ?>
					<span><?php echo $organization; ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php endif;?>
</div>
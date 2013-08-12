<table style="width: 630px;" class="my_table">
	<tr>
		<td style="width:60px">
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step'); ?>1:</span>
		</td>
		<td>
			<h4 class="fix"><?php echo Kohana::lang('dssg.ui.install'); ?>. </h4>
			<p>
				<?php echo Kohana::lang('dssg.ui.description'); ?>
			</p>
		</td>
	</tr>
	<tr>
		<td style="width:60px;">
			<span class="big_blue_span"><?php echo Kohana::lang('ui_main.step'); ?>2:</span>
		</td>
		<td>
			<h4 class="fix"><?php echo Kohana::lang('dssg.ui.configure'); ?></h4>
			<div class="row">
				<h4><?php echo Kohana::lang('dssg.ui.api_url'); ?></h4>
				<?php 
					$attributes = array(
						'name' => 'dssg_api_url',
						'class' => 'text long2',
						'placeholder' => Kohana::lang('dssg.ui.example_url')
					);
					echo form::input($attributes, $form['dssg_api_url']);
				?>
			</div>
		</td>
	</tr>
</table>

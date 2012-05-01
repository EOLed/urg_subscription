<div class="subscriptions form">
<?php echo $this->Form->create('Subscription');?>
	<fieldset>
 		<legend><?php echo __('Add Subscription'); ?></legend>
	<?php
		echo $this->Form->input('group_id');
		echo $this->Form->input('email');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Subscriptions'), array('action' => 'index'));?></li>
	</ul>
</div>
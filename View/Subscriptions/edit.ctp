<div class="subscriptions form">
<?php echo $this->Form->create('Subscription');?>
	<fieldset>
 		<legend><?php echo __('Edit Subscription'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('group_id');
		echo $this->Form->input('email');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('Subscription.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('Subscription.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Subscriptions'), array('action' => 'index'));?></li>
	</ul>
</div>
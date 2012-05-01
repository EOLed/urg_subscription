<div class="subscriptions view">
<h2><?php echo __('Subscription');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $subscription['Subscription']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Group Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $subscription['Subscription']['group_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Email'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $subscription['Subscription']['email']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $subscription['Subscription']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $subscription['Subscription']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Subscription'), array('action' => 'edit', $subscription['Subscription']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Subscription'), array('action' => 'delete', $subscription['Subscription']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $subscription['Subscription']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Subscriptions'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Subscription'), array('action' => 'add')); ?> </li>
	</ul>
</div>

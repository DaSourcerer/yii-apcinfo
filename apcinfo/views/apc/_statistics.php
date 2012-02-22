<div class="column span-10">
	<?php $this->beginWidget('zii.widgets.CPortlet', array(
		'title'=>'General Information',
	)); ?>
	<?php $this->widget('zii.widgets.CDetailView', array(
		'data'=>$info,
		'formatter'=>$formatter,
		'attributes'=>array(
			'apc_version:text:APC Version',
			'php_version:text:PHP Version',
			'host:text:APC Host',
			'server_software:text',
			'shared_memory:text',
			'memory_type:text',
			'start_time:datetime',
			'start_time:duration:Uptime',
			'file_upload_support:boolean',
		),
	)); ?>
	<?php $this->endWidget(); ?>
</div>

<div class="column span-6">
	<?php $this->beginWidget('zii.widgets.CPortlet', array(
		'title'=>'File Cache Information',
	)); ?>
	<?php $this->widget('zii.widgets.CDetailView', array(
		'data'=>$fileCache,
		'formatter'=>$formatter,
		'attributes'=>array(
			'cached_files:number',
			'cached_file_size:datasize',
			'hits:number',
			'misses:number',
			'request_rate:rate',
			'hit_rate:rate',
			'miss_rate:rate',
			'insert_rate:rate',
			'cache_full_count:number:Cache full count',
		),
	)); ?>
	<?php $this->endWidget(); ?>
</div>

<div class="column span-6 last">
	<?php $this->beginWidget('zii.widgets.CPortlet', array(
		'title'=>'User Cache Information',
	)); ?>
	<?php $this->widget('zii.widgets.CDetailView', array(
		'data'=>$userCache,
		'formatter'=>$formatter,
		'attributes'=>array(
			'cached_entries:number',
			'cached_entry_size:datasize',
			'hits:number',
			'misses:number',
			'request_rate:rate',
			'hit_rate:rate',
			'miss_rate:rate',
			'insert_rate:rate',
			'cache_full_count:number:Cache full count',
		),
	)); ?>
	<?php $this->endWidget(); ?>
</div>

<div class="clear"></div>

<div class="column span-24 last">
	<?php $this->beginWidget('zii.widgets.CPortlet', array(
		'title'=>'Runtime Settings'
	)); ?>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>new CArrayDataProvider($iniSettings, array(
			'sort'=>array(
				'attributes'=>array(
					'id',
					'access',
				),
				'defaultOrder'=>array(
					'id'=>false,
				),
			),
			'pagination'=>array(
				'pageSize'=>15,
			),
		)),
		'formatter'=>$formatter,
		'columns'=>array(
			array(
				'name'=>'id',
				'type'=>'raw',
				'value'=>'CHtml::link($data["id"], "http://php.net/manual/apc.configuration.php#ini." . str_replace("_", "-", $data["id"]), array(
					"target"=>"_new",
				))',
			),
			array(
				'name'=>'local',
				'header'=>'Local Setting'
			),
			array(
				'name'=>'global',
				'header'=>'Global Setting'
			),
			'access:access:Access Level',
		),
	)); ?>
	<?php $this->endWidget(); ?>
</div>

<div class="clear"></div>
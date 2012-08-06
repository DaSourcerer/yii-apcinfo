<?php $this->widget('system.web.widgets.CTabView', array(
	'tabs'=>array(
		'stats'=>array(
			'title'=>'Host Stats',
			'view'=>'_statistics',
			'data'=>array(
				'formatter'=>$formatter,
				'fileCache'=>$fileCache,
				'userCache'=>$userCache,
				'iniSettings'=>$iniSettings,
				'info'=>$info,
				'blocks'=>$blocks,
				'fragInfo'=>$fragInfo,
			),
		),
		'opcode'=>array(
			'title'=>'Opcode Cache',
			'url'=>$this->createUrl('opcode/index'),
		),
		'user'=>array(
			'title'=>'User Cache',
			'url'=>$this->createUrl('user/index'),
		),
	),
)); ?>
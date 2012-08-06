<?php
class OpcodeController extends CController
{
	public function actionIndex()
	{
		$formatter=new CustomFormatter;
		$opcodeInfo=apc_cache_info('opcode');

		$this->render('index', array(
			'formatter'=>$formatter,
			'opcodeList'=>$opcodeInfo['cache_list'],	
		));
	}
	
	public function actionClear($outdated=false)
	{
		if($outdated)
		{
			$cache=apc_cache_info('opcode');
			$files=array();
			foreach($cache['cache_list'] as $file)
			{
				if(filemtime($file['filename']) > $file['mtime'])
					$files[]=$file['filename'];
			}
			$result=apc_delete_file($files);
			if(empty($result))
				Yii::app()->user->setFlash('success', count($files) . ' outdated cache files cleared.');
			else
				Yii::app()->user->setFlash('error', 'Puriging the following files from the cache has failed: ' . implode(', ', $result));
		}
		else
		{
			if(apc_clear_cache())
				Yii::app()->user->setFlash('success', 'Opcode cache cleared.');
			else
				Yii::app()->user->setFlash('error', 'Clearing the opcode cache failed!');
		}
		$this->redirect(array('index'));
	}
}
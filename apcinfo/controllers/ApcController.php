<?php
class ApcController extends Controller
{
	public function init()
	{
		if(!extension_loaded('apc'))
			throw new CException('PHP\'s APC extension must be loaded!');
		parent::init();
	}
	
	public function actionIndex()
	{
		$formatter = new CustomFormatter;
		$userCacheInfo=apc_cache_info('user');
		$fileCacheInfo=apc_cache_info('opcode');
		$mem=apc_sma_info();
		
		$iniSettings=array();
		foreach(ini_get_all('apc') as $id=>$settings)
		{
			$iniSettings[]=array(
				'id'=>$id,
				'local'=>$settings['local_value'],
				'global'=>$settings['global_value'],
				'access'=>$settings['access'],
			);
		}
		
		$info=array(
			'apc_version'=>phpversion('apc'),
			'php_version'=>phpversion(),
			'host'=>$_SERVER['SERVER_NAME'] . ' (' . php_uname('n') . ' [' . $_SERVER['SERVER_ADDR'] . '])',
			'server_software'=>$_SERVER['SERVER_SOFTWARE'],
			'shared_memory'=>"{$mem['num_seg']} Segment(s) with {$formatter->formatDatasize($mem['seg_size'])}",
			'memory_type'=>"{$fileCacheInfo['memory_type']} with {$fileCacheInfo['locking_type']}",
			'start_time'=>$fileCacheInfo['start_time'],
			'file_upload_support'=>$fileCacheInfo['file_upload_progress'],
		);
		
		$delta=($_SERVER['REQUEST_TIME'] - $fileCacheInfo['start_time']);
		if($delta===0)
			$delta=1; //prevent div by zero
		
		$fileCache=array(
			'cached_files'=>$fileCacheInfo['num_entries'],
			'cached_file_size'=>$fileCacheInfo['mem_size'],
			'hits'=>$fileCacheInfo['num_hits'],
			'misses'=>$fileCacheInfo['num_misses'],
			'request_rate'=>($fileCacheInfo['num_hits'] + $fileCacheInfo['num_misses']) / $delta,
			'hit_rate'=>$fileCacheInfo['num_hits'] / $delta,
			'miss_rate'=>$fileCacheInfo['num_misses'] / $delta,
			'insert_rate'=>$fileCacheInfo['num_inserts'] / $delta,
			'cache_full_count'=>$fileCacheInfo['expunges'],
		);
		
		$delta=($_SERVER['REQUEST_TIME'] - $userCacheInfo['start_time']);
		if($delta===0)
			$delta=1; //prevent div by zero
		
		$userCache=array(
				'cached_entries'=>$userCacheInfo['num_entries'],
				'cached_entry_size'=>$userCacheInfo['mem_size'],
				'hits'=>$userCacheInfo['num_hits'],
				'misses'=>$userCacheInfo['num_misses'],
				'request_rate'=>($userCacheInfo['num_hits'] + $userCacheInfo['num_misses']) / $delta,
				'hit_rate'=>$userCacheInfo['num_hits'] / $delta,
				'miss_rate'=>$userCacheInfo['num_misses'] / $delta,
				'insert_rate'=>$userCacheInfo['num_inserts'] / $delta,
				'cache_full_count'=>$userCacheInfo['expunges'],
		);
		
		$this->render('index', array(
			'formatter'=>$formatter,
			'fileCache'=>$fileCache,
			'fileCacheList'=>$fileCacheInfo['cache_list'],
			'userCache'=>$userCache,
			'userCacheList'=>$userCacheInfo['cache_list'],
			'iniSettings'=>$iniSettings,
			'info'=>$info,
		));
	}
	
	public function actionClearFileCache()
	{
		if(apc_clear_cache())
			Yii::app()->user->setFlash('success', 'File cache cleared.');
		else
			Yii::app()->user->setFlash('error', 'Clearing the file cache failed!');
		$this->redirect(array('index', '#'=>'fileCache'));
	}
	
	public function actionClearOutdated()
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
		$this->redirect(array('index', '#'=>'fileCache'));
	}
	
	public function actionClearUserCache()
	{
		if(apc_clear_cache('user'))
			Yii::app()->user->setFlash('success', 'User cache cleared.');
		else
			Yii::app()->user->setFlash('error', 'Clearing the user cache failed!');
		$this->redirect(array('index', '#'=>'userCache'));
	}
	
	public function actionDelete($id)
	{
		apc_delete($id);
	}
	
	public function actionView($id)
	{
		if(!Yii::app()->request->isAjaxRequest)
			throw new CHttpException(400, 'Invalid request. Please do not repeat this.');
		
		$success=false;
		$data=apc_fetch($id, $success);
		
		if(!$success)
			throw new CHttpException(404, "No dataset for key {$id} found!");
		
		CVarDumper::dump($data, 10, true);
		Yii::app()->end();
	}
}
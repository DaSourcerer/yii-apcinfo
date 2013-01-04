<?php
class DefaultController extends Controller
{
	public function actionIndex()
	{
		$formatter=new CustomFormatter;
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
		
		$position=0;
		$freesegs=0;
		$fragsize=0;
		$freetotal=0;
		$blocks=array();
		foreach($mem['block_lists'] as $i=>$list)
		{
			uasort($list,array($this, 'blockSort'));
			foreach($list as $block)
			{
				if($position != $block['offset']) {
					$blocks[]=array(
						'segment'=>$i,
						'free'=>false,
						'offset'=>$position,
						'size'=>($block['offset']-$position),
						'percent'=>100*($block['offset']-$position)/$mem['seg_size'],
					);
				}
    			$blocks[]=array(
      				'segment'=>$i,
      				'free'=>true,
      				'offset'=>$block['offset'],
      				'size'=>$block['size'],
      				'percent'=>(100*$block['size'])/$mem['seg_size'],
    			);
				$position=$block['offset']+$block['size'];
				
				if($block['size'] < 5*1024*024)
					$fragsize+=$block['size'];
				$freetotal+=$block['size'];
			}
			$freesegs+=count($list);
		}
		if($position < $mem['seg_size'])
			$blocks[]=array(
				'segment'=>$i,
				'free'=>false,
				'offset'=>$position,
				'size'=>$mem['seg_size']-$position,
				'percent'=>100*($mem['seg_size']-$position)/$mem['seg_size'],
			);
			
		$this->render('index', array(
			'formatter'=>$formatter,
			'fileCache'=>$fileCache,
			'userCache'=>$userCache,
			'iniSettings'=>$iniSettings,
			'info'=>$info,
			'blocks'=>$blocks,
			'fragInfo'=>array(
				'freesegs'=>$freesegs,
				'fragsize'=>$fragsize,
				'freetotal'=>$freetotal,
			),
		));
	}
	
	private function blockSort($a, $b)
	{
		if($a['offset'] > $b['offset'])
			return 1;
		return -1;
	}
}

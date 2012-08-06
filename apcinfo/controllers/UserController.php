<?php
class UserController extends Controller
{
	public function actionIndex()
	{
		$formatter=new CustomFormatter;
		$userInfo=apc_cache_info('user');

		$this->render('index', array(
			'formatter'=>$formatter,
			'userList'=>$userInfo['cache_list'],	
		));
	}
	
	public function actionClear()
	{
		if(apc_clear_cache('user'))
			Yii::app()->user->setFlash('success', 'User cache cleared.');
		else
			Yii::app()->user->setFlash('error', 'Clearing the user cache failed!');
		$this->redirect(array('index'));
	}
	
	public function actionDelete($key)
	{
		apc_delete($key);
	}
	
	public function actionView($key)
	{
		if(!Yii::app()->request->isAjaxRequest)
			throw new CHttpException(400, 'Invalid request. Please do not repeat this.');
	
		$success=false;
		$data=apc_fetch($key, $success);
	
		if(!$success)
			throw new CHttpException(404, "No dataset for key {$key} found!");
	
		if(($_data=@unserialize($data))!==false)
			$data=$_data;
		elseif(function_exists('igbinary_unserialize') && ($_data=@igbinary_unserialize($data))!==false)
			$data=$_data;
	
		CVarDumper::dump($data, 10, true);
		Yii::app()->end();
	}
}
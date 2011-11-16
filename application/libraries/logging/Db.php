<?php

class Log_Route_Db extends Log_Route
{
	/**
	 * 要使用的数据库连接名，在config/database.php中配置
	 * 
	 * @var string
	 */
	public $log_db_name='default';
	/**
	 * 要保存到的数据表
	 * 
	 * @var string
	 */
	public $log_table='logs';
	
	private $_db;
	
	/**
	 * @see Log_Route::init()
	 */
	function init()
	{
		if(isset($GLOBALS['CI']))
			$this->_db=$GLOBALS['CI']->load->database($this->log_db_name,true);
	}
	
	/**
	 * 将日志保存到文件
	 * 
	 * @param array $logs
	 * @see Log_Route::processLogs()
	 */
	protected function processLogs($logs)
	{
		// 避免数据库未初始化
		if(!$this->_db)
			return;
		
		// 将日志保存到数据表中，当然表要存在
		foreach($logs as $log)
			$this->_db->insert($this->log_table,array(
				'level'=>$log[0],
				'message'=>$log[1],
				'time'=>@date($this->log_date_format,$log[2]),
			));
	}
}
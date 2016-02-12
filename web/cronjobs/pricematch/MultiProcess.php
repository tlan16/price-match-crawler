<?php

class Multiprocess {
	private $maxChildProcesses = 5;
	private $sizeOfParentData= 0;
	private $sizeOfEachChildData = 0;
	private $childProcessCount = 0;
	//protected $processes = array();
	private $work_queue = array();
	private $callback;

	
	public function __construct($data, $callback, $maxChildProcesses = 5) 
	{
		if (! function_exists('pcntl_fork')) die('PCNTL functions not available on this PHP installation');
		$this->maxChildProcesses  = $maxChildProcesses;
		$this->work_queue = $data;
		$this->callback = $callback;
		$this->sizeOfParentData = count($data);
		$this->sizeOfEachChildData = ceil($this->sizeOfParentData / $this->maxChildProcesses);
		$this->work_queue = array_chunk($this->work_queue, $this->sizeOfEachChildData);
	}
	
	public function run() 
	{
		
		foreach($this->work_queue as $data)
		{
			$this->childProcessCount++;
			$pid = pcntl_fork();
			switch ($pid) 
			{
				case -1:
					throw new Exception("Fork failed !!! \n");	
				case 0:
					// child process
					$childPid = getmypid();
					call_user_func($this->callback, $data, $childPid);
					exit($this->childProcessCount);					
				default:
					// parent process
					//$this->processes[$pid] = TRUE; // log the child process ID
			}
		
		}
		
		while (pcntl_waitpid(0, $status) != -1)
		{
			if (pcntl_wifexited($status))
			{
				echo "Child exited normally \n";
			}
			else if (pcntl_wifstopped($status))
			{
				echo "Signal: ", pcntl_wstopsig($status), " caused this child to stop. \n";
			}
			else if (pcntl_wifsignaled($status))
			{
				echo "Signal: ",pcntl_wtermsig($status)," caused this child to exit with return code: ", pcntl_wexitstatus($status) , "\n";
			}
						
			$status = pcntl_wexitstatus($status);

			echo "Child $status completed\n";
		}
		
	}
}
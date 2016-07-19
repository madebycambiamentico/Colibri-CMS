<?php
/**
* check if delivery service should run or be delayed/blocked.
*
* @copyright  Copyright (C)2016 Nereo Costacurta
* @license    GPLv3
*/

class DeliveryCheck{
	
	//string date ISO 8601 (YYYY-mm-ddThh:ii:ss+GMT) --- e.g. 2016-05-31T20:59:59+00:02
	private $start_date;
	//bools
	public $die_on_error;
	public $log_on_error;
	//string | NULL
	public $last_error;
	
	
	
	
	/**
	* @summary initialize start script date. add option to log errors and/or stop global script on error.
	*
	* @param (bool) $die_on_error [optional]	if an error occurs, stop global script and print error. default: true.
	* @param (bool) $log_on_error [optional]	if an error occurs, first of all try to log in "error.log". default: false.
	*/
	function __construct($die_on_error = true, $log_on_error = false){
		$this->start_date = date('c');
		$this->die_on_error = $die_on_error;
		$this->log_on_error = $log_on_error;
		$this->last_error = null;
	}
	
	
	
	
	/**
	* @summary return function for any exception/error occurred during DeliveryCheck functions.
	*
	* @description	can log error, stop global script or simply return false when an error occurs.
	*					every error is stored in public variable last_error
	*
	* @param (string) $error [optional]		the error to be stored/printed.
	* @param (bool) $skip_log [optional]	if log_on_error enabled, this override and skip the file logging.
	*
	* @return (bool) if die_on_error enabled return (void) and die. else return false.
	*/
	private function return_on_error( $error='undefined error', $skip_log=false ){
		//option 1) die...
		if ($this->die_on_error)
			die($error);
		//option 2) store error, and log on "error.log" (if not overridden by $skip_log). Eventually return false.
		$this->last_error = $error;
		if ($this->log_on_error && !$skip_log)
			@file_put_contents('error.log', $this->start_date." {$error}\n", LOCK_EX | FILE_APPEND);
		return false;
	}
	
	
	
	/**
	* @summary checks if delivery should start or be delayed.
	*
	* @description
	* - block delivery service if a previus delivery script is already running in a loop.
	* - delay delivery service if called before a previous script has ended but isn't going to loop.
	* - *completely* block delivery if you put a file named "force_stop_script.txt" in same directory.
	* - create/overwrite "last_delivery.log" file with first line containing start script date (ISO 8601)
	*	1. a complete "last_delivery.log" contains this lines:
	*		0 => (date)		start script date ISO 8601
	*		1 => (int)		n. of sent mails
	*		2 => (int)		n. of failed mails
	*		3 => (string)	id of previous script: if not 0 then it's looping.
	*							check if equal to $_GET['loopid'] to know if this call is made right from that script!
	*		4 => (date)		end script date ISO 8601
	*	2. in a nearly started script "last_delivery.log" contains this lines:
	*		0 => (date)		start script date ISO 8601
	*	3. in a started script which has sent at least 5 emails "last_delivery.log" contains this lines:
	*		0 => (date)		start script date ISO 8601
	*		1 => (date)		last 5th email sent date ISO 8601. will update every 5 emails.
	*
	* @see return_on_error()
	* @global (int) $delay				must contain delay in seconds
	* @global (string) DELIVERY_URL	must contain the url of this script. used if execution has to be delayed
	*
	* @return (bool|void)	if die_on_error enabled returns (void) and die.
	*								else returns true on success, false if delivery should not be performed.
	*/
	function check_start(){
		
		//phisycal override: put 'force_stop_script.txt' file in 'email-bodies' folder to force script to stop every loop.
		//file can contain anythig. Remove file (via FTP or any other way) to re-eanble script execution.
		if (is_file('force_stop_script.txt')){
			return $this->return_on_error("Script has been blocked.\nRemove force_stop_script.txt to enable script execution.");
		}
		
		//start check
		if (is_file('last_delivery.log')){
			$last_delivery = @file_get_contents('last_delivery.log');
			if (false !== $last_delivery){
				//-------------------------------------------------
				if (empty($last_delivery)){
					//did you just debug and cleared the log file?
					//write delivery start time
					if (false === @file_put_contents('last_delivery.log', $this->start_date, LOCK_EX) )
						return $this->return_on_error("Cannot write last_delivery.log!!!");
					return true;
				}
				//-------------------------------------------------
				else{
					$last_delivery = explode("\n",$last_delivery);
					/*
					0 = start script date ISO 8601
					1 = sent mails
					2 = failed mails
					3 = will trigger automatic delivery again
					4 = end script date ISO 8601
					*/
					if (count($last_delivery) == 5){
						//previous script has finished.
						//if it will trigger automatic delivery again, and you are trying to request another mail delivery, then do nothing.
						//else if end delivery too close wait for remaining $delay.
						//else start script
						global $delay;
						if ($last_delivery[3] != 0){
							//will be triggered automatically... but what if that script time out???
							// TODO...
							if (!isset($_GET['loopid']) || $_GET['loopid'] != $last_delivery[3])
								return $this->return_on_error("Script loop already running and waiting to send next emails!\n".
																		"Or something has gone wrong and the script has timed out...");
							else
								return true;
						}
						elseif ($delay){
							//(int) seconds... may cause loss of 0.999 seconds? i don't now how to GET milliseconds right now...
							//$t = microtime(true);
							//$micro = $t - floor($t);
							////$micro = sprintf("%06d",($t - floor($t)) * 1000000);
							$back_to_the_future = time() - strtotime($last_delivery[4]);
							if ($back_to_the_future < $delay+1){
								sleep($back_to_the_future+1);
								if (!async_curl(DELIVERY_URL))
									return $this->return_on_error("cURL failed on link ".DELIVERY_URL);
								return $this->return_on_error('Script called too early. It will be delayed of '.($back_to_the_future+1).' s', true);//skip error log
							}
							else{
								//allow dalayed script start
								if (false === @file_put_contents('last_delivery.log', $this->start_date, LOCK_EX) )
									return $this->return_on_error("Cannot write last_delivery.log!!!");
								return true;
							}
						}
						else{
							//allow manual script start
							if (false === @file_put_contents('last_delivery.log', $this->start_date, LOCK_EX) )
								return $this->return_on_error("Cannot write last_delivery.log!!!");
							return true;
						}
					}
					else{
						/*
						0 = start script date ISO 8601
						[1 = last sent email date ISO 8601]
						*/
						
						//probably previous script is already running...
						//but what if that script has timed out and cannot write again to the log file since it has been stopped???
						//a solution could be:
						//- write to the file $start_script AND, every N emails [...], the last time the mail has been sent.
						//- if dt from that last sent email is too much [...] then start the script anyway.
						//- if any sent mail and dt from script start is too much [...] then start the script anyway.
						// TODO...
						//		$delay_from_last_email = time() - strtotime($last_delivery[1]);
						//		$max_delay = max(3600,$delay);// ?how much seconds to be sure the script has failed?
						//		if ($delay_from_last_email > $max_delay){ /* start script anyway... */ }
						return $this->return_on_error("A previous script is already running!\n".
																"Or something has gone wrong and the script has timed out...".
																(isset($last_delivery[1]) ?
																	"\nLast time an e-mail was sent was {$last_delivery[1]}, which is ".(time() - strtotime($last_delivery[1])).' seconds ago'
																	: '')
																);
					}
				}
			}
			else
				return $this->return_on_error("An error occurred when trying to open log");
		}
		//-------------------------------------------------
		else{
			//first time you send mails eh? or did you just deleted the file?
			//add file and write delivery start time
			if (false === @file_put_contents('last_delivery.log', $this->start_date, LOCK_EX) )
				return $this->return_on_error("Cannot write last_delivery.log!!!");
			return true;
		}
	}



	/**
	* @summary update last n-th email sent date. should be called every 5-10 emails
	*
	* @return (bool|void)	if die_on_error enabled return (void) and die.
	*								else return true on success, false if delivery should not be performed.
	*/
	function keep_alive(){
		/*
		0 = start script date ISO 8601
		1 = last sent email date ISO 8601
		*/
		if (false === @file_put_contents('last_delivery.log', $this->start_date."\n".date('c'), LOCK_EX) )
			return $this->return_on_error("Cannot write last_delivery.log!!!");
		return true;
	}



	/**
	* @summary create complete 'last_delivery.log'. call when script has ended.
	*
	* @return (bool|void)	if die_on_error enabled return (void) and die.
	*								else return true on success, false if couldn't write on file.
	*/
	function close( $e_s, $e_f, $will_run_again=0 ){
		/*
		0 = start script date ISO 8601
		1 = sent mails
		2 = failed mails
		3 = will trigger automatic delivery again
		4 = end script date ISO 8601
		*/
		if (false === @file_put_contents(
			'last_delivery.log',
			$this->start_date."\n{$e_s}\n{$e_f}\n{$will_run_again}\n".date('c'),
			LOCK_EX)
		){
			return $this->return_on_error("Cannot write last_delivery.log!!!");
		}
		return true;
	}

}

?>
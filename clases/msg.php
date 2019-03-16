<?php    
	class msg
	{
		function success($msg )
		{ 
            return "<div class='alert alert-success' >
					  <div class='msg_success'></div>
					  <div cass=''>$msg</div>
					</div>"; 
		}
		 
		function warning($msg)
		{ 
            return "<div class='alert alert-warning' >
					  <div class='msg_warming'></div>
					  <div cass=''>$msg</div>
					</div>";  
		}
		function info($msg)
		{ 
            return "<div class='alert alert-info' >
					  <div class='msg_info'></div>
					  <div cass=''>$msg</div>
					</div>"; 
		}
		function danger($msg)
		{ 
            return "<div class='alert alert-danger'>
					  <div class='msg_danger'></div>
					  <div cass=''>$msg</div>
					</div>"; 
		} 

	}
?>

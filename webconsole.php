<?php
	
	//Me mashing my keyboard, aka uncrackable password.
	//Don't want to accidentally leave this lying around unsecure.
	$password = 'vagrant';
	
	session_start();
	
	if (isset($_POST['clear']) AND $_POST['clear'] == 'clear') {
		clear_command();
	}
	
	if ( ! isset($_SESSION['persist_commands']) OR ! isset($_SESSION['commands'])) {
		$_SESSION['persist_commands'] = array();
		$_SESSION['commands'] = array();
		$_SESSION['command_responses'] = array();
	}
	
	$toggling_persist = FALSE;
	$toggling_current_persist_command = FALSE;
	
	if (isset($_POST['persist_command_id']) AND is_numeric($_POST['persist_command_id'])) {
		$toggling_persist = TRUE;
		$persist_command_id = $_POST['persist_command_id'];
		if (count($_SESSION['persist_commands']) == $persist_command_id) {
			$toggling_current_persist_command = TRUE;
		} else {
			$_SESSION['persist_commands'][$persist_command_id] =
				! $_SESSION['persist_commands'][$persist_command_id];
		}
	}
	
	$previous_commands = '';
	
	foreach ($_SESSION['persist_commands'] as $index => $persist) {
		if ($persist) {
			$current_command = $_SESSION['commands'][$index];
			if ($current_command != '') {
				$previous_commands .= $current_command . '; ';
			}
		}
	}
	
	if (isset($_POST['command'])) {
		$command = $_POST['command'];
        $_SESSION['logged_in'] = TRUE;
        // TODO: kill this login stuff
		if ( ! isset($_SESSION['logged_in'])) {
			if ($command == $password) {
				$_SESSION['logged_in'] = TRUE;
				$response = array('Welcome, ' . str_replace("\n", '', `whoami`) . '!!');
			} else {
				$response = array('Incorrect Password');
			}
			array_push($_SESSION['persist_commands'], FALSE);
			array_push($_SESSION['commands'], 'Password: ');
			array_push($_SESSION['command_responses'], $response);
        // TODO
		} else {
			if ($command != '' AND ! $toggling_persist) {/*
				if ($command == 'logout') {
					session_unset();
					$response = array('Successfully Logged Out');
				} else*/if ($command == 'clear') {
					clear_command();
				} else {
					exec($previous_commands . $command . ' 2>&1', $response, $error_code);
					if ($error_code > 0 AND $response == array()) {
						$response = array('Error');
					}
				}
			} else {
				$response = array();
			}
			if ($command != 'logout' AND $command != 'clear') {
                $temp = mb_substr($command, 0, 2);
				if ($toggling_persist || $temp == 'cd') {
					//if ($toggling_current_persist_command) {
						array_push($_SESSION['persist_commands'], TRUE);
						array_push($_SESSION['commands'], $command);
						array_push($_SESSION['command_responses'], $response);
						if ($command != '') {
							$previous_commands = $previous_commands . $command . '; ';
						}
					//}
				} else {
					array_push($_SESSION['persist_commands'], FALSE);
					array_push($_SESSION['commands'], $command);
					array_push($_SESSION['command_responses'], $response);
				}
			}
		}
	}
	
	function clear_command()
	{
		if (isset($_SESSION['logged_in'])) {
			$logged_in = TRUE;
		} else {
			$logged_in = FALSE;
		}
		session_unset();
		if ($logged_in) {
			$_SESSION['logged_in'] = TRUE;
		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>PHP Manager | Web Console</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <style type="text/css">
        body {font-family: "Ubuntu Mono", "Monospace", "Monaco", "Courier New"; font-size: 12px; padding-top: 70px}
        th, td {padding: 15px; text-align: center; border-collapse: collapse}
        tbody {display: table; width: 100%}
        table {border-collapse: collapse}
        table, th, td {border: 1px solid #8892bf}
        /* Bootstrap */
        .navbar-default {background-color: #8892bf; border-color: #4f5b93}
        .navbar-default .navbar-brand {color: #ecf0f1}
        .navbar-default .navbar-brand:hover,
        .navbar-default .navbar-brand:focus {color: #ecdbff}
        .navbar-default .navbar-text {color: #ecf0f1}
        .navbar-default .navbar-nav > li > a {color: #ecf0f1}
        .navbar-default .navbar-nav > li > a:hover,
        .navbar-default .navbar-nav > li > a:focus {color: #ecdbff}
        .navbar-default .navbar-nav > .active > a,
        .navbar-default .navbar-nav > .active > a:hover,
        .navbar-default .navbar-nav > .active > a:focus {color: #ecdbff; background-color: #4f5b93}
        .navbar-default .navbar-nav > .open > a,
        .navbar-default .navbar-nav > .open > a:hover,
        .navbar-default .navbar-nav > .open > a:focus {color: #ecdbff; background-color: #4f5b93}
        .navbar-default .navbar-toggle {border-color: #4f5b93}
        .navbar-default .navbar-toggle:hover,
        .navbar-default .navbar-toggle:focus {background-color: #4f5b93}
        .navbar-default .navbar-toggle .icon-bar {background-color: #ecf0f1}
        .navbar-default .navbar-collapse,
        .navbar-default .navbar-form {border-color: #ecf0f1}
        .navbar-default .navbar-link {color: #ecf0f1}
        .navbar-default .navbar-link:hover {color: #ecdbff}

        @media (max-width: 767px) {
            .navbar-default .navbar-nav .open .dropdown-menu > li > a {color: #ecf0f1}
            .navbar-default .navbar-nav .open .dropdown-menu > li > a:hover,
            .navbar-default .navbar-nav .open .dropdown-menu > li > a:focus {color: #ecdbff}
            .navbar-default .navbar-nav .open .dropdown-menu > .active > a,
            .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover,
            .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {color: #ecdbff; background-color: #4f5b93}
        }
        
        /* Kill CSS stuff */
        pre {
            padding: 0px !important;
            margin: 0 0 0px !important;
            border: none !important;
            border-radius: 0px !important;
        }
        #command {border: none !important; width: 95% !important}
	</style>
    <link rel="shortcut icon" href="http://php.net/favicon.ico" type="image/x-icon" />
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top navbar-default" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">
                    <img src="http://php.net/favicon.ico" alt="">
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="./index.php">Projects</a>
                    </li>
                    <li>
                        <a href="./phpinfo.php">phpinfo</a>
                    </li>
                    <li>
                        <a href="">Web Console</a>
                    </li>
                    <li>
                        <a href="http://github.com/kenpb/phpmanager" target="_blank">About</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    
    <div class="container" style="padding-bottom:30px;">
        <div class="row">
            <div class="col-md-12">
                <div class="terminal" onclick="document.getElementById('command').focus();" id="terminal">
                    <div class="bar" style="background-color:#8892bf;border:1px solid #4f5b93;height:40px;padding-left:15px;line-height:40px;color:#fff;">
                        <?php echo `whoami`, ' - ', exec($previous_commands . 'pwd'); ?>
                    </div>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="commands" id="commands">
                        <input type="hidden" name="persist_command_id" id="persist_command_id" />
                        <?php if ( ! empty($_SESSION['commands'])) { ?>
                        <div>
                            <?php foreach ($_SESSION['commands'] as $index => $command) { ?>
                            <!-- <input type="button" value="<?php if ($_SESSION['persist_commands'][$index]) { ?>Un-Persist<?php } else { ?>Persist<?php } ?>" onfocus="this.style.color='#0000FF';" onblur="this.style.color='';" onclick="toggle_persist_command(<?php echo $index; ?>);" class="persist_button" /> !-->
                            <pre><?php echo '$ ', $command, "\n"; ?></pre>
                            <?php foreach ($_SESSION['command_responses'][$index] as $value) { ?>
                            <pre><?php echo htmlentities($value), "\n"; ?></pre>
                            <?php } ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        $ <?php if ( ! isset($_SESSION['logged_in'])) { ?>Password:
                        <input type="password" name="command" id="command" />
                        <?php } else { ?>
                        <input type="text" name="command" id="command" autocomplete="off" onkeydown="return command_keyed_down(event);" />
                        <!-- <input type="button" value="Persist" onfocus="this.style.color='#0000FF';" onblur="this.style.color='';" onclick="toggle_persist_command(<?php if (isset($_SESSION['commands'])) { echo count($_SESSION['commands']); } else { echo 0; } ?>);" class="persist_button" /> !--> 
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
	<script type="text/javascript">
		
		<?php
			$single_quote_cancelled_commands = array();
			if ( ! empty( $_SESSION['commands'] ) ) {
				foreach ($_SESSION['commands'] as $command) {
					$cancelled_command = str_replace('\\', '\\\\', $command);
					$cancelled_command = str_replace('\'', '\\\'', $command);
					$single_quote_cancelled_commands[] = $cancelled_command;
				}
			}
		?>
		
		var previous_commands = ['', '<?php echo implode('\', \'', $single_quote_cancelled_commands) ?>', ''];
		
		var current_command_index = previous_commands.length - 1;
		
		document.getElementById('command').select();
		
		document.getElementById('terminal').scrollTop = document.getElementById('terminal').scrollHeight;
		
		function toggle_persist_command(command_id)
		{
			document.getElementById('persist_command_id').value = command_id;
			document.getElementById('commands').submit();
		}
		
		function command_keyed_down(event)
		{
			var key_code = get_key_code(event);
			if (key_code == 38) { //Up arrow
				fill_in_previous_command();
			} else if (key_code == 40) { //Down arrow
				fill_in_next_command();
			} else if (key_code == 9) { //Tab
				
			} else if (key_code == 13) { //Enter
				if (event.shiftKey) {
					toggle_persist_command(<?php
						if (isset($_SESSION['commands'])) {
							echo count($_SESSION['commands']);
						} else {
							echo 0;
						}
					?>);
					return false;
				}
			}
			return true;
		}
		
		function fill_in_previous_command()
		{
			current_command_index--;
			if (current_command_index < 0) {
				current_command_index = 0;
				return;
			}
			document.getElementById('command').value = previous_commands[current_command_index];
		}
		
		function fill_in_next_command()
		{
			current_command_index++;
			if (current_command_index >= previous_commands.length) {
				current_command_index = previous_commands.length - 1;
				return;
			}
			document.getElementById('command').value = previous_commands[current_command_index];
		}
		
		function get_key_code(event)
		{
			var event_key_code = event.keyCode;
			return event_key_code;
		}
	</script>
</body>
</html>

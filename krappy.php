<?
	// Krappy IRC Logger v3.3

	$server = "irc.rizon.net";
	$port = "6667";
	$channel = "#testchannel"; // Channel to connect to
	$nickbot = "Krappy"; // Bot nickname
	$initialpath = ""; // Path to store log files
	date_default_timezone_set("Europe/Madrid"); // Set time zone
	
	// Arrays to substitute UTF-8 special characters to ISO one using str_replace
	$utfchars = array("Ã¡", "Ã©", "Ã­", "Ã³", "Ãº", "Ã±", "Ã", "Ã‰", "Ã", "Ã“", "Ãš", "Ã‘");
	$isochars = array("á", "é", "í", "ó", "ú", "ñ", "Á", "É", "Í", "Ó", "Ú", "Ñ");
	
	// HTML Header
	$starthtml='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html lang="es-ES" xml:lang="es-ES" xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>IRC log for channel '.$channel.'</title>
			<link rel="stylesheet" href="../../logstyle.css" type="text/css" />
		</head>
		<body>
		';
	
	// HTML footer
	$endhtml='</body>
	</html>';
	
	while (1)
	{
		$connect = fsockopen($server, $port, $errno, $errstr, 30); // Make the connection
		
		$firstmsg = explode(' ',fgets ($connect,2048)); // We get the first message and split it in parts using space as a separator
		$servername = $firstmsg[0]; // First item in the array is the server name (preceded by :)
		
		fputs($connect, "USER $nickbot $nickbot $nickbot : $nickbot\n"); // Server identification
		fputs($connect, "NICK $nickbot -\r\n"); // Server identification
		
		if ($connected==0)
		{
			set_time_limit(0); // To avoid the script closing because of timeout
			fputs($connect,"JOIN $channel\n");
			fputs($connect,"privmsg $channel :Krappy IRC Logger started!\n");
			$connected = 1;
		}
		
		$date = date("Y-m-d"); // Get current date
		$year = date("Y"); // Get current year
		$month = date("m"); // Get current month	
		
		// Test if the directory for log storage exists
		if (@opendir("logs/$year")==false)
		{
			mkdir("logs/$year",0777); // Create directory if it doesn't exist
		}	
		if (@opendir("logs/$year/$month")==false)
		{
			mkdir("logs/$year/$month",0777); // Create directory if it doesn't exist
		}
	
		$path = $initialpath."logs/$year/$month/";
		$logfilename = $path."log".$date.".html"; // Create filename for log file
		
		if ( @is_file ( $logfilename ) == false ) { // If log file doesn't exist
			$open = fopen($logfilename,"a+"); // Open logfile to add text...
			fwrite($open,$starthtml); // ...and write HTML header
		} else {
			$open = fopen($logfilename,"a+"); // If it exists, open logfile to add text
		}
		
		while (!feof($connect))  // While connection doesn't die
		{
			$log = fgets($connect,2048); 
			$parts = explode(' ',$log, 3); 
			$sender = $parts[0]; // Get message sender's name
			
			if ($date != date("Y-m-d")) // Test if date has changed
			{
				fwrite($open,$endhtml); // Write HTML footer
				fclose($open); // Close old logfile
				$date = date("Y-m-d"); // Get new date
				$year = date("Y"); 
				$month = date("m"); 	
			
				// Test if the directory for log storage exists
				if (@opendir("logs/$year")==false)
				{
					mkdir("logs/$year",0777); // Create directory if it doesn't exist
				}	
				if (@opendir("logs/$year/$month/")==false)
				{
					mkdir("logs/$year/$month/",0777); // Create directory if it doesn't exist
				}
				
				$path = $initialpath."logs/$year/$month/";
				$logfilename = $path."log".$date.".html"; // Create filename for new log file
				$open = fopen($logfilename,"a+"); // Open new logfile
				fwrite($open,$starthtml); // Write HTML header
			}
			
			if (substr($log, 0, 6) == "PING :") // Check if server sent a ping...
			{
				fputs($connect,"PONG :".substr($log, 6)."\n\r"); // ...and reply with a pong
			}
			elseif (($sender == $servername) || ($log == ""))
			{		
				// If it is the server that sends the message, or message is empty, don't log it
			}
			else // This block contains everything related to writing of the final log fileN
			{
				$usermask=explode("!",$sender);
				$nickname=substr($usermask[0],1);
				if ($parts[1]=="NICK") // If user changes nickname
				{
					$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="nickchange">*** '.$nickname.' changes nickname to '.htmlspecialchars(str_replace($utfchars, $isochars,substr($parts[2],1)), ENT_QUOTES).'</span><br/>
					';
					fwrite($open, $finallogline); // Write to log file
				}
				elseif ($parts[1]=="PRIVMSG") // If user writes a normal message
				{
					if ( substr($parts[2],strlen($channel)+2,strlen($channel)+1) == "ACTION") // Check if /me command was used
					{
						$finallogline='<span class="timestamp">'.date("[H:i:s] ").'</span> '.'<span class="me">*** '.$nickname.htmlspecialchars(str_replace($utfchars, $isochars,substr($parts[2],strlen($channel)+2+7,-3)), ENT_QUOTES).'</span><br/>
						';
						fwrite($open, $finallogline); // Write to log file
					}
					else // Otherwise, message is logged normally
					{
						$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="nick">&lt;'.$nickname.'&gt; </span>'.htmlspecialchars(str_replace($utfchars, $isochars,substr($parts[2],strlen($channel)+2)), ENT_QUOTES).'<br/>
						';
						fwrite($open, $finallogline); // Write to log file
					}			
				}	
				elseif ($parts[1]=="PART") // If user leaves the channel
				{
					$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="part">*** '.$nickname.' left the channel </span><br/>
					';
					fwrite($open, $finallogline); // Write to log file
				}
				elseif ($parts[1]=="JOIN") // If user joins the channel
				{
					$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="join">*** '.$nickname.' has joined the channel</span><br/>
					';
					fwrite($open, $finallogline);  // Write to log file
				}
				elseif ($parts[1]=="TOPIC") // If topic is changed
				{
					$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="topic">*** '.$nickname.' changed topic to '.htmlspecialchars(str_replace($utfchars, $isochars,substr($parts[2],strlen($channel)+2)), ENT_QUOTES).'</span><br/>
					';
					fwrite($open, $finallogline);  // Write to log file
				}
				elseif ($parts[1]=="QUIT") // If user leaves the server
				{
					$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="quit">*** '.$nickname.' left IRC network - '.htmlspecialchars(str_replace($utfchars, $isochars,substr($parts[2],1)), ENT_QUOTES).'</span><br/>
					';
					fwrite($open, $finallogline);  // Write to log file
				}
				elseif ($parts[1]=="MODE") // If mode is changed
				{
					$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="mode">*** '.$nickname.' changed mode to '.htmlspecialchars(str_replace($utfchars, $isochars,substr($parts[2],strlen($channel)+1)), ENT_QUOTES).'</span><br/>
					';
					fwrite($open, $finallogline);  // Write to log file
				}
				elseif ($parts[1]=="KICK") // If a user is kicked
				{
					$finallogline='<span class="timestamp">'.date("[H:i:s]").'</span> '.'<span class="kick">*** '.$nickname.' kicked '.htmlspecialchars(str_replace($utfchars, $isochars,substr($parts[2],strlen($channel)+1)), ENT_QUOTES).'</span><br/>
					';
					fwrite($open, $finallogline);  // Write to log file
				}
				
				
				else
				{
					// If server reply doesn't match any of the above, it is not logged
				}
			}
		}
		fwrite($open,$endhtml); // Write HTML footer
		fclose($open); // Close logfile
		fclose($connect); // Close connection
	}
?>

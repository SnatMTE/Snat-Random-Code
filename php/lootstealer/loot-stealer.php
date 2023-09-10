<?php
/*
    @Application: Loot Stealer
    @Description: This bot controls everything raid related and let people know what the fuck is going on - uses webhooks!
*/

//date_default_timezone_set('Europe/London');

require_once 'vendor/autoload.php';
$credentialsPath = 'XXXXXXXXXXXXXXXXXXXXX';
$apiKey = 'XXXXXXXXXXXXXXXXXXXXX';
$spreadsheetId = 'XXXXXXXXXXXXXXXXXXXXX';


function recordAccessDate() {
    $accessFile = 'accessed.txt';

    // Get the current timestamp (date and time)
    $currentTimestamp = time();

    // Save the current timestamp to the file
    if (file_put_contents($accessFile, $currentTimestamp) !== false) {
        // Timestamp recorded successfully
        exit();
    } else {
        $help = "HELP! <@XXXXXXXXXXXXXXXXXXXXX>, I can't record the timestamp. I'm alright being lazy, but I can't do shit!";
        sendToDiscord($help);
        exit();
    }
}


function checkAccessDate($spreadsheetId, $credentialsPath, $apiKey) {
    // Read the timestamp from "accessed.txt"
    $accessFile = 'accessed.txt';
    if (file_exists($accessFile)) {
        $accessTimestamp = (int) file_get_contents($accessFile); // Read the timestamp from the file

        // Retrieve the timestamp from Google Sheets cell C14
        $rangeC14 = 'Schedule!C14';

        $client = new \Google\Client();
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig($credentialsPath);
        $client->setDeveloperKey($apiKey);

        $service = new \Google\Service\Sheets($client);

        try {
            // Get the date from cell C14 in Google Sheets
            $responseC14 = $service->spreadsheets_values->get($spreadsheetId, $rangeC14);
            $spreadsheetTimestampC14 = strtotime($responseC14->getValues()[0][0]);

            // Get today's timestamp
            $currentTimestamp = $accessTimestamp;

           ///sendToDiscord("Current Timestamp: " . $currentTimestamp . " - " . date('d M Y H:i:s Z', $currentTimestamp));
            //sendToDiscord("Spreadsheet Timestamp: " . $spreadsheetTimestampC14 . " - " . date('d M Y H:i:s Z', $spreadsheetTimestampC14));

            if ($currentTimestamp >= $spreadsheetTimestampC14) {
                //sendToDiscord("Debug: We are on the wrong week");
                sendToDiscord("Hey <@XXXXXXXXXXXXXXXXXXXXX>, can you switch to the next raid week for me?");
                exit();
            } else {
                //sendToDiscord("Debug: We are on the correct week.");
                // Retrieve the date from Google Sheets cell D1
                $rangeD1 = 'Schedule!D1';

                // Get the date from cell D1 in Google Sheets
                $responseD1 = $service->spreadsheets_values->get($spreadsheetId, $rangeD1);
                $spreadsheetTimestampD1 = strtotime($responseD1->getValues()[0][0]);

                // Calculate the difference in seconds between D1 and today
                $timeDifferenceD1 = $spreadsheetTimestampD1 - $currentTimestamp;

                if ($timeDifferenceD1 <= 604800 || date('N') == 2) {
                    //sendToDiscord("Debug: I was murdered because it hasn't been seven days yet or it's Tuesday");
                    exit();
                } else {
                    //sendToDiscord("Debug: I am still alive because it HAS been seven days and it's not Tuesday.");
                }                
                
            }
        } catch (Google_Service_Exception $e) {
            sendToDiscord("Error fetching the date from Google Sheets: " . $e->getMessage());
        }
    } else {
        $help = "HELP! <@XXXXXXXXXXXXXXXXXXXXX>, I can't record the date. I'm alright being lazy, but I can't do shit!";
        sendToDiscord($help);
        exit();
    }
}


function sendToDiscord($message)

{
    $webhookurl = "https://discord.com/api/webhooks/XXXXXXXXXXXXXXXXXXXXX";
    $timestamp = date("c", strtotime("now"));
    $json_data = json_encode([
        "content" => "$message",
        "username" => "Loot Stealer",
        "tts" => false,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    $ch = curl_init( $webhookurl );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec( $ch );
    curl_close( $ch );
}

if ($_GET["cmd"] == "raid-days")
    {

        //Lets check when it was last updated.

        checkAccessDate($spreadsheetId, $credentialsPath, $apiKey);
        
        // Set the range of the cell you want to retrieve (H1 in this case)
        $range = 'Schedule!H1:H1';

        // Create a new Google Client instance
        $client = new \Google\Client();
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig($credentialsPath);
        $client->setDeveloperKey($apiKey);

        // Create a Google Sheets service instance
        $service = new \Google\Service\Sheets($client);

        // Retrieve the values from the spreadsheet
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        // Get the value in H1
        $value = $values[0][0];

        //$message = "Value in H1: " . $value;
        $raidDays = explode(",", $value);
        $lootDays = $value;

        if (count($raidDays) > 3 OR count($raidDays) == 3) {
            // Randomly pick three values
            $randomDays = array_rand($raidDays, 3);
            $selectedDays = [];
            
            // Retrieve the selected days
            foreach ($randomDays as $randomDay) {
                $selectedDays[] = $raidDays[$randomDay];
            }
            
            $raidDays = "Hey <@XXXXXXXXXXXXXXXXXXXXX>. This week you are getting me loot on " . implode(', ', $selectedDays) . ".";
            sendToDiscord($raidDays); 
            recordAccessDate();
            exit();

        } else {
            try {
                $range = 'Schedule!J12';
                $responseDays = $service->spreadsheets_values->get($spreadsheetId, $range);
                $valueDays = $responseDays->getValues()[0][0];
                
                if ($valueDays === 'Yes') {
                    $raidDays = "Aww <@XXXXXXXXXXXXXXXXXXXXX, you are only getting me loot on $lootDays.";
                    sendToDiscord($raidDays); 
                    recordAccessDate();
                    exit();
                } elseif ($valueDays === 'No') {
                    $namesAndRanges = [
                        'XXXXXXXXXXXXXXXXXXXXX' => 'J4',   
                        'XXXXXXXXXXXXXXXXXXXXX' => 'J5',   
                        'XXXXXXXXXXXXXXXXXXXXX' => 'J6',   
                        'XXXXXXXXXXXXXXXXXXXXX'  => 'J7',   
                        'XXXXXXXXXXXXXXXXXXXXX' => 'J8',   
                        'XXXXXXXXXXXXXXXXXXXXX' => 'J9',   
                        'XXXXXXXXXXXXXXXXXXXXX' => 'J10',  
                        'XXXXXXXXXXXXXXXXXXXXX' => 'J11',  
                    ];
            
                    foreach ($namesAndRanges as $name => $range) {
                        $response = $service->spreadsheets_values->get($spreadsheetId, "Schedule!$range");
                        $value = $response->getValues()[0][0];
            
                        if ($value === 'No') {
                            $discordMessage = "Hey <@$name>, can you update the speadsheet so I can figure out the days?";
                            sendToDiscord($discordMessage);
                        }
                    }
                    exit();
                }
            } catch (Google_Service_Exception $e) {
                echo "Error fetching J12 value: " . $e->getMessage();
            }
          
        }
    }

?>

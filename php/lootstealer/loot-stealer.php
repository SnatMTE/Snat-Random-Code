<?php
/*
    @Application: Loot Stealer
    @Description: This bot controls everything raid related and let people know what the fuck is going on - uses webhooks and Google Spreadsheet!
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
$credentialsPath = 'XXXXXXXXXXXXXXXXXXXXXX.json';
$apiKey = 'XXXXXXXXXXXXXXXXXXXX';

// Set the ID of your spreadsheet
$spreadsheetId = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

function sendToDiscord($message)

{

    //Lets send a message to Discord!
    $webhookurl = "https://discord.com/api/webhooks/XXXXXXXXXXXXXXXXXXXXXXXX";

    $timestamp = date("c", strtotime("now"));

    $json_data = json_encode([

        "content" => "$message",
        "username" => "Loot Stealer",

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

if ($_GET["cmd"] == "test")
    {
        $message = "Hello World!";
        sendtoDiscord($message);
    }

if ($_GET["cmd"] == "raid-days")
    {

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

        if (count($raidDays) > 3 OR count($raidDays) == 3) {
            // Randomly pick three values
            $randomDays = array_rand($raidDays, 3);
            $selectedDays = [];
            
            // Retrieve the selected days
            foreach ($randomDays as $randomDay) {
                $selectedDays[] = $raidDays[$randomDay];
            }
            
            $raidDays = "Hey <@XXXXXXXXXXXXXXXXX>. This week you are getting me loot on " . implode(', ', $selectedDays);
        } else {
            // Lets check everyone has updated the sheet.
            $range = 'Schedule!C4:I11'; // Replace with your desired range

            // Create a new Google Client instance
            $client = new \Google\Client();
            $client->setScopes([\Google\Service\Sheets::SPREADSHEETS_READONLY]);
            $client->setAuthConfig($credentialsPath);
            $client->setDeveloperKey($apiKey);

        // Create a Google Sheets service instance
        $service = new \Google\Service\Sheets($client);

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();
        
            $isEmpty = false;
            foreach ($values as $row) {
                foreach ($row as $cell) {
                    if (empty($cell)) {
                        $isEmpty = true;
                        break 2;
                    }
                }
            }
        
            if ($isEmpty) {
                $raidDays = 'Can everyone update the spreadsheet so I can pick the days!';
            } else {
                //$raidDays = 'All cells within the range '.$range.' are filled.';
            }
        } catch (Google_Service_Exception $e) {
            echo 'Error occurred: ' . $e->getMessage();
        }
        }
        sendToDiscord($raidDays);
    }

?>
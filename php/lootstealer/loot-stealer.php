<?php
/*
    @Application: Loot Stealer
    @Description: This bot controls everything raid related and lets people know what's going on via webhooks!
    @Version: 1.3
*/

// Set debug mode
$debug = false;  // Set to true to enable debug mode

// Load Google Sheets API client
require_once 'vendor/autoload.php';

// Google Sheets credentials and API key
$credentialsPath = 'XXXXXXXXXXXXXXXXXXXXXX.apps.googleusercontent.com.json';
$apiKey = 'XXXXXXXXXXXXXXXXXXXXXX';
$spreadsheetId = 'XXXXXXXXXXXXXXXXXXXXXX';
$discordToken = 'XXXXXXXXXXXXXXXXXXXXXX';
$discordServer = 'XXXXXXXXXXXXXXXXXXXXXX';

// Function to send message to Discord webhook
function sendToDiscord($message, $userId = '') {
    global $debug;

    // Prepare Discord webhook URL
    $webhookurl = "https://discord.com/api/webhooks/XXXXXXXXXXXXXXXXXXXXXX/";

    // Prepare JSON data
    $json_data = [
        "content" => $message,
        "username" => "Loot Stealer",
        "tts" => false,
    ];

    // Encode JSON data
    $json_payload = json_encode($json_data);

    // Set up cURL request
    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute cURL request
    $response = curl_exec($ch);
    curl_close($ch);

    // Handle response if needed
    if ($response === false) {
        echo 'Error sending message to Discord: ' . curl_error($ch);
    }
}

// Function to fetch availability status from Google Sheets for each user
function fetchAvailabilityStatus($spreadsheetId, $credentialsPath, $apiKey) {
    global $debug;
    // Initialize Google Sheets API client
    $client = new \Google\Client();
    $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
    $client->setAuthConfig($credentialsPath);
    $client->setAccessType('offline');

    // Create Google Sheets service instance
    $service = new \Google\Service\Sheets($client);

    // Define the mapping of Discord user IDs to Google Sheets ranges and names
    $userRanges = [
        'XXXXXXXXXXXXXXXXXXXXXX' => ['range' => 'J4',  'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
        'XXXXXXXXXXXXXXXXXXXXXX' => ['range' => 'J5',  'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
        'XXXXXXXXXXXXXXXXXXXXXX' => ['range' => 'J6',  'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
        'XXXXXXXXXXXXXXXXXXXXXX' => ['range' => 'J7',  'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
        'XXXXXXXXXXXXXXXXXXXXXX' => ['range' => 'J8',  'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
        'XXXXXXXXXXXXXXXXXXXXXX' => ['range' => 'J9',  'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
        'XXXXXXXXXXXXXXXXXXXXXX'  => ['range' => 'J10', 'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
        'XXXXXXXXXXXXXXXXXXXXXX' => ['range' => 'J11', 'name' => 'XXXXXXXXXXXXXXXXXXXXXX'],
    ];

    try {
        // Check each user's availability status
        foreach ($userRanges as $userId => $info) {
            $cellValue = '';
            $response = $service->spreadsheets_values->get($spreadsheetId, "Schedule!{$info['range']}");
            $values = $response->getValues();

            if (!empty($values)) {
                $cellValue = isset($values[0][0]) ? $values[0][0] : '';
            }

            // If cell value is not 'Yes', send the notification
            if ($cellValue !== 'Yes') {
                // Notify the user
                if ($debug) {
                    sendToDiscord("Hey {$info['name']}, can you update the spreadsheet with your availability for raid days?");
                } else {
                    sendToDiscord("Hey <@$userId>, can you update the spreadsheet with your availability for raid days?");
                }
                
                // Optionally, add a short delay to avoid rate limits or duplicate handling issues
                usleep(500000); // Sleep for 0.5 seconds (500,000 microseconds) - adjust as needed
            }
        }
        //exit();
    } catch (\Google\Service\Exception $e) { 
        echo "Error fetching availability status: " . $e->getMessage();
    }
}

// Function to fetch a specific cell value
function getCellValue($spreadsheetId, $range, $credentialsPath) {
    $client = new \Google\Client();
    $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
    $client->setAuthConfig($credentialsPath);

    $service = new \Google\Service\Sheets($client);
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    return isset($response->getValues()[0][0]) ? $response->getValues()[0][0] : null;
}

// Function to copy data from one range to another
function copyRange($spreadsheetId, $sourceRange, $destinationRange, $service) {
    try {
        // Get data from the source range
        $response = $service->spreadsheets_values->get($spreadsheetId, $sourceRange);
        $data = $response->getValues();

        if ($data === null) {
            $data = []; // Ensure $data is an array even if null
        }

        // Process the data to handle empty values
        foreach ($data as &$row) {
            foreach ($row as &$value) {
                // If value is empty, either leave it as is or convert to 0
                if ($value === null || $value === "") {
                    // $value = 0;
                }
            } 
        }

        // Prepare the data to be copied to the destination
        $body = new \Google\Service\Sheets\ValueRange(['values' => $data]);
        $params = ['valueInputOption' => 'USER_ENTERED']; // Ensure correct formatting

        // Update the destination range with the copied data
        $service->spreadsheets_values->update($spreadsheetId, $destinationRange, $body, $params);
    } catch (\Google\Service\Exception $e) {
        echo "Error copying range: " . $e->getMessage();
    }
}


// Function to clear values in a range
function clearRange($spreadsheetId, $range, $service) {
    try {
        // Clear the values in the specified range
        $clearRequest = new \Google\Service\Sheets\ClearValuesRequest();
        $service->spreadsheets_values->clear($spreadsheetId, $range, $clearRequest);
    } catch (\Google\Service\Exception $e) {
        echo "Error clearing range: " . $e->getMessage();
    }
}

// Main logic to check date and copy data
function checkAndCopyData($spreadsheetId, $credentialsPath) {
    // Initialize Google Sheets API client
    $client = new \Google\Client();
    $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
    $client->setAuthConfig($credentialsPath);
    $client->setAccessType('offline');
    $service = new \Google\Service\Sheets($client);

    // Get the current date and the date from C14
    $currentDate = date('d-m-Y');
    $c14Date = getCellValue($spreadsheetId, 'Schedule!C14', $credentialsPath);

    if ($c14Date === $currentDate) {
        // Dates match, copy data from C17:I17 to C4:I4

        $updateRange = 'Schedule!E1';
        $updateBody = new Google_Service_Sheets_ValueRange([
            'range' => $updateRange,
            'values' => [[0]]
        ]);

        try {
            copyRange($spreadsheetId, 'Schedule!C17:I17', 'Schedule!C4:I4', $service);
            copyRange($spreadsheetId, 'Schedule!C18:I18', 'Schedule!C5:I5', $service);
            copyRange($spreadsheetId, 'Schedule!C19:I19', 'Schedule!C6:I6', $service);
            copyRange($spreadsheetId, 'Schedule!C20:I20', 'Schedule!C7:I7', $service);
            copyRange($spreadsheetId, 'Schedule!C21:I21', 'Schedule!C8:I8', $service);
            copyRange($spreadsheetId, 'Schedule!C22:I22', 'Schedule!C9:I9', $service);
            copyRange($spreadsheetId, 'Schedule!C23:I23', 'Schedule!C10:I10', $service);
            copyRange($spreadsheetId, 'Schedule!C24:I24', 'Schedule!C11:I11', $service);

            clearRange($spreadsheetId, 'Schedule!C17:I24', $service);

            $params = ['valueInputOption' => 'RAW'];
            $service->spreadsheets_values->update($spreadsheetId, $updateRange, $updateBody, $params);
        } catch (\Google\Service\Exception $e) {
            echo "Error updating ranges: " . $e->getMessage();
        }

              // Retrieve and format date from C14
              $c14Date = getCellValue($spreadsheetId, 'Schedule!C14', $credentialsPath);
              if ($c14Date) {
                  // Convert date from UK format (DD/MM/YYYY) to USA format (MM-DD-YYYY)
                  try {
                      $date = DateTime::createFromFormat('d-m-Y', $c14Date);
                      if ($date === false) {
                          throw new Exception("Date format is incorrect.");
                      }
                      $formattedDate = $date->format('m-d-Y');
                  } catch (Exception $e) {
                      echo "Error formatting date: " . $e->getMessage();
                      exit();
                  }
  
                  // Update the formatted date to cell D1
                  $destinationRangeDate = 'Schedule!D1';
                  $bodyDate = new \Google\Service\Sheets\ValueRange([
                      'values' => [[$formattedDate]]
                  ]);
                  $paramsDate = ['valueInputOption' => 'USER_ENTERED'];
                  $service->spreadsheets_values->update($spreadsheetId, $destinationRangeDate, $bodyDate, $paramsDate);
              }

    } else {
        // Dates do not match, no action needed
        echo "Skipping copy operation as dates do not match.";
    }
}

if ($_GET["cmd"] == "raid-days") {
    // Initialize Google Sheets API client
    $client = new \Google\Client();
    $client->setScopes([
        'https://www.googleapis.com/auth/spreadsheets', 
    ]);    
    $client->setAuthConfig($credentialsPath);
    $client->setDeveloperKey($apiKey);

    // Create Google Sheets service instance
    $service = new \Google\Service\Sheets($client);

    // Run the check and copy data logic
    checkAndCopyData($spreadsheetId, $credentialsPath);

    // Fetch availability status of users
    fetchAvailabilityStatus($spreadsheetId, $credentialsPath, $apiKey);

    try {
        // Retrieve raid days from cell H1 in Google Sheets
        $range = 'Schedule!H1';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $raidDays = $response->getValues()[0][0] ?? '';

        // Retrieve the value from cell E1
        $e1Range = 'Schedule!E1';
        $e1Response = $service->spreadsheets_values->get($spreadsheetId, $e1Range);
        $e1Value = $e1Response->getValues()[0][0] ?? '';

        // Retrieve the data from C4:I4 and C11:I11 to check for empty cells
        $c4ToI4Range = 'Schedule!C4:I4';
        $c4ToI4Response = $service->spreadsheets_values->get($spreadsheetId, $c4ToI4Range);
        $c4ToI4Values = $c4ToI4Response->getValues()[0] ?? [];

        $c11ToI11Range = 'Schedule!C11:I11';
        $c11ToI11Response = $service->spreadsheets_values->get($spreadsheetId, $c11ToI11Range);
        $c11ToI11Values = $c11ToI11Response->getValues()[0] ?? [];

        // Check if any cells in C4:I4 or C11:I11 are empty
        $hasEmptyCells = false;
        
        // Loop through both ranges to check for empty cells
        foreach (array_merge($c4ToI4Values, $c11ToI11Values) as $cellValue) {
            if (empty(trim($cellValue))) {
                $hasEmptyCells = true;
                break;
            }
        }

        // If any cells are empty, skip the message
        if ($hasEmptyCells) {
            echo "Skipping message because C4:I4 or C11:I11 contains empty cells.";
            return;
        }

        // If raidDays is empty, skip the message
        if (empty($raidDays)) {
            echo "Skipping message because raidDays is empty.";
            return;
        }

        // Convert raid days string to an array
        $raidDaysArray = explode(",", $raidDays);

        // Determine the number of raid days available
        $numRaidDays = count($raidDaysArray);

        // If there are fewer than three raid days, use all available days
        if ($numRaidDays < 3) {
            $selectedDays = $raidDaysArray;
        } else {
            // Randomly select three raid days
            $randomKeys = array_rand($raidDaysArray, 3);
            $selectedDays = [];
            foreach ($randomKeys as $key) {
                $selectedDays[] = $raidDaysArray[$key];
            }
        }

        // Check if E1 is not set to 1
        if ($e1Value != 1) {
            // Determine the message based on debug mode
            if ($debug) {
                $raidDaysMessage = "Hey Raiders. You are getting me loot on: " . implode(', ', $selectedDays) . ".";
            } else {
                $raidDaysMessage = "Hey <@&XXXXXXXXXXXXXXXXXXXXXX>. You are getting me loot on: " . implode(', ', $selectedDays) . ".";
            }

            // Send Discord message with selected raid days
            sendToDiscord($raidDaysMessage);

            // Create a Discord event for each selected raid day
            foreach ($selectedDays as $raidDay) {
                createDiscordEvent($raidDay);  // Call to the function to create Discord events
            }

            // Update cell E1 to 1 only after sending the message
            $updateRange = 'Schedule!E1';
            $updateBody = new Google_Service_Sheets_ValueRange([
                'range' => $updateRange,
                'values' => [[1]]
            ]);
            $params = ['valueInputOption' => 'RAW'];
            $service->spreadsheets_values->update($spreadsheetId, $updateRange, $updateBody, $params);
        } else {
            echo "Skipping message because E1 is set to 1.";
        }

    } catch (\Google\Service\Exception $e) {
        echo "Error fetching raid days: " . $e->getMessage();
    }
}

function createDiscordEvent($raidDay) {
    // Define timezone for British time (GMT/BST depending on the time of year)
    $timezone = new DateTimeZone('Europe/London');
    
    // Create a DateTime object for the start and end times
    $startTime = new DateTime($raidDay . ' 19:30:00', $timezone);
    $endTime = new DateTime($raidDay . ' 21:30:00', $timezone);
    
    // Convert to UTC for Discord API (Z denotes UTC time)
    $startTimeUtc = $startTime->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    $endTimeUtc = $endTime->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');

    // Define event details
    echo "We are about to make an event";
    $eventName = "Taco Wipe Night on " . $raidDay;
    $eventDescription = "Scheduled raid event. Let's get that loot!";

    // Prepare Discord API request to create the event
    $url = "https://discord.com/api/v10/guilds/$discordServer/scheduled-events";
    $data = [
        "name" => $eventName,
        "scheduled_start_time" => $startTimeUtc,
        "scheduled_end_time" => $endTimeUtc,
        "privacy_level" => 2,  // Guild Only
        "entity_type" => 3,    // External event
        "description" => $eventDescription,
        "entity_metadata" => [
            "location" => "Final Fantasy XIV" // Customize based on your needs
        ]
    ];

    // Make API call using cURL
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bot $discordToken", 
        'Content-Type: application/json',
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    
    // Set a timeout for the connection and response
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // Time to wait for connection (in seconds)
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);        // Time to wait for the whole response (in seconds)

    // Execute the API request
    $response = curl_exec($curl);

    // Check for cURL errors
    if (curl_errno($curl)) {
        $error_message = curl_error($curl);
        curl_close($curl);
        echo "cURL error occurred: $error_message\n";
        return false;
    }

    // Check the HTTP status code
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($http_status >= 200 && $http_status < 300) {
        // Successful request
        echo "Event created for $raidDay.\n";
        return true;
    } else {
        // Something went wrong (Discord returned an error)
        echo "Failed to create event for $raidDay. HTTP Status: $http_status\n";
        echo "Response: $response\n";
        return false;
    }
}

if ($_GET["cmd"] == "debug") {
    sendToDiscord("Hey. I have just had a bump in version number and there is a good chance the schedule is now wrong. Could you all double check what days you are getting me loot? ");
}


?>
<?php
/*
    @Application: Loot Stealer
    @Description: This bot controls everything raid-related and lets people know what's happening via webhooks!
    @Version: 1.2
*/

// Set debug mode
$debug = true;  // Set to true to enable debug mode

// Load Google Sheets API client
require_once 'vendor/autoload.php';

// Google Sheets credentials and API key
$credentialsPath = 'XXXXXXXXXXXXXXXXXXXXXXXXXX.apps.googleusercontent.com.json';
$apiKey = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$spreadsheetId = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';

// Function to send message to Discord webhook
function sendToDiscord($message, $userId = '') {
    global $debug;

    // Prepare Discord webhook URL
    $webhookurl = "https://discord.com/api/webhooks/XXXXXXXXXXXXXXXXXXXXXXXXXXX";

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
        'XXXXXXXXXXX' => ['range' => 'J4',  'name' => 'XXXXXXXXXXX'],
        'XXXXXXXXXXX' => ['range' => 'J5',  'name' => 'XXXXXXXXXXX'],
        'XXXXXXXXXXX' => ['range' => 'J6',  'name' => 'XXXXXXXXXXX'],
        'XXXXXXXXXXX' => ['range' => 'J7',  'name' => 'XXXXXXXXXXX'],
        'XXXXXXXXXXX' => ['range' => 'J8',  'name' => 'XXXXXXXXXXX'],
        'XXXXXXXXXXX' => ['range' => 'J9',  'name' => 'XXXXXXXXXXX'],
        'XXXXXXXXXXX'  => ['range' => 'J10', 'name' => 'XXXXXXXXXXX'],
        'XXXXXXXXXXX' => ['range' => 'J11', 'name' => 'XXXXXXXXXXX'],
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
                usleep(500000); // Sleep for 0.5 seconds
            }
        }

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

            clearRange($spreadsheetId, 'Schedule!C17:I17', $service);
            clearRange($spreadsheetId, 'Schedule!C18:I18', $service);
            clearRange($spreadsheetId, 'Schedule!C19:I19', $service);
            clearRange($spreadsheetId, 'Schedule!C20:I20', $service);
            clearRange($spreadsheetId, 'Schedule!C21:I21', $service);
            clearRange($spreadsheetId, 'Schedule!C22:I22', $service);
            clearRange($spreadsheetId, 'Schedule!C23:I23', $service);
            clearRange($spreadsheetId, 'Schedule!C24:I24', $service);

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

            // Notify users who haven't filled the required information
            //fetchAvailabilityStatus($spreadsheetId, $credentialsPath, $apiKey); 
        } catch (\Google\Service\Exception $e) {
            echo "Error in checkAndCopyData: " . $e->getMessage();
        }
    }
}

// Main logic based on the command received
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
    
        // Check if either cell H1 or E1 is empty
        if (empty($raidDays)) {
            echo "Skipping message because required cells are empty.";
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
    
        if ($e1Value != 1) {
            // Determine the message based on debug mode
            if ($debug) {
                $raidDaysMessage = "Hey Raiders. You are getting me loot on: " . implode(', ', $selectedDays) . ".";
            } else {
                $raidDaysMessage = "Hey <@&XXXXXXXXXXX>. You are getting me loot on: " . implode(', ', $selectedDays) . ".";
            }
        
            // Send Discord message with selected raid days
            sendToDiscord($raidDaysMessage);
        } else {
            echo "Skipping message because E1 is set to 1.";
        }
        
        // Update cell E1 to 1 after sending the message or if it was already 1
        $updateRange = 'Schedule!E1';
        $updateBody = new Google_Service_Sheets_ValueRange([
            'range' => $updateRange,
            'values' => [[1]]
        ]);
        $params = ['valueInputOption' => 'RAW'];
        $service->spreadsheets_values->update($spreadsheetId, $updateRange, $updateBody, $params);
        
    } catch (\Google\Service\Exception $e) {
        echo "Error fetching raid days: " . $e->getMessage();
    }
}

if ($_GET["cmd"] == "debug") {
    sendToDiscord("Hey. I have just had a bump in version number and there is a good chance the schedule is now wrong. Could you all double check what days you are getting me loot? ");
}
?>

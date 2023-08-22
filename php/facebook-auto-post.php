<?php
$accessToken = 'YOUR_USER_ACCESS_TOKEN'; // Obtain this through OAuth2
$postId = '##################'; // Replace with the actual post ID

$commentText = 'Your comment here';

$graphApiUrl = "https://graph.facebook.com/v13.0/{$postId}/comments";

$commentData = array(
    'message' => $commentText,
    'access_token' => $accessToken
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $graphApiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $commentData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

if ($response) {
    $responseData = json_decode($response, true);
    if (isset($responseData['id'])) {
        echo "Comment posted successfully!";
    } else {
        echo "Error posting comment: " . print_r($responseData, true);
    }
} else {
    echo "Error sending cURL request.";
}
?>

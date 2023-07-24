<?php
// setting the current working branch
$current_branch = getenv('CI_COMMIT_BRANCH');
// specifying the project id
$project_id = getenv('CI_PROJECT_ID');
// the api endpoint to post the new data
$url_post = 'https://source.updraftplus.com/api/v4/projects/' . urlencode($project_id) . '/repository/files/changes%2F' . urlencode($current_branch) . '.txt';
// project token
// if a New Token is needed, you need to go to:
// https://source.updraftplus.com/team-updraft/{name_of_project}/-/settings/access_tokens to create one
$access_token = getenv('Updraft_CI');

// Check if the file exists before making the API call
$file_path = 'changes/' . $current_branch . '.txt';
if (file_exists($file_path)) {
    echo "File already exists: $file_path";
    exit(0);
}

// POST request Data
$data = array(
    'branch' => $current_branch,
    'content' => ' ',
    'commit_message' => 'Created ' . $current_branch . '.txt',
    'file_path' => 'changes/' . $current_branch . '.txt'
);
$options = array(
    'http' => array(
        'header' => "Content-Type: application/json\r\n" .
            'PRIVATE-TOKEN: '.$access_token,
        'method' => 'POST',
        'content' => json_encode($data)
    )
);
$context = stream_context_create($options);
$response = file_get_contents($url_post, false, $context);


// Check the response from the POST request
if ($response !== false) {
    $responseData = json_decode($response, true);
    if (isset($responseData['file_path']) && $responseData['file_path'] === 'changes/' . $current_branch . '.txt') {
        echo 'File created successfully' . PHP_EOL;
        exit(0);
    } else {
        echo 'Oops! File was not created for ' . $current_branch . '.txt';
        exit(1);
    }
} else {
    echo 'Oops! Something went wrong with the POST request - Your file may have been created.';
    exit(1);
}

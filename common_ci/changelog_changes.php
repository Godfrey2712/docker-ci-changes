<?php
// setting the current working branch
$current_branch = getenv('CI_COMMIT_BRANCH');
// specifying the project id
$project_id = getenv('CI_PROJECT_ID');
// the api endpoint to post the new data
$url_post = 'https://source.updraftplus.com/api/v4/projects/' . urlencode($project_id) . '/repository/files/changes%2F' . urlencode($current_branch) . '.txt';
// the api endpoint to get the new data
$url_get = "https://source.updraftplus.com/api/v4/projects/" . urlencode($project_id) . "/repository/tree?ref=" . urlencode($current_branch) . "&path=changes";
// project token
// if a New Token is needed, you need to go to:
// https://source.updraftplus.com/team-updraft/{name_of_project}/-/settings/access_tokens to create one
$access_token = getenv('Updraft_CI');

// check if file already exists
if (file_exists("changes/{$current_branch}.txt")) {
    echo "File already exists.\n";
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

// extra check with a GET request
$headers = array(
    'PRIVATE-TOKEN: '.$access_token
);
$options = array(
    'http' => array(
        'method' => 'GET',
        'header' => implode('\r\n', $headers)
    )
);
$context = stream_context_create($options);
$result = file_get_contents($url_get, false, $context);
if (strpos($result, '"name":"' . $current_branch . '.txt"') !== false) {
    echo 'File created successfully' . PHP_EOL;
    exit(0);
} else {
    echo 'Oops!!! ' . $current_branch . '.txt not found';
    exit(1);
}

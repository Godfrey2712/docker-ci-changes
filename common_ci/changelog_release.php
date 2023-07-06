<?php
// setting the current working branch
$current_branch = getenv('CI_COMMIT_BRANCH');
// specifying the project id
$project_id = getenv('CI_PROJECT_ID');
// the path to the readme changelog file
$readme_file_path = 'src/readme.txt';
// the path to the created changes file
$changes_files = glob('changes/*.txt');
// the api endpoint to insert the new data
$url_put = 'https://source.updraftplus.com/api/v4/projects/' . urlencode($project_id) . '/repository/files/src%2F' . 'readme.txt';
// the api endpoint to delete the .txt files in the changes folder
$url_delete = 'https://source.updraftplus.com/api/v4/projects/' . urlencode($project_id) . '/repository/files/';
// project token
// if a New Token is needed, you need to go to: 
// https://source.updraftplus.com/team-updraft/{name_of_project}/-/settings/access_tokens to create one
$access_token = getenv('Updraft_CI');

// sort the file contents
$fileContents = array();
// Read file contents and remove leading white spaces
foreach ($changes_files as $file) {
    $content = file_get_contents($file);
    // remove leading white spaces
    $content = preg_replace('/^\s+/m', '', $content);
    $fileContents[] = $content;
}
sort($fileContents);
$sortedContent = implode("\n", $fileContents);
file_put_contents('sorted_tweak.txt', $sortedContent);

// insert the sorted content into the readme.txt file after the "== Changelog ==" line
$fileContents = file_get_contents($readme_file_path);
$updatedContent = str_replace('== Changelog ==', "== Changelog ==\n" . $sortedContent, $fileContents);
file_put_contents($readme_file_path, $updatedContent);

// output the modified content of the readme.txt file
$modified_content = file_get_contents($readme_file_path);
echo $modified_content . "\n";

// commit the changes to the readme.txt file
$commit_message = 'Created new Changelog Entry';

// the data components to be inserted to "PUT" request endpoint
$data = array(
    'branch' => $current_branch,
    'content' => $modified_content,
    'commit_message' => $commit_message,
    'file_path' => 'src/readme.txt'
);

// creating the "PUT" request
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $url_put,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    // Request timeout after 30 seconds. Set to 0 to never timeout.
    CURLOPT_TIMEOUT => 30,  
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => array(
        'PRIVATE-TOKEN: '.$access_token,
        "Content-Type: application/x-www-form-urlencoded"
    ),
));

// get the http response for request
$response = curl_exec($curl);
$curl_error = curl_errno($curl);
curl_close($curl);

// print the response on console
if ($curl_error) {
    echo "Error: " . curl_error($curl) . " (Error code: " . $curl_error . ")\n";
    exit (1);
} else {
    echo "Success: $response\n";
    echo "Proceeding to delete the files...\n";
    //====================================================================//
    //== Iterate through the file extension (.txt) and delete each file ==//
    //====================================================================//
    foreach ($changes_files as $file) {
        $files_path = urlencode($file);
        $url = $url_delete . $files_path;
        echo $url;
        $payload = array(
            'branch' => $current_branch,
            'commit_message' => 'Deleted file in changes directory',
            'actions' => array(
                array(
                    'action' => 'delete',
                    'file_path' => $files_path,
                ),
            ),
        );
        $payload_json = json_encode($payload);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'PRIVATE-TOKEN: ' . $access_token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload_json),
        ));
        $result = curl_exec($ch);
        $curl_error = curl_errno($ch);
        curl_close($ch);
        if ($curl_error) {
            echo "Error deleting $file: \n" . curl_error($ch) . " (Error Code: " . $curl_error . ")\n";
        } else {
            echo "Delete request response for $file: $result\n";
        }
    }
}

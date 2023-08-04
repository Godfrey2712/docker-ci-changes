<?php

// The scripts are essentially useful in throwing an alert to the slack channel,
// should a job fail on any default pipeline (main or master) on any Project.
// Follows similar pattern with the release notificaton scripts only with some inclusions.
// Once this is on any release tag, you can use that tag on a project and create a job by,
// using this "php /docker_ci/failed_job_notification.php" as a gitlab job

// If a New Token is needed, you need to go to: https://api.slack.com/apps/APCA2G406/oauth to generate one
define('SLACK_API_TOKEN', 'xoxb-32505566439-4297528362499-VhwUWSNX9mhntvG1Cw3V4LsI');

// Get the GitLab project ID from the CI_PROJECT_ID environment variable
$project_id = getenv('CI_PROJECT_ID');

// Get the GitLab project namespace with the project name
$project_path = getenv('CI_PROJECT_PATH');

// Extract the project name from the project path
$project_name = substr($project_path, strrpos($project_path, '/') + 1);

// Get the branch name from the CI_COMMIT_REF_NAME environment variable
$branchName = getenv('CI_COMMIT_REF_NAME');

// Set the default ref to 'master' if it's not 'main' or 'master'
$ref = ($branchName === 'main' || $branchName === 'master') ? $branchName : 'master';

// Get the GitLab job status (success, failure, etc.) from the CI_JOB_STATUS environment variable
$gitlab_job_status = getenv('CI_JOB_STATUS');

// Get the name of the failed job
$failed_job_name = getenv('CI_JOB_NAME');

// Get the pipeline ID
$pipeline_id = getenv('CI_PIPELINE_ID');

// Function to fetch the GitLab project name from the API
function get_gitlab_project_name($project_url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $project_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    if ($result === false) {
        return 'Error fetching project details';
    } else {
        $project_details = json_decode($result, true);
        return $project_details['name'];
    }
}

// Check if the GitLab job status is 'failed' and it's the default branch
if ($gitlab_job_status === 'failed' && $branchName === $ref) {
    // Your Slack notification message with the project name
    $notification_title = "There's a Failed Job on a Default Branch for '$project_name' ";
    $notification_message = "The GitLab job '$failed_job_name' failed on the Default Branch, named '$branchName'.";
    // Send the Slack notification
    send_slack_message("#development", $notification_title, $notification_message);
}

/**
 * This function sends a Slack message to the Slackbot
 * informing users of WIP and Merge conflicts
 *
 * @param string $channel Slack Channel log being posted to
 * @param string $title   Title of Message
 * @param string $message Message body containing the change log
 * @return boolean
 */
function send_slack_message($channel, $title, $message)
{
    ob_start();
    $attachments = array(
        array(
            "title" => $title,
            "text" => $message,
            "color" => "0000ff"
        )
    );
    $parameters = array(
        'channel' => $channel,
        'attachments' => json_encode($attachments),
    );
    // Use groups.list if you run into channel_not_found issues with private channels, make sure your bot is invited
    $parameters['token'] = SLACK_API_TOKEN;
    $send_slack_message = curl_request("https://slack.com/api/chat.postMessage", $parameters);
    return $send_slack_message;
    ob_end_clean();
}

/**
 * cURL Request
 *
 * @param string $url		   URL to send cURL request
 * @param array  $data		   An Array of values to be sent to the API
 * @return void
 */
function curl_request($url, $data = null)
{
    ob_start();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    if ($result === false) {
        return 'Curl error: ' . curl_error($ch);
    } else {
        return json_decode($result);
    }
    ob_end_clean();
}

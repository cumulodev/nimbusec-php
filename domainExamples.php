<?php
// Include autoloader to load Nimbusec API automatically.
require_once("vendor/autoload.php");

// Set an alias for Nimbusec API.
use Nimbusec\API as API;

// Set credentials.
$NIMBUSEC_KEY = "";
$NIMBUSEC_SECRET = "";

// Create a Nimbusec API client instance.
// The default URL parameter may be omitted.
//
// The last parameter marks Guzzle options as described on: http://docs.guzzlephp.org/en/stable/request-options.html 
// By passing options, the default options we set for the client can be extended by e.g proxy features.
// The options can be passed as a variable, otherwise be left empty. Please note: in order to use the options, the URL parameter must be passed.
// $options = [
//     "timeout" => 30,
//     "proxy" => [
//         "http"  => "tcp://localhost:8125",
//     ],
// ];

$options = [
    'max'             => 5,
    'strict'          => false,
    'referer'         => false,
    'protocols'       => ['http', 'https'],
    'track_redirects' => false
];

const DEV_URL = "https://api-dev.nimbusec.com";

$api = new API($NIMBUSEC_KEY, $NIMBUSEC_SECRET, DEV_URL, $options);

try {
	//===================================DOMAIN===================================

	echo "\n----Domain----\n";

	echo "list of domains: \n";
	// list all domains
	$domains = $api->listDomains();
	foreach ($domains as $domain) {
		echo $domain["name"] . "\n";
	}
	echo "-END-\n";

	// testing with domain at index 0
	$domainID=$domains[0]["id"];

	// create domain
	$domain=[
			"name"=>"newdomain.com",
	];
	$domain = $api->createDomain($domain,true);
	echo "added {$domain['name']} to domains \n";

	// get a domain by id
	$domain = $api->getDomain($domain["id"]);
	echo "found it's ID: " . $domain["id"] . "\n";

	// update domain
	$domainNameOld=$domain["name"];
	$domain["name"]="{$domainNameOld}/new";
	$domain=$api->updateDomain($domain["id"],$domain);
	echo "updated domain: {$domainNameOld}  to: + {$domain['name']}\n" ;

	// delete domain
	$api->deleteDomain($domain["id"]);
	echo "deleted {$domain['name']}\n";

	//==================================METDATA===================================

	echo "\n---Metadata---\n";

	// get all metadata
	$metadata=$api->listDomainMetadata();
	echo "found metadata for following domains: ";
	foreach ($metadata as $k=>$m) {
			if($k!=0){
					echo ", ";
			}
			echo $m["domain"];
	}
	echo "\n";

	// get metadata by domainid
	$metadata=$api->getDomainMetadata($domainID);
	echo "specific metadata found for: ".$metadata["domain"] . "\n";


	// get statistics for domains
	echo "these are the corresponding domain statistics: \n";
	$stats = $api->listDomainStats();
	print_r($stats);

	//===============================NOTIFICATIONS================================

	echo "\n--Notifications--\n";

	$notification = [
			"domain" => $domainID,
			"user" => "86976ebd-1f4a-4748-7ac4-bb655fd0a7af",
			"transport" => "mail",
			"blacklist" => 1,
			"defacement" => 1,
			"malware" => 1
	];

	// create a new notification 
	$notification=$api->createNotification($notification);
	echo "notification regarding domainID: {$domainID} has been created: ";
	if($notification["blacklist"]==1){
			echo "blacklist ";
	}
	if($notification["defacement"]==1){
			echo "defacement ";
	}
	if($notification["malware"]==1){
			echo "malware ";
	}
	echo "\n";

	// get notification by id
	$notification=$api->getNotification($notification["id"]);
	echo "found our notification again, id: {$notification["id"]} \n";

	// update the notification
	$notification["malware"]=0;
	$notification=$api->updateNotification($notification["id"], $notification);
	echo "updated malware to: " . $notification["malware"] . "\n";

	// get notifications for a domain
	$notifications = $api->getDomainNotifications($domainID);
	echo "found " . count($notifications) . " notification(s) for domain: " . $domainID . "\n";

	// delete the newly created notification
	$notification=$api->deleteNotification($notification["id"]);
	echo "deleted our notification again \n";

	// find all notifications
	$notifications = $api->listNotifications();
	echo "found " . count($notifications) . " notification(s) across all domains \n";

	//================================APPLICATIONS================================

	echo "\n--Applications--\n";

	$apps=$api->listApplicationsByDomain($domainID);
	echo "applications for domain {$domainID}: ";
	foreach ($apps as $k=>$a) {
			if($k!=0){
					echo ", ";
			}
			echo $a["name"];
	}
	echo "\n";

	//===============================DOMAIN-DISABLE===============================
	//TODO: y no workerino?
	// disable domain
	// $msg=$api->disableDomain($issue["domain"]);
	// echo $msg;

} catch (Exception $e) {
    echo "[x] an error occured: {$e->getMessage()}\n";
}

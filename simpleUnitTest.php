<?php

require_once 'vendor/autoload.php';
require_once 'config.apiconekta.php';
require_once 'vtapi/VtapiConnection.php';
require_once 'vtapi/VtapiContacts.php';
require_once 'conekta/ApiConektaCustomers.php';

function DPrint($variable)
{
    echo '<pre>';
    print_r($variable);
    echo '</pre>';
}


$connection = new VtapiConnection();
$sessionName = $connection->getSessionName();
$userId = $connection->getUserId();

if ($userId !== "") {
//    DPrint($userId);
    DPrint('success');
}else {
    DPrint('failure');
}

if ($sessionName !== "") {
//    DPrint($sessionName);
    DPrint('success');
}else {
    DPrint('failure');
}

$username = ConfigurationVtapi::$username;
try {
    ConfigurationVtapi::$username = "bad_username";
    $connection = new VtapiConnection();
    DPrint('failure');
} catch (Exception $e) {
    ConfigurationVtapi::$username = $username;
//    DPrint($e->getMessage());
    if ($e->getMessage() === "Error trying to generate the Token") {
        DPrint('success');
    }else{
        DPrint('failure');
    }
}

$accessKey = ConfigurationVtapi::$accessKey;

try {
    ConfigurationVtapi::$accessKey = "bad_access_key";
    $connection = new VtapiConnection();
    DPrint('failure');
} catch (Exception $e) {
    ConfigurationVtapi::$accessKey = $accessKey;
//    DPrint($e->getMessage());
    if ($e->getMessage() === "Error trying to generate the SessionName and UserId") {
        DPrint('success');
    }else{
        DPrint('failure');
    }
}

$contacts = new VtapiContacts();

try {
    $contacts->createNewContact('tester', 'admin','adm@adm.amd','1112223344');
    DPrint('success');
} catch (Exception $e) {
//    DPrint($e->getMessage());
    DPrint('failure');
}

try {
    $contacts->createNewContact('', '','','');
    DPrint('failure');
} catch (Exception $e) {
//    DPrint($e->getMessage());
    if ($e->getMessage() === "Unable to create new contact: MANDATORY_FIELDS_MISSING") {
        DPrint('success');
    }else{
        DPrint('failure');
    }
}

$CustomerConekta = new ApiConektaCustomers();

$customerId = "";

try {
    $customerId = $CustomerConekta->createCustomer('Unit Test', 'test@test.com', '1112223344');
    DPrint('success');
} catch (Exception $e) {
//    DPrint($e->getMessage());
    DPrint('failure');

}

try {
    $result = $CustomerConekta->customerExist($customerId);
    if ($result) {
        DPrint('success');
    }else{
        DPrint('failure');
    }
} catch (Exception $e) {
//    DPrint($e->getMessage());
    DPrint('failure');

}

try {
    $CustomerConekta->updateCustomer($customerId, 'Edit Test', 'test2@test.com', '1912823344');
    DPrint('success');
} catch (Exception $e) {
//    DPrint($e->getMessage());
    DPrint('failure');

}


ConfigApiConekta::$bearerToken = "badTokens987654321";
$CustomerConekta = new ApiConektaCustomers();

try {
    $customerId = $CustomerConekta->createCustomer('Unit Test', 'test@test.com', '1112223344');
    DPrint('failure');
} catch (Exception $e) {
//    DPrint($e->getMessage());
    if ($e->getMessage() === "Error al registrar en conekta") {
        DPrint('success');
    }else{
        DPrint('failure');
    }

}

try {
    $CustomerConekta->updateCustomer($customerId, 'Edit Test', 'test2@test.com', '1912823344');
    DPrint('failure');
} catch (Exception $e) {
//    DPrint($e->getMessage());
    if ($e->getMessage() === "Error al actualizar en conekta") {
        DPrint('success');
    }else{
        DPrint('failure');
    }

}






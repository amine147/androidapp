<?php
/**
 * Created by PhpStorm.
 * User: moncifbounif
 * Date: 15/03/2016
 * Time: 15:07
 */

require_once '../include/DbHandler.php';
require_once '../include/AlterStuff.php';
require_once '../include/PassHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

/**
 * User Registration
 * url - /register
 * method - POST
 * params - name, email, password
 */
$app->post('/register', function() use ($app) {
    $fct = new AlterStuff();
    $db = new DbHandler();

    $fct->verifyRequiredParams(array('nom','prenom','fonction','email','password'));
    $response = array();

    // reading post params
    $nom = $app->request->post('nom');
    $prenom = $app->request->post('prenom');
    $fonction = $app->request->post('fonction');
    $email = $app->request->post('email');
    $password = $app->request->post('password');

    // validating email address
    $fct->validateEmail($email);
 
    $res = $db->createUser($nom, $prenom, $fonction, $email, $password);

    if ($res == USER_CREATED_SUCCESSFULLY) {
        $status = 201;
        $response["error"] = false;
        $response["message"] = "Inscription avec succés";
    } else if ($res == USER_CREATE_FAILED) {
        $status = 200;
        $response["error"] = true;
        $response["message"] = "Oops! Erreur lors de l'inscription";
    } else if ($res == USER_ALREADY_EXISTED) {
        $status = 200;
        $response["error"] = true;
        $response["message"] = "Adresse mail déjà utilisée";     
    } 
    $fct-> echoRespnse(200, $response);
});



$app->post('/addTransaction', function() use ($app) {
    $fct = new AlterStuff();
    $db = new DbHandler();

    $fct->verifyRequiredParams(array('email','solde'));
    $response = array();

    // reading post params
    $email= $app->request->post('email');
    $solde = $app->request->post('solde');

    // validating email address
    $fct->validateEmail($email);
 
    $res = $db->addSolde($email, $solde);

    if ($res == true) {
        $status = 200;
        $response["error"] = false;
        $response["message"] = "Transaction réussie";
    } else{
        $status = 201;
        $response["error"] = true;
        $response["message"] = "Oops! Erreur lors de la transaction";
    } 
    $fct-> echoRespnse($status, $response);
});



$app->post('/login', function() use ($app){
    $fct = new AlterStuff();
    $db = new DbHandler();
    // check for required params
    $fct->verifyRequiredParams(array('email', 'password'));

    // reading post params
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $response = array();
    
    // check for correct email and password
    if ($db->checkLogin($email, $password)) {
        // get the user by email
        $user = $db->getUserByEmail($email);

        if ($user != NULL) {
            $response["error"] = false;
            $response['nom'] = $user['nom'];
            $response['email'] = $user['email'];
            $response['apiKey'] = $user['api_key'];
            $response['createdAt'] = $user['created_at'];
        } else {
            // unknown error occurred
            $response['error'] = true;
            $response['message'] = "Une erreur s'est produite, veuillez réessayer";
        }
    } else {
        // user credentials are wrong
        $response['error'] = true;
        $response['message'] = 'Verifiez votre adresse mail et mot de passe';
    }

    $fct->echoRespnse(200, $response);
});

$app->post('/getUser', function() use ($app){
    $fct = new AlterStuff();
    $db = new DbHandler();
    $response = array();
    // check for required params
    $fct->verifyRequiredParams(array('api_key'));
    $apikey = $app->request()->post('api_key');

    $idUser = $db->getUserId($apikey);
    $id = $idUser['id'];

    $user = $db->getUserById($id);

    if ($user != NULL) {
        $response["error"] = false;
        $response['nom'] = $user['nom'];
        $response['email'] = $user['email'];
        $response['apiKey'] = $user['api_key'];
        $response['createdAt'] = $user['created_at'];
    } else {
        // user credentials are wrong
        $response['error'] = true;
        $response['message'] = 'Aucun utilisateur avec cette clé API';
    }
    $fct->echoRespnse(200, $response);
});


$app->run();
?>
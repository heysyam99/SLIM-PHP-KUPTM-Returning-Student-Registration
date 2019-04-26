<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
use Slim\Http\UploadedFile;

// require_once '../include/DbHandler.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, POST, DELETE, OPTIONS");
header("Access-Control-Max-Age: 86400");
header("Access-Control-Allow-Headers: *");

// Function of comparing Integer number to be use

function intcmp($a,$b) {
    if((int)$a == (int)$b)return 0;
    if((int)$a  > (int)$b)return 1;
    if((int)$a  < (int)$b)return -1;
}

$app->group('/student', function () use ($app) {

    $app->post('/login', function (Request $request, Response $response, array $args) {

        $error = array();
     
        $input = $request->getParsedBody();
        $settings = $this->get('settings');
        // $sql = "SELECT id, ic FROM students WHERE id =:id";
        $sql = "SELECT id, ic FROM students WHERE id =:id AND ic =:ic";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("id", $input['id']);
        $sth->bindParam("ic", $input['ic']);
        $sth->execute();
        $user = $sth->fetchObject();

        // Start verifying user id 
        if(!$user) {
            $error[] = "Your student ID and Password did not match";
        }

        // End of Verifying student id 
        //                             
        // Start checking student debt status

        $query_1 = "SELECT * FROM debtor WHERE studentid =:studentid";
        $sth_2 = $this->db->prepare($query_1);
        $sth_2->bindParam("studentid", $input['id']);
        $sth_2->execute();
        $debt = $sth_2->fetchObject();

        if($debt) {
            $error[] = "You have a debt in your account";
        }

        // End of checking student debt status
        //
        // Start checking student status from table status

        $query_2 = "SELECT * FROM status WHERE studentid =:studentid";
        $sth_3 = $this->db->prepare($query_2);
        $sth_3->bindParam("studentid", $input['id']);
        $sth_3->execute();
        $pregStat = $sth_3->fetchObject();

        if($pregStat) {
            if(!intcmp('1',$pregStat->preregstatus)) {
                $error[] = "You did not complete your pre-registration";                 
            }
        }

        // End of checking student status from table status
        //
        // Start checking student result status

        $sth_4 = $this->db->prepare($query_2);
        $sth_4->bindParam("studentid", $input['id']);
        $sth_4->execute();
        $resStat = $sth_4->fetchObject();

        if($resStat) {

            $stat_1 = "DIS1";
            $stat_2 = "DIS2";
            $stat_3 = "DIS3";

            if(!strcmp($stat_1, $resStat->resultstat)) {
                $error[] = "Your result did not passed";

            } else if(!strcmp($resStat->resultstat, $stat_2)) {
                $error[] = "Your result did not passed";

            } else if(!strcmp($resStat->resultstat, $stat_3)) {
                $error[] = "Your result did not passed"; 
            }
        }

        // End of checking student result status
        //
        // Start checking student set A status

        $sth_5 = $this->db->prepare($query_2);
        $sth_5->bindParam("studentid", $input['id']);
        $sth_5->execute();
        $setA = $sth_5->fetchObject();

        if($setA) {

            if(!intcmp('1',$pregStat->setA)) {
                $error[] = "You did not submit your set A";
            }
        }

        // End of checking student set A status
        //
        // Start checking student Active or not

        $query_3 = "SELECT * FROM users WHERE id =:id";
        $sth_6 = $this->db->prepare($query_3);
        $sth_6->bindParam("id", $input['id']);
        $sth_6->execute();
        $active = $sth_6->fetchObject();

        if($active) {

            if(!intcmp('0',$active->active)) {
                $error[] = "Your account is not active";
            }
        }

        // End of checking student Activation
        //
        // Start checking for an error or else create a payload

        if(sizeof($error) >= 1) {
            return $this->response->withJson(['error' => true, 'message' => $error], 403)
                        ->withHeader('Content-type', 'application/json;charset=utf-8');
        } else {
            $query_4 = "SELECT * FROM feepaid WHERE studentid =:studentid";
            $sth_7 = $this->db->prepare($query_4);
            $sth_7->bindParam("studentid", $input['id']);
            $sth_7->execute();
            $image = $sth_7->fetchObject();
    
            $payload = array(
                "iat" => time(),
                "exp" => time() + 36000,
                "context" => [
                    "user" => [
                        "id" => $input['id']
                    ]
                ]
            );
        
            try {
                $token = JWT::encode(
                    $payload,                       // Data to be encoded in the jWT 
                    $settings['jwt']['secret'],     // Secret code 
                    "HS256");                      // Algorithm used to sign the token
            } catch (\Exception $e) {
                echo json_encode($e);
            }
    
            $image = "http://".$_SERVER['SERVER_NAME'].':8080/uploads/'.$image->filename;
        
            return $this->response->withJson(array('id'=>$user->id, 'ic'=>$user->ic, 'token'=>$token, "image"=>$image), 200)
                                  ->withHeader('Content-type', 'application/json')
                                  ->withAddedHeader('Authorization', $token);
        }
    
    });

    $app->get('/studentdetail/[{id}]', function (Request $request, Response $response, array $args) {

        $sth_1 = $this->db->prepare("SELECT name from users WHERE id =:id");
        $sth_1->bindParam("id", $args['id']);
        $sth_1->execute();
        $fetch_1 = $sth_1->fetchObject();

        $sth_2 = $this->db->prepare("SELECT id, ic, prgcode from students WHERE id =:id");
        $sth_2->bindParam("id", $args['id']);
        $sth_2->execute();
        $fetch_2 = $sth_2->fetchObject();

        return $this->response->withJson(array('name'=>$fetch_1->name, 'id'=>$fetch_2->id, 'ic'=>$fetch_2->ic, 'prgcode'=>$fetch_2->prgcode));

    });
    
    // End of route login
    //
    // Group all route for address

    $app->group('/address', function () use ($app) {

        $app->put('/permaddress/[{id}]', function ($request, $response, $args) {
    
            $jwt = $request->getHeaders();
    
            $settings = $this->get('settings');
        
            $uncodedToken = JWT::decode($jwt['HTTP_AUTHORIZATION'][0], $settings['jwt']['secret'], array('HS256')); // $token store the token of the user
        
            $expTime = $uncodedToken->exp;
        
            if($uncodedToken) {
                if(time() < $expTime) { // EXECUTE THE PROCESS IF THE JWT IS NOT EXPIRED
        
                    $input = $request->getParsedBody();
                    $sql = "UPDATE permadd SET phone =:phone, address1 =:address1, address2 =:address2 ,
                                    postcode =:postcode, city =:city, state =:state, country =:country WHERE id =:id";
                    $sth = $this->db->prepare($sql);
                    $sth->bindParam("id", $args['id']);
                    $sth->bindParam("phone", $input['phone']);
                    $sth->bindParam("address1", $input['address1']);
                    $sth->bindParam("address2", $input['address2']);
                    $sth->bindParam("postcode", $input['postcode']);
                    $sth->bindParam("city", $input['city']);
                    $sth->bindParam("state", $input['state']);
                    $sth->bindParam("country", $input['country']);
                    $sth->execute();
                
                    return $response->withJson($input, 200)->withStatus(200)->withHeader('Content-type', 'application/json');
                } else {
                    return $response->withStatus(404);
                }
            } else {
                return $response->withStatus(401);
            }
    
        });
    
        $app->put('/rentaddress/[{id}]', function ($request, $response, $args) {
        
            $jwt = $request->getHeaders();
    
            $settings = $this->get('settings');
        
            $uncodedToken = JWT::decode($jwt['HTTP_AUTHORIZATION'][0], $settings['jwt']['secret'], array('HS256')); // $token store the token of the user
    
            $expTime = $uncodedToken->exp;
        
            if($uncodedToken) {
                if(time() < $expTime) { // EXECUTE THE PROCESS IF THE JWT IS NOT EXPIRED
        
                    $input = $request->getParsedBody();
                    $sql = "UPDATE rentadd SET address1 =:address1, address2 =:address2,
                                    postcode =:postcode, city =:city, state =:state, country =:country WHERE id =:id";
                    $sth = $this->db->prepare($sql);
                    $sth->bindParam("id", $args['id']);
                    $sth->bindParam("address1", $input['address1']);
                    $sth->bindParam("address2", $input['address2']);
                    $sth->bindParam("postcode", $input['postcode']);
                    $sth->bindParam("city", $input['city']);
                    $sth->bindParam("state", $input['state']);
                    $sth->bindParam("country", $input['country']);
                    $sth->execute();
                
                    return $response->withJson($input, 200)->withStatus(200)->withHeader('Content-type', 'application/json');
                } else {
                    return $response->withStatus(404);
                }
            } else {
                return $response->withStatus(401);
            }
            
        });
    
        $app->get('/permaddress/[{id}]', function (Request $request, Response $response, array $args) {

            $sth = $this->db->prepare("SELECT * from permadd WHERE id =:id");
            $sth->bindParam("id", $args['id']);
            $sth->execute();
            $fetch = $sth->fetchObject();

            return $this->response->withJson(array('data'=>$fetch));

        });
        
        $app->get('/rentaladdress/[{id}]', function (Request $request, Response $response, array $args) {
        
            $sth = $this->db->prepare("SELECT * from rentadd WHERE id =:id");
            $sth->bindParam("id", $args['id']);
            $sth->execute();
            $fetch = $sth->fetchObject();
            return $this->response->withJson(array('data'=>$fetch));
        
        });
    });

    //  End of route address
    //
    //  Start of route finance

    $app->group('/finance', function () use ($app) {

        $app->post('/upload/{studentid}', function(Request $request, Response $response, $args) {

            $uploadedFiles = $request->getUploadedFiles();
            
            //  HANDLE SINGLE INPUT WITH SINGLE FILE UPLOAD
            $uploadedFile = $uploadedFiles['filename'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                
                $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
                
                //  CHANGE THE NAME OF THE FILE WITH STUDENT ID
                $filename = sprintf('%s.%0.8s', $args["studentid"], $extension);
                
                $directory = $this->get('settings')['upload_directory'];
                $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        
                // SAVE THE FILE INTO THE SERVER
                $sql = "UPDATE feepaid SET filename=:filename WHERE studentid=:studentid";
                $stmt = $this->db->prepare($sql);
                $params = [
                    ":studentid" => $args["studentid"],
                    ":filename" => $filename
                ];
                
                if($stmt->execute($params)){
                    $url = $request->getUri()->getBaseUrl()."/Upload/".$filename;
                    return $response->withJson(["status" => "success", "data" => $url], 200);
                } else {
                    return $response->withJson(["status" => "failed", "data" => "0"], 200);
                } 
            }
        });
    
        $app->put('/[{studentid}]', function ($request, $response, $args) {   
    
            $input = $request->getParsedBody();
            $sql = "UPDATE feepaid SET amount =:amount, refNumber =:refNumber, datePaid =:datePaid WHERE studentid =:studentid";
            $date = (new DateTime($input['datePaid']))->format('Y-m-d H:i:s');
            $sth = $this->db->prepare($sql);
            $sth->bindParam("studentid", $args['studentid']);
            $sth->bindParam("amount", $input['amount']);
            $sth->bindParam("refNumber", $input['refNumber']);
            $sth->bindParam("datePaid", $date);
            $sth->execute();
    
            return $response->withJson($input, 200)->withStatus(200)->withHeader('Content-type', 'application/json');
            
        });
    
        $app->get('/[{studentid}]', function ($request, $response, $args) {   
    
            $sth = $this->db->prepare("SELECT * from feepaid WHERE studentid =:studentid");
            $sth->bindParam("studentid", $args['studentid']);
            $sth->execute();
            $fetch = $sth->fetchObject();

            return $this->response->withJson(array('data'=>$fetch));
            
        });

        // FOR FUTURE USE
        // TO CHECK AN UPLOADED IMAGE ON THE SERVER
        //
        // START TO CHECK AN UPLOADED IMAGE ON THE SERVER

        $app->get('/image/[{studentid}]', function ($request, $response, $args) {   
    
            $sql = "SELECT filename FROM feepaid WHERE studentid =:studentid";
            $sth = $this->db->prepare($sql);
            $sth->bindParam("studentid", $input['id']);
            $sth->execute();
            $image = $sth->fetchObject();

            $image = "http://".$_SERVER['SERVER_NAME'].':8080/uploads/AM1705002120';

            return $this->response->withJson(array('data'=>$image));
        });

        $app->get('/upload/[{studentid}]', function ($request, $response, $args) {   
    
            $sth = $this->db->prepare("SELECT filename from feepaid WHERE studentid =:studentid");
            $sth->bindParam("studentid", $args['studentid']);
            $sth->execute();
            $fetch = $sth->fetchObject();
            return $this->response->withJson(array('data'=>$fetch));
            
        });

        // END OF CHECKING UPLOADED IMAGE ON THE SERVER

    });
});

// FOR FUTURE USE
// TO CHECK THE JWT TOKEN 
//

$app->get('/token', function (Request $request, Response $response, array $args) {

    $jwt = $request->getHeaders();  

    $settings = $this->get('settings'); // get settings array.
 
    $token = JWT::decode($jwt['HTTP_AUTHORIZATION'][0], $settings['jwt']['secret'], array('HS256')); // $token store the token of the user

    $expTime = $token->exp;

    if ($token) {
        if(time() < $expTime) {
            print_r(time());
            print_r("Space");
            print_r($expTime);
        } else {
            print_r("expired");
        }
    } else  {
        print_r ('FAILED');
    }
});

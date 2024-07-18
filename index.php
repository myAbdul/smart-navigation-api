<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require_once 'include/models/User.php';
require_once 'include/models/Facility.php';
require_once 'include/models/College.php';
require_once 'include/models/Department.php';
require_once 'include/models/Program.php';
require_once 'include/models/ClassSchedule.php';
require_once 'include/models/CampusEvent.php';
require_once 'include/models/Level.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$app->setBasePath('/smart-navigation-api');

$app->post('/register_user', function (Request $request, Response $response) {
    $request_body = $request->getParsedBody();

    $username = $request_body["username"];
    $password = $request_body["password"];
    $first_name = $request_body["first_name"];
    $last_name = $request_body["last_name"];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $user_model = new User();

    $user = $user_model->getUser($username);
    if ($user != null) {
        return $response->withStatus(422, "User already exists!");
    }

    $date_created = date("Y-m-d H:i:s");
    $user_data = array (
        'username' => $username,
        'password' => $hashed_password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'date_created' => $date_created
    );
    $user_id = $user_model->insertSingle($user_data);

    if ($user_id == 0) {
        return $response->withStatus(422, "Registration failed!");
    }

    $data = array ("user_id" => $user_id);
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/login', function (Request $request, Response $response) {
    $request_body = $request->getParsedBody();

    $username = $request_body["username"];
    $password = $request_body["password"];

    $user_model = new User();

    $user = $user_model->getUser($username);
    if ($user == null) {
        return $response->withStatus(401, "Login failed... User does not exist.");
    }

    if (password_verify($password, $user["password"])) {
        unset($user["password"]);
        unset($user["date_created"]);
        $payload = json_encode($user);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    } else {
        return $response->withStatus(401, "Login failed... Incorrect password.");
    }
});

$app->post('/add_facility', function (Request $request, Response $response) {
    $request_body = $request->getParsedBody();

    $name = $request_body["name"];
    $latitude = $request_body["latitude"];
    $longitude = $request_body["longitude"];
    $user_id = $request_body["user_id"];
    $image = $request_body["image"];

    $facility_model = new Facility();

    $date_created = date("Y-m-d H:i:s");
    $facility_data = array (
        'name' => $name,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'user_id' => $user_id,
        'date_created' => $date_created
    );
    $facility_id = $facility_model->insertSingle($facility_data);

    if ($facility_id == 0) {
        return $response->withStatus(422, "Adding facility failed!");
    }

    if($image!=null){
        $image_location = "images/facility_$facility_id.txt";
        $file = fopen($image_location, "w");
        if(!$file) {
            $response->withStatus(422, "Adding facility failed!");
        }
        fwrite($file, $image);
        fclose($file);

        $update_result = $facility_model->updateImageLocation($facility_id, $image_location);
        if(!$update_result){
            $response->withStatus(422, "Adding facility failed!");
        }
    }

    $data = array ("facility_id" => $facility_id);
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/get_all_facilities', function (Request $request, Response $response) {
    $facility_model = new Facility();

    $all_facilities = $facility_model->fetchAll();

    if ($all_facilities == null) {
        $all_facilities = array();
    }

    for ($i = 0; $i < count($all_facilities); $i++) {
        $file_location = $all_facilities[$i]["image_location"];
        $file = fopen($file_location, "r");
        $all_facilities[$i]["image"] = fread($file,filesize($file_location));
        fclose($file);
        unset($all_facilities[$i]["image_location"]);
    }
    
    $payload = json_encode($all_facilities);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/get_class_schedule_form_data', function (Request $request, Response $response) {
    $college_model = new College();
    $department_model = new Department();
    $program_model = new Program();
    $level_model = new Level();

    $all_colleges = $college_model->fetchAll();
    $all_departments = $department_model->fetchAll();
    $all_programs = $program_model->fetchAll();
    $all_levels = $level_model->fetchAll();
    $data = array (
        "all_colleges" => $all_colleges,
        "all_departments" => $all_departments,
        "all_programs" => $all_programs,
        "all_levels" => $all_levels
    );
    
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/add_class_schedule', function (Request $request, Response $response) {
    $request_body = $request->getParsedBody();

    $department_id = $request_body["department_id"];
    $program_name = $request_body["program_name"];
    $level_id = $request_body["level_id"];
    $course_name = $request_body["course_name"];
    $day = $request_body["day"];
    $time = $request_body["time"];
    $user_id = $request_body["user_id"];

    $program_model = new Program();
    $program = $program_model->getProgram($department_id, $program_name);
    if(!$program){
        $program_data = array(
            "department_id" => $department_id,
            "name" => $program_name
        );
        $program_id = $program_model->insertSingle($program_data);
    } else {
        $program_id = $program["program_id"];
    }

    if ($program_id == 0) {
        return $response->withStatus(401, "Adding class schedule failed!");
    }

    $date_created = date("Y-m-d H:i:s");
    $class_schedule_model = new ClassSchedule();
    $class_schedule_data = array(
        "program_id" => $program_id,
        "level_id" => $level_id,
        "course_name" => $course_name,
        "day" => $day,
        "time" => $time,
        "user_id" => $user_id,
        "date_created" => $date_created
    );

    $class_schedule_id = $class_schedule_model->insertSingle($class_schedule_data); 
    if ($class_schedule_id == 0) {
        return $response->withStatus(401, "Adding class schedule failed!");
    } else {
        $payload = json_encode(array("class_schedule_id" => $class_schedule_id));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    } 
});

$app->get('/get_class_schedules', function (Request $request, Response $response, array $args) {
    $queryParams = $request->getQueryParams();
    $program_id = $queryParams["program_id"];
    $level_id = $queryParams["level_id"];

    $class_schedule_model = new ClassSchedule();
    $class_schedules = $class_schedule_model->fetchAll("program_id=$program_id and level_id=$level_id");
    
    $payload = json_encode($class_schedules);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/add_campus_event', function (Request $request, Response $response) {
    $request_body = $request->getParsedBody();

    $name = $request_body["name"];
    $date = $request_body["date"];
    $time = $request_body["time"];
    $facility_id = $request_body["facility_id"];
    $user_id = $request_body["user_id"];

    $date_created = date("Y-m-d H:i:s");
    $campus_event_model = new CampusEvent();
    $data = array(
        "name" => $name,
        "date" => $date,
        "time" => $time,
        "facility_id" => $facility_id,
        "user_id" => $user_id,
        "date_created" => $date_created
    );

    $campus_event_id = $campus_event_model->insertSingle($data); 
    if ($campus_event_id == 0) {
        return $response->withStatus(401, "Adding campus event failed!");
    } else {
        $payload = json_encode(array("campus_event_id" => $campus_event_id));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    } 
});

$app->get('/get_campus_events', function (Request $request, Response $response, array $args) {
    $campus_event_model = new CampusEvent();
    $campus_events = $campus_event_model->getCampusEvents();
    
    $payload = json_encode($campus_events);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
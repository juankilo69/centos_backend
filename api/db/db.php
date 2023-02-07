<?php

//Guardo los datos de conexión de la base de datos en un fichero XML
function getDBConfig() {
    $dbFileConfig=dirname(__FILE__)."/../../config/dbconfiguration.xml";

	$config = new DOMDocument();
	$config->load($dbFileConfig);

	$datos = simplexml_load_file($dbFileConfig);	
	$ip = $datos->xpath("//ip");
	$name = $datos->xpath("//dbname");
	$user = $datos->xpath("//user");
	$pass = $datos->xpath("//pass");	
	$cad = sprintf("mysql:dbname=%s;host=%s;charset=UTF8", $name[0], $ip[0]);
    $result = array(
        "cad" => $cad,
        "user" => $user[0],
        "pass" => $pass[0]
    );

	return $result;
}

function getDBConnection() {
    try {
        $res = getDBConfig();

        $bd = new PDO($res["cad"], $res["user"], $res["pass"]);

        return $bd;
    } catch(PDOException $e) {
        return null;
    }
}

/* ------------ LOGIN --------------- */
function checkLogin($email, $password) {
    try {
    	$bd = getDBConnection();

        if(!is_null($bd)) {
            $sqlPrepared = $bd->prepare("SELECT email from user WHERE email = :email AND password = :password " );
            $params = array(
                ':email' => $email,
                ':password' => $password
            );
            $sqlPrepared->execute($params);

            return $sqlPrepared->rowCount() > 0 ? true : false;
         } else
            return $bd;

    } catch (PDOException $e) {
       return null;
    }
}


/* ------------ PELÍCULAS  --------------- */
function getFilmsDB() {
    try {
    	$bd = getDBConnection();

        if(!is_null($bd)) {
            $sqlPrepared = $bd->prepare("SELECT id,name, director, classification from film");
            $sqlPrepared->execute();

            return $sqlPrepared->fetchAll(PDO::FETCH_ASSOC);
        } else
            return $bd;

    } catch (PDOException $e) {
       return null;
    }
}

function getFilmDB($id) {
    try {
    	$bd = getDBConnection();

        if(!is_null($bd)) {
            $sqlPrepared = $bd->prepare("SELECT * from film WHERE id = :id");
            $params = array(
                ':id' => $id,
            );
            $sqlPrepared->execute($params);

            return $sqlPrepared->fetchAll(PDO::FETCH_ASSOC);
        } else
            return $bd;

    } catch (PDOException $e) {
       return null;
    }
}

function addFilmDB($data) {
    try {
    	$bd = getDBConnection();

        if(!is_null($bd)) {

            $sqlPrepared = $bd->prepare("
                INSERT INTO film (name,director,classification,img,plot)
                VALUES (:name,:director,:classification,:img,:plot)
            ");

            $params = array(
                ':name' => $data["name"],
                ':director' => $data["director"],
                ':classification' => $data["classification"],
                ':img' => $data["img"],
                ':plot' => $data["plot"]
            );

            return $sqlPrepared->execute($params);

            return $sqlPrepared->rowCount();// check affected rows using rowCount

        } else
            return $bd;

    } catch (PDOException $e) {
        echo $e->getMessage();
       return null;
    }
}
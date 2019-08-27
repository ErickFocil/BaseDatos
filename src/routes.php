<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    	$container = $app->getContainer();
	date_default_timezone_set("America/Mexico_City");

	$app->get('/', function (Request $request, Response $response, array $args) use ($container) {
		
		$json = array(
			"status" => "success",
			"message" => "Hola mundo"
		);
	
		return $this->response->withJson($json,200);
	});

	$app->get('/listproducts', function (Request $request, Response $response, array $args) use ($container) {
		$data = array();
		try{
			$stm = $this->db->prepare("SELECT * FROM productos");
			$stm->execute();
			$result = $stm->fetchAll();
			
			if(count($result) == 0){
				$json = array(
					"status" => "error",
					"messaje" => "No hay datos en la base de datos"
				);
			}else{
				$json = array(
					"status" => "success",
					"data" => $result
				);
			}
		}catch(PDOException $e){

		}
		

		/*if( count($data) > 0){
			$json = array(
				"status" => "success",
				"data" => $data
			);
		}else{
			$json = array(
				"status" => "error",
				"message" => "No hay productos disponibles"
			);
		}*/

		return $this->response->withJson($json,200);
	});

	$app->get('/convertdate', function (Request $request, Response $response, array $args) use ($container) {
		$get = $request->getQueryParams();
            if(!empty($get['date'])){
			$date_convert = $get['date'];
		}else{
                  $json = array( "status" => "error", "message" => "El campo fecha es necesario"); 
                  return $this->response->withJson($json,200);
            }

            $json = convertDateSpanish($date_convert);
		return $this->response->withJson($json,200);
	});

	function convertDateSpanish($date) {
		$dia = explode("-", $date, 3);
		$year = $dia[0];
		$month = (string)(int)$dia[1];
		$day = (string)(int)$dia[2];

		$dias = array("domingo","lunes","martes","miércoles" ,"jueves","viernes","sábado");
		$tomadia = $dias[intval((date("w",mktime(0,0,0,$month,$day,$year))))];

		$meses = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");

		return $tomadia." - ".$day." de ".$meses[$month]." de ".$year;
	}
};
<?php

//================================================================== constantes
define('REMOTO_IP_SERVIDOR','192.168.27.37');
define('REMOTO_USUARIO','root');
define('REMOTO_PASSWORD','');
define('REMOTO_NOMBRE_BASE_DATOS', 'cobro_buses_origen');


define('LOCAL_IP_SERVIDOR','localhost');
define('LOCAL_USUARIO','root');
define('LOCAL_PASSWORD','');
define('LOCAL_NOMBRE_BASE_DATOS', 'cazabaches');


//======================================================================================== variables globales
$connRemota = "";
$connLocal = "";

//========================================================================================= funciones
function RemotoInstanciarConexion()
{
	global $connRemota;
	$connRemota = mysqli_connect(REMOTO_IP_SERVIDOR, REMOTO_USUARIO, REMOTO_PASSWORD, REMOTO_NOMBRE_BASE_DATOS);
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Conexion Fallida: %s\n", mysqli_connect_error());
		exit();
	}
}
function RemotoInstanciarConexionRetornaRespuesta()
{
	global $connRemota;
	$connRemota = mysqli_connect(REMOTO_IP_SERVIDOR, REMOTO_USUARIO, REMOTO_PASSWORD, REMOTO_NOMBRE_BASE_DATOS);
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Conexion Fallida: %s\n", mysqli_connect_error());
		//exit();
		return(0);
	}
	return (1);
}
function LocalInstanciarConexion()
{
	global $connLocal;
	$connLocal = mysqli_connect(LOCAL_IP_SERVIDOR, LOCAL_USUARIO, LOCAL_PASSWORD, LOCAL_NOMBRE_BASE_DATOS);
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Conexion Fallida: %s\n", mysqli_connect_error());
		exit();
	}
}





function LocalExecuteQuery($consultaSql)
{	
	global  $connLocal;                                                                                                                                           ;
	$r = array();
	if ($result = mysqli_query($connLocal, $consultaSql)) {
		switch ($result)
		{
			case '1':
				//insert,update, ....
				return ($result);
				break;
			default;
				// select.
				/* fetch associative array */
				while ($row = mysqli_fetch_assoc($result)) {
					$r[] = $row; 
				}
				/* free result set */
				mysqli_free_result($result);
				return ($r);
				break;
		}
	}
	else
	{
	  return (0);
	}	
}

function executeQueryLocal($query){

$conexion = mysql_connect(LOCAL_IP_SERVIDOR,LOCAL_USUARIO,LOCAL_PASSWORD );  
  mysql_select_db(LOCAL_NOMBRE_BASE_DATOS);  
  $result = mysql_query ($query, $conexion);
  $r = array();
  while ($row = @mysql_fetch_array ($result, MYSQL_ASSOC)) {
      $r[] = $row;
  }
  return $r;
}

function RemotoExecuteQuery($consultaSql)
{	
	global $connRemota;
	//echo "REMOTO-Ejecutar --> ".$consultaSql."</br>";
	$r = array();
	if ($result = mysqli_query($connRemota, $consultaSql)) {
		switch ($result)
		{
			case '1':
				//insert,update, ....
				return ($result);
				break;
			default;
				// select.
				/* fetch associative array */
				while ($row = mysqli_fetch_assoc($result)) {
					$r[] = $row; 
				}
				/* free result set */
				mysqli_free_result($result);
				return ($r);
				break;
		}
	}
	else
	{
	  return (0);
	}
	
}

function doGenerarJsonRespuesta($arrayRespuesta, $IndiceInicio, $CantidadRegistros)
{
	switch($arrayRespuesta)
	{
		case 0:
			echo json_encode(array("success" => false));
		break;
		case 1:
			echo json_encode(array("success" => true));
		break;
		default:
			list($nLen, $aData) = array(sizeof($arrayRespuesta), array_slice($arrayRespuesta, $IndiceInicio, $CantidadRegistros));
			echo json_encode(array("success" => true, "resultTotal" => $nLen, "resultRoot" => $aData));
		break;
	}

}
function LocalCerrarConexion()
{
	global $connLocal; 
	mysqli_close($connLocal);
}


function RemotoCerrarConexion()
{
	global $connRemota;
	mysqli_close($connRemota);
}
function RemotoTransactionSql($arrayConsultasSql)
{
	$respuestaMySqlGlobal = 1;//operacion exitosa en MySql
	$existeConexionRemota = RemotoInstanciarConexionRetornaRespuesta();
	if(!$existeConexionRemota)
	{
		return 0;
	}
	RemotoExecuteQuery("SET AUTOCOMMIT=0");
	RemotoExecuteQuery("START TRANSACTION" );
	

	foreach ($arrayConsultasSql as $elemento) {
		//echo "<font color=red>".$elemento."</font>";
		$respuestaMySqlIndividual = RemotoExecuteQuery($elemento);
		if($respuestaMySqlIndividual == 0)
		{
			$respuestaMySqlGlobal = 0;
		}
	}

	if($respuestaMySqlGlobal == 1)
	{
		RemotoExecuteQuery("COMMIT" );
	}
	else
	{
		RemotoExecuteQuery("ROLLBACK");
	}
	RemotoCerrarConexion();
	return($respuestaMySqlGlobal);
}



function RemotoSqlInsertRetornaIdGenerado($consultaSqlInsert, $NombreTabla)
{	
	global $connRemota;
	$IdGenerado = -1;
	if ($result = mysqli_query($connRemota, $consultaSqlInsert)) {
		
		$queryGrupo="SELECT LAST_INSERT_ID() as ULTIMO_ID_INGRESADO_CONEXION_ACTUAL
					FROM ".$NombreTabla."  
					LIMIT 1";
		
		if ($result = mysqli_query($connRemota, $queryGrupo)) {

			/* fetch associative array */
			while ($row = mysqli_fetch_assoc($result)) {
				$IdGenerado = $row["ULTIMO_ID_INGRESADO_CONEXION_ACTUAL"];
			}
			/* free result set */
			mysqli_free_result($result);
			return ($IdGenerado);
		}
		else
		{
			return (-1);
		}
		
	}
	else
	{
	  return (-2);
	}
	
}

function LocalSqlInsertRetornaIdGenerado($consultaSqlInsert, $NombreTabla)
{	
	global $connLocal;
	$IdGenerado = -1;
	//echo "</br><font color='brown'>".$consultaSqlInsert."</font></br>";
	if ($result = mysqli_query($connLocal, $consultaSqlInsert)) {
		
		$queryGrupo="SELECT LAST_INSERT_ID() as ULTIMO_ID_INGRESADO_CONEXION_ACTUAL
					FROM ".$NombreTabla."  
					LIMIT 1";
		
		if ($result = mysqli_query($connLocal, $queryGrupo)) {

			/* fetch associative array */
			while ($row = mysqli_fetch_assoc($result)) {
				$IdGenerado = $row["ULTIMO_ID_INGRESADO_CONEXION_ACTUAL"];
			}
			/* free result set */
			mysqli_free_result($result);
			return ($IdGenerado);
		}
		else
		{
			return (-1);
		}
		
	}
	else
	{
	  return (-2);
	}
	
}

?>

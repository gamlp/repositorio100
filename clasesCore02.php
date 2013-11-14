<?php
require('dbVersion02.php');
LocalInstanciarConexion();
session_start();
//==========================================================================================================clase caso inicio 
class Caso {
	protected $CASO_ID				="";
	protected $CASO_ACT_ID          ="";
	protected $CASO_USUARIO_ID      ="";
	protected $CASO_PRC_ID          ="";
	protected $CASO_CODIGO          ="";
	protected $CASO_SOLICITANTE     ="";
	protected $CASO_CI              ="";
	protected $CASO_DETALLE         ="";
	protected $CASO_ASUNTO          ="";
	protected $CASO_NRO_HOJA        ="";
	protected $CASO_DESCRIPCION     ="";
	protected $CASO_PROVEIDO        ="";
	protected $CASO_ACTUACION       ="";
	protected $CASO_VENTANILLA      ="";
	protected $CASO_OPERADOR        ="";
	protected $CASO_NODO_DESTINO    ="";
	protected $CASO_NODO_ORIGEN     ="";
	protected $CASO_NODO_ACTUAL     ="";
	protected $CASO_REGISTRADO      ="";
	protected $CASO_MODIFICADO      ="";
	protected $CASO_USUARIO         ="";
	protected $CASO_ESTADO          ="";
	
	//auxiliares
	public $PRC_REPOSITORIO_NOMBRE	="";

	// Constructor function
	public function __construct() {
		
		$this->CASO_ID          	= isset($_REQUEST["CASO_ID"]			)?$_REQUEST["CASO_ID"]				:"0";
		$this->CASO_ACT_ID          = isset($_REQUEST["CASO_ACT_ID"]		)?$_REQUEST["CASO_ACT_ID"]			:"0";
		$this->CASO_USUARIO_ID      = isset($_REQUEST["CASO_USUARIO_ID"]	)?$_REQUEST["CASO_USUARIO_ID"]		:"0";
		$this->CASO_PRC_ID          = isset($_REQUEST["H_DATO_ID"]			)?$_REQUEST["H_DATO_ID"]			:"0";
		$this->CASO_CODIGO          = isset($_REQUEST["CASO_CODIGO"]		)?$_REQUEST["CASO_CODIGO"]			:"0";
		$this->CASO_SOLICITANTE     = isset($_REQUEST["CASO_SOLICITANTE"]	)?$_REQUEST["CASO_SOLICITANTE"]		:"0";
		$this->CASO_CI              = isset($_REQUEST["CASO_CI"]			)?$_REQUEST["CASO_CI"]				:"0";
		$this->CASO_DETALLE         = isset($_REQUEST["CASO_DETALLE"]		)?$_REQUEST["CASO_DETALLE"]			:"0";
		$this->CASO_ASUNTO          = isset($_REQUEST["CASO_ASUNTO"]		)?$_REQUEST["CASO_ASUNTO"]			:"0";
		$this->CASO_NRO_HOJA        = isset($_REQUEST["CASO_NRO_HOJA"]		)?$_REQUEST["CASO_NRO_HOJA"]		:"0";
		$this->CASO_DESCRIPCION     = isset($_REQUEST["CASO_DESCRIPCION"]	)?$_REQUEST["CASO_DESCRIPCION"]		:"0";
		$this->CASO_PROVEIDO        = isset($_REQUEST["CASO_PROVEIDO"]		)?$_REQUEST["CASO_PROVEIDO"]		:"0";
		$this->CASO_ACTUACION       = isset($_REQUEST["CASO_ACTUACION"]		)?$_REQUEST["CASO_ACTUACION"]		:"0";
		$this->CASO_VENTANILLA      = isset($_REQUEST["CASO_VENTANILLA"]	)?$_REQUEST["CASO_VENTANILLA"]		:"0";
		$this->CASO_OPERADOR        = isset($_REQUEST["CASO_OPERADOR"]		)?$_REQUEST["CASO_OPERADOR"]		:"0";
		$this->CASO_NODO_DESTINO    = isset($_REQUEST["CASO_NODO_DESTINO"]	)?$_REQUEST["CASO_NODO_DESTINO"]	:"0";
		$this->CASO_NODO_ORIGEN     = isset($_REQUEST["CASO_NODO_ORIGEN"]	)?$_REQUEST["CASO_NODO_ORIGEN"]		:"0";
		$this->CASO_NODO_ACTUAL     = isset($_REQUEST["CASO_NODO_ACTUAL"]	)?$_REQUEST["CASO_NODO_ACTUAL"]		:"0";
		$this->CASO_REGISTRADO      = isset($_REQUEST["CASO_REGISTRADO"]	)?$_REQUEST["CASO_REGISTRADO"]		:"0";
		$this->CASO_MODIFICADO      = isset($_REQUEST["CASO_MODIFICADO"]	)?$_REQUEST["CASO_MODIFICADO"]		:"0";
		$this->CASO_USUARIO         = isset($_REQUEST["CASO_USUARIO"]		)?$_REQUEST["CASO_USUARIO"]			:"0";
		$this->CASO_ESTADO          = isset($_REQUEST["CASO_ESTADO"]		)?$_REQUEST["CASO_ESTADO"]			:"0";
		
		try {
			$this->CASO_REGISTRADO = strftime( "%Y-%m-%d-%H-%M-%S", time() ); 
			$this->CASO_MODIFICADO = strftime( "%Y-%m-%d-%H-%M-%S", time() );      
			$this->CASO_ESTADO = 'ACTIVO';
			
			$this->CASO_USUARIO_ID      = isset($_SESSION["CASO_USUARIO_ID"]	)?$_SESSION["CASO_USUARIO_ID"]		:"0";
			$this->CASO_NODO_DESTINO    = isset($_SESSION["CASO_NODO_DESTINO"]	)?$_SESSION["CASO_NODO_DESTINO"]	:"1";
			$this->CASO_NODO_ORIGEN     = isset($_SESSION["CASO_NODO_ORIGEN"]	)?$_SESSION["CASO_NODO_ORIGEN"]		:"1";
			$this->CASO_NODO_ACTUAL     = isset($_SESSION["CASO_NODO_ACTUAL"]	)?$_SESSION["CASO_NODO_ACTUAL"]		:"1";
		
		} catch (Exception $e) {
			echo null;
		}
	}
	
	public function doNuevoCaso()
	{
		$query="SELECT * FROM TRS_PROCESOS WHERE PRC_ID='".$this->CASO_PRC_ID."'";
        $respuesta3=LocalExecuteQuery($query);
        $gestion=$respuesta3[0]['PRC_GESTION'];
        $Correlativo=$respuesta3[0]['PRC_CORRELATIVO'];
        $Correlativo = $Correlativo + 1;
        $this->CASO_CODIGO = $Correlativo."/".$gestion;
		
		$this->PRC_REPOSITORIO_NOMBRE = $respuesta3[0]['PRC_REPOSITORIO_NOMBRE'];
	
	
        $consulta="SELECT a.ACT_ID 
					FROM TRS_ACTIVIDADES a INNER JOIN TRS_TIPOS_ACTIVIDADES b ON a.ACT_TA_ID = b.TA_ID
                    AND a.ACT_PRC_ID = '".$this->CASO_PRC_ID."' AND b.TA_NOMBRE='INICIAL'";	
        $respuesta1=LocalExecuteQuery($consulta);
        $sCASO_ACT_ID=$respuesta1[0]['ACT_ID'];

        $consultaSql1 = "INSERT INTO TRS_CASOS( CASO_ACT_ID ,  CASO_USUARIO_ID ,  CASO_PRC_ID ,  CASO_CODIGO ,  CASO_SOLICITANTE ,  CASO_CI ,  CASO_DETALLE ,  CASO_ASUNTO ,  CASO_NRO_HOJA , 
												CASO_DESCRIPCION ,  CASO_PROVEIDO ,  CASO_ACTUACION ,  CASO_VENTANILLA ,  CASO_OPERADOR ,  CASO_NODO_DESTINO ,  CASO_NODO_ORIGEN ,
												CASO_NODO_ACTUAL ,  CASO_REGISTRADO ,  CASO_MODIFICADO ,  CASO_USUARIO ,  CASO_ESTADO ) VALUES 
												(
												 '".$this->CASO_ACT_ID           ."',
												 '".$this->CASO_USUARIO_ID       ."',
												 '".$this->CASO_PRC_ID           ."',
												 '".$this->CASO_CODIGO           ."',
												 '".$this->CASO_SOLICITANTE      ."',
												 '".$this->CASO_CI               ."',
												 '".$this->CASO_DETALLE          ."',
												 '".$this->CASO_ASUNTO           ."',
												 '".$this->CASO_NRO_HOJA         ."',
												 '".$this->CASO_DESCRIPCION      ."',
												 '".$this->CASO_PROVEIDO         ."',
												 '".$this->CASO_ACTUACION        ."',
												 '".$this->CASO_VENTANILLA       ."',
												 '".$this->CASO_OPERADOR         ."',
												 '".$this->CASO_NODO_DESTINO     ."',
												 '".$this->CASO_NODO_ORIGEN      ."',
												 '".$this->CASO_NODO_ACTUAL      ."',
												 '".$this->CASO_REGISTRADO       ."',
												 '".$this->CASO_MODIFICADO       ."',
												 '".$this->CASO_USUARIO          ."',
												 '".$this->CASO_ESTADO           ."')";
        $CASO_ID = LocalSqlInsertRetornaIdGenerado($consultaSql1,"TRS_CASOS");
		$this->CASO_ID = $CASO_ID;
		//update procesos
		$query2="UPDATE TRS_PROCESOS SET PRC_CORRELATIVO='$Correlativo' WHERE PRC_ID='".$this->CASO_PRC_ID."'";
        $respuesta7=LocalExecuteQuery($query2);
	}
	
	public function doInsertRepositorio()
	{
        
		$consultaSql4 = "INSERT INTO"." ".$this->PRC_REPOSITORIO_NOMBRE." (RT_CASO_ID,RT_REGISTRADO,RT_MODIFICADO,RT_USUARIO,RT_ESTADO) 
						VALUES(					'".$this->CASO_ID		        ."',
												'".$this->CASO_REGISTRADO       ."',
												'".$this->CASO_MODIFICADO       ."',
												'".$this->CASO_USUARIO          ."',
												'".$this->CASO_ESTADO           ."')";
						
		$respuesta4=LocalExecuteQuery($consultaSql4);
	}
	
}
//==========================================================================================================clase caso fin 


try {
	$objetoCaso = new Caso();
	$objetoCaso->doNuevoCaso();
	$objetoCaso->doInsertRepositorio();
	} catch (Exception $e) {
		echo null;
	}
LocalCerrarConexion();
?>
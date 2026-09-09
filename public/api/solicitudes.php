<?php
// public/api/solicitudes.php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_CONTROLADOR . "/SolicitudController.php";

session_start();

$controlador = new SolicitudController();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
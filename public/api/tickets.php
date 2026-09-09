<?php
// public/api/tickets.php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_CONTROLADOR . "/TicketController.php";

session_start();

$controlador = new TicketController();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);

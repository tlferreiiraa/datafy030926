<?php
// app/controlador/SolicitudController.php

require_once RUTA_MODELO . "/ConectarPDO.php";
require_once RUTA_MODELO . "/SolicitudDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

/**
 * Controlador unificado de Solicitud. Captura las peticiones del cliente
 * a traves del metodo gestionar() y delega segun el metodo HTTP recibido.
 */
class SolicitudController
{
    public function gestionar(string $metodo): void
    {
        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            default => RespuestaJson::error("Metodo no soportado", 405)
        };
    }

    private function conectar(): PDO
    {
        $conectorPDO = new ConectorPDO(
            $_ENV["DB_HOST"],
            $_ENV["DB_USUARIO"],
            $_ENV["DB_CLAVE"],
            $_ENV["DB_NOMBRE"]
        );

        $conexion = $conectorPDO->establecerConexion();

        if ($conexion === null) {
            RespuestaJson::error("Error de conexion a la base de datos", 500);
        }

        return $conexion;
    }

    private function listar(): void
    {
        $conexion = $this->conectar();

        $solicitudDAO = new SolicitudDAO($conexion);
        $solicitudes = $solicitudDAO->listarSolicitudes();

        RespuestaJson::exito($solicitudes);
    }

    private function alta(): void
    {
        $datos = json_decode(file_get_contents("php://input"), true);

        $fechaSolicitada = $datos["fechaSolicitada"] ?? "";
        $descripcion = trim($datos["descripcion"] ?? "");

        if ($fechaSolicitada === "" || $descripcion === "") {
            RespuestaJson::error("Faltan campos obligatorios", 400);
        }

        $conexion = $this->conectar();
        $solicitudDAO = new SolicitudDAO($conexion);

        $registroCorrecto = $solicitudDAO->registrarSolicitud(
            $fechaSolicitada,
            $descripcion
        );

        if ($registroCorrecto) {
            RespuestaJson::exito(["mensaje" => "Solicitud registrada correctamente"], 201);
        } else {
            RespuestaJson::error("No se pudo registrar la solicitud", 500);
        }
    }
}
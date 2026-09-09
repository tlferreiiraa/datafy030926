<?php
// app/controlador/TicketController.php

require_once RUTA_MODELO . "/ConectarPDO.php";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

/**
 * Controlador unificado de Ticket. Captura las peticiones del cliente
 * a traves del metodo gestionar() y delega segun el metodo HTTP recibido.
 */
class TicketController
{
    public function gestionar(string $metodo): void
    {
        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            default => RespuestaJson::error("Metodo no soportado", 405)
        };
    }

    /**
     * Centraliza la conexion a la base de datos, usando las variables de entorno.
     */
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

        $ticketDAO = new TicketDAO($conexion);
        $tickets = $ticketDAO->listarTickets();

        RespuestaJson::exito($tickets);
    }

    private function alta(): void
    {
        // Como ahora la peticion llega como JSON y no como formulario,
        // leemos la peticion en vez de usar $_POST
        $datos = json_decode(file_get_contents("php://input"), true);

        $estudianteACargo = trim($datos["estudiante_a_cargo"] ?? "");
        $horaDeEntrada = $datos["hora_de_entrada"] ?? "";
        $horaDeSalida = $datos["hora_de_salida"] ?? "";
        $tipoDeSalon = $datos["tipo_de_salon"] ?? "";
        $numeroDeSalon = $datos["numero_de_salon"] ?? "";
        $numeroDeEquipo = $datos["numero_de_equipo"] ?? "";
        $asignatura = trim($datos["asignatura"] ?? "");
        $grupo = trim($datos["grupo"] ?? "");
        $turno = $datos["turno"] ?? "";
        $estado = trim($datos["estado"] ?? "");

        if (
            $estudianteACargo === "" ||
            $horaDeEntrada === "" ||
            $horaDeSalida === "" ||
            $tipoDeSalon === "" ||
            $numeroDeSalon === "" ||
            $numeroDeEquipo === "" ||
            $asignatura === "" ||
            $grupo === "" ||
            $turno === "" ||
            $estado === ""
        ) {
            RespuestaJson::error("Faltan campos obligatorios", 400);
        }

        $conexion = $this->conectar();
        $ticketDAO = new TicketDAO($conexion);

        $registroCorrecto = $ticketDAO->registrarTickets(
            $estudianteACargo,
            $horaDeEntrada,
            $horaDeSalida,
            $tipoDeSalon,
            (int) $numeroDeSalon,
            (int) $numeroDeEquipo,
            $asignatura,
            $grupo,
            $turno,
            $estado
        );

        if ($registroCorrecto) {
            RespuestaJson::exito(["mensaje" => "Ticket registrado correctamente"], 201);
        } else {
            RespuestaJson::error("No se pudo registrar el ticket", 500);
        }
    }
}
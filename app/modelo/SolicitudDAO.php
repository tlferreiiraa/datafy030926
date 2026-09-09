<?php
// app/modelo/SolicitudDAO.php

/**
 * Clase encargada de unificar la comunicacion a la base de datos
 * sobre la entidad Solicitud (antes separada en AltaDatosSolicitud y AccesoDatosSolicitudes).
 */
class SolicitudDAO
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Registra una nueva solicitud de servicio en la base de datos.
     */
    public function registrarSolicitud(
        string $fechaSolicitada,
        string $descripcion
    ): bool {

        try {

            $sql = "INSERT INTO SOLICITUD 
                    (fecha_Solicitada, descripcion)
                    VALUES 
                    (:fecha_Solicitada, :descripcion)";

            $consulta = $this->conexion->prepare($sql);

            $consulta->execute([
                "fecha_Solicitada" => $fechaSolicitada,
                "descripcion" => $descripcion
            ]);

            return true;

        } catch (PDOException $error) {

            echo "ERROR SQL: " . $error->getMessage();
            exit;
        }
    }

    /**
     * Devuelve el listado completo de solicitudes con los datos del usuario que las pidio.
     */
    public function listarSolicitudes(): array
    {
        $sql = "
            SELECT
                s.id_solicitud,
                s.descripcion,
                s.fecha_solicitada,
                u.nombre,
                u.apellido

            FROM SOLICITUD AS s

            INNER JOIN PIDE AS p
                ON p.id_solicitud = s.id_solicitud

            INNER JOIN USUARIO AS u
                ON u.documento_identidad = p.documento_identidad

            ORDER BY s.fecha_solicitada DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $consulta = null;

        return $solicitudes;
    }
}
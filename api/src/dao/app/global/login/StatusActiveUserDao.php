<?php

namespace tezlikv3\dao;

use tezlikv3\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class StatusActiveUserDao
{
  private $logger;
  private const MAX_RETRIES = 3;
  private const RETRY_DELAY_MS = 100;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  /**
   * Buscar estado de sesión de un usuario
   * @param int $id_user ID del usuario
   * @return array|null Estado de sesión
   */
  public function findSessionUser($id_user)
  {
    try {
      $connection = Connection::getInstance()->getConnection();

      $sql = "SELECT session_active FROM users WHERE id_user = :id_user";

      $stmt = $connection->prepare($sql);
      $stmt->execute(['id_user' => $id_user]);
      return $stmt->fetch($connection::FETCH_ASSOC);
    } catch (\PDOException $e) {
      error_log("❌ Error en findSessionUser: " . $e->getMessage());
      return null;
    }
  }

  /* Actualizar estado de sesion de Usuario */
  public function changeStatusUserLogin(): bool
  {
    $success = false;
    $id_user = null;
    $case = null;

    try {
      // 1. CAPTURAR DATOS DE SESIÓN ANTES DE CUALQUIER OPERACIÓN
      if (!isset($_SESSION['idUser']) || !isset($_SESSION['case'])) {
        error_log("⚠️ changeStatusUserLogin: Sesión sin datos válidos");
        return false;
      }

      $id_user = $_SESSION['idUser'];
      $case = $_SESSION['case'];

      error_log("🔓 Iniciando logout para " . ($case == 1 ? 'usuario' : 'admin') . " ID: $id_user");

      // 2. DESACTIVAR EN BASE DE DATOS CON REINTENTOS
      $success = $this->deactivateSessionInDB($id_user, $case);

      // 3. REGISTRAR RESULTADO
      if ($success) {
        error_log("✅ Sesión desactivada exitosamente en BD para ID: $id_user");
      } else {
        error_log("⚠️ No se pudo desactivar sesión en BD para ID: $id_user (se procederá con logout de todas formas)");
      }

      return $success;
    } catch (\Exception $e) {
      error_log("❌ ERROR CRÍTICO en changeStatusUserLogin: " . $e->getMessage());
      error_log("📍 Stack trace: " . $e->getTraceAsString());

      // INCLUSO SI FALLA TODO, INTENTAR DESACTIVAR DIRECTAMENTE
      if ($id_user !== null && $case !== null) {
        $this->forceDeactivateSession($id_user, $case);
      }

      return false;
    }
  }

  /**
   * ✅ NUEVO: Desactivar sesión en BD con reintentos automáticos
   * Garantiza máxima probabilidad de éxito incluso con problemas de conexión
   */
  private function deactivateSessionInDB(int $id_user, int $case): bool
  {
    $retries = 0;
    $lastException = null;

    while ($retries < self::MAX_RETRIES) {
      try {
        $connection = Connection::getInstance()->getConnection();

        // Verificar que la conexión está viva
        if (!$connection) {
          throw new \PDOException("Conexión a BD es null");
        }

        // Ejecutar UPDATE según el tipo de usuario
        if ($case == 1) {
          // USUARIOS NORMALES
          $sql = "UPDATE users SET session_active = 0 WHERE id_user = :id_user";
          $stmt = $connection->prepare($sql);
          $stmt->execute(['id_user' => $id_user]);
        } else if ($case == 2) {
          // ADMINISTRADORES
          $sql = "UPDATE admins SET session_active = 0 WHERE id_admin = :id_admin";
          $stmt = $connection->prepare($sql);
          $stmt->execute(['id_admin' => $id_user]);
        } else {
          error_log("⚠️ Caso no reconocido: $case");
          return false;
        }

        // ÉXITO
        $rowsAffected = $stmt->rowCount();
        error_log("✅ UPDATE exitoso. Filas afectadas: $rowsAffected (intento " . ($retries + 1) . ")");
        return true;
      } catch (\PDOException $e) {
        $lastException = $e;
        $retries++;

        error_log("⚠️ Intento $retries/" . self::MAX_RETRIES . " falló: " . $e->getMessage());

        // Si no es el último intento, esperar antes de reintentar
        if ($retries < self::MAX_RETRIES) {
          usleep(self::RETRY_DELAY_MS * 1000 * $retries); // Backoff exponencial
        }
      }
    }

    // TODOS LOS REINTENTOS FALLARON
    error_log("❌ FALLO TOTAL después de " . self::MAX_RETRIES . " intentos: " .
      ($lastException ? $lastException->getMessage() : 'Unknown error'));

    return false;
  }

  /**
   * ✅ NUEVO: Método de última instancia - forzar desactivación
   * Se ejecuta incluso si todo lo demás falla
   */
  private function forceDeactivateSession(int $id_user, int $case): void
  {
    try {
      error_log("🚨 Ejecutando forceDeactivateSession como último recurso");

      // Intentar con una nueva conexión
      $connection = Connection::getInstance()->getConnection();

      $table = ($case == 1) ? 'users' : 'admins';
      $idColumn = ($case == 1) ? 'id_user' : 'id_admin';

      $sql = "UPDATE $table SET session_active = 0 WHERE $idColumn = :id";
      $stmt = $connection->prepare($sql);
      $stmt->execute(['id' => $id_user]);

      error_log("✅ forceDeactivateSession ejecutado correctamente");
    } catch (\Exception $e) {
      error_log("❌ Incluso forceDeactivateSession falló: " . $e->getMessage());
      // En este punto, la sesión PHP se destruirá de todas formas
    }
  }

  /* ========================================
   * ✅ NUEVO MÉTODO: ACTIVAR SESIÓN EN LOGIN
   * ========================================
   * Método dedicado para activar sesión SOLO en login
   * Separa claramente la lógica de activar vs desactivar
   */
  public function activateSession(int $id_user, int $case = 1): bool
  {
    try {
      $connection = Connection::getInstance()->getConnection();

      if ($case == 1) {
        // USUARIOS NORMALES
        $sql = "UPDATE users SET session_active = 1 WHERE id_user = :id_user";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
      } else if ($case == 2) {
        // ADMINISTRADORES
        $sql = "UPDATE admins SET session_active = 1 WHERE id_admin = :id_admin";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id_admin' => $id_user]);
      } else {
        error_log("⚠️ activateSession: Caso no válido: $case");
        return false;
      }

      $rowsAffected = $stmt->rowCount();
      error_log("✅ Sesión ACTIVADA correctamente para " .
        ($case == 1 ? 'usuario' : 'admin') . " ID: $id_user (filas: $rowsAffected)");

      return true;
    } catch (\PDOException $e) {
      error_log("❌ ERROR al activar sesión: " . $e->getMessage());
      throw $e;
    }
  }

  /* ========================================
   * ✅ MEJORADO: Desactivar sesión (usado en logout por inactividad)
   * ========================================
   * Este método YA funcionaba correctamente
   * Mejoras: Logs detallados + reintentos + manejo robusto de errores
   */
  public function deactivateSession(int $id_company, int $id_user): bool
  {
    $retries = 0;
    $lastException = null;

    while ($retries < self::MAX_RETRIES) {
      try {
        $connection = Connection::getInstance()->getConnection();

        $sql = "UPDATE users SET session_active = 0 
                WHERE id_user = :id_user AND id_company = :id_company";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
          'id_user' => $id_user,
          'id_company' => $id_company
        ]);

        $rowsAffected = $stmt->rowCount();

        error_log("✅ deactivateSession exitoso. Usuario: $id_user, Empresa: $id_company, Filas: $rowsAffected");

        return $rowsAffected > 0;
      } catch (\PDOException $e) {
        $lastException = $e;
        $retries++;

        error_log("⚠️ deactivateSession intento $retries/" . self::MAX_RETRIES .
          " falló: " . $e->getMessage());

        if ($retries < self::MAX_RETRIES) {
          usleep(self::RETRY_DELAY_MS * 1000 * $retries);
        }
      }
    }

    // Todos los reintentos fallaron
    error_log("❌ deactivateSession FALLÓ completamente después de " .
      self::MAX_RETRIES . " intentos");

    if ($lastException) {
      throw $lastException;
    }

    return false;
  }

  /* ========================================
   * ✅ NUEVO: Desactivación forzada sin validaciones
   * ========================================
   * Usado en casos extremos donde no hay sesión PHP válida
   * pero necesitamos limpiar la BD de todas formas
   */
  public function forceDeactivateById(int $id_user, int $case = 1): bool
  {
    try {
      $connection = Connection::getInstance()->getConnection();

      if ($case == 1) {
        $sql = "UPDATE users SET session_active = 0 WHERE id_user = :id";
      } else {
        $sql = "UPDATE admins SET session_active = 0 WHERE id_admin = :id";
      }

      $stmt = $connection->prepare($sql);
      $stmt->execute(['id' => $id_user]);

      error_log("✅ forceDeactivateById ejecutado para ID: $id_user, case: $case");
      return true;
    } catch (\PDOException $e) {
      error_log("❌ forceDeactivateById falló: " . $e->getMessage());
      return false;
    }
  }

  /* ========================================
   * ✅ NUEVO: Limpieza masiva de sesiones huérfanas
   * ========================================
   * Útil para mantenimiento o recuperación de desastres
   */
  public function cleanupOrphanedSessions(int $inactiveMinutes = 30): int
  {
    try {
      $connection = Connection::getInstance()->getConnection();

      // Limpiar usuarios
      $sql = "UPDATE users 
              SET session_active = 0 
              WHERE session_active = 1 
              AND TIMESTAMPDIFF(MINUTE, last_login, NOW()) > :minutes";

      $stmt = $connection->prepare($sql);
      $stmt->execute(['minutes' => $inactiveMinutes]);
      $usersAffected = $stmt->rowCount();

      // Limpiar admins
      $sql = "UPDATE admins 
              SET session_active = 0 
              WHERE session_active = 1 
              AND TIMESTAMPDIFF(MINUTE, last_login, NOW()) > :minutes";

      $stmt = $connection->prepare($sql);
      $stmt->execute(['minutes' => $inactiveMinutes]);
      $adminsAffected = $stmt->rowCount();

      $total = $usersAffected + $adminsAffected;

      error_log("✅ Limpieza de sesiones: $usersAffected usuarios, $adminsAffected admins (total: $total)");

      return $total;
    } catch (\PDOException $e) {
      error_log("❌ Error en cleanupOrphanedSessions: " . $e->getMessage());
      return 0;
    }
  }
}

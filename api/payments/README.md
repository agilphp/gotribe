# Payments Service

## Database Setup

Execute the following SQL in phpMyAdmin for the `tribew_projects` database:

```sql
-- Table: cuentas_por_pagar (Accounts Payable)
CREATE TABLE IF NOT EXISTS `cuentas_por_pagar` (
  `id` VARCHAR(50) PRIMARY KEY,
  `creator_id` VARCHAR(50) NOT NULL,
  `project_id` VARCHAR(50) NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `estado` ENUM('PENDIENTE', 'PAGADA', 'VENCIDA') DEFAULT 'PENDIENTE',
  `fecha_creacion` DATETIME NOT NULL,
  `fecha_vencimiento` DATETIME NOT NULL,
  `fecha_pago` DATETIME NULL,
  INDEX `idx_creator` (`creator_id`),
  INDEX `idx_project` (`project_id`),
  INDEX `idx_estado` (`estado`),
  UNIQUE KEY `unique_creator_project` (`creator_id`, `project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pagos (Payments)
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` VARCHAR(50) PRIMARY KEY,
  `cuenta_por_pagar_id` VARCHAR(50) NOT NULL,
  `creator_id` VARCHAR(50) NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `fecha_pago` DATETIME NOT NULL,
  `metodo_pago` VARCHAR(50) NOT NULL,
  `referencia_pago` VARCHAR(100) NOT NULL,
  INDEX `idx_cuenta` (`cuenta_por_pagar_id`),
  INDEX `idx_creator` (`creator_id`),
  FOREIGN KEY (`cuenta_por_pagar_id`) REFERENCES `cuentas_por_pagar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## API Endpoints

- `GET /api/payments/health` - Health check
- `POST /api/payments/cuentas` - Create cuenta por pagar
- `GET /api/payments/cuentas/creator/{creatorId}` - Get creator accounts
- `POST /api/payments/pagos` - Register payment
- `POST /api/payments/vencidas` - Mark overdue accounts (cron job)

## Environment Variables

Create a `.env` file in this directory with:

```
DB_HOST=localhost
DB_NAME=tribew_projects
DB_USER=tribew_eli4as
DB_PASS=8TK4Nqp8d9SX4uxa
```

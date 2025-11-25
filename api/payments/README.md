# Payments Service

## Overview

This service manages the **10% commission** that creators owe to GoTribe when members pay to join their adventures.

## Payment Flow

1. **Member registers** for a paid adventure created by a creator
2. **Member receives PDF** with QR code and payment instructions
3. **Member pays creator** directly at the meeting point
4. **Payment validation** (2 options):
   - Creator scans member's QR code, OR
   - Member scans creator's QR code
5. **System automatically**:
   - Marks participation as ATTENDED
   - Calculates 10% commission
   - Creates/updates cuenta por pagar for creator
6. **Creator pays GoTribe** the accumulated 10% commission

## Database Tables

Tables already exist in the database (see `localhost.sql`):

### `cuentas_por_pagar`
Tracks commissions that creators owe to GoTribe
- `id`: Unique identifier
- `creator_id`: Creator who owes the commission
- `project_id`: Related project
- `monto`: Total commission amount (accumulates with each validated payment)
- `estado`: PENDIENTE | PAGADA | VENCIDA
- `fecha_creacion`: When the account was created
- `fecha_vencimiento`: Payment due date (30 days after event)
- `fecha_pago`: When creator paid GoTribe

### `pagos`
Records when creators pay their commissions to GoTribe
- `id`: Payment identifier
- `cuenta_por_pagar_id`: Related account
- `creator_id`: Creator making the payment
- `monto`: Amount paid
- `fecha_pago`: Payment date
- `metodo_pago`: Payment method
- `referencia_pago`: Transaction reference

## API Endpoints

### Internal (called by participation service)
- `POST /api/payments/cuentas` - Register member payment (calculates 10% commission)

### Public
- `GET /api/payments/health` - Health check
- `GET /api/payments/cuentas/creator/{creatorId}` - Get creator's pending commissions
- `POST /api/payments/pagos` - Register creator payment to GoTribe
- `POST /api/payments/vencidas` - Mark overdue accounts (cron job)

## Environment Variables

Create a `.env` file in this directory with:

```
DB_HOST=localhost
DB_NAME=tribew_projects
DB_USER=tribew_eli4as
DB_PASS=8TK4Nqp8d9SX4uxa
```

## Integration

The payment service is automatically called by the participation service when a QR code is scanned to validate a member's payment. No manual intervention needed.

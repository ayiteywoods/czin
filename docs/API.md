# CZIN Foods Mobile API (v1)

Base URL: `{APP_URL}/api/v1`

Auth: Laravel Sanctum Bearer tokens (`Authorization: Bearer {token}`)

## Auth

| Method | Path | Notes |
|--------|------|-------|
| POST | `/auth/login` | Returns token + user profile/permissions |
| POST | `/auth/register` | Customer registration |
| GET | `/auth/me` | Current user |
| POST | `/auth/refresh` | Rotate current token |
| POST | `/auth/logout` | Revoke current token |

## Customer

| Method | Path | Notes |
|--------|------|-------|
| GET | `/menu` | Categories + products |
| GET | `/menu/products/{slug}` | Product detail |
| GET/POST/PATCH/DELETE | `/cart…` | Guest carts need `X-Device-Id` |
| GET | `/checkout/summary` | Totals preview + shipping regions |
| POST | `/checkout` | Guest or auth; Paystack init (`X-Device-Id` for guests) |
| GET | `/account/orders` | Order history |

## Staff (admin + permissions)

| Method | Path | Permission |
|--------|------|------------|
| GET | `/dashboard/summary` | dashboard |
| GET | `/pos/bootstrap` | orders |
| POST | `/pos/orders` | orders |
| GET | `/orders`, `/orders/{id}/receipt` | orders |
| GET | `/kitchen/board` | kitchen |
| PATCH | `/kitchen/orders/{id}/status` | kitchen |
| GET/PATCH | `/tables…` | tables |

## Flutter app

Sibling project: `~/Documents/Projects/czinfoods_app`

Default admin: `admin@czin.com` / `password`

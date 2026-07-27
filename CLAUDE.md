# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Cloud hosting billing/management system (similar to WHMCS) built on **ThinkPHP 8** with PHP 8.1+. Handles client accounts, product catalog, orders, host provisioning, payment gateways, and admin RBAC.

## Commands

```bash
# Start dev server (localhost:8000)
php think run

# Scheduled tasks (order expiry, cleanup)
php think cron

# Process async task queue (host create/suspend/terminate)
php think task

# Process task notifications
php think task_notice
```

No test framework or linter is configured.

## Architecture

**Multi-app structure** with layered pattern per app:

- `app/admin/` — Admin panel API (`/v1/admin/*`)
- `app/home/` — Client-facing API (`/v1/*`)
- `app/http/middleware/` — All middleware classes
- `app/common/` — Shared contracts, enums, services
- `app/command/` — CLI commands (Cron, Task, TaskNotice)

**Layer pattern within each app:**
- `controller/` — Request handling, delegates to Logic
- `logic/` — Business logic (service layer)
- `model/` — ThinkORM models (auto-timestamps)
- `entity/` — Data transfer objects
- `validate/` — ThinkPHP validation rules

**Plugin system** (`plugins/` directory):
- 11 plugin types: addon, gateway, sms, mail, captcha, certification, oauth, oss, invoice, server, reserver
- Each type has PSR-4 namespace mapped in composer.json
- Plugins implement `app\common\contract\PluginInterface` (info/install/uninstall)
- Plugin manager handles hooks, route registration, and method calls

## Authentication & Middleware

JWT-based (HS256) with separate keys for admin and client (`JWT_ADMIN_KEY`, `JWT_CLIENT_KEY` in .env). Token revocation via Redis cache. Admin uses RBAC permission system (cached 2 hours).

Key middleware: `AdminAuth`, `ClientAuth`, `OptionalAuth`, `Pagination`, `ThrottleRepeat`, `OperatePassword`, `MaintenanceMode`, `Cors`.

## Domain Model

Core lifecycle flows:
- **Order**: Unpaid → Paid → Cancelled/Refunded
- **Host**: Unpaid → Pending → Active → Suspended → Deleted
- **Task queue**: Async provisioning operations (create/suspend/unsuspend/terminate)

Domain enums in `app/common/enum/`: BillingCycle, HostStatus, OrderStatus, OrderType, TaskStatus.

## Configuration

Key config files in `config/`:
- `database.php` — MySQL (credentials from .env)
- `cache.php` — Redis as default cache driver
- `jwt.php` — JWT keys (required, from .env)
- `plugin.php` — Plugin types and namespace mappings
- `middleware.php` — Middleware aliases and priority
- `console.php` — CLI command registration

## Conventions

- Language: Code comments and commit messages mix Chinese and English
- Commit style: Conventional commits with Chinese descriptions (`fix(security): ...`, `feature: ...`)
- Routing: RESTful API, strict matching (`route_complete_match = true`)
- Internationalization: `app/lang/` with zh-cn and en-us packs
- Error tracking: Sentry integration in `ExceptionHandle.php`

# Purchase Menu Registration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Register the missing purchase navigation groups and menus through idempotent MSJFramework seeders.

**Architecture:** Keep the existing Master Pemasok and Perencanaan Pembelian registrations unchanged. Add only the remaining `sys_gmenu`, `sys_dmenu`, and `sys_auth` records in one idempotent seeder, registered after the purchase role exists.

**Tech Stack:** Laravel 12, PHP 8.2+, MSJFramework configuration seeders.

## Global Constraints

- IDs in `sys_gmenu` and `sys_dmenu` use lowercase letters and are at most six characters.
- Preserve framework controllers, routes, existing menu records, and unrelated seeders.
- Use `updateOrInsert` so the configuration is safe to seed repeatedly.
- This task registers navigation only; tables, workflows, controllers, and views remain separate implementation work.

---

### Task 1: Register missing purchase navigation

**Files:**
- Create: `database/seeders/menu_pembelian_lanjutan.php`
- Modify: `database/seeders/role_pembelian.php`

**Interfaces:**
- Consumes: existing `admins` and `pembel` roles and `sys_gmenu`, `sys_dmenu`, `sys_auth` tables.
- Produces: menu groups `tertag`, `bayarp`, `koreks` and six authorized detail menus.

- [ ] **Step 1: Add the idempotent menu seeder**

```php
DB::table('sys_gmenu')->updateOrInsert(
    ['gmenu' => 'tertag'],
    ['urut' => 4, 'name' => 'Penerimaan dan Tagihan', 'icon' => 'ni-box-2', 'isactive' => '1']
);
```

- [ ] **Step 2: Register it after the purchase role exists**

```php
$this->call([
    menu_master_pemasok::class,
    menu_perencanaan_pembelian::class,
    menu_pembelian_lanjutan::class,
]);
```

- [ ] **Step 3: Verify syntax and seed idempotency**

Run: `php -l database/seeders/menu_pembelian_lanjutan.php; php artisan db:seed --class=menu_pembelian_lanjutan; php artisan db:seed --class=menu_pembelian_lanjutan`

Expected: all commands exit with code `0`, with no duplicate-key error.

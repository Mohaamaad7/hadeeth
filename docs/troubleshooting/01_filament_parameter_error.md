# Filament Parameter Error: `hidden: true` in Table Columns

**Date:** 2025-11-23

## Error Message
```
Unknown named parameter $hidden
File: app\Filament\Resources\Books\BookResource.php:38
```

## Cause
- The code used the syntax: `TextColumn::make('created_at', hidden: true)` or `->toggleable(hidden: true)`.
- Filament v3/v4 and PHP 8.2+ do **not** support named parameters in the `make()` or `toggleable()` methods.
- Only chained methods are supported for column visibility.

## Correct Usage
- ❌ **Wrong:**
  ```php
  TextColumn::make('created_at', hidden: true)
  // or
  ->toggleable(hidden: true)
  ```
- ✅ **Right:**
  ```php
  TextColumn::make('created_at')->toggleable()->hidden()
  ```

## Solution
- Replaced all `toggleable(hidden: true)` with `->toggleable()->hidden()` in all affected table files.
- Audited all Filament resource tables for this error.

## Reference
- [Filament Table Columns Docs](https://filamentphp.com/docs/3.x/tables/columns)
- [PHP 8.2 Named Parameters](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments)

---

**This log documents the error and fix to prevent future mistakes.**

# Database Schema & Migrations Report

**Date:** 2025-11-21

## 1. Generated Files

### Migrations
- `database/migrations/2025_11_21_205524_create_books_table.php`
- `database/migrations/2025_11_21_205524_create_sources_table.php`
- `database/migrations/2025_11_21_205525_create_narrators_table.php`
- `database/migrations/2025_11_21_205526_create_hadiths_table.php`
- `database/migrations/2025_11_21_205527_create_hadith_source_table.php`

### Models
- `app/Models/Book.php`
- `app/Models/Source.php`
- `app/Models/Narrator.php`
- `app/Models/Hadith.php`

## 2. Schema & Relationships

- All tables use `utf8mb4` charset and collation for full Unicode/Arabic support.
- All migrations use `declare(strict_types=1);` and proper typing.
- **Book**: HasMany `Hadiths` (relation defined in model).
- **Source**: BelongsToMany `Hadiths` via `hadith_source` (relation defined in model).
- **Narrator**: HasMany `Hadiths` (relation defined in model).
- **Hadith**: BelongsTo `Book`, BelongsTo `Narrator`, BelongsToMany `Sources` (all relations defined in model).
- **Pivot Table**: `hadith_source` with unique constraint and foreign keys.
- All required columns, indexes, and constraints are present as per requirements.

---

**Task:** 1.2 Database Schema & Migrations — All migrations and models are ready for use.

# Namespace Hallucination: `Filament\Schemas\Components` Does Not Exist

**Date:** 2025-11-23

## Error Message
```
Class "Filament\Schemas\Components\TextInput" not found
File: app\Filament\Resources\Books\Schemas\BookForm.php:14
```

## Root Cause
- The code incorrectly imported Form components from `Filament\Schemas\Components\*`.
- **This namespace does not exist in Filament.**
- The correct namespace for Form components is `Filament\Forms\Components\*`.

## Architecture Explanation
Filament v3/v4 has a clear separation of concerns:

### ✅ Correct Namespaces
- **Forms (Input Components):** `Filament\Forms\Components\*`
  - `TextInput`, `Select`, `Textarea`, `ColorPicker`, etc.
  - Used for user input in create/edit forms.

- **Schemas (Layout Components):** `Filament\Schemas\Components\*`
  - `Section`, `Grid`, `Tabs`, `Wizard`, etc.
  - Used for organizing and structuring forms/infolists.

- **Schema Base:** `Filament\Schemas\Schema`
  - The base class for form and infolist schemas.
  - This is correct and should remain `Filament\Schemas\Schema`.

- **Tables (Display Columns):** `Filament\Tables\Columns\*`
  - `TextColumn`, `BadgeColumn`, `ImageColumn`, etc.
  - Used for displaying data in resource tables.

### ❌ Wrong Namespace (Does Not Exist)
- `Filament\Schemas\Components\TextInput` ❌
- `Filament\Schemas\Components\Select` ❌
- `Filament\Schemas\Components\Textarea` ❌
- `Filament\Schemas\Components\ColorPicker` ❌

## Type Aliases: `Get` and `Set`
- The error also affected `Get` and `Set` imports.
- **These are NOT classes** in Filament.
- They are type aliases for `Closure` used in reactive form logic.

### ✅ Correct Usage
```php
use Closure;

// In afterStateUpdated callback:
->afterStateUpdated(function (?string $state, Closure $set, Closure $get) {
    $set('field_name', 'value');
    $currentValue = $get('field_name');
})
```

### ❌ Wrong Usage
```php
use Filament\Schemas\Get;  // Does not exist
use Filament\Schemas\Set;  // Does not exist
```

## Solution
### Files Fixed:
1. `app/Filament/Resources/Books/Schemas/BookForm.php`
2. `app/Filament/Resources/Sources/Schemas/SourceForm.php`
3. `app/Filament/Resources/Narrators/Schemas/NarratorForm.php`
4. `app/Filament/Resources/Hadiths/Schemas/HadithForm.php`

### Changes Made:
```php
// BEFORE (Wrong):
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Get;
use Filament\Schemas\Set;

// AFTER (Correct):
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Closure;
```

## Prevention Strategy
- **Always verify namespaces against official Filament documentation:**
  - [Forms Documentation](https://filamentphp.com/docs/3.x/forms)
  - [Tables Documentation](https://filamentphp.com/docs/3.x/tables)
  - [Schemas Documentation](https://filamentphp.com/docs/3.x/support/schemas)

- **Check vendor files** when in doubt:
  - `vendor/filament/forms/src/Components/`
  - `vendor/filament/schemas/src/Components/`
  - `vendor/filament/tables/src/Columns/`

- **Rule of Thumb:**
  - Input fields (user enters data) → `Filament\Forms\Components\*`
  - Layout containers (organize fields) → `Filament\Schemas\Components\*`
  - Display columns (show data) → `Filament\Tables\Columns\*`

## Reference
- [Filament v3 Forms](https://filamentphp.com/docs/3.x/forms/fields)
- [Filament v3 Schemas](https://filamentphp.com/docs/3.x/support/schemas)
- [PHP Closure Type](https://www.php.net/manual/en/class.closure.php)

---

**This error was caused by namespace confusion. Always verify against official documentation before writing imports.**

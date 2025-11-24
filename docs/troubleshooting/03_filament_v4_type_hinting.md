# Filament v4 Type Hinting: `Get` and `Set` Dependency Injection

**Date:** 2025-11-23

## Error Message
```
TypeError: Argument #1 ($get) must be of type Closure, Filament\Schemas\Components\Utilities\Get given
File: app\Filament\Resources\Hadiths\Schemas\HadithForm.php
```

## Root Cause
- The code incorrectly used `Closure` type hints for reactive form callbacks.
- **Filament v4** uses Dependency Injection with specialized `Get` and `Set` classes, not generic `Closure` objects.

---

## Understanding Filament v4 Dependency Injection

### ❌ Wrong (Filament v2/v3 Pattern):
```php
use Closure;

// Incorrect for Filament v4:
->afterStateUpdated(function (?string $state, Closure $set, Closure $get) {
    $set('field_name', 'value');
    $value = $get('field_name');
})

->options(function (Closure $get) {
    return Model::where('id', $get('parent_id'))->pluck('name', 'id');
})

->disabled(fn(Closure $get) => !$get('parent_id'))
```

### ✅ Correct (Filament v4 Pattern):
```php
use Filament\Forms\Get;
use Filament\Forms\Set;

// Correct for Filament v4:
->afterStateUpdated(function (?string $state, Set $set, Get $get) {
    $set('field_name', 'value');
    $value = $get('field_name');
})

->options(function (Get $get) {
    return Model::where('id', $get('parent_id'))->pluck('name', 'id');
})

->disabled(fn(Get $get) => !$get('parent_id'))
```

---

## What Changed in Filament v4?

### Type System Evolution:
- **Filament v2/v3:** Used generic `Closure` types for callbacks.
- **Filament v4:** Introduced specialized classes for type safety and IDE autocomplete.

### The `Get` Class (`Filament\Forms\Get`):
```php
namespace Filament\Forms;

class Get
{
    /**
     * Get the current state of a form field.
     *
     * @param  string  $path  The field name/path.
     * @return mixed  The current field value.
     */
    public function __invoke(string $path): mixed;
}
```

**Purpose:**
- Retrieve the current state of form fields.
- Used in reactive logic to read other field values.

**Usage:**
```php
->options(function (Get $get) {
    $parentId = $get('parent_id'); // Get value of 'parent_id' field
    return Book::where('parent_id', $parentId)->pluck('name', 'id');
})
```

### The `Set` Class (`Filament\Forms\Set`):
```php
namespace Filament\Forms;

class Set
{
    /**
     * Set the state of a form field.
     *
     * @param  string  $path  The field name/path.
     * @param  mixed  $state  The value to set.
     * @return void
     */
    public function __invoke(string $path, mixed $state): void;
}
```

**Purpose:**
- Update the state of form fields programmatically.
- Used in reactive logic to populate dependent fields.

**Usage:**
```php
->afterStateUpdated(function (?string $state, Set $set) {
    $set('book_id', null); // Reset 'book_id' field
    $set('content', $parsedText); // Set 'content' field
})
```

---

## Solution Applied to `HadithForm.php`

### Import Statements (Fixed):
```php
// BEFORE (Wrong):
use Closure;

// AFTER (Correct):
use Filament\Forms\Get;
use Filament\Forms\Set;
```

### Smart Parser Callback (Fixed):
```php
// BEFORE (Wrong):
->afterStateUpdated(function (?string $state, Closure $set, Closure $get) {
    $set('content', $result['clean_text']);
})

// AFTER (Correct):
->afterStateUpdated(function (?string $state, Set $set, Get $get) {
    $set('content', $result['clean_text']);
})
```

### Hierarchical Book Selects (Fixed):
```php
// Main Book Filter:
// BEFORE (Wrong):
->afterStateUpdated(fn(Closure $set) => $set('book_id', null))

// AFTER (Correct):
->afterStateUpdated(fn(Set $set) => $set('book_id', null))

// Bab Selector (Options):
// BEFORE (Wrong):
->options(function (Closure $get) {
    $mainBookId = $get('main_book_id');
    // ...
})

// AFTER (Correct):
->options(function (Get $get) {
    $mainBookId = $get('main_book_id');
    // ...
})

// Bab Selector (Disabled State):
// BEFORE (Wrong):
->disabled(fn(Closure $get) => !$get('main_book_id'))

// AFTER (Correct):
->disabled(fn(Get $get) => !$get('main_book_id'))
```

---

## Benefits of the v4 Type System

### 1. Type Safety:
- PHP 8.2+ enforces strict type checking.
- Prevents passing incorrect arguments to callbacks.

### 2. IDE Autocomplete:
- `Get` and `Set` classes have documented methods.
- IDEs can provide better code completion and inline documentation.

### 3. Backward Compatibility Detection:
- Using `Closure` immediately triggers a `TypeError`.
- Forces developers to update to the v4 pattern.

### 4. Future-Proofing:
- Filament can extend `Get`/`Set` with new methods without breaking changes.
- Example: `$get->all()`, `$set->multiple([...])` (hypothetical).

---

## Common Patterns in Filament v4

### Pattern 1: Dependent Select (Parent → Child)
```php
Select::make('parent_id')
    ->options(Category::pluck('name', 'id'))
    ->live()
    ->afterStateUpdated(fn(Set $set) => $set('child_id', null)),

Select::make('child_id')
    ->options(fn(Get $get) => Category::where('parent_id', $get('parent_id'))->pluck('name', 'id'))
    ->disabled(fn(Get $get) => !$get('parent_id')),
```

### Pattern 2: Auto-Population (Smart Form)
```php
TextInput::make('full_name')
    ->live(onBlur: true)
    ->afterStateUpdated(function (?string $state, Set $set) {
        if (!$state) return;
        
        [$first, $last] = explode(' ', $state, 2);
        $set('first_name', $first);
        $set('last_name', $last ?? '');
    }),
```

### Pattern 3: Conditional Visibility
```php
Select::make('payment_method')
    ->options(['cash' => 'Cash', 'card' => 'Card'])
    ->live(),

TextInput::make('card_number')
    ->visible(fn(Get $get) => $get('payment_method') === 'card'),
```

---

## Migration Checklist (v3 → v4)

If migrating from Filament v3 to v4:

- [ ] Replace `use Closure;` with `use Filament\Forms\Get;` and `use Filament\Forms\Set;`.
- [ ] Update all `fn(Closure $get)` to `fn(Get $get)`.
- [ ] Update all `fn(Closure $set)` to `fn(Set $set)`.
- [ ] Update all `function (?string $state, Closure $set, Closure $get)` signatures.
- [ ] Test all reactive forms to ensure callbacks work correctly.
- [ ] Check for `Filament\Schemas\Get` or `Filament\Schemas\Set` (invalid namespaces).

---

## Reference

### Official Filament v4 Documentation:
- [Forms - Reactive Fields](https://filamentphp.com/docs/4.x/forms/advanced#reactive-fields)
- [Forms - Dependent Selects](https://filamentphp.com/docs/4.x/forms/fields/select#dependent-selects)

### Namespace Reference:
- **Correct:** `Filament\Forms\Get` and `Filament\Forms\Set`
- **Invalid:** `Filament\Schemas\Get` and `Filament\Schemas\Set` (Do Not Exist)
- **Legacy:** `Closure` (v2/v3 pattern, incompatible with v4)

---

## Files Modified

- `app/Filament/Resources/Hadiths/Schemas/HadithForm.php`

---

**Conclusion:**  
Filament v4 requires explicit `Get` and `Set` type hints for dependency injection in reactive forms. Using generic `Closure` types will cause `TypeError` exceptions. Always import `Filament\Forms\Get` and `Filament\Forms\Set` for v4 compatibility.

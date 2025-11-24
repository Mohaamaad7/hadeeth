# Report 08: Hierarchical Books Structure (Kitab → Bab)

**Date:** 2025-11-23  
**Feature:** Self-Referencing Books with Dependent Selects  
**Status:** ✅ Implemented & Migrated

---

## Overview
This report documents the implementation of a hierarchical structure for books in the Sahih Al-Jami project. The system now supports:
- **Kitab (كتاب):** Main Book Category (parent_id = NULL)
- **Bab (باب):** Sub-category/Chapter (parent_id points to Kitab)
- **Hadiths:** Assigned to the Bab (child book)

---

## 1. Database Changes

### Migration: `2025_11_23_204821_add_parent_id_to_books_table`

#### Schema Update:
```php
Schema::table('books', function (Blueprint $table) {
    $table->foreignId('parent_id')
        ->nullable()
        ->after('id')
        ->constrained('books')
        ->nullOnDelete();
    $table->index('parent_id');
});
```

#### Key Features:
- **Self-Referencing Foreign Key:** `parent_id` references `books.id`.
- **Nullable:** Allows root-level books (Kitabs) with `parent_id = NULL`.
- **Cascade Handling:** `nullOnDelete()` prevents orphaned records if a parent is deleted.
- **Indexed:** For optimal query performance on hierarchical lookups.

---

## 2. Model Changes: `App\Models\Book`

### Updated Fillable:
```php
protected $fillable = ['name', 'sort_order', 'parent_id'];
```

### Self-Referencing Relationships:

#### Parent Relationship (BelongsTo):
```php
public function parent(): BelongsTo
{
    return $this->belongsTo(Book::class, 'parent_id');
}
```
- Returns the parent Kitab for a Bab.
- `NULL` for root-level Kitabs.

#### Children Relationship (HasMany):
```php
public function children(): HasMany
{
    return $this->hasMany(Book::class, 'parent_id');
}
```
- Returns all Babs (child books) for a given Kitab.
- Empty collection for Babs (leaf nodes).

### Query Scope: `mainBooks()`
```php
public function scopeMainBooks(Builder $query): Builder
{
    return $query->whereNull('parent_id');
}
```
- Filters only root-level books (Kitabs).
- Used in Filament selects to show only main categories.

**Usage:**
```php
Book::mainBooks()->get(); // Returns all Kitabs
```

---

## 3. Filament Updates

### A. BookResource Form (`BookForm.php`)

#### Added Parent Selector:
```php
Select::make('parent_id')
    ->label('الكتاب الرئيسي (اختياري)')
    ->relationship('parent', 'name')
    ->searchable()
    ->preload()
    ->nullable()
    ->helperText('اترك فارغاً لإنشاء كتاب رئيسي (Kitab)، أو اختر كتاباً لإنشاء باب فرعي (Bab)');
```

**Behavior:**
- If empty → Creates a **Kitab** (main category).
- If selected → Creates a **Bab** (sub-category under the selected Kitab).

---

### B. HadithResource Form (`HadithForm.php`)

#### Dependent Select Logic:

**Step 1: Virtual Filter (Main Book)**
```php
Select::make('main_book_id')
    ->label('الكتاب (القسم الرئيسي)')
    ->options(fn() => \App\Models\Book::mainBooks()->pluck('name', 'id'))
    ->searchable()
    ->live()
    ->afterStateUpdated(fn(Closure $set) => $set('book_id', null))
    ->helperText('اختر الكتاب الرئيسي أولاً لعرض الأبواب الفرعية');
```
- **Virtual Field:** Not saved to database.
- **Purpose:** Filters the child books (Babs) in the next field.
- **Reactive (`->live()`)**: Triggers updates when changed.
- **Resets `book_id`:** Clears the Bab selection when Main Book changes.

**Step 2: Actual Book ID (Bab)**
```php
Select::make('book_id')
    ->label('الباب (القسم الفرعي)')
    ->options(function (Closure $get) {
        $mainBookId = $get('main_book_id');
        
        if (!$mainBookId) {
            return [];
        }
        
        return \App\Models\Book::where('parent_id', $mainBookId)
            ->pluck('name', 'id');
    })
    ->searchable()
    ->disabled(fn(Closure $get) => !$get('main_book_id'))
    ->helperText('اختر الباب الفرعي من الكتاب المحدد')
    ->required();
```
- **Dependent Options:** Only shows Babs (children) of the selected Kitab.
- **Disabled State:** Grayed out until a Main Book is selected.
- **Required:** Ensures every Hadith is assigned to a Bab.

---

### C. BooksTable (`BooksTable.php`)

#### Added Parent Column:
```php
TextColumn::make('parent.name')
    ->searchable()
    ->sortable()
    ->label('الكتاب الرئيسي')
    ->placeholder('—')
    ->badge()
    ->color('success');
```
- Displays the parent Kitab name for each Bab.
- Shows "—" for root-level Kitabs.
- Uses badge styling for visual clarity.

---

## 4. Technical Explanation: Recursive Relationships

### What is a Self-Referencing Relationship?
A self-referencing (recursive) relationship is when a table has a foreign key pointing to itself. This allows modeling hierarchical data structures like:
- Categories → Sub-categories
- Employees → Managers
- Kitabs → Babs

### Database Structure:
```
books Table
┌────┬──────────────────┬───────────┬────────────┐
│ id │ name             │ parent_id │ sort_order │
├────┼──────────────────┼───────────┼────────────┤
│ 1  │ كتاب الإيمان     │ NULL      │ 1          │ ← Kitab
│ 2  │ باب النية        │ 1         │ 1          │ ← Bab (child of #1)
│ 3  │ باب الصدق        │ 1         │ 2          │ ← Bab (child of #1)
│ 4  │ كتاب الصلاة      │ NULL      │ 2          │ ← Kitab
│ 5  │ باب أوقات الصلاة │ 4         │ 1          │ ← Bab (child of #4)
└────┴──────────────────┴───────────┴────────────┘
```

### Eloquent Queries:

**Get all Kitabs:**
```php
Book::whereNull('parent_id')->get();
// or
Book::mainBooks()->get();
```

**Get all Babs of a specific Kitab:**
```php
$kitab = Book::find(1);
$babs = $kitab->children; // Returns collection of Babs
```

**Get the parent Kitab of a Bab:**
```php
$bab = Book::find(2);
$kitab = $bab->parent; // Returns the parent Book model
```

---

## 5. User Workflow

### Creating a Kitab (Main Book):
1. Navigate to **Books → Create**.
2. Enter the name (e.g., "كتاب الإيمان").
3. Leave **"الكتاب الرئيسي"** empty.
4. Save.

### Creating a Bab (Sub-category):
1. Navigate to **Books → Create**.
2. Enter the name (e.g., "باب النية").
3. Select a parent from **"الكتاب الرئيسي"** dropdown.
4. Save.

### Assigning a Hadith to a Bab:
1. Navigate to **Hadiths → Create**.
2. In **"الكتاب (القسم الرئيسي)"**, select a Kitab (e.g., "كتاب الإيمان").
3. The **"الباب (القسم الفرعي)"** dropdown updates to show only Babs under that Kitab.
4. Select the desired Bab.
5. Save.

---

## 6. Leaf Node Handling (Books Without Sub-chapters)

### The Problem:
Some books (e.g., "Kitab Al-Wahi") may not have sub-chapters (Babs). In the original implementation:
- User selects the Main Book.
- The "Bab" dropdown becomes empty (no children).
- Form cannot be submitted because `book_id` is required.

### The Solution: Smart Auto-Selection

#### Updated `main_book_id` Logic:
```php
Select::make('main_book_id')
    ->afterStateUpdated(function (?string $state, Set $set) {
        if (!$state) {
            $set('book_id', null);
            return;
        }
        
        // Check if this book has children
        $hasChildren = \App\Models\Book::where('parent_id', $state)->exists();
        
        if ($hasChildren) {
            // Has children: Clear book_id so user must select a child
            $set('book_id', null);
        } else {
            // Leaf node: Auto-select the main book itself
            $set('book_id', $state);
        }
    })
```

**Behavior:**
- **Books with children:** Clears `book_id`, forcing user to select a Bab.
- **Leaf books (no children):** Auto-fills `book_id` with the main book's ID.

#### Updated `book_id` Options Logic:
```php
Select::make('book_id')
    ->options(function (Get $get) {
        $mainBookId = $get('main_book_id');
        
        if (!$mainBookId) {
            return [];
        }
        
        // Fetch children
        $children = \App\Models\Book::where('parent_id', $mainBookId)
            ->pluck('name', 'id');
        
        // If children exist, return them
        if ($children->isNotEmpty()) {
            return $children;
        }
        
        // No children: Return the main book itself as a leaf node
        $mainBook = \App\Models\Book::find($mainBookId);
        if ($mainBook) {
            return [$mainBook->id => $mainBook->name . ' (تصنيف مباشر)'];
        }
        
        return [];
    })
```

**Behavior:**
- **Books with children:** Shows list of Babs.
- **Leaf books:** Shows the main book with "(تصنيف مباشر)" label indicating direct classification.

### User Experience Flow:

**Scenario 1: Book with Sub-chapters (e.g., "كتاب الإيمان")**
1. User selects "كتاب الإيمان" as Main Book.
2. System detects children exist.
3. `book_id` is cleared.
4. User sees list of Babs in the dropdown.
5. User selects a Bab.

**Scenario 2: Leaf Book (e.g., "Kitab Al-Wahi")**
1. User selects "Kitab Al-Wahi" as Main Book.
2. System detects NO children.
3. `book_id` is auto-filled with "Kitab Al-Wahi" ID.
4. Dropdown shows: "Kitab Al-Wahi (تصنيف مباشر)".
5. User can proceed to save without additional selection.

### Benefits:
- ✅ **No Dead Ends:** Users can always submit the form.
- ✅ **Clear Indication:** "(تصنيف مباشر)" label shows this is a direct classification.
- ✅ **Flexible Structure:** Supports both hierarchical and flat book structures.
- ✅ **Data Integrity:** Every hadith always has a valid `book_id`.

---

## 7. Benefits of This Structure

### ✅ Data Integrity:
- Foreign key ensures `parent_id` always references a valid book.
- `nullOnDelete()` prevents orphaned Babs.

### ✅ User Experience:
- Dependent selects prevent selecting invalid combinations.
- Clear visual hierarchy in the Books table.
- Leaf node handling prevents form submission dead ends.

### ✅ Performance:
- Indexed `parent_id` for fast lookups.
- Eager loading can optimize nested queries.

### ✅ Scalability:
- Can be extended to 3+ levels (e.g., Kitab → Bab → Sub-Bab) by querying `children` recursively.

---

## 8. Future Enhancements

### Potential Improvements:
1. **Tree View Widget:**
   - Display books in a collapsible tree structure.
   - Filament package: `filament/tree`.

2. **Breadcrumb Display:**
   - Show full path (e.g., "كتاب الإيمان → باب النية") in Hadith details.

3. **Recursive Deletion:**
   - Option to cascade delete all child Babs when deleting a Kitab.

4. **Drag & Drop Sorting:**
   - Reorder Babs within a Kitab using drag-and-drop.

---

## 8. Files Modified

### Migration:
- `database/migrations/2025_11_23_204821_add_parent_id_to_books_table.php`

### Models:
- `app/Models/Book.php`

### Filament Resources:
- `app/Filament/Resources/Books/Schemas/BookForm.php`
- `app/Filament/Resources/Books/Tables/BooksTable.php`
- `app/Filament/Resources/Hadiths/Schemas/HadithForm.php`

---

## 9. Testing Checklist

- [x] Migration runs successfully without errors.
- [x] Can create a Kitab with `parent_id = NULL`.
- [x] Can create a Bab with `parent_id` pointing to a Kitab.
- [ ] Main Book selector shows only Kitabs in Hadith form.
- [ ] Bab selector updates dynamically when Main Book is selected.
- [ ] Bab selector is disabled until Main Book is chosen.
- [ ] Books table displays parent name correctly.
- [ ] Deleting a Kitab sets `parent_id = NULL` for its Babs.
- [ ] **Leaf Node:** Selecting a book with children clears `book_id`.
- [ ] **Leaf Node:** Selecting a book without children auto-fills `book_id`.
- [ ] **Leaf Node:** Dropdown shows "(تصنيف مباشر)" for leaf books.

---

## 10. References

### Laravel Documentation:
- [Self-Referencing Relationships](https://laravel.com/docs/11.x/eloquent-relationships#one-to-many)
- [Query Scopes](https://laravel.com/docs/11.x/eloquent#local-scopes)

### Filament Documentation:
- [Dependent Select Fields](https://filamentphp.com/docs/3.x/forms/fields/select#dependent-selects)
- [Reactive Fields](https://filamentphp.com/docs/3.x/forms/advanced#reactive-fields)

---

**Conclusion:**  
The hierarchical books structure is now fully implemented and ready for testing. The dependent select logic ensures data integrity and provides a smooth user experience for categorizing Hadiths under the correct Kitab and Bab.

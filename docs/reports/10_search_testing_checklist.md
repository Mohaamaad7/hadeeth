# Search Feature Testing Checklist

**Date:** 2025-11-24  
**Feature:** Frontend Search with Tashkeel-Insensitive MySQL Full-Text

---

## Pre-Testing Verification

### Database Check:
- [ ] Run: `php artisan tinker` then `Hadith::count()` (should return >= 3)
- [ ] Verify: `Hadith::first()->content_searchable` is not null
- [ ] Check index: Run in MySQL: `SHOW INDEX FROM hadiths WHERE Key_name = 'hadiths_content_searchable_fulltext'`

### File Integrity:
- [x] `app/Http/Controllers/SearchController.php` exists and has `__invoke()` method
- [x] `resources/views/frontend/search-results.blade.php` exists
- [x] `routes/web.php` has `/search` route
- [x] `resources/views/home.blade.php` form action is `{{ route('search') }}`
- [x] No compile errors in any file

---

## Functional Testing

### Test 1: Basic Search (With Results)
**Steps:**
1. Navigate to homepage: `http://localhost:8000`
2. Enter search term: "الله" (a common word likely in your 3 Hadiths)
3. Click "بحث" button

**Expected Result:**
- [ ] Redirects to `/search?q=الله`
- [ ] Gold header shows: "نتائج البحث عن: الله"
- [ ] Results count displays (e.g., "تم العثور على 3 حديث")
- [ ] Hadith cards display in grid layout
- [ ] No PHP errors

### Test 2: Tashkeel-Insensitive Search
**Steps:**
1. Search with Tashkeel: "اللَّهِ"
2. Note the results
3. Search without Tashkeel: "الله"
4. Compare results

**Expected Result:**
- [ ] Both searches return identical results
- [ ] Result count is the same
- [ ] Same Hadiths appear in both

### Test 3: No Results
**Steps:**
1. Search for: "xyz123" (gibberish that won't match)
2. Submit search

**Expected Result:**
- [ ] Shows empty state message
- [ ] Icon displays (sad face)
- [ ] Message: "لم نعثر على أحاديث تطابق بحثك عن xyz123"
- [ ] Search suggestions box appears
- [ ] "العودة للصفحة الرئيسية" button works

### Test 4: Validation (Too Short)
**Steps:**
1. Try to search with 1 character: "ا"
2. Submit search

**Expected Result:**
- [ ] HTML5 validation prevents submission (minlength="2")
- [ ] OR Laravel validation returns error: "The q field must be at least 2 characters"

### Test 5: Pagination (If > 15 Results)
**Steps:**
1. Search for a very common word (if you have > 15 Hadiths)
2. Check bottom of results

**Expected Result:**
- [ ] Pagination links appear
- [ ] Clicking page 2 works
- [ ] URL preserves query: `/search?q=...&page=2`

### Test 6: Search Refinement
**Steps:**
1. Perform a search
2. Use the search bar at the top of results page
3. Enter a different query
4. Submit

**Expected Result:**
- [ ] New search executes
- [ ] Results update
- [ ] URL changes to new query

### Test 7: Back Button
**Steps:**
1. From search results, click "العودة للرئيسية"

**Expected Result:**
- [ ] Returns to homepage
- [ ] Homepage loads normally

---

## UI/UX Testing

### Responsive Design:
- [ ] **Mobile (< 768px):** Single column grid
- [ ] **Tablet (768px - 1024px):** 2-column grid
- [ ] **Desktop (> 1024px):** 3-column grid

### Arabic RTL:
- [ ] Text aligns right
- [ ] Search input is RTL
- [ ] Buttons are positioned correctly (left side for "بحث")

### Visual Design:
- [ ] Gold/charcoal gradient header looks good
- [ ] Hadith cards have gold border-right
- [ ] Pagination links are Tailwind-styled
- [ ] Empty state is centered and readable
- [ ] Icons render correctly (SVGs)

---

## Performance Testing

### Speed:
- [ ] Search completes in < 1 second
- [ ] No N+1 query issues (check Laravel Debugbar if installed)
- [ ] Page loads smoothly

### Database Query:
**Run in tinker:**
```php
DB::enableQueryLog();
Hadith::search('الله')->paginate(15);
dd(DB::getQueryLog());
```

**Expected:**
- [ ] Uses full-text search (MATCH AGAINST in SQL)
- [ ] Only 1-2 queries (main + count)
- [ ] No additional relationship queries if not eager-loaded

---

## Security Testing

### XSS Protection:
**Steps:**
1. Search for: `<script>alert('XSS')</script>`

**Expected Result:**
- [ ] Script does not execute
- [ ] Blade escapes output: `&lt;script&gt;...`
- [ ] No alert appears

### SQL Injection:
**Steps:**
1. Search for: `' OR '1'='1`

**Expected Result:**
- [ ] Treated as literal string
- [ ] No SQL error
- [ ] No unauthorized data access

---

## Error Handling

### Test Invalid Route:
- [ ] Visit `/search` directly (no query param) → Validation error or graceful message

### Test Malformed Query:
- [ ] Try query > 255 chars → Validation error

---

## Browser Compatibility

Test in:
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)
- [ ] Mobile browsers (Chrome Mobile, Safari iOS)

---

## Final Checklist

Before marking task complete:
- [ ] All functional tests pass
- [ ] UI is responsive and beautiful
- [ ] No console errors (F12 → Console)
- [ ] No PHP errors in `storage/logs/laravel.log`
- [ ] Arabic text is readable and RTL
- [ ] Documentation is complete (`10_search_logic.md`)
- [ ] Task box checked in `PROGRESS.md`

---

## Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| "No results" for valid search | `content_searchable` not populated | Re-save Hadiths to trigger observer |
| Full-text error | Index missing | Run migration: `2025_11_22_212938_add_fulltext_index_to_hadiths.php` |
| Variable not found | Controller/view mismatch | Ensure both use `$results` (not `$hadiths`) |
| Route not found | Route not registered | Check `routes/web.php` has search route |
| Form doesn't submit | Wrong input name | Ensure input `name="q"` |

---

**Testing Status:** ⏳ Awaiting Manual Testing  
**Next Action:** Run through this checklist and mark items complete  
**Success Criteria:** All tests pass, no errors, beautiful UI

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of books.
     */
    public function index(Request $request): View
    {
        $query = Book::with(['parent', 'children'])->withCount('hadiths');

        // Search
        if ($search = $request->get('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Filter by type
        if ($request->get('type') === 'main') {
            $query->whereNull('parent_id');
        } elseif ($request->get('type') === 'sub') {
            $query->whereNotNull('parent_id');
        }

        $books = $query->orderBy('sort_order')->paginate(20);

        return view('dashboard.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(): View
    {
        $mainBooks = Book::whereNull('parent_id')->orderBy('sort_order')->get();
        
        return view('dashboard.books.create', compact('mainBooks'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:books,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Auto-increment sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $maxOrder = Book::where('parent_id', $validated['parent_id'] ?? null)->max('sort_order');
            $validated['sort_order'] = ($maxOrder ?? 0) + 1;
        }

        $book = Book::create($validated);

        return redirect()
            ->route('dashboard.books.show', $book)
            ->with('success', 'تم إضافة الكتاب بنجاح!');
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book): View
    {
        $book->load(['parent', 'children' => function($query) {
            $query->withCount('hadiths')->orderBy('sort_order');
        }, 'hadiths' => function($query) {
            $query->with(['narrator', 'sources'])->orderBy('number_in_book')->take(10);
        }]);

        return view('dashboard.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book): View
    {
        $mainBooks = Book::whereNull('parent_id')
            ->where('id', '!=', $book->id)
            ->orderBy('sort_order')
            ->get();

        return view('dashboard.books.edit', compact('book', 'mainBooks'));
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:books,id',
            'sort_order' => 'required|integer|min:0',
        ]);

        // Prevent self-referencing
        if (isset($validated['parent_id']) && $validated['parent_id'] == $book->id) {
            return back()->withErrors(['parent_id' => 'الكتاب لا يمكن أن يكون تابعاً لنفسه!']);
        }

        $book->update($validated);

        return redirect()
            ->route('dashboard.books.show', $book)
            ->with('success', 'تم تحديث الكتاب بنجاح!');
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book): RedirectResponse
    {
        // Check if book has hadiths
        if ($book->hadiths()->count() > 0) {
            return back()->withErrors([
                'delete' => 'لا يمكن حذف الكتاب لأنه يحتوي على ' . $book->hadiths()->count() . ' حديث!'
            ]);
        }

        // Check if book has children
        if ($book->children()->count() > 0) {
            return back()->withErrors([
                'delete' => 'لا يمكن حذف الكتاب لأنه يحتوي على ' . $book->children()->count() . ' باب فرعي!'
            ]);
        }

        $book->delete();

        return redirect()
            ->route('dashboard.books.index')
            ->with('success', 'تم حذف الكتاب بنجاح!');
    }

    /**
     * Reorder books.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'books' => 'required|array',
            'books.*' => 'exists:books,id',
        ]);

        foreach ($validated['books'] as $order => $bookId) {
            Book::where('id', $bookId)->update(['sort_order' => $order + 1]);
        }

        return back()->with('success', 'تم إعادة ترتيب الكتب بنجاح!');
    }
}

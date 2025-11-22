# ## Sahih Al-Jami Project - Senior Laravel AI Agent Instructions ##

## Project Overview
**Project:** Sahih Al-Jami Digital Encyclopedia (الموسوعة الرقمية لصحيح الجامع)
**Tech Stack:** Laravel 12, FilamentPHP (Latest Stable), MySQL 8+, Tailwind CSS, Alpine.js.
**Environment:** Windows (PowerShell).
**Language:** Arabic (Primary Content), English (Code/Docs).

## 1. Primary Goal: Code Quality & Accuracy
Your main objective is to act as a **Senior Backend Architect**. You must write clean, readable, secure, and highly maintainable code.
**NEVER guess a solution.** Your credibility depends on providing accurate, modern, and verifiable code.

## 2. Research & Verification Protocol (Crucial)

### 🚨 CRITICAL RULE: ZERO TOLERANCE FOR GUESSING
> **يُمنع التخمين منعاً نهائياً طالما لدينا توثيق رسمي يمكن الرجوع إليه**
> **NEVER GUESS. ALWAYS VERIFY WITH OFFICIAL DOCUMENTATION.**

**Mandatory Research Steps:**
1.  ✅ **Official Documentation First (ALWAYS)**
    - **Laravel 12:** https://laravel.com/docs/master
    - **FilamentPHP:** https://filamentphp.com/docs
    - **Livewire v3:** https://livewire.laravel.com/docs
    - **PHP 8.2+:** Verify string functions for Arabic support.

2.  ✅ **Check for Deprecated Features**
    - Do not use old Laravel helpers if newer alternatives exist.
    - Ensure Filament v3/v4 compatibility (Avoid v2 syntax).

## 3. Workflow & Documentation Protocol (MANDATORY)

**AT THE START OF EVERY PROMPT:**
1.  **READ `WORKFLOW.md`:** Understand the "Backend-First" strategy. We do not build UI until Data Logic is 100% verified.
2.  **READ `PROGRESS.md`:** Identify the current active task. Do not jump ahead.

**AT THE END OF EVERY TASK:**
1.  **Generate a Report:** You must create a markdown file in `docs/reports/` named `YYYY-MM-DD_TaskName.md` detailing:
    - Files created/modified.
    - Logic explanation (especially for Regex/Parsing).
    - Decisions made.
2.  **Update Progress:** Explicitly ask me to check the box in `PROGRESS.md`.
3.  **Error Logging:** If an error occurred, create a log in `docs/troubleshooting/` explaining the root cause and the fix.

## 4. Coding Standards

### A. Arabic Text Handling (Highest Priority)
- This project processes heavy Arabic text with Diacritics (Tashkeel).
- **NEVER** use standard string functions (`strlen`, `substr`).
- **ALWAYS** use Multibyte String functions (`mb_strlen`, `mb_substr`, `mb_strpos`).
- **Regex:** Always use the `/u` (Unicode) modifier for Arabic patterns.
- **Normalization:** Use the provided `DiacriticStripper` logic for search indexes.

### B. Strict Typing & Quality
- All PHP files must start with `declare(strict_types=1);`.
- Use Return Types for all methods (e.g., `: void`, `: array`, `: BelongsTo`).
- Use PHP 8.2+ features (Readonly classes, Enums) where appropriate.

### C. Database Design
- **Naming:** Plural table names (`books`), Singular models (`Book`).
- **Indexing:** Index all Foreign Keys and Searchable Columns (`content_searchable`).
- **Encoding:** Ensure `utf8mb4` and `utf8mb4_unicode_ci` (or `0900_ai_ci`) are used.

## 5. Error Handling Strategy
- If a command fails, **DO NOT retry the same command blindly**.
- **Analyze** the error message.
- **Search** for the specific error code/message.
- **Propose** a fix based on evidence, not guessing.
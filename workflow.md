# 🗺️ Workflow Strategy - Sahih Al-Jami Encyclopedia

> **Philosophy:** Backend Logic & Data Integrity First -> User Interface Second.
> **Objective:** Build a robust, error-free parsing engine for 9000+ Hadiths before writing a single line of Frontend CSS.

---

## 🏗️ Phase 1: Infrastructure & Core Domain Logic (The Foundation)
**Focus:** Database Schema, Models, and The Parser Engine.
**Goal:** Transform raw text `[436] (صحيح) (د)` into structured relational data.

### 1.1 Database Architecture
- **Objective:** Design a normalized schema that supports complex relationships (Many-to-Many).
- **Tasks:**
    - `Books` Table: Hierarchy of the book content.
    - `Sources` Table: Decoding symbols (e.g., 'د' -> 'Abu Dawood').
    - `Narrators` Table: Storing metadata and color codes.
    - `Hadiths` Table: Storing Text, Search Text, and Relations.

### 1.2 The Smart Parser Engine (Core Business Logic)
- **Objective:** Automate the data entry process. Manual entry for 9000 records is impossible.
- **Strategy:**
    - Develop `HadithParser` Service using advanced Regex.
    - Logic to extract: Number, Grade, Narrator, Source Codes.
    - Logic to expand codes: `(4)` -> `['d', 't', 'n', 'h']`.
- **Validation:** Write Unit Tests to verify the parser against edge cases.

### 1.3 Data Normalization (Search Optimization)
- **Objective:** Ensure "Instant Search" works regardless of Tashkeel (Diacritics).
- **Strategy:**
    - Implement a Model Observer (`HadithObserver`).
    - On `saving` event: Strip diacritics from `content` and save to `content_searchable`.

---

## 🎛️ Phase 2: Administration & Data Verification (Filament)
**Focus:** Building the tools for the Content Team.
**Goal:** A "Copy/Paste" workflow that takes seconds per Hadith.

### 2.1 Admin Resources
- **HadithResource:** Custom form with "Raw Input" field.
- **Action:** Integrate the `HadithParser` service into the form. When user pastes text, the form auto-fills.
- **BookResource:** Drag-and-drop sorting to match the printed book index.

### 2.2 Quality Assurance (QA)
- **Task:** Import a sample of 50 Hadiths from different chapters.
- **Verification:** Check if relations (Narrators/Sources) are linked correctly.

---

## 🔍 Phase 3: The Search Engine (The Brain)
**Focus:** Performance and Speed.
**Goal:** Google-like search experience (Milliseconds response).

### 3.1 Indexing
- **Tech:** Laravel Scout + MeiliSearch.
- **Config:** Configure index settings (typo tolerance, ranking rules).
- **Task:** Index the `content_searchable` column.

### 3.2 API Development
- **Objective:** Headless Architecture readiness.
- **Task:** Build JSON Resources (`HadithResource`) to return data + relationships + chain colors.

---

## 🎨 Phase 4: Frontend Experience (The Face)
**Focus:** "Islamic Luxury" Design & UX.
**Goal:** A spiritual, distraction-free reading experience.

### 4.1 UI Design System
- **Palette:** Gold, Charcoal, Off-White.
- **Typography:** Amiri (Arabic), Serif (English).
- **Components:** Hadith Card, Visual Sanad (Chain), Book Tree.

### 4.2 Interactivity
- **Tech:** Livewire / Alpine.js.
- **Features:** Instant Search, Infinite Scroll, "Copy with Reference" button.
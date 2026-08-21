---
name: claretian-ui-design
description: >-
  Standard guidelines, style tokens, and workflows for designing and modernizing
  the Claretian ERP User Interfaces (e.g., General Journal, Chart of Accounts, Accounts Receivable).
---

# Claretian Bookstore ERP - UI Design Guidelines

This skill documents the standard design tokens, layouts, and components used to maintain consistent, premium aesthetics across the Claretian Bookstore ERP modules.

---

## 🎨 1. Core Theme & Design Tokens

* **Primary Branding Color (Claretian Red)**: 
  * Hex Code: `#D9251C`
  * Use for: Primary action buttons, active tab borders, active pagination blocks, and key warning badges.
* **Secondary / Neutral Grays**:
  * Off-White (Table Headers / Hover): `#f8fafc`
  * Light Gray Borders: `#e2e8f0` (cards) and `#cbd5e1` (inputs/borders)
  * Dark Slate Text (Keys): `#475569`
  * Deep Dark Text (Values/Titles): `#000000` or `#0f172a`
* **Status Tint Accents**:
  * Success Green Tint (Rating / Paid): `rgba(16, 185, 129, 0.08)` background with `#10b981` text.
  * Danger Red Tint (Outstanding / Overdue): `rgba(217, 37, 28, 0.08)` background with `#D9251C` text.
  * Info Blue Tint (Rating / View): `rgba(59, 130, 246, 0.08)` background with `#3b82f6` text.

---

## 📐 2. Page Layout & Grid Width Expansion

To maximize widescreen real estate for accounting tables and cards, prevent nested margins:
1. **Container Gutter Override**: Add this custom CSS rule inside the page's `@push('styles')` block:
   ```css
   .content-body .container-fluid {
       padding-left: 15px !important;
       padding-right: 15px !important;
       max-width: 100% !important;
   }
   ```
2. **Remove Double Nesting**: Always declare the main wrapper element inside the content body as `<div class="container-fluid p-0">` to clear outer padding overlaps.

---

## 📋 3. Modern Table Designs (General Journal Style)

Accounting lists and records tables must be styled cleanly without heavy grid lines:
* **Table Wrapper**: Wrap table inside a `.table-responsive` block with no outer borders.
* **Header Style (`thead th`)**:
  * Background: `#f8fafc !important`
  * Text Color: `#475569 !important` (bold `700`, uppercase `text-transform: uppercase`, spacing `letter-spacing: 0.8px`)
  * Size: `font-size: 0.72rem !important`
  * Padding: `12px 16px !important`
  * Bottom Border: `2px solid #e2e8f0 !important`
* **Body Style (`tbody td`)**:
  * Padding: `12px 16px !important`
  * Text Color: `#475569 !important` (standard cells) and `#0f172a` (highlighted keys/names)
  * Size: `font-size: 0.84rem !important`
  * Bottom Border: `1px solid #f1f5f9 !important` (no vertical lines)
* **Hover State**: Add transition and highlight on row hover:
  ```css
  tbody tr { transition: all 0.15s ease-in-out !important; }
  tbody tr:hover { background-color: #f8fafc !important; }
  ```

---

## 🔢 4. Paginator Styles (Server-Side Pagination)

Pagination must inherit Bootstrap's native capsule styling while applying Claretian brand colors:
* **HTML Structure (Blade)**:
  ```html
  <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
      {{ $items->onEachSide(0)->links('pagination::bootstrap-4') }}
  </div>
  ```
* **CSS Overrides (Unchecked layouts, inherits native rounded ends `0.75rem`)**:
  ```css
  .pagination .page-item.active .page-link {
      background-color: #D9251C !important;
      border-color: #D9251C !important;
      color: #ffffff !important;
      box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15) !important;
  }

  .pagination .page-link {
      color: #475569 !important;
      border-color: #cbd5e1 !important;
      padding: 8px 14px !important;
      font-size: 0.85rem !important;
      transition: all 0.15s ease-in-out !important;
      background-color: #ffffff !important;
  }

  .pagination .page-link:hover {
      background-color: #f1f5f9 !important;
      color: #0f172a !important;
      border-color: #cbd5e1 !important;
  }
  ```

---

## 🔍 5. Search & Filter Form Layouts

Filters should be placed at the card header in a horizontal flex layout:
* **Left Alignment**: Dropdown selectors (e.g. status, rating, terms).
* **Right Alignment**: Search box.
* **Component Styling**:
  * **Input Group**: Merged magnifying glass container on the left of input box.
    ```html
    <div class="input-group input-group-sm" style="width: 250px;">
        <span class="input-group-text bg-white border-end-0" style="border-color: #cbd5e1; height: 38px; display: flex; align-items: center; justify-content: center; padding: 0 10px; border-top-left-radius: 4px; border-bottom-left-radius: 4px;">
            <i class="las la-search text-muted fs-16"></i>
        </span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Search..." value="{{ request('search') }}" style="height: 38px; border-color: #cbd5e1; border-top-right-radius: 4px; border-bottom-right-radius: 4px; font-size: 0.82rem; padding-left: 0; outline: none; box-shadow: none;">
    </div>
    ```
  * **Separate Action Buttons**:
    * Separate Search/Filter submit button next to the input: `height: 38px`, background `#D9251C` (red), `border-radius: 4px`.
    * Separate Clear button (link styled light gray) to reset parameters.

---

## 📦 6. Detailed Ledgers Modals (Modal-XL)

For displaying customer ledger cards, statements, or detailed accounts:
1. **Modal Size**: Always use **`modal-xl`** to provide plenty of space for widescreen grids.
2. **Modern Info Header**:
   * Left side: Bold customer name (`h4`), outline badge for company under.
   * Right side: High-contrast Outstanding Balance badge:
     ```html
     <span class="px-3 py-2 rounded fw-bold text-danger d-inline-block" style="font-size: 1.15rem; background-color: rgba(217, 37, 28, 0.08); border: 1px solid rgba(217, 37, 28, 0.15);">
         ₱{{ number_format($balance, 2) }}
     </span>
     ```
3. **Tabs Styling**: Use clean flat borderless sub-tabs (`modal-tabs`) with a Claretian Red indicator border on the active tab item.
4. **Customer Profile definition lists (Instead of heavy tables)**:
   * Render details using list items with left-aligned **LineAwesome icons** and dashed bottom borders:
     ```html
     <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-bottom: 1px dashed #e2e8f0 !important;">
         <div class="d-flex align-items-center gap-2">
             <div class="text-muted d-flex align-items-center justify-content-center" style="width: 24px;"><i class="las la-id-badge fs-18"></i></div>
             <span class="text-muted small">Label</span>
         </div>
         <span class="fw-bold text-dark small">Value</span>
     </div>
     ```
5. **Modal Client-Side Pagination Constraint**:
   * When paginating inner grids in tabs using JavaScript, target only actual data grids (`table.table-hover`) so that static definition tables inside the profile tab are not incorrectly paginated.
     ```javascript
     const tables = body.querySelectorAll('table.table-hover');
     tables.forEach(table => initTablePagination(table, 5));
     ```

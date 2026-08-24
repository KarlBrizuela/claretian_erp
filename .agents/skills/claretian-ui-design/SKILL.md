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
* **Action Button Color Mapping**:
  * **View Details**: Blue (`#0ea5e9` or `#3b82f6`)
  * **Edit**: Yellow/Orange-Yellow (`#ffb800` or `#f59e0b`)
  * **Delete**: Red (`#ef4444` or `#D9251C`)
* **Text Depths & Visual Hierarchy**:
  * **Titles & Value Fields**: MUST be Deep Black (`#000000` or `#0f172a`) for maximum readability. This includes section headers (e.g. "Company Details", "Contact Information", "SUPPLIER PROFILE") and user profile value texts.
  * **Keys & Labels**: MUST be Dark Slate Gray (`#475569`) to contrast nicely against values. Never make them too light (do not use `#94a3b8` or `.text-muted` without specific scoping).
* **Secondary / Neutral Grays**:
  * Off-White (Table Headers / Hover): `#f8fafc`
  * Light Gray Borders: `#e2e8f0` (cards) and `#cbd5e1` (inputs/borders)

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
* **Row Action Buttons**:
  * Action buttons inside table cells must use the small, sharp, shadow-shaded icon-only button styles:
    * **View / Details**: `btn btn-info shadow btn-xs sharp text-white` (with icon `<i class="las la-eye"></i>`)
    * **Edit**: `btn btn-warning shadow btn-xs sharp text-white` (with icon `<i class="las la-pen"></i>`)
    * **Delete**: `btn btn-danger shadow btn-xs sharp` (with icon `<i class="las la-trash"></i>`)

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

---

## 📝 7. Form Modals Design Guidelines

For all forms inside popups/modals (e.g. Add/Edit Supplier, Record Invoice, Record Payment):
1. **Modal Header Aesthetics**:
   * NEVER use dark or vibrant colored headers (e.g. `bg-danger`, `bg-dark`, `bg-success`).
   * Headers MUST be clean white or light gray (`#ffffff` or `#f8fafc`), utilizing bold pure black title text (`#000000`) and standard close icons (`btn-close`).
2. **Form Labels Visual Hierarchy**:
   * Form labels MUST use the Dark Slate Gray color (`#475569`), uppercase rendering, bold weight (`600`), and a small size (`0.72rem`) with letter-spacing (`0.5px`).
3. **Inputs and Select Controls**:
   * Borders must be light gray (`#cbd5e1`), with a border-radius of `6px`.
   * Input text value MUST be deep black (`#000000`) for high-contrast.
   * Active inputs must display a subtle Claretian Red `#D9251C` focus ring shadow.
4. **Action Buttons**:
   * Submit/Save buttons must be branded in Claretian Red (`#D9251C`).
   * Cancel/Dismiss buttons must be standard neutral outline/light border style (`btn-light border`).
   * Do not stack buttons; align them horizontally in the footer.

---

## 📐 8. Viewport Spacing & Cutoff Fixes

To prevent fixed main sidebars/containers from cutting off buttons at the bottom of standard pages:
1. **Physical Padding Bottom**: Add `padding-bottom: 80px !important;` to `.content-body .container-fluid` via a style tag inside the view to guarantee clear viewport scrolling.
2. **Button Vertical Text Alignment**: Avoid setting arbitrary `line-height` on forms buttons. Instead, center text vertically using flexbox utilities:
   ```html
   <button type="submit" class="btn btn-sm btn-danger d-inline-flex align-items-center justify-content-center" style="height: 38px;">
       <i class="las la-trash-alt me-1"></i> Button Text
   </button>
   ```

---

## 🔍 9. Select2 Custom Integration

When replacing standard selects with searchable Select2 inputs, enforce the Claretian theme branding manually inside `<style>` overrides:
```css
/* Select2 Height & Border Match */
.select2-container .select2-selection--single {
    height: 38px !important;
    border-color: #cbd5e1 !important;
    border-radius: 6px !important;
}
/* Select2 Rendering Line Height Match */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    color: #000000 !important;
}
/* Focus border override */
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #D9251C !important;
}
/* Prevent sidebar and modal overlapping z-index issues */
.select2-dropdown {
    z-index: 9999 !important;
}
```

---

## 🖇️ 10. Blade Component Layout `@push` Constraints

When building pages inheriting view component layouts like `<x-app-layout>`:
* **Tag Nesting Rule**: All `@push('styles')`, `@push('scripts')`, and styles/scripts stack directives MUST be nested **inside** the layout element tags (before the closing `</x-app-layout>`).
* **Correct Syntax**:
  ```html
  <x-app-layout>
      <!-- Content here -->
      
      @push('scripts')
          <script>...</script>
      @endpush
  </x-app-layout>
  ```
* **Why**: Blade layout components compile within a specific slots rendering cycle. Anything pushed outside the component tags is completely ignored.

---

## 🗂️ 11. Stacking Cards & Height Adjustments

To prevent stacked card containers from stretching artificially when placed side-by-side:
1. **Auto Height**: Set `height: auto !important;` on cards when grouping multiple boxes in a column layout (to override the theme's global `height: 100%` rules).
2. **Top Alignment**: Set `style="align-items: flex-start;"` on the row container. This ensures side-by-side cards maintain their natural content height rather than stretching to match the taller column's bottom.

---

## ⚠️ 12. SweetAlert2 Confirmation Modals

Always override native browser confirmations with SweetAlert2 warnings for destructive actions (e.g. Delete, Void):
1. **Interactive Triggering**: Change confirmation button types to `type="button"` and handle submit events via jQuery:
   ```javascript
   $(document).on('click', '.btn-void-confirm', function(e) {
       e.preventDefault();
       const form = $(this).closest('form');
       Swal.fire({
           title: 'Are you sure?',
           text: "This action will reverse ledger entries and restore/deduct inventory.",
           icon: 'warning',
           showCancelButton: true,
           confirmButtonColor: '#D9251C',
           cancelButtonColor: '#475569',
           confirmButtonText: 'Yes, execute it!'
       }).then((result) => {
           if (result.isConfirmed) {
               form.submit();
           }
       });
   });
   ```

---

## ⚡ 13. Server-Side AJAX Table Search & Pagination (Reload-Free Pattern)

For high-performance data listings, tables, or ledgers, always replace pure client-side filter scripts with server-side query filters refreshed dynamically via AJAX:
1. **Controller Query Mapping**:
   * Eager load parameters, paginate the primary model listing (`paginate(10)->withQueryString()`), and accept search query inputs.
2. **HTML Layout Wrappers**:
   * Wrap the primary table section in `<div id="cashTableContainer">` (or similar ID).
   * Wrap standard paginator links in `<div id="paginationContainer">`:
     ```html
     <div id="paginationContainer" class="mt-4 d-flex justify-content-end pe-4">
         {{ $items->onEachSide(0)->links('pagination::bootstrap-4') }}
     </div>
     ```
3. **Vanilla Fetch AJAX Swapper**:
   * Intercept clicks on links inside `#paginationContainer` and form submits.
   * Fetch the updated page via `fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })`.
   * Parse the HTML response string using `DOMParser`, isolate `#cashTableContainer` and `#paginationContainer` elements from the parsed document, and swap their inner content into the active DOM.
   * Leverage `history.pushState(null, '', url)` to update browser address URLs and handle `popstate` events to support native browser Back/Forward navigation.
   * Auto-synchronize search inputs and toggle search action button text (Search ↔ Clear) and color backgrounds depending on query strings.

---

## 📊 14. Dashboard Metric Card Layouts & Flex Headers

To prevent metric grids and action buttons from wrapping or displaying visual misalignments on dashboard views:
1. **Top-Down Metric Layout**:
   * Layout summaries vertically: display the small gray uppercase label at the top, and place the large highlighted currency values below.
   * Use standard Bootstrap `.row` wrappers to structure metric grids rather than override classes like `g-3` that cause overflow layout issues.
2. **Flex-Shrink Header Controls**:
   * To prevent header titles and action buttons from stacking vertically on standard desktop viewports, apply `flex-shrink: 0;` and `flex: 1 1 auto;` to container headers, forcing items to remain aligned side-by-side.

---

## 🗄️ 15. Cascading Audit Logs Preservation

When designing models where records must be preserved for accounting history audits even if parent relationships are hard-deleted:
1. **Fallback Data Tracking**:
   * Add redundant string columns to the target migration (e.g. `supplier_invoice_no` in the `purchase_returns` schema).
   * Populated these columns during creation so transaction histories are preserved even if parent items are removed.

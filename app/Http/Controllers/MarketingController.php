<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\EmployeeCashAdvance;
use Illuminate\Support\Facades\Storage;

class MarketingController extends Controller
{
    public function dashboard()
    {
        $start = \Carbon\Carbon::now()->startOfMonth();
        $end = \Carbon\Carbon::now()->endOfMonth();

        // Orders in period
        $ordersQuery = \App\Models\SalesOrder::whereBetween('created_at', [$start, $end]);
        $totalOrders = (int) $ordersQuery->count();
        $totalSales = (float) $ordersQuery->sum('total_amount');
        $avgOrder = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0;

        // Sales by channel (platform)
        $channels = \App\Models\SalesOrder::select('platform', \DB::raw('COALESCE(SUM(total_amount),0) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('platform')
            ->orderByDesc('total')
            ->get();
        $topChannel = $channels->first();

        // Chart: daily revenue for the current month
        $daysInMonth = $start->daysInMonth;
        $daily = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $daily[$d] = 0;
        }
        $dailyRows = \App\Models\SalesOrder::select(\DB::raw('DAY(created_at) as day'), \DB::raw('SUM(total_amount) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy(\DB::raw('DAY(created_at)'))
            ->get();
        foreach ($dailyRows as $r) {
            $daily[(int)$r->day] = (float)$r->total;
        }

        // Top products
        $topProducts = \App\Models\SalesOrderItem::select('book_id', \DB::raw('SUM(quantity) as qty'))
            ->groupBy('book_id')
            ->orderByDesc('qty')
            ->with('book')
            ->take(5)
            ->get();

        return view('marketing.dashboard', [
            'title' => 'Marketing Dashboard',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'periodLabel' => 'This Month',
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'avgOrder' => $avgOrder,
            'topChannel' => $topChannel,
            'channels' => $channels,
            'chartCategories' => array_map(function($d){ return (string)$d; }, array_keys($daily)),
            'chartRevenue' => array_values($daily),
            'topProducts' => $topProducts,
        ]);
    }

    public function approvalQueue()
    {
        // 1. Pending Department Approvals (Marketing Manager needs to approve these)
        $salesOrders = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('status', 'pending_mkt_approval')
            ->latest()
            ->get();

        // 2. Pending Cash Advances (Only Marketing Manager or Super Admin)
        $user = auth()->user();
        $isAuthorized = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';
        
        $pendingCashAdvances = $isAuthorized 
            ? EmployeeCashAdvance::where('status', 'pending_supervisor_approval')
                ->where('department_source', 'Marketing')
                ->latest()
                ->get()
            : collect();

        $pendingMaterials = $isAuthorized
            ? \App\Models\Admin\MIS\MaterialReq::with('user')
                ->where('status', 'pending_supervisor_approval')
                ->get()
                ->filter(function ($request) use ($user) {
                    return $request->canBeApprovedBy($user);
                })
            : collect();

        // 3. Pending Stock Transfers (Marketing Manager approves Marketing-origin requests)
        $pendingTransfers = $isAuthorized
            ? \App\Models\StockTransfer::with('fromSite', 'toSite', 'book', 'createdBy')
                ->where('status', 'pending')
                ->where(function ($query) {
                    $query->where('approval_division', 'Marketing')
                        ->orWhere(function ($legacyQuery) {
                            $legacyQuery->whereNull('approval_division')
                                ->whereHas('createdBy', function ($creatorQuery) {
                                    $creatorQuery->where('division', 'like', '%Marketing%')
                                        ->orWhereHas('divisions', function ($divisionQuery) {
                                            $divisionQuery->where('division', 'like', '%Marketing%');
                                        });
                                });
                        });
                })
                ->latest()
                ->get()
            : collect();

        $pendingCctvRequests = $isAuthorized
            ? \App\Models\Admin\MIS\CCTVReq::with('user')
                ->where('status', 'pending approval')
                ->whereHas('user', function ($query) {
                    $query->where('division', 'like', '%Marketing%')
                        ->orWhereHas('divisions', function ($divisionQuery) {
                            $divisionQuery->where('division', 'like', '%Marketing%');
                        });
                })
                ->latest()
                ->get()
            : collect();
        


        // 2. My Activity - My Submissions
        $soSubmissions = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('prepared_by', auth()->id())
            ->latest()
            ->get();

        $caSubmissions = EmployeeCashAdvance::where('user_id', auth()->id())
            ->latest()
            ->get();

        $mySubmissions = collect();
        foreach($soSubmissions as $so) {
            $mySubmissions->push((object)[
                'type' => 'Sales Order',
                'id' => $so->id,
                'reference_no' => $so->so_number,
                'submitted_date' => $so->created_at,
                'amount' => $so->total_amount,
                'status' => $so->status,
                'url' => route('marketing.sales-orders.detail', $so->id),
                'original' => $so
            ]);
        }
        foreach($caSubmissions as $ca) {
            $mySubmissions->push((object)[
                'type' => 'Cash Advance',
                'id' => $ca->id,
                'reference_no' => 'CA-' . str_pad($ca->id, 4, '0', STR_PAD_LEFT),
                'submitted_date' => $ca->created_at,
                'prep_name' => auth()->user()->name,
                'amount' => $ca->amount,
                'status' => $ca->status,
                'original' => $ca
            ]);
        }

        $materialSubmissions = \App\Models\Admin\MIS\MaterialReq::where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($materialSubmissions as $req) {
            $mySubmissions->push((object)[
                'type' => 'Material',
                'id' => $req->material_req_id,
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
                'submitted_date' => $req->created_at,
                'prep_name' => auth()->user()->name,
                'amount' => $req->amount,
                'status' => $req->status,
                'original' => $req
            ]);
        }
        $mySubmissions = $mySubmissions->sortByDesc('submitted_date');

        // 3. My Approved Requests (Requests this manager has already approved)
        $caApproved = EmployeeCashAdvance::where('approved_by_manager', auth()->id())
            ->latest()
            ->get();
        
        $materialApproved = \App\Models\Admin\MIS\MaterialReq::where('approved_by_manager', auth()->id())
            ->latest()
            ->get();
        
        $myApprovedRequests = collect();
        foreach($caApproved as $ca) {
            $myApprovedRequests->push((object)[
                'type' => 'Cash Advance',
                'id' => $ca->id,
                'reference_no' => 'CA-' . str_pad($ca->id, 4, '0', STR_PAD_LEFT),
                'submitted_by' => $ca->employee_name,
                'submitted_date' => $ca->created_at,
                'amount' => $ca->amount,
                'status' => $ca->status,
                'original' => $ca
            ]);
        }

        foreach ($materialApproved as $req) {
            $myApprovedRequests->push((object)[
                'type' => 'Material',
                'id' => $req->material_req_id,
                'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
                'submitted_by' => $req->user->name ?? $req->requested_by,
                'submitted_date' => $req->created_at,
                'amount' => $req->amount,
                'status' => $req->status,
                'original' => $req
            ]);
        }

        return view('marketing.approval-queue', [
            'title' => 'Approval Queue',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'salesOrders' => $salesOrders,
            'pendingCashAdvances' => $pendingCashAdvances,
            'pendingTransfers' => $pendingTransfers,
            'pendingCctvRequests' => $pendingCctvRequests,
            'pendingMaterials' => $pendingMaterials,
            'mySubmissions' => $mySubmissions,
            'myApprovedRequests' => $myApprovedRequests->sortByDesc('submitted_date')
        ]);
    }

    public function myRequests()
    {
        $cashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())
            ->latest()
            ->get();
        $materialRequests = \App\Models\Admin\MIS\MaterialReq::where('user_id', auth()->id())
            ->latest()
            ->get();
        $cctvRequests = \App\Models\Admin\MIS\CCTVReq::where('user_id', auth()->id())
            ->latest()
            ->get();

        $mergedRequests = $cashAdvances->concat($materialRequests)->sortByDesc('created_at');

        return view('marketing.my-requests.index', [
            'title' => '',
            'role' => auth()->user()->position,
            'sidebar' => 'marketing',
            'cashAdvances' => $mergedRequests,
            'cctvRequests' => $cctvRequests,
        ]);
    }


    public function products(Request $request)
    {
        $search = $request->input('search');
        
        $query = Book::with(['product', 'bookCategory', 'bookSubCategory'])
            ->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }

        $books = $query->paginate(15)->withQueryString();
        $categories = BookCategory::whereNull('parent_id')->orderBy('name', 'asc')->get();

        return view('marketing.book-list', [
            'books' => $books,
            'categories' => $categories,
            'title' => 'Book List (Master Registry)',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'search' => $search
        ]);
    }


    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'price' => 'nullable|numeric',
            'category' => 'nullable',
            'sales_description' => 'nullable',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'nullable',
        ]);

        // Check if already listed
        if (Product::where('book_id', $request->book_id)->exists()) {
            return response()->json(['error' => 'This book is already listed on the POS'], 422);
        }

        $book = Book::findOrFail($request->book_id);
        
        $productData = [
            'book_id' => $book->id,
            'name' => $book->name, // Keep a copy for easier searching or can be different
            'price' => $request->price ?? 0,
            'category' => $request->category,
            'sales_description' => $request->sales_description,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image_file')) {
        $path = $request->file('image_file')->store('products', 'public');
        $productData['image'] = $path;
    } elseif ($book->image) {
        // Use the book's existing image if no new one is provided
        $productData['image'] = $book->image;
    }

    Product::create($productData);

        return response()->json(['message' => 'Book successfully listed as a POS product']);
    }

    public function checkSku(Request $request)
    {
        $sku = trim((string)$request->query('sku'));
        $excludeId = $request->query('exclude_id');

        if ($sku === '') {
            return response()->json(['exists' => false]);
        }

        $query = Book::withTrashed()->where('sku', $sku);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:books,sku', // Validates against Books table
            'barcode' => 'nullable|string',
            'nbs_barcode' => 'nullable|string',
            'author' => 'nullable|string',
            'publisher' => 'nullable|string',
            'sub_category' => 'nullable|string',
            'size' => 'nullable|string',
            'pages' => 'nullable|integer',
            'cover_type' => 'nullable|string',
            'book_type' => 'nullable|string',
            'copyright' => 'nullable|string',
            'weight' => 'nullable|string',
            'stock' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'max_stock' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'shelf_number' => 'nullable|string|max:50',
            'rack_number' => 'nullable|string|max:50',
            'category' => 'nullable|string',
            'category_id' => 'nullable|exists:book_categories,id',
            'sub_category_id' => 'nullable|exists:book_categories,id',
            'purchase_description' => 'nullable',
            'item_code' => 'nullable|string|unique:books,item_code',
            'email' => 'nullable|email',
            'contact_number' => 'nullable|string',
            'royalty' => 'nullable|string',
            'article' => 'nullable|string',
            'cogs_account' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        // Explicitly handle empty strings for unique or nullable fields
        if (empty($validated['item_code'])) {
            $validated['item_code'] = null;
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = null;
        }

        // Set defaults for nullable fields that are not nullable in DB
        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['reorder_point'] = $validated['reorder_point'] ?? 0;
        $validated['max_stock'] = $validated['max_stock'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['pages'] = $validated['pages'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        Book::create($validated);

        return response()->json(['message' => 'Book added to Master Registry']);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Books Template');

        $headers = [
            '', // Column A (empty)
            'BOOK TITLE',
            'SKU/CATAGLOG #',
            'ITEM CODE',
            'BARCODE/ISBN',
            'SELLING PRICE',
            'AUTHOR',
            'PUBLISHER',
            'SIZE(LXW)',
            'WEIGHT',
            'PAGES',
            'COVER TYPE',
            'CLASSIFICATION',
            'COPYRIGHT',
            'UNIT COST',
            'CATEGORY',
            'SUB-CATEGORY',
            'ARTILE',
            'ROYALTY',
            'EMAIL',
            'NBS BARCODE'
        ];

        // Write Headers
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Write Sample Data matching user's template Row 2
        $sampleData = [
            '', // Column A
            'Advent Arts and Christmas Crafts : With Prayers and Rituals for Family, School and Church',
            '978-8809125860',
            '', // ITEM CODE (Leave blank to generate automatically)
            '9788809125860',
            255.00,
            'Joanna Rotberg',
            'PAULIST PRESS',
            '11 x 8.500 x .250',
            '230',
            0,
            'Paper',
            'Foreign Book',
            '2020',
            0.00,
            'Pastoral',
            'Liturgy',
            '', // ARTILE
            '', // ROYALTY
            '', // EMAIL
            ''  // NBS BARCODE
        ];

        foreach ($sampleData as $colIndex => $value) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '2', $value);
        }

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9251C'], // Claretian Red color
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:U1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Auto-fit column widths
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            if ($colIndex === 0) {
                $sheet->getColumnDimension('A')->setWidth(5);
            } else {
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="book_import_template.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    public function importBooks(Request $request)
    {
        // Increase time and memory limits for processing large files
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        try {
            $file = $request->file('excel_file');
            // Use setReadDataOnly(true) to significantly reduce memory usage of PhpSpreadsheet
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error reading spreadsheet file: ' . $e->getMessage()], 422);
        }

        if (empty($rows) || count($rows) < 2) {
            return response()->json(['error' => 'The uploaded file contains no data rows.'], 422);
        }

        // Headers cleaning & mapping
        $headers = array_map(function($h) {
            return strtolower(trim(str_replace("\ufeff", '', $h ?? '')));
        }, $rows[0]);

        $findHeader = function($keys, $headers) {
            foreach ($keys as $key) {
                $idx = array_search(strtolower(trim($key)), $headers);
                if ($idx !== false) {
                    return $idx;
                }
            }
            return false;
        };

        // Define column maps with fallbacks
        $colMap = [
            'name' => $findHeader(['book title', 'name', 'title'], $headers),
            'sku' => $findHeader(['sku/cataglog #', 'sku/cataglog', 'cataglog #', 'sku/catalog #', 'sku/catalog', 'sku', 'catalog #'], $headers),
            'item_code' => $findHeader(['item code', 'item_code'], $headers),
            'barcode' => $findHeader(['barcode/isbn', 'barcode', 'isbn'], $headers),
            'price' => $findHeader(['selling price', 'price'], $headers),
            'author' => $findHeader(['author'], $headers),
            'publisher' => $findHeader(['publisher'], $headers),
            'size' => $findHeader(['size(lxw)', 'size', 'size(l x w)'], $headers),
            'weight' => $findHeader(['weight'], $headers),
            'pages' => $findHeader(['pages'], $headers),
            'cover_type' => $findHeader(['cover type', 'cover_type'], $headers),
            'book_type' => $findHeader(['classification', 'book type', 'book_type', 'book-type'], $headers),
            'copyright' => $findHeader(['copyright'], $headers),
            'cost' => $findHeader(['unit cost', 'cost'], $headers),
            'category' => $findHeader(['category'], $headers),
            'sub_category' => $findHeader(['sub-category', 'sub category', 'sub_category'], $headers),
            'article' => $findHeader(['artile', 'article'], $headers),
            'royalty' => $findHeader(['royalty'], $headers),
            'email' => $findHeader(['email'], $headers),
            'nbs_barcode' => $findHeader(['nbs barcode', 'nbs_barcode'], $headers),
            'stock' => $findHeader(['stock'], $headers),
            'shelf_number' => $findHeader(['shelf number', 'shelf_number'], $headers),
            'rack_number' => $findHeader(['rack number', 'rack_number'], $headers),
            'reorder_point' => $findHeader(['reorder point', 'reorder_point'], $headers),
            'max_stock' => $findHeader(['max stock', 'max_stock'], $headers),
            'purchase_description' => $findHeader(['purchase description', 'purchase_description'], $headers),
            'contact_number' => $findHeader(['contact number', 'contact_number'], $headers),
            'cogs_account' => $findHeader(['cogs account', 'cogs_account'], $headers),
        ];

        // Book Title is required
        if ($colMap['name'] === false) {
            return response()->json(['error' => 'Critical column "Book Title" is missing in the Excel sheet.'], 422);
        }

        // Pre-scan file to collect all SKUs and Barcodes for bulk database queries
        $skusInSheet = [];
        $barcodesInSheet = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row, function($cell) { return !is_null($cell) && trim((string)$cell) !== ''; }))) {
                continue;
            }
            $sku = $colMap['sku'] !== false ? trim((string)($row[$colMap['sku']] ?? '')) : '';
            if (!empty($sku)) {
                $skusInSheet[] = $sku;
            }
            $barcode = $colMap['barcode'] !== false ? trim((string)($row[$colMap['barcode']] ?? '')) : '';
            if (!empty($barcode)) {
                $barcodesInSheet[] = $barcode;
            }
        }

        // Fetch existing books by SKU and Barcode in chunks
        $existingBooksBySku = [];
        if (!empty($skusInSheet)) {
            foreach (array_chunk(array_unique($skusInSheet), 1000) as $chunk) {
                $booksChunk = Book::withTrashed()->whereIn('sku', $chunk)->get();
                foreach ($booksChunk as $book) {
                    $existingBooksBySku[$book->sku] = $book;
                }
            }
        }

        $existingBooksByBarcode = [];
        if (!empty($barcodesInSheet)) {
            foreach (array_chunk(array_unique($barcodesInSheet), 1000) as $chunk) {
                $booksChunk = Book::whereIn('barcode', $chunk)->get();
                foreach ($booksChunk as $book) {
                    $existingBooksByBarcode[$book->barcode] = $book;
                }
            }
        }

        // Load all existing categories to memory mapping
        $categories = BookCategory::all();
        $categoryMap = [];
        $subCategoryMap = [];
        foreach ($categories as $cat) {
            if (is_null($cat->parent_id)) {
                $categoryMap[strtolower(trim($cat->name))] = $cat;
            } else {
                $subCategoryMap[$cat->parent_id][strtolower(trim($cat->name))] = $cat;
            }
        }

        // Pre-fetch existing SKU prefixes for query-free autoincrement generation
        $existingSkuPrefixes = Book::withTrashed()
            ->where('sku', 'like', 'SKU-%')
            ->pluck('sku')
            ->toArray();
        $existingSkuPrefixesMap = array_flip($existingSkuPrefixes);

        $skuAutoIncrement = (Book::withTrashed()->max('id') ?? 0) + 1;
        $createdCount = 0;
        $updatedCount = 0;
        $errors = [];
        $processedBarcodes = [];
        $processedSkus = [];

        \DB::beginTransaction();

        try {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                // Check if row is entirely empty
                if (empty(array_filter($row, function($cell) { return !is_null($cell) && trim((string)$cell) !== ''; }))) {
                    continue;
                }

                $rowNum = $i + 1;

                $sku = $colMap['sku'] !== false ? trim((string)($row[$colMap['sku']] ?? '')) : '';
                $name = trim((string)($row[$colMap['name']] ?? ''));

                $isAutoSku = false;
                if (empty($sku)) {
                    $isAutoSku = true;
                    do {
                        $sku = 'SKU-' . str_pad($skuAutoIncrement, 5, '0', STR_PAD_LEFT);
                        $skuAutoIncrement++;
                    } while (isset($existingSkuPrefixesMap[$sku]) || isset($existingBooksBySku[$sku]) || isset($processedSkus[$sku]));
                }

                $hasSkuError = false;
                if (!$isAutoSku) {
                    if (isset($existingBooksBySku[$sku])) {
                        $errors[] = "Row {$rowNum}: SKU \"{$sku}\" already exists in the database.";
                        $hasSkuError = true;
                    }
                    if (isset($processedSkus[$sku])) {
                        $errors[] = "Row {$rowNum}: SKU \"{$sku}\" is duplicated in the uploaded spreadsheet (previously seen on Row {$processedSkus[$sku]}).";
                        $hasSkuError = true;
                    }
                }

                $processedSkus[$sku] = $rowNum;

                if ($hasSkuError) {
                    continue;
                }

                if (empty($name)) {
                    $errors[] = "Row {$rowNum}: Book Title is required.";
                    continue;
                }

                $data = [
                    'sku' => $sku,
                    'name' => $name,
                    'item_code' => $colMap['item_code'] !== false ? trim((string)($row[$colMap['item_code']] ?? '')) : null,
                    'author' => $colMap['author'] !== false ? trim((string)($row[$colMap['author']] ?? '')) : null,
                    'publisher' => $colMap['publisher'] !== false ? trim((string)($row[$colMap['publisher']] ?? '')) : null,
                    'copyright' => $colMap['copyright'] !== false ? trim((string)($row[$colMap['copyright']] ?? '')) : null,
                    'book_type' => $colMap['book_type'] !== false ? trim((string)($row[$colMap['book_type']] ?? '')) : null,
                    'cover_type' => $colMap['cover_type'] !== false ? trim((string)($row[$colMap['cover_type']] ?? '')) : null,
                    'pages' => $colMap['pages'] !== false ? (int)($row[$colMap['pages']] ?? 0) : 0,
                    'size' => $colMap['size'] !== false ? trim((string)($row[$colMap['size']] ?? '')) : null,
                    'weight' => $colMap['weight'] !== false ? trim((string)($row[$colMap['weight']] ?? '')) : null,
                    'stock' => $colMap['stock'] !== false ? (int)($row[$colMap['stock']] ?? 0) : 0,
                    'cost' => $colMap['cost'] !== false ? (float)($row[$colMap['cost']] ?? 0) : 0.0,
                    'price' => $colMap['price'] !== false ? (float)($row[$colMap['price']] ?? 0) : 0.0,
                    'reorder_point' => $colMap['reorder_point'] !== false ? (int)($row[$colMap['reorder_point']] ?? 0) : 0,
                    'max_stock' => $colMap['max_stock'] !== false ? (int)($row[$colMap['max_stock']] ?? 0) : 0,
                    'shelf_number' => $colMap['shelf_number'] !== false ? trim((string)($row[$colMap['shelf_number']] ?? '')) : null,
                    'rack_number' => $colMap['rack_number'] !== false ? trim((string)($row[$colMap['rack_number']] ?? '')) : null,
                    'barcode' => $colMap['barcode'] !== false ? trim((string)($row[$colMap['barcode']] ?? '')) : null,
                    'nbs_barcode' => $colMap['nbs_barcode'] !== false ? trim((string)($row[$colMap['nbs_barcode']] ?? '')) : null,
                    'purchase_description' => $colMap['purchase_description'] !== false ? trim((string)($row[$colMap['purchase_description']] ?? '')) : null,
                    'article' => $colMap['article'] !== false ? trim((string)($row[$colMap['article']] ?? '')) : null,
                    'royalty' => $colMap['royalty'] !== false ? trim((string)($row[$colMap['royalty']] ?? '')) : null,
                    'email' => $colMap['email'] !== false ? trim((string)($row[$colMap['email']] ?? '')) : null,
                    'contact_number' => $colMap['contact_number'] !== false ? trim((string)($row[$colMap['contact_number']] ?? '')) : null,
                    'cogs_account' => $colMap['cogs_account'] !== false ? trim((string)($row[$colMap['cogs_account']] ?? '')) : null,
                    'unit' => 'pcs',
                    'is_active' => true,
                ];

                if (empty($data['item_code'])) $data['item_code'] = null;
                if (empty($data['barcode'])) $data['barcode'] = null;
                if (empty($data['nbs_barcode'])) $data['nbs_barcode'] = null;

                // Handle categories on the fly
                $categoryName = $colMap['category'] !== false ? trim((string)($row[$colMap['category']] ?? '')) : '';
                $subCategoryName = $colMap['sub_category'] !== false ? trim((string)($row[$colMap['sub_category']] ?? '')) : '';

                if (!empty($categoryName)) {
                    $categoryKey = strtolower(trim($categoryName));
                    $category = $categoryMap[$categoryKey] ?? null;
                    if (!$category) {
                        $category = BookCategory::create([
                            'name' => $categoryName,
                            'parent_id' => null
                        ]);
                        $categoryMap[$categoryKey] = $category;
                    }
                    $data['category'] = $categoryName;
                    $data['category_id'] = $category->id;

                    if (!empty($subCategoryName)) {
                        $subCategoryKey = strtolower(trim($subCategoryName));
                        $subCategory = ($subCategoryMap[$category->id] ?? [])[$subCategoryKey] ?? null;
                        if (!$subCategory) {
                            $subCategory = BookCategory::create([
                                'name' => $subCategoryName,
                                'parent_id' => $category->id
                            ]);
                            $subCategoryMap[$category->id][$subCategoryKey] = $subCategory;
                        }
                        $data['sub_category'] = $subCategoryName;
                        $data['sub_category_id'] = $subCategory->id;
                    }
                }

                // Check uniqueness constraints other than SKU (barcode)
                if (!empty($data['barcode'])) {
                    $conflict = $existingBooksByBarcode[$data['barcode']] ?? null;
                    if ($conflict && $conflict->sku !== $sku) {
                        $errors[] = "Row {$rowNum}: Barcode \"{$data['barcode']}\" already exists for book with SKU \"{$conflict->sku}\".";
                        continue;
                    }
                    if (isset($processedBarcodes[$data['barcode']]) && $processedBarcodes[$data['barcode']] !== $sku) {
                        $errors[] = "Row {$rowNum}: Barcode \"{$data['barcode']}\" is duplicated in the uploaded spreadsheet.";
                        continue;
                    }
                    $processedBarcodes[$data['barcode']] = $sku;
                }

                $book = $existingBooksBySku[$sku] ?? null;
                if ($book) {
                    $book->update($data);
                    $updatedCount++;
                } else {
                    $newBook = Book::create($data);
                    $existingBooksBySku[$sku] = $newBook;
                    if (!empty($data['barcode'])) {
                        $existingBooksByBarcode[$data['barcode']] = $newBook;
                    }
                    $createdCount++;
                }
            }

            if (!empty($errors)) {
                \DB::rollBack();
                return response()->json([
                    'error' => 'Import failed due to row errors. No changes were saved.',
                    'details' => $errors
                ], 422);
            }

            \DB::commit();
        } catch (\Throwable $e) {
            if (\DB::transactionLevel() > 0) {
                \DB::rollBack();
            }
            return response()->json(['error' => 'An error occurred during import: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Import completed successfully.',
            'created' => $createdCount,
            'updated' => $updatedCount
        ]);
    }

    public function editProduct($id)
    {
        $product = Product::with('book')->findOrFail($id);
        return response()->json($product);
    }

    public function editBook($id)
    {
        $book = Book::findOrFail($id);
        return response()->json($book);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'price' => 'nullable|numeric',
            'category' => 'nullable',
            'sales_description' => 'nullable',
            'asset_account' => 'nullable',
            'is_active' => 'nullable',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image_file')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return response()->json(['message' => 'POS Listing updated successfully']);
    }

    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:books,sku,' . $id,
            'barcode' => 'nullable',
            'nbs_barcode' => 'nullable|string',
            'author' => 'nullable',
            'publisher' => 'nullable',
            'size' => 'nullable',
            'pages' => 'nullable|integer',
            'unit' => 'nullable',
            'copyright' => 'nullable',
            'book_type' => 'nullable',
            'weight' => 'nullable',
            'cover_type' => 'nullable',
            'royalty' => 'nullable',
            'article' => 'nullable',
            'sub_category' => 'nullable',
            'email' => 'nullable|email',
            'contact_number' => 'nullable',
            'stock' => 'nullable|integer',
            'reorder_point' => 'nullable|integer',
            'max_stock' => 'nullable|integer',
            'cost' => 'nullable|numeric|min:0',
            'cogs_account' => 'nullable',
            'purchase_description' => 'nullable',
            'price' => 'nullable|numeric',
            'category' => 'nullable|string',
            'category_id' => 'nullable|exists:book_categories,id',
            'sub_category_id' => 'nullable|exists:book_categories,id',
            'item_code' => 'nullable|string|unique:books,item_code,' . $id,
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Explicitly handle empty strings
        if (empty($validated['item_code'])) {
            $validated['item_code'] = null;
        }
        if (empty($validated['barcode'])) {
            $validated['barcode'] = null;
        }

        // Set defaults for nullable fields
        $validated['stock'] = $validated['stock'] ?? $book->stock;
        $validated['reorder_point'] = $validated['reorder_point'] ?? 0;
        $validated['max_stock'] = $validated['max_stock'] ?? 0;
        $validated['cost'] = $validated['cost'] ?? 0;
        $validated['pages'] = $validated['pages'] ?? 0;
        $validated['price'] = $validated['price'] ?? 0;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        $book->update($validated);

        return response()->json(['message' => 'Master Book entry updated successfully']);
    }

    // Area Sales
    public function salesOrdersList()
    {
        $orders = \App\Models\SalesOrder::with('customer', 'preparedBy')
                    ->latest()
                    ->paginate(10);

        return view('marketing.sales-orders.list', [
            'title' => 'Sales Orders List',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'orders' => $orders
        ]);
    }
    public function exportSalesOrders()
    {
        $orders = \App\Models\SalesOrder::with(['customer', 'items', 'preparedBy'])
                    ->latest()
                    ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Orders');

        // Headers
        $headers = ['A1' => 'Order Number', 'B1' => 'Customer', 'C1' => 'Order Date',
                    'D1' => 'Platform / Source', 'E1' => 'Total Amount', 'F1' => 'Items Count',
                    'G1' => 'Status', 'H1' => 'Prepared By', 'I1' => 'Pick Qty'];
        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFCC0000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                              'color'       => ['argb' => 'FF999999']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        $row = 2;
        foreach ($orders as $order) {
            $typeDisplay = str_replace('_', ' ', $order->type);
            if ($order->type === 'calculator_pos') $typeDisplay = 'Direct POS';
            if ($order->type === 'ecom_direct')    $typeDisplay = 'ECOM POS';
            $typeDisplay = strtoupper($typeDisplay);

            $displayStatus = str_replace('_', ' ', $order->status);
            if ($order->status === 'draft') {
                $displayStatus = ($order->freight_charges && $order->freight_charges > 0)
                    ? 'Draft (Freight Approved)' : 'Draft (Pending Freight)';
            }
            if ($order->status === 'pending_si_prep')       $displayStatus = 'Gathered (In SI Prep)';
            if ($order->status === 'si_created')            $displayStatus = 'SI Created';
            if ($order->status === 'pending_dr_prep')       $displayStatus = 'SI Signed (In DR Prep)';
            if ($order->status === 'pending_mkt_approval')  $displayStatus = 'Pending Marketing Approval';
            if ($order->status === 'pending_prod_approval') $displayStatus = 'Pending Production Approval';
            $displayStatus = ucwords($displayStatus);

            $sheet->setCellValue("A{$row}", $order->so_number);
            $sheet->setCellValue("B{$row}", $order->customer->customer_name ?? 'Unknown Customer');
            $sheet->setCellValue("C{$row}", $order->created_at->format('Y-m-d'));
            $sheet->setCellValue("D{$row}", $typeDisplay);
            $sheet->setCellValue("E{$row}", (float) $order->total_amount);
            $sheet->setCellValue("F{$row}", $order->items->count());
            $sheet->setCellValue("G{$row}", $displayStatus);
            $sheet->setCellValue("H{$row}", optional($order->preparedBy)->name ?? '');
            $sheet->setCellValue("I{$row}", '');

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:I{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF5F5F5');
            }
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $filename = 'Sales_Orders_' . now()->format('Y-m-d_His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportSingleSalesOrder($id)
    {
        $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'areaSalesStaff'])
                    ->where('type', 'area_sales_consignment')
                    ->findOrFail($id);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SO ' . $order->so_number);

        // Order header banner (row 1)
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'AREA SALES CONSIGNMENT — ' . $order->so_number);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFCC0000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Order meta info rows 2-7
        $meta = [
            ['Sales Order #',    $order->so_number],
            ['Order Date',       $order->created_at->format('Y-m-d')],
            ['Area Sales Staff', optional($order->areaSalesStaff)->name ?? '—'],
            ['Status',           ucwords(str_replace('_', ' ', $order->status))],
            ['Total Amount',     '₱' . number_format($order->total_amount, 2)],
            ['Customer Name',    $order->customer?->customer_name ?? ''],  // blank if no customer — staff fills this in
        ];
        $metaRow = 2;
        foreach ($meta as [$label, $value]) {
            $sheet->setCellValue("A{$metaRow}", $label);
            $sheet->setCellValue("B{$metaRow}", $value);
            $sheet->getStyle("A{$metaRow}")->getFont()->setBold(true);
            $metaRow++;
        }

        // Highlight the Customer Name row (B7) in light blue so staff knows to fill it
        $sheet->getStyle("A7:B7")->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                       'startColor' => ['argb' => 'FFD6EAF8']],
            'font' => ['color' => ['argb' => 'FF1A5276'], 'bold' => true],
        ]);
        // Add placeholder hint in B7 if empty
        if (empty($order->customer?->customer_name)) {
            $sheet->getComment('B7')->getText()->createTextRun('Fill in the customer name here before importing back.');
        }

        // Items table header
        $tableStart = $metaRow + 1;
        $colHeaders = ['#', 'Book Title / Product', 'Unit', 'Order Qty', 'Unit Price', 'Subtotal', 'Pick Qty'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($cols as $i => $col) {
            $sheet->setCellValue("{$col}{$tableStart}", $colHeaders[$i]);
        }
        // Style columns A-F (dark header)
        $sheet->getStyle("A{$tableStart}:F{$tableStart}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FF333333']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                              'color'       => ['argb' => 'FFAAAAAA']]],
        ]);
        // Style column G - Pick Qty header (orange bg, dark bold text)
        $sheet->getStyle("G{$tableStart}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FF7B3F00']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFA500']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                              'color'       => ['argb' => 'FFAAAAAA']]],
        ]);
        $sheet->getRowDimension($tableStart)->setRowHeight(20);

        // Item data rows
        $dataRow = $tableStart + 1;
        $seq = 1;
        foreach ($order->items as $item) {
            $sheet->setCellValue("A{$dataRow}", $seq++);
            $sheet->setCellValue("B{$dataRow}", optional($item->book)->name ?? 'Unknown Product');
            $sheet->setCellValue("C{$dataRow}", $item->unit ?? 'pcs');
            $sheet->setCellValue("D{$dataRow}", (int) $item->quantity);
            $sheet->setCellValue("E{$dataRow}", (float) $item->price);
            $sheet->setCellValue("F{$dataRow}", (float) $item->subtotal);
            $sheet->setCellValue("G{$dataRow}", ''); // Pick Qty — blank for manual entry

            $sheet->getStyle("E{$dataRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("F{$dataRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            if ($dataRow % 2 === 0) {
                $sheet->getStyle("A{$dataRow}:G{$dataRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF9F9F9');
            }
            $sheet->getStyle("A{$dataRow}:G{$dataRow}")->getBorders()
                ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->getColor()->setARGB('FFDDDDDD');

            $dataRow++;
        }

        // Highlight Pick Qty data cells — light orange background, dark orange text
        $sheet->getStyle("G" . ($tableStart + 1) . ":G" . ($dataRow - 1))->applyFromArray([
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFF0D9']],
            'font'      => ['color' => ['argb' => 'FF7B3F00'], 'italic' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(8);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->freezePane("A" . ($tableStart + 1));

        // Download
        $filename = 'SO_' . $order->so_number . '_' . now()->format('Ymd') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    public function salesOrderDetail($id = null)
    {
        $order = null;
        if ($id) {
            $order = \App\Models\SalesOrder::with('customer', 'items.book', 'preparedBy', 'areaSalesStaff')->findOrFail($id);
        }

        return view('marketing.sales-orders.detail', [
            'title' => 'Sales Order',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'order' => $order
        ]);
    }

    public function shippingLabel($id, Request $request)
    {
        $order = \App\Models\SalesOrder::with('customer')->findOrFail($id);
        
        // Get address from query parameter if provided (edited address), otherwise use original
        $address = $request->query('address') ? urldecode($request->query('address')) : ($order->shipping_address ?: ($order->customer->shipping_address ?: $order->customer->billing_address));

        return view('marketing.sales-orders.shipping-label', [
            'order' => $order,
            'address' => $address
        ]);
    }

    public function createSalesOrder()
    {
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $products = \App\Models\Book::where('is_active', true)->get();
        $areaSalesStaff = \App\Models\User::where('department', 'Area Sales')->get();

        return view('marketing.sales-orders.create', [
            'title' => 'Create Sales Order',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'areaSalesStaff' => $areaSalesStaff
        ]);
    }

    public function storeSalesOrder(\Illuminate\Http\Request $request)
    {
        $action = $request->input('action', 'submit'); // 'draft' or 'submit'
        
        $validated = $request->validate([
            'customer_id' => $request->input('type') === 'area_sales_consignment' ? 'nullable|exists:customers,customer_id' : 'required|exists:customers,customer_id',
            'area_sales_staff_id' => $request->input('type') === 'area_sales_consignment' ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'type' => 'required',
            'so_number' => 'required|unique:sales_orders,so_number',
            'items' => $action === 'draft' ? 'nullable|array' : 'required|array|min:1', // Items optional for draft
            'remarks' => 'nullable',
            'terms' => 'nullable',
            'ref_number' => 'nullable',
            'billing_address' => 'nullable',
            'attachment' => 'nullable|file|max:5120', // 5MB Limit
            'proof_of_payment' => 'nullable|file|max:5120', // 5MB Limit
            'freight_option' => 'nullable|string|in:freight_collect,freight_billing',
        ]);

        // For submitted SOs, validate stock
        if ($action === 'submit') {
            // STOCK VALIDATION: Check if all items have sufficient stock
            $insufficientItems = [];
            foreach ($request->items ?? [] as $item) {
                $book = \App\Models\Book::find($item['product_id']);
                if (!$book || $book->stock < $item['quantity']) {
                    $bookName = $book ? $book->name : "Product #{$item['product_id']}";
                    $availableStock = $book ? $book->stock : 0;
                    $insufficientItems[] = "$bookName (Available: $availableStock pcs, Requested: {$item['quantity']} pcs)";
                }
            }

            if (!empty($insufficientItems)) {
                return redirect()->back()->with('error', 'Insufficient stock for the following items: ' . implode('<br>• ', $insufficientItems));
            }
        }

        // 1. Determine Initial Status
        if ($action === 'draft') {
            // Draft mode: wait for freight quotation
            $initialStatus = 'draft';
        } else {
            // Submit mode: proceed with approval flow
            $initialStatus = 'pending_mkt_approval';
            
            // Check if user is already a Manager/Supervisor to auto-approve to next stage
            $isMktManager = str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor');
            
            if ($isMktManager) {
                $initialStatus = 'pending_acct_approval';
            }
        }

        // 2. Handle Attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('sales_orders', 'public');
        }

        $proofOfPaymentPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $proofOfPaymentPath = $request->file('proof_of_payment')->store('sales_orders', 'public');
        }

        // 3. Create Header
        $so = \App\Models\SalesOrder::create([
            'customer_id' => $request->customer_id,
            'area_sales_staff_id' => $request->type === 'area_sales_consignment' ? $request->area_sales_staff_id : null,
            'so_number' => $request->so_number,
            'type' => $request->type,
            'status' => $initialStatus,
            'prepared_by' => auth()->id(),
            'approved_by_mkt' => $action === 'submit' ? auth()->id() : null, // Only set for submissions
            'remarks' => $request->remarks,
            'terms' => $request->terms,
            'ref_number' => $request->ref_number,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'attachment' => $attachmentPath,
            'proof_of_payment' => $proofOfPaymentPath,
            'freight_option' => $validated['freight_option'] ?? null,
        ]);

        // 4. Create Items (only if provided)
        $totalAmount = 0;
        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $totalAmount += $subtotal;

                $book = \App\Models\Book::find($item['product_id']);
                \App\Models\SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'book_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'unit' => $item['unit'] ?? 'pcs',
                    'area' => $item['area'] ?? null,
                    'source_price_at_sale' => $book ? $book->source_price : 0,
                ]);

            }
        }

        if (($validated['freight_option'] ?? null) === 'freight_collect') {
            $totalAmount += 50.00;
        }

        // 5. Update Total
        $so->update(['total_amount' => $totalAmount]);

        // 6. Set transaction type to COD if SO type is 'cod'
        if ($validated['type'] === 'cod') {
            $so->update([
                'transaction_type' => 'COD',
            ]);
        }

        $message = $action === 'draft' 
            ? 'Sales Order saved as draft. Please request freight quotation from Logistics.'
            : 'Sales Order created and routed successfully!';
        
        return redirect()->route('marketing.sales-orders.list')->with('success', $message);
    }

    public function approveSalesOrder(Request $request, $id)
    {
        // Role Enforcement: Only Marketing Manager or Supervisor
        if (!str_contains(auth()->user()->position, 'Manager') && !str_contains(auth()->user()->position, 'Supervisor')) {
            return redirect()->back()->with('error', 'Only Marketing Managers or Supervisors can approve Sales Orders.');
        }

        $order = \App\Models\SalesOrder::findOrFail($id);
        
        // E-com direct orders go directly to pending_si_prep (Sales Invoice Prep)
        // All other SO types proceed to Accounting approval after Marketing Manager approval
        $nextStatus = $order->type === 'ecom_direct' ? 'pending_si_prep' : 'pending_acct_approval';
        
        $order->update([
            'status' => $nextStatus,
            'approved_by_mkt' => auth()->id(),
            'mkt_approved_at' => now()
        ]);

        $successMsg = 'Sales Order #' . $order->so_number . ' has been approved by Marketing.';
        if ($order->type === 'ecom_direct') {
            $successMsg .= ' It now appears in the Sales Invoice list for preparation.';
        } else {
            $successMsg .= ' Awaiting Accounting approval.';
        }

        return redirect()->route('marketing.approval-queue')->with('success', $successMsg);
    }

    public function proceedToFinalSalesOrder(Request $request, $id)
    {
        /**
         * This method finalizes a draft SO after freight charges have been approved
         * Transitions: draft (with freight_charges) → pending_mkt_approval
         */
        $so = \App\Models\SalesOrder::findOrFail($id);
        
        // Validate: only draft SOs with freight charges can proceed
        if ($so->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft sales orders can be finalized.');
        }

        if (!$so->freight_charges || $so->freight_charges <= 0) {
            return redirect()->back()->with('error', 'Freight charges must be approved before proceeding.');
        }

        // Transition to pending approval
        $isMktManager = str_contains(auth()->user()->position, 'Manager') || str_contains(auth()->user()->position, 'Supervisor');
        $nextStatus = $isMktManager ? 'pending_acct_approval' : 'pending_mkt_approval';
        
        $so->update([
            'status' => $nextStatus,
            'approved_by_mkt' => $isMktManager ? auth()->id() : null,
            'mkt_approved_at' => $isMktManager ? now() : null,
        ]);


        $message = 'Sales Order #' . $so->so_number . ' has been finalized with freight charges (₱' . number_format($so->freight_charges, 2) . ') and routed for approval.';
        return redirect()->route('marketing.sales-orders.list')->with('success', $message);
    }

    public function editSalesOrder($id)
    {
        $order = \App\Models\SalesOrder::with('items.book')->findOrFail($id);
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        $products = \App\Models\Book::where('is_active', true)->get();
        $areaSalesStaff = \App\Models\User::where('department', 'Area Sales')->get();

        return view('marketing.sales-orders.create', [
            'title' => 'Edit Sales Order',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'order' => $order,
            'areaSalesStaff' => $areaSalesStaff
        ]);
    }

    // Handle AJAX/JSON updates for freight option only
    public function updateSalesOrderQuick(Request $request, $id)
    {
        try {
            $so = \App\Models\SalesOrder::findOrFail($id);
            
            // Only allow updates on non-completed/cancelled orders
            if (in_array($so->status, ['completed', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit completed or cancelled orders.'
                ], 422);
            }

            $validated = $request->validate([
                'freight_option' => 'nullable|string|in:,freight_collect,freight_billing'
            ]);

            // Calculate items subtotal (sum of all line items)
            $itemsSubtotal = $so->items()->sum('subtotal');
            
            // Add service fee (₱50.00) if freight_option is 'freight_collect'
            $serviceFee = ($validated['freight_option'] === 'freight_collect') ? 50.00 : 0;
            $newTotal = $itemsSubtotal + ($so->freight_charges ?? 0) + $serviceFee;

            $so->update([
                'freight_option' => $validated['freight_option'],
                'total_amount' => $newTotal
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sales Order updated successfully!',
                'data' => $so
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSalesOrder(Request $request, $id)
    {
        $so = \App\Models\SalesOrder::with('items')->findOrFail($id);
        
        $validated = $request->validate([
            'customer_id' => $request->input('type') === 'area_sales_consignment' ? 'nullable|exists:customers,customer_id' : 'required|exists:customers,customer_id',
            'area_sales_staff_id' => $request->input('type') === 'area_sales_consignment' ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'type' => 'required',
            'items' => 'required|array|min:1',
            'remarks' => 'nullable',
            'terms' => 'nullable',
            'ref_number' => 'nullable',
            'billing_address' => 'nullable',
            'attachment' => 'nullable|file|max:5120',
            'proof_of_payment' => 'nullable|file|max:5120',
            'freight_option' => 'nullable|string|in:freight_collect,freight_billing',
        ]);

        if ($request->hasFile('attachment')) {
            // optional: delete old file
            $path = $request->file('attachment')->store('sales_orders', 'public');
            $so->attachment = $path;
        }

        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('sales_orders', 'public');
            $so->proof_of_payment = $path;
        }

        $so->update([
            'customer_id' => $request->customer_id,
            'area_sales_staff_id' => $request->type === 'area_sales_consignment' ? $request->area_sales_staff_id : null,
            'type' => $request->type,
            'remarks' => $request->remarks,
            'terms' => $request->terms,
            'ref_number' => $request->ref_number,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'freight_option' => $validated['freight_option'] ?? null,
        ]);


        // Re-create items
        $so->items()->delete();
        
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $subtotal = $item['quantity'] * $item['price'];
            $totalAmount += $subtotal;

            $book = \App\Models\Book::find($item['product_id']);
            \App\Models\SalesOrderItem::create([
                'sales_order_id' => $so->id,
                'book_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $subtotal,
                'unit' => $item['unit'] ?? 'pcs',
                'area' => $item['area'] ?? null,
            ]);

        }

        if (($validated['freight_option'] ?? null) === 'freight_collect') {
            $totalAmount += 50.00;
        }

        $totalAmount += $so->freight_charges ?? 0;

        $so->update(['total_amount' => $totalAmount]);

        return redirect()->route('marketing.sales-orders.list')->with('success', 'Sales Order updated successfully!');
    }

    public function directInvoiceWebsite()
    {
        $customers = \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get();
        $products = \App\Models\Book::where('is_active', true)->orderBy('name')->get();
        $invoices = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'website_direct')
            ->latest()
            ->get();

        return view('marketing.direct-invoice-website', [
            'title' => 'Direct Invoice (Website)',
            'role' => auth()->user()->position ?? 'Marketing Staff',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'invoices' => $invoices,
        ]);
    }

    public function storeDirectInvoice(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'transaction_subtype' => 'required|in:foreign,local',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'terms' => 'nullable|string',
            'remarks' => 'nullable|string',
            'proof_of_payment' => 'required|file|max:10240',
            'order_list' => 'required|file|max:10240',
        ]);

        // STOCK VALIDATION: Check if all items have sufficient stock
        $insufficientItems = [];
        foreach ($request->items as $item) {
            $book = Book::find($item['product_id']);
            if (!$book || $book->stock < $item['quantity']) {
                $bookName = $book ? $book->name : "Product #{$item['product_id']}";
                $availableStock = $book ? $book->stock : 0;
                $insufficientItems[] = "$bookName (Available: $availableStock pcs, Requested: {$item['quantity']} pcs)";
            }
        }

        if (!empty($insufficientItems)) {
            return redirect()->back()->with('error', 'Insufficient stock for the following items: ' . implode('<br>• ', $insufficientItems));
        }

        // Generate unique invoice number
        $lastInvoice = \App\Models\SalesOrder::where('type', 'website_direct')
            ->orderBy('id', 'desc')
            ->first();
        $nextNum = $lastInvoice ? (intval(substr($lastInvoice->so_number, -4)) + 1) : 1;
        $invoiceNumber = 'DI-WEB-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // Store attachments
        $popPath = $request->file('proof_of_payment')->store('direct_invoices/pop', 'public');
        $olPath = $request->file('order_list')->store('direct_invoices/order_lists', 'public');

        // Determine initial status based on transaction subtype and role
        $user = auth()->user();
        $isManagerOrSupervisor = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';

        if ($request->transaction_subtype === 'foreign') {
            // Foreign → Production Manager/Supervisor
            $initialStatus = $isManagerOrSupervisor ? 'picking' : 'pending_prod_approval';
        } else {
            // Local → Marketing Manager/Supervisor
            $initialStatus = $isManagerOrSupervisor ? 'picking' : 'pending_mkt_approval';
        }

        // Create the Sales Order (Invoice)
        $so = \App\Models\SalesOrder::create([
            'customer_id' => $request->customer_id,
            'so_number' => $invoiceNumber,
            'type' => 'website_direct',
            'transaction_subtype' => $request->transaction_subtype,
            'status' => $initialStatus,
            'prepared_by' => auth()->id(),
            'approved_by_mkt' => ($isManagerOrSupervisor && $request->transaction_subtype === 'local') ? auth()->id() : null,
            'approved_by_prod' => ($isManagerOrSupervisor && $request->transaction_subtype === 'foreign') ? auth()->id() : null,
            'mkt_approved_at' => ($isManagerOrSupervisor && $request->transaction_subtype === 'local') ? now() : null,
            'prod_approved_at' => ($isManagerOrSupervisor && $request->transaction_subtype === 'foreign') ? now() : null,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'terms' => $request->terms,
            'remarks' => $request->remarks,
            'proof_of_payment' => $popPath,
            'order_list_attachment' => $olPath,
        ]);

        // Create items
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $subtotal = $item['quantity'] * $item['price'];
            $totalAmount += $subtotal;

            $book = Book::find($item['product_id']);
            \App\Models\SalesOrderItem::create([
                'sales_order_id' => $so->id,
                'book_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $subtotal,
                'unit' => $item['unit'] ?? 'pcs',
                'source_price_at_sale' => $book ? $book->source_price : 0,
            ]);

        }

        $so->update(['total_amount' => $totalAmount]);

        $statusMsg = $initialStatus === 'picking'
            ? 'Invoice created and auto-approved! Routed to Logistics for picking.'
            : 'Invoice created and submitted for approval.';

        return redirect()->route('marketing.direct-invoice.website')->with('success', $statusMsg . ' Invoice #' . $invoiceNumber);
    }

    public function directInvoiceList()
    {
        $invoices = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'website_direct')
            ->latest()
            ->paginate(15);

        return view('marketing.direct-invoice-list', [
            'title' => 'Website Invoices',
            'role' => auth()->user()->position ?? 'Marketing Staff',
            'sidebar' => 'marketing',
            'invoices' => $invoices,
        ]);
    }

    public function approveDirectInvoice(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if ($order->type !== 'website_direct') {
            return redirect()->back()->with('error', 'This is not a website direct invoice.');
        }

        $user = auth()->user();
        $isManager = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';

        if (!$isManager) {
            return redirect()->back()->with('error', 'Only Managers or Supervisors can approve invoices.');
        }

        // Foreign: Production Manager approves
        // Local: Marketing Manager approves
        if ($order->transaction_subtype === 'foreign') {
            $order->update([
                'status' => 'picking',
                'approved_by_prod' => auth()->id(),
                'prod_approved_at' => now(),
            ]);
        } else {
            $order->update([
                'status' => 'picking',
                'approved_by_mkt' => auth()->id(),
                'mkt_approved_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Invoice #' . $order->so_number . ' approved! Routed to Logistics for picking.');
    }

    public function directInvoiceEcom()
    {
        $customers = \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get();
        $products = \App\Models\Book::where('is_active', true)->orderBy('name')->get();
        $invoices = \App\Models\SalesOrder::with('customer', 'preparedBy')
            ->where('type', 'ecom_direct')
            ->latest()
            ->get();

        return view('marketing.direct-invoice-ecom', [
            'title' => 'Direct Invoice (E-com)',
            'role' => auth()->user()->position ?? 'Marketing Staff',
            'sidebar' => 'marketing',
            'customers' => $customers,
            'products' => $products,
            'invoices' => $invoices,
        ]);
    }

    public function storeDirectInvoiceEcom(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'ecom_platform' => 'required|in:lazada,shopee,tiktok',
            'platform_order_id' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'terms' => 'nullable|string',
            'pick_list' => 'nullable|file|max:10240',
            'shipping_label' => 'nullable|file|max:10240',
        ]);

        // STOCK VALIDATION: Check if all items have sufficient stock
        $insufficientItems = [];
        foreach ($request->items as $item) {
            $book = Book::find($item['product_id']);
            if (!$book || $book->stock < $item['quantity']) {
                $bookName = $book ? $book->name : "Product #{$item['product_id']}";
                $availableStock = $book ? $book->stock : 0;
                $insufficientItems[] = "$bookName (Available: $availableStock pcs, Requested: {$item['quantity']} pcs)";
            }
        }

        if (!empty($insufficientItems)) {
            return redirect()->back()->with('error', 'Insufficient stock for the following items:' . implode('<br>• ', $insufficientItems));
        }

        // Generate unique invoice number
        $lastInvoice = \App\Models\SalesOrder::where('type', 'ecom_direct')
            ->orderBy('id', 'desc')
            ->first();
        $nextNum = $lastInvoice ? (intval(substr($lastInvoice->so_number, -4)) + 1) : 1;
        $invoiceNumber = 'DI-ECOM-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // Store attachments (optional)
        $pickListPath = $request->hasFile('pick_list') ? $request->file('pick_list')->store('direct_invoices/pick_lists', 'public') : null;
        // $shippingLabelPath = $request->file('shipping_label')->store('direct_invoices/shipping_labels', 'public');

        // E-com direct invoices always require approval before proceeding to Sales Invoice
        // They bypass the regular picking workflow
        $initialStatus = 'pending_mkt_approval';

        $so = \App\Models\SalesOrder::create([
            'customer_id' => $request->customer_id,
            'so_number' => $invoiceNumber,
            'type' => 'ecom_direct',
            'ecom_platform' => $request->ecom_platform,
            'platform_order_id' => $request->platform_order_id,
            'status' => $initialStatus,
            'prepared_by' => auth()->id(),
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->billing_address,
            'terms' => $request->terms,
            'pick_list_attachment' => $pickListPath,
            // 'shipping_label_attachment' => $shippingLabelPath,
        ]);

        // Create items
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $subtotal = $item['quantity'] * $item['price'];
            $totalAmount += $subtotal;

            $book = Book::find($item['product_id']);
            \App\Models\SalesOrderItem::create([
                'sales_order_id' => $so->id,
                'book_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $subtotal,
                'unit' => $item['unit'] ?? 'pcs',
                'source_price_at_sale' => $book ? $book->source_price : 0,
            ]);

        }

        $so->update(['total_amount' => $totalAmount]);

        return redirect()->route('marketing.direct-invoice.ecom')->with('success', 'Direct Invoice #' . $invoiceNumber . ' created and submitted for Marketing approval.');
    }

    public function approveDirectInvoiceEcom(Request $request, $id)
    {
        $order = \App\Models\SalesOrder::findOrFail($id);

        if ($order->type !== 'ecom_direct') {
            return redirect()->back()->with('error', 'This is not an E-com direct invoice.');
        }

        $user = auth()->user();
        $isManager = str_contains($user->position, 'Manager') || str_contains($user->position, 'Supervisor') || $user->position === 'Super Admin';

        if (!$isManager) {
            return redirect()->back()->with('error', 'Only Managers or Supervisors can approve invoices.');
        }

        // Direct Invoice Ecom workflow: After approval, route to Sales Invoice (Accounting)
        $order->update([
            'status' => 'pending_si_prep',
            'approved_by_mkt' => auth()->id(),
            'mkt_approved_at' => now(),
        ]);

        return redirect()->route('admin-finance.accounting.sales-invoice')
            ->with('success', 'Invoice #' . $order->so_number . ' approved! It now appears in the Sales Invoice list for preparation.');
    }

    public function acknowledgementReceipt()
    {
        return view('marketing.acknowledgement-receipt', [
            'title' => 'Acknowledgement Receipt',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function creditMemo()
    {
        return view('marketing.credit-memo', [
            'title' => 'Credit Memo Form',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function proofOfPayment()
    {
        return view('marketing.proof-of-payment', [
            'title' => 'Proof of Payment',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function salesInvoice()
    {
        return view('marketing.sales-invoice', [
            'title' => 'Sales Invoice',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function pickListManagement()
    {
        return view('marketing.pick-list-management', [
            'title' => 'Pick List Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function pickLists()
    {
        return view('marketing.pick-lists', [
            'title' => 'Pick Lists',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function deliveryReceipt()
    {
        // Get sales orders that need delivery receipt
        $salesOrders = \App\Models\SalesOrder::with('customer', 'items.product')
            ->whereIn('status', ['gathered', 'pending_si_prep', 'pending_si_approval', 'ready_for_delivery'])
            ->latest()
            ->get();
        
        // Get all customers
        $customers = \App\Models\Customer::orderBy('customer_name')->get();

        // Get existing delivery receipts with related data
        $deliveryReceipts = \App\Models\DeliveryReceipt::with('salesOrder', 'salesInvoice', 'customer', 'items', 'preparedByUser')
            ->latest()
            ->get();

        return view('marketing.delivery-receipt', [
            'title' => 'Delivery Receipt',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'salesOrders' => $salesOrders,
            'customers' => $customers,
            'deliveryReceipts' => $deliveryReceipts
        ]);
    }

    public function deliveryReceiptList()
    {
        return view('marketing.delivery-receipt-list', [
            'title' => 'Delivery Receipts',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function orderFulfillment()
    {
        return view('marketing.order-fulfillment', [
            'title' => 'Order Fulfillment',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function packingScheduling()
    {
        return view('marketing.packing-scheduling', [
            'title' => 'Packing & Scheduling',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function deliveryScheduling()
    {
        return view('marketing.delivery-scheduling', [
            'title' => 'Delivery Scheduling',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function deliveryTracking()
    {
        return view('marketing.delivery-tracking', [
            'title' => 'Delivery Tracking',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function salesReports()
    {
        return view('marketing.sales-reports', [
            'title' => 'Sales Reports',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function territoryManagement()
    {
        return view('marketing.territory-management', [
            'title' => 'Territory Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    // Direct Sales
    public function posSale()
    {
        $products = Book::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'category' => strtolower($p->category ?? 'books'),
                    'name' => $p->name,
                    'price' => (float)$p->price,
                    'barcode' => $p->barcode,
                    'sku' => $p->sku,
                    'image' => $p->image ? asset('storage/' . $p->image) : asset('images/no-book-cover.svg')
                ];
            });

        return view('marketing.direct-sales.pos', [
            'products' => $products,
            'customers' => \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get(),
            'title' => 'New Sale - Point of Sale',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function posProducts()
    {
        // Fetch all books for POS management
        $products = Book::orderBy('name', 'asc')->get();

        return view('marketing.direct-sales.products', [
            'products' => $products,
            'title' => 'POS Products Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    // E-Com
    public function ecomPos()
    {
        $products = Book::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'category' => strtolower($p->category ?? 'books'),
                    'name' => $p->name,
                    'price' => (float)$p->price,
                    'image' => $p->image ? asset('storage/' . $p->image) : asset('images/no-book-cover.svg')
                ];
            });

        return view('marketing.ecom.pos', [
            'title' => 'E-Commerce POS',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'products' => $products,
            'customers' => \App\Models\Customer::where('is_inactive', false)->orderBy('customer_name')->get()
        ]);
    }

    // Suppliers & Purchases
    public function suppliers()
    {
        return view('marketing.suppliers', [
            'title' => 'Supplier Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }


    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // This will now be permanent because SoftDeletes was removed from model

        return response()->json(['message' => 'Product deleted and erased from database successfully']);
    }

    public function destroySalesOrder($id)
    {
        $so = \App\Models\SalesOrder::findOrFail($id);
        
        $so->items()->delete();
        $so->delete();

        return redirect()->route('marketing.sales-orders.list')->with('success', 'Sales Order deleted successfully!');
    }

    // Ads and Promo
    public function adsPromoCampaigns()
    {
        return view('marketing.ads-promo.campaigns');
    }

    public function adsPromoPromotions()
    {
        return view('marketing.ads-promo.promotions', [
            'title' => 'Promotions',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function crpr()
    {
        return view('marketing.ads-promo.crpr', [
            'title' => 'Marketing Plan Itinerary Budget',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    public function sponsors()
    {
        $sponsors = \App\Models\Sponsor::latest()->get();
        return view('marketing.ads-promo.sponsors', [
            'title' => 'List of Sponsors',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'sponsors' => $sponsors
        ]);
    }

    public function storeSponsor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'remarks' => 'nullable|string',
            'contact_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('contact_file')) {
            $path = $request->file('contact_file')->store('sponsors', 'public');
            $validated['file_path'] = $path;
        }

        \App\Models\Sponsor::create($validated);

        return redirect()->back()->with('success', 'Sponsor added successfully.');
    }

    public function updateSponsor(Request $request, $id)
    {
        $sponsor = \App\Models\Sponsor::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'remarks' => 'nullable|string',
            'contact_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('contact_file')) {
            if ($sponsor->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($sponsor->file_path);
            }
            $path = $request->file('contact_file')->store('sponsors', 'public');
            $validated['file_path'] = $path;
        }

        $sponsor->update($validated);

        return redirect()->back()->with('success', 'Sponsor updated successfully.');
    }

    public function destroySponsor($id)
    {
        $sponsor = \App\Models\Sponsor::findOrFail($id);
        
        if ($sponsor->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($sponsor->file_path);
        }
        
        $sponsor->delete();

        return redirect()->back()->with('success', 'Sponsor deleted successfully.');
    }

    public function destroyBook($id)
    {
        $book = Book::findOrFail($id);
        
        try {
            // Delete associated POS listing if exists
            if ($book->product) {
                $book->product->delete();
            }

            // Attempt to force delete to allow SKU reuse
            $book->forceDelete();

            return response()->json(['message' => 'Book deleted permanently from Master Registry']);
        } catch (\Exception $e) {
            // Refetch the book to reset any Eloquent internal flags (like forceDeleting)
            $book = Book::findOrFail($id);

            // Fallback: Soft delete if book is referenced by other tables (sales invoices, transactions, etc.)
            // Rename SKU and other unique fields to allow SKU reuse
            $timestamp = time();
            $book->sku = $book->sku . '-DELETED-' . $timestamp;
            
            if ($book->item_code) {
                $book->item_code = $book->item_code . '-DELETED-' . $timestamp;
            }
            if ($book->barcode) {
                $book->barcode = $book->barcode . '-DELETED-' . $timestamp;
            }
            if ($book->nbs_barcode) {
                $book->nbs_barcode = $book->nbs_barcode . '-DELETED-' . $timestamp;
            }
            
            $book->save();
            $book->delete();

            return response()->json(['message' => 'Book is referenced in transactions and has been safely archived. SKU is now free to be reused.']);
        }
    }

    public function getCategories()
    {
        $categories = BookCategory::whereNull('parent_id')->with('children')->orderBy('name', 'asc')->get();
        return response()->json($categories);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:book_categories,id',
        ]);

        $category = BookCategory::create($validated);

        return response()->json([
            'message' => 'Category added successfully',
            'category' => $category
        ]);
    }

    public function getSubcategories($id)
    {
        $subcategories = BookCategory::where('parent_id', $id)->orderBy('name', 'asc')->get();
        return response()->json($subcategories);
    }

    public function destroyCategory($id)
    {
        $category = BookCategory::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}

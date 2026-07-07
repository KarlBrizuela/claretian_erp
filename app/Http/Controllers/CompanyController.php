<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only load top-level companies (where parent_id is null) in the main list
        $companies = Company::whereNull('parent_id')->orderBy('company_name', 'asc')->get();
        $parentCompanies = Company::orderBy('company_name', 'asc')->get();

        return view('marketing.companies', [
            'companies' => $companies,
            'parentCompanies' => $parentCompanies,
            'title' => 'Company Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companies,company_id',
            'account_number' => 'nullable|string|unique:companies,account_number',
            'mobile' => 'nullable|string',
            'main_email' => 'nullable|email',
            'shipping_address' => 'nullable|string',
            'is_inactive' => 'nullable|boolean',
        ]);

        if (empty($validated['account_number'])) {
            $validated['account_number'] = 'COMP-' . strtoupper(uniqid());
        }

        // Copy parent contact/address fields if none are provided
        if (!empty($validated['parent_id'])) {
            $parent = Company::find($validated['parent_id']);
            if ($parent) {
                if (empty($validated['mobile'])) {
                    $validated['mobile'] = $parent->mobile;
                }
                if (empty($validated['main_email'])) {
                    $validated['main_email'] = $parent->main_email;
                }
                if (empty($validated['shipping_address'])) {
                    $validated['shipping_address'] = $parent->shipping_address;
                }
            }
        }

        $company = Company::create($validated);

        return response()->json([
            'message' => 'Company created successfully',
            'company' => $company
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return response()->json($company);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        return response()->json($company->load('branches'));
    }

    /**
     * Return a company's direct branches as JSON (for cascade dropdowns).
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBranches(Company $company)
    {
        $branches = $company->branches()
            ->where('is_inactive', false)
            ->orderBy('company_name', 'asc')
            ->get(['company_id', 'company_name', 'account_number']);

        return response()->json($branches);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can edit companies.'], 403);
        }

        if ($request->parent_id && $request->parent_id == $company->company_id) {
            return response()->json(['message' => 'A company/branch cannot be its own parent.'], 422);
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companies,company_id',
            'account_number' => 'nullable|string|unique:companies,account_number,' . $company->company_id . ',company_id',
            'mobile' => 'nullable|string',
            'main_email' => 'nullable|email',
            'shipping_address' => 'nullable|string',
            'is_inactive' => 'nullable|boolean',
        ]);

        $company->update($validated);

        return response()->json(['message' => 'Company updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can delete companies.'], 403);
        }

        // Set child branches parent_id to null or handle them
        Company::where('parent_id', $company->company_id)->update(['parent_id' => null]);

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }
}

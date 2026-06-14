<?php

namespace App\Http\Controllers\Admin\GSD;

use App\Http\Controllers\Controller;
use App\Models\Admin\MIS\MaterialReq;
use Illuminate\Http\Request;

class MaterialReqController extends Controller
{
    /**
     * Store a newly created material request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_by' => 'required|string|max:255',
            'request_date' => 'required|date',
            'request_details' => 'required|string',
        ]);

        // Default status for GSD material requests - starts as 'to submit'
        $validated['user_id'] = auth()->id();
        $validated['module'] = 'GSD';
        $validated['status'] = 'to submit';

        MaterialReq::create($validated);

        return redirect()->back()->with('success', 'Material Request created successfully. Submit for approval to add to approval queue.');
    }

    /**
     * Submit the specified material request for approval.
     * Transitions status from 'to submit' to 'pending approval'
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function submit($id)
    {
        $materialRequest = MaterialReq::findOrFail($id);
        
        if ($materialRequest->status !== 'to submit') {
            return redirect()->back()->with('warning', 'Only requests in "to submit" status can be submitted.');
        }
        
        $materialRequest->update(['status' => 'pending approval']);
        
        return redirect()->back()->with('success', 'Material Request submitted for approval.');
    }

    /**
     * Update the specified material request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $materialRequest = MaterialReq::findOrFail($id);
        
        $validated = $request->validate([
            'requested_by' => 'sometimes|required|string|max:255',
            'request_date' => 'sometimes|required|date',
            'request_details' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:to submit,pending approval,Pending Final Approval,forwarded to accounting,received,rejected',
        ]);

        $materialRequest->update($validated);

        return redirect()->back()->with('success', 'Material Request updated successfully');
    }

    /**
     * Remove the specified material request from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $materialRequest = MaterialReq::findOrFail($id);
        $materialRequest->delete();

        return redirect()->back()->with('success', 'Material Request deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MstDistributor;
use Illuminate\Http\Request;
use App\Services\MasterDistributorService;
use Illuminate\Validation\Rule;

class MasterDistributorController extends Controller
{
    public function __construct(
        protected MasterDistributorService $service
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index($id = null)
    {
        $result = $this->service->getAllData();

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'md_code' => ['required','string','max:30','unique:mst_distributor,md_code'],
            'md_name' => ['required','string','max:150'],
            'md_email' => ['nullable','email','max:150'],
            'md_phone' => ['nullable','string','max:30'],
            'md_address' => ['nullable','string'],
        ]);

        $result = $this->service->storeData($validated);

        return response()->json([
            'success' => true,
            'message' => "Data Distributor created sucessfully",
            'data' => $result,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $result = $this->service->getData($id);

        return response()->json([
            'success' => true,
            'data' => $result ?? 'No data available',
        ]);
    }

    public function merchants(MstDistributor $mstDistributor)
    {
        $result = $this->service->getDataDetail($mstDistributor);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MstDistributor $mstDistributor)
    {
        // dd($mstDistributor->toArray());
        $validated = $request->validate([
            // 'md_code' => [
            //     'required',
            //     'string',
            //     'max:30',
            //     Rule::unique('mst_distributor', 'md_code')
            //         ->ignore($distributor->id),
            // ],
            'md_name' => ['required','string','max:150'],
            'md_email' => ['nullable','email','max:150'],
            'md_phone' => ['nullable','string','max:30'],
            'md_address' => ['nullable','string'],
            'is_active' => ['boolean'],
        ]);

        $result = $this->service->updateData($mstDistributor, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Distributor updated successfully',
            'data' => $result
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MstDistributor $mstDistributor)
    {
        //  dd('MASUK DESTROY', $mstDistributor->toArray());
        $this->service->deleteData($mstDistributor);

        return response()->json([
            'success' => true,
            'message' => 'Distributor deleted successfully',
        ]);
    }
}

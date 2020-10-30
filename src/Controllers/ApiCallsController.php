<?php

namespace Yves\Mopay\Controllers;

use Illuminate\Support\Facades\Request;
use Yajra\DataTables\DataTables;
use Yves\Mopay\Models\Payment;

class ApiCallsController extends Controller
{

    public function table(Request $request)
    {
    }


    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        return view('mopay::payments_list');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        return Datatables::of(Payment::query())->make(true);
    }
}

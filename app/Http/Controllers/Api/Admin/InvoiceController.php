<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::with('customer')
            ->when(request()->q, function($invoices) {
                $invoices = $invoices->where('invoice', 'like', '%'. request()->q . '%');
            })
            ->when(request()->status, function($invoices) {
                $invoices = $invoices->where('status', request()->status);
            })
            ->when(request()->from_date && request()->to_date, function($invoices) {
                $invoices = $invoices->whereBetween('created_at', [
                    request()->from_date.' 00:00:00', 
                    request()->to_date.' 23:59:59'
                ]);
            })
            ->latest()
            ->paginate(request()->per_page ?? 5);

        //return with Api Resource
        return new InvoiceResource(true, 'List Data Invoices', $invoices);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::with('orders.product', 'customer', 'city', 'province')->whereId($id)->first();
        
        if($invoice) {
            //return success with Api Resource
            return new InvoiceResource(true, 'Detail Data Invoice!', $invoice);
        }

        //return failed with Api Resource
        return new InvoiceResource(false, 'Detail Data Invoice Tidak Ditemukan!', null);
    }

    /**
     * Update invoice status
     * 
     * @param Request $request
     * @param Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $invoice->update([
            'status' => $request->status
        ]);

        if($invoice) {
            //return success with Api Resource
            return new InvoiceResource(true, 'Status Invoice Berhasil Diupdate!', $invoice);
        }

        //return failed with Api Resource
        return new InvoiceResource(false, 'Status Invoice Gagal Diupdate!', null);
    }
}
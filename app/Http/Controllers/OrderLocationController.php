<?php

namespace App\Http\Controllers;

use App\Models\OrderLocation;
use App\Http\Requests\StoreOrderLocationRequest;
use App\Http\Requests\UpdateOrderLocationRequest;

class OrderLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderLocationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderLocation $orderLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderLocationRequest $request, OrderLocation $orderLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderLocation $orderLocation)
    {
        //
    }
}

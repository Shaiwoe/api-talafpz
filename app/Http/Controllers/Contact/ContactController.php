<?php

namespace App\Http\Controllers\Contact;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Contact\ContactResource;

class ContactController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contact = Contact::orderBy('created_at', 'desc')->paginate(5);

        return $this->successResponse([
            'articles' => ContactResource::collection($contact),
            'links' => ContactResource::collection($contact)->response()->getData()->links,
            'meta' => ContactResource::collection($contact)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'text' => $request->text
        ]);

        DB::commit();

        return $this->successResponse('success', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return $this->successResponse(new ContactResource($contact));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        DB::beginTransaction();
        $contact->delete();
        DB::commit();
        return $this->successResponse(new ContactResource($contact), 200);
    }
}

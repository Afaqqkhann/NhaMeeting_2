<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Books;
use Validator;
use Session;
use DB;

class BooksController extends Controller
{
    public function __construct()
    {
        DB::setDateFormat('DD-Mon-YY');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Books';
        $data = Books::all();

        return view('books.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Book';
        $subcategories = ['' => 'Select Subcategory'] + SubCategory::lists('sc_title', 'sc_id')->all();

        return view('books.create', compact('page_title', 'subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
                'sc_id'  => 	'required',
                //'duration_days' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Books::orderBy('book_id', 'desc')->first();

        $book = new Books();
        $book->book_id = ($record) ? $record->book_id + 1 : 1;
        $book->book_title = $request->input('book_title');
        $book->auther_1 = $request->input('auther_1');
        $book->auther_2 = $request->input('auther_2');
        $book->subject_1 = $request->input('subject_1');
        $book->subject_2 = $request->input('subject_2');
        $book->rec_date = ($request->input('rec_date')) ? date('y-M-d', strtotime($request->input('rec_date'))) : null;
        $book->publish_date = ($request->input('publish_date')) ? date('y-M-d', strtotime($request->input('publish_date'))) : null;
        $book->purchase_date = ($request->input('purchase_date')) ? date('y-M-d', strtotime($request->input('purchase_date'))) : null;
        $book->bill_no = $request->input('bill_no');
        $book->cost = $request->input('cost');
        $book->source_name = $request->input('source_name');
        $book->sc_id = $request->input('sc_id');
        $book->place_name = $request->input('place_name');
        $book->publisher_name = $request->input('publisher_name');

        if ($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $fileName = sha1(time()) . '.' . $file->getClientOriginalExtension();;
            $request->file('edoc')->move(
                base_path() . '/storage/books/', $fileName
            );
            $book->edoc = $fileName;
        }
		 if ($request->hasFile('book_img')) {
            $file = $request->file('book_img');
            $imageName = $book->book_id . '.' . $file->getClientOriginalExtension();;
            $request->file('book_img')->move(
                base_path() . '/storage/book_img/', $imageName
            );
        }

        $book->book_status = 1;
        $book->save();

        Session::flash('success', 'Book added successfully.');

        return redirect('books');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Book';
        $data = Books::find($id);

        return view('books.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Book';
        $data = Books::find($id);
        $subcategories = ['' => 'Select Subcategory'] + SubCategory::lists('sc_title', 'sc_id')->all();

        return view('books.edit', compact('page_title', 'data', 'subcategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(),
            [
                'sc_id'  => 	'required',
                //'duration_days' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Books::orderBy('book_id', 'desc')->first();

        $book = Books::find($id);
        $book->book_title = $request->input('book_title');
        $book->auther_1 = $request->input('auther_1');
        $book->auther_2 = $request->input('auther_2');
        $book->subject_1 = $request->input('subject_1');
        $book->subject_2 = $request->input('subject_2');
        $book->rec_date = ($request->input('rec_date')) ? date('y-M-d', strtotime($request->input('rec_date'))) : null;
        $book->publish_date = ($request->input('publish_date')) ? date('y-M-d', strtotime($request->input('publish_date'))) : null;
        $book->purchase_date = ($request->input('purchase_date')) ? date('y-M-d', strtotime($request->input('purchase_date'))) : null;
        $book->bill_no = $request->input('bill_no');
        $book->cost = $request->input('cost');
        $book->source_name = $request->input('source_name');
        $book->sc_id = $request->input('sc_id');
        $book->place_name = $request->input('place_name');
        $book->publisher_name = $request->input('publisher_name');

        if ($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $fileName = sha1(time()) . '.' . $file->getClientOriginalExtension();;
            $request->file('edoc')->move(
                base_path() . '/storage/books/', $fileName
            );
            $book->edoc = $fileName;
        }
		if ($request->hasFile('book_img')) {
            $file = $request->file('book_img');
            $imageName = $book->book_id . '.' . $file->getClientOriginalExtension();;
            $request->file('book_img')->move(
                base_path() . '/storage/book_img/', $imageName
            );
        }

        $book->book_status = 1;
        $book->save();

        Session::flash('success', 'Book updated successfully.');

        return redirect('books');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Books::where('BOOK_ID', '=', $id)->delete();
        Session::flash('success', 'Book has been deleted successfully.');

        return redirect('books');
    }
}

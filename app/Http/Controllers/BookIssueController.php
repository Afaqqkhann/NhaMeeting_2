<?php

namespace App\Http\Controllers;

use App\Models\Books;
use App\Models\Employees\Employees;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\BookIssue;
use Validator;
use Session;
use DB;

class BookIssueController extends Controller
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
        $page_title = 'Book Issued';
        $data = BookIssue::all();

        return view('book_issue.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Issue Book';
        $books = ['' => 'Select Book'] + Books::lists('book_title', 'book_id')->all();
        $employees = ['' => 'Select Employee'] + Employees::lists('emp_name', 'emp_id')->all();

        return view('book_issue.create', compact('page_title', 'books', 'employees'));
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
                'book_id'  => 	'required',
                'emp_id' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = BookIssue::orderBy('bi_id', 'desc')->first();

        $book_issued = new BookIssue();
        $book_issued->bi_id = ($record) ? $record->bi_id + 1 : 1;
        $book_issued->book_id = $request->input('book_id');
        $book_issued->emp_id = $request->input('emp_id');
        $book_issued->date_issue = ($request->input('date_issue')) ? date('y-M-d', strtotime($request->input('date_issue'))) : null;
        $book_issued->date_return = ($request->input('date_return')) ? date('y-M-d', strtotime($request->input('date_return'))) : null;

        if ($request->hasFile('bi_edoc')) {
            $file = $request->file('bi_edoc');
            $fileName = sha1(time()) . '.' . $file->getClientOriginalExtension();;
            $request->file('bi_edoc')->move(
                base_path() . '/storage/book_issued/', $fileName
            );
            $book_issued->edoc = $fileName;
        }

        $book_issued->bi_status = 1;
        $book_issued->save();

        Session::flash('success', 'Book issued successfully.');

        return redirect('book_issued');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Book Issued';
        $data = BookIssue::find($id);

        return view('book_issue.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Book Issued';
        $data = BookIssue::find($id);

        $books = ['' => 'Select Book'] + Books::lists('book_title', 'book_id')->all();
        $employees = ['' => 'Select Employee'] + Employees::lists('emp_name', 'emp_id')->all();

        return view('book_issue.edit', compact('page_title', 'data', 'books', 'employees'));
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
                'book_id'  => 	'required',
                'emp_id' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $book_issued = BookIssue::where('bi_id', $id)->get()->first();
        $book_issued->book_id = $request->input('book_id');
        $book_issued->emp_id = $request->input('emp_id');
        $book_issued->date_issue = ($request->input('date_issue')) ? date('y-M-d', strtotime($request->input('date_issue'))) : null;
        $book_issued->date_return = ($request->input('date_return')) ? date('y-M-d', strtotime($request->input('date_return'))) : null;

        if ($request->hasFile('bi_edoc')) {
            $file = $request->file('bi_edoc');
            $fileName = sha1(time()) . '.' . $file->getClientOriginalExtension();;
            $request->file('bi_edoc')->move(
                base_path() . '/storage/book_issued/', $fileName
            );
            $book_issued->edoc = $fileName;
        }

        $book_issued->save();

        Session::flash('success', 'Book issued updated successfully.');

        return redirect('book_issued');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        BookIssue::where('BI_ID', '=', $id)->delete();
        Session::flash('success', 'Book issued has been deleted successfully.');

        return redirect('book_issued');
    }
}

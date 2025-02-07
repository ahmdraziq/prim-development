<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Organization;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Psy\Command\WhereamiCommand;
use App\Http\Controllers\AppBaseController;
use App\Models\Category;
use App\Models\Fee_New;

class FeesController extends AppBaseController
{
    public function index()
    {
        //
        $fees = DB::table('fees')->orderBy('nama')->get();
        $organization = $this->getOrganizationByUserId();
        $listcategory = DB::table('categories')->get();
        return view('pentadbir.fee.index', compact('fees', 'listcategory', 'organization'));
    }



    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        return view('pentadbir.fee.add', compact('organization'));
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // get type org
        // get year from class name
        $fee = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('classes', 'class_organization.class_id', '=', 'classes.id')
            ->join('organizations', 'organizations.id', '=', 'class_organization.organization_id')
            ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'organizations.id as organization_id', 'organizations.type_org', 'classes.nama')
            ->where('fees.id', $id)
            ->first();

        $aa = $fee->nama;
        $getyear = substr($aa, 0, 1);

        $getallclass = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as classname')
            ->where('organizations.id', $fee->organization_id)
            ->where('classes.nama', 'LIKE', '%' . $getyear . '%')
            ->orderBy('classes.nama')
            ->get();

        $getclass = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('classes', 'class_organization.class_id', '=', 'classes.id')
            ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'class_organization.organization_id', 'classes.id as cid', 'classes.nama as classname')
            ->where('fees.id', $id)
            ->orderBy('classes.nama')
            ->get();

        // $getclassid = $getclass->cid;

        // dd($getclass);
        $organization = $this->getOrganizationByUserId();
        return view('pentadbir.fee.update', compact('fee', 'organization', 'getyear', 'getclass', 'getallclass'));
    }

    public function update(Request $request, $id)
    {
        //
        // dd($request);
        //class return array
        $class = $request->get('cb_class');

        $req = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname', 'class_organization.id as co_id')
            ->where('organizations.id', $request->get('organization'))
            ->whereIn('classes.id', $class)
            ->get()->toArray();

        // $getclassfees = DB::table('class_fees')->where('class_organization_id', $list->co_id->array())->get();
        // $arr = $req->toArray();
        // dd(count($req));
        // dd($req[0]);

        //get all class that have been store with that fees
        $getclassfees = DB::table('fees')
            ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
            ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
            ->join('classes', 'class_organization.class_id', '=', 'classes.id')
            ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'class_organization.organization_id', 'classes.id as cid', 'classes.nama as classname')
            ->where('fees.id', $id)
            ->get()->toArray();


        for ($i = 0; $i < count($req); $i++) {

            //check if that kelas (in request) have been store with that fees or not
            $query = DB::table('fees')
                ->join('class_fees', 'class_fees.fees_id', '=', 'fees.id')
                ->join('class_organization', 'class_fees.class_organization_id', '=', 'class_organization.id')
                ->join('classes', 'class_organization.class_id', '=', 'classes.id')
                ->select('fees.id as feeid', 'fees.nama as feename', 'fees.totalamount', 'class_organization.organization_id', 'classes.id as cid', 'classes.nama as classname')
                ->where('fees.id', $id)
                ->where('class_fees.class_organization_id', $req[$i]->co_id)
                ->first();

            for ($j = 0; $j < count($getclassfees); $j++) {
                if (is_null($query)) {
                    // dd('haha');

                    DB::table('class_fees')->insert([
                        'status'                =>  '1',
                        'class_organization_id' =>  $req[$i]->co_id,
                        'fees_id'               =>  $id
                    ]);
                } elseif ($req[$i]->co_id != $getclassfees[$j]) {
                    DB::table('class_fees')
                        ->where('fees_id', $id)
                        ->update([
                            'status'                =>  '0'
                        ]);
                } else {
                    DB::table('class_fees')
                        ->where('fees_id', $id)
                        ->update([
                            'status'                =>  '1',
                            'class_organization_id' =>  $req[$i]->co_id
                        ]);
                }
            }
        }
    }

    public function destroy($id)
    {
        //
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::all();
        } elseif (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru')) {

            // user role pentadbir n guru
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5);
                });
            })->get();
        } else {
            // user role ibu bapa
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('role_id', '6')->OrWhere('role_id', '7')->OrWhere('role_id', '8');
            })->get();
        }
    }

    public function fetchYear(Request $request)
    {

        // dd($request->get('schid'));
        $oid = $request->get('oid');
        $category = Category::where('organization_id', $oid)->get();

        $list = DB::table('organizations')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
            ->where('organizations.id', $oid)
            ->first();

        return response()->json(['success' => $list, 'category' => $category]);
    }


    public function fetchClass(Request $request)
    {

        // dd($request->get('schid'));
        $oid    = $request->get('oid');
        $year   = $request->get('year');

        $list = DB::table('organizations')
            ->join('class_organization', 'class_organization.organization_id', '=', 'organizations.id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as organizationname', 'classes.id as cid', 'classes.nama as cname')
            ->where('organizations.id', $oid)
            ->where('classes.nama', 'LIKE', '%' . $year . '%')
            ->orderBy('classes.nama')
            ->get();

        return response()->json(['success' => $list]);
    }

    public function feesReport()
    {
        $organization = $this->getOrganizationByUserId();

        $all_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->count();

        // dd($all_student);
        $student_complete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->where('class_student.fees_status', 'Completed')
            ->count();

        $student_notcomplete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', 22)
            ->where('class_student.fees_status', 'Not Complete')
            ->count();

        $all_parent =  DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->count();

        $parent_complete =  DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Completed')
            ->count();

        $parent_notcomplete =  DB::table('organization_user')
            ->where('organization_id', 22)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Not Complete')
            ->count();

        // dd($all_student);

        return view('fee.report', compact('organization', 'all_student', 'student_complete', 'student_notcomplete', 'all_parent', 'parent_complete', 'parent_notcomplete'));
    }

    public function feesReportByOrganizationId(Request $request)
    {
        $oid = $request->oid;

        $all_student = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', $oid)
            ->count();

        // dd($all_student);
        $student_complete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.fees_status', 'Completed')
            ->count();

        $student_notcomplete = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->where('class_organization.organization_id', $oid)
            ->where('class_student.fees_status', 'Not Complete')
            ->count();

        $all_parent =  DB::table('organization_user')
            ->where('organization_id', $oid)
            ->where('role_id', 6)
            ->where('status', 1)
            ->count();

        $parent_complete =  DB::table('organization_user')
            ->where('organization_id', $oid)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Completed')
            ->count();

        $parent_notcomplete =  DB::table('organization_user')
            ->where('organization_id', $oid)
            ->where('role_id', 6)
            ->where('status', 1)
            ->where('fees_status', 'Not Complete')
            ->count();

        return response()->json(['all_student' => $all_student, 'student_complete' => $student_complete, 'student_notcomplete' => $student_notcomplete, 'all_parent' => $all_parent, 'parent_complete' => $parent_complete, 'parent_notcomplete' => $parent_notcomplete], 200);

    }

    public function reportByClass($type, $class_id)
    {
        $class = DB::table('classes')
            ->where('id', $class_id)->first();

        return view('fee.reportbyclass', compact('type', 'class'));
    }

    public function getTypeDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $type = $request->type;
            $oid = $request->oid;
            // dd($type);
            $userId = Auth::id();

            if ($type == 'Selesai') {

                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_organization.organization_id as oid', 'classes.id', 'classes.nama', DB::raw('COUNT(students.id) as totalstudent'), 'class_student.fees_status')
                    ->where('class_organization.organization_id', $oid)
                    ->where('class_student.fees_status', 'Completed')
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->get();
            } else {
                $data = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('class_organization.organization_id as oid', 'classes.id', 'classes.nama', DB::raw('COUNT(students.id) as totalstudent'), 'class_student.fees_status')
                    ->where('class_organization.organization_id', $oid)
                    ->where('class_student.fees_status', 'Not Complete')
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->get();
            }

            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('total', function ($row) {

                $first = DB::table('students')
                    ->join('class_student', 'class_student.student_id', '=', 'students.id')
                    ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                    ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                    ->select('classes.nama', DB::raw('COUNT(students.id) as totalallstudent'))
                    ->where('class_organization.organization_id', $row->oid)
                    ->where('classes.id', $row->id)
                    ->groupBy('classes.nama')
                    ->orderBy('classes.nama')
                    ->first();

                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . $row->totalstudent . '/' . $first->totalallstudent . '</div>';
                return $btn;
            });

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('fees.reportByClass', ['type' => $row->fees_status, 'class_id' => $row->id]) . '"" class="btn btn-primary m-1">Butiran</a></div>';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->feeid) . '" class="btn btn-primary m-1">Edit</a>';
                // $btn = $btn . '<button id="' . $row->feeid . '" data-token="' . $token . '" class="btn btn-danger m-1">Details</button></div>';
                return $btn;
            });

            $table->rawColumns(['total', 'action']);
            return $table->make(true);
        }
    }

    public function getParentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $type = $request->type;
            $oid = $request->oid;
            // dd($type);
            $userId = Auth::id();

            if ($type == 'Selesai') {

                $data = DB::table('users')
                    ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                    ->select('users.*', 'organization_user.organization_id')
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Completed')
                    ->get();
            } else {
                $data = DB::table('users')
                    ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
                    ->select('users.*', 'organization_user.organization_id')
                    ->where('organization_user.organization_id', $oid)
                    ->where('organization_user.role_id', 6)
                    ->where('organization_user.status', 1)
                    ->where('organization_user.fees_status', 'Not Complete')
                    ->get();
            }

            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a class="btn btn-primary m-1 user-id" id="' . $row->id . '-' . $row->organization_id . '">Butiran</a></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getstudentDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $status = $request->status;
            $class_id = $request->class_id;
            // dd($type);
            $userId = Auth::id();

            $data = DB::table('students')
                ->join('class_student', 'class_student.student_id', '=', 'students.id')
                ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('students.*')
                ->where('classes.id', $class_id)
                ->where('class_student.fees_status', $status)
                ->orderBy('students.nama')
                ->get();


            // dd($first);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a class="btn btn-primary m-1 student-id" id="' . $row->id . '">Butiran</a></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function CategoryA()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_A.index', compact('organization'));
    }

    public function createCategoryA()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_A.add', compact('organization'));
    }

    public function StoreCategoryA(Request $request)
    {
        $dt = Carbon::now();
        $price          = $request->get('price');
        $quantity       = $request->get('quantity');
        $oid            = $request->get('organization');
        $date_started   = $dt->toDateString($request->get('date_started'));
        $date_end       = $dt->toDateString($request->get('date_end'));
        $total          = $price * $quantity;

        $data = array(
            'data' => 'All'
        );

        $target = json_encode($data);

        $fee = new Fee_New([
            'name'              =>  $request->get('name'),
            'desc'              =>  $request->get('description'),
            'category'          =>  "Kategory A",
            'quantity'          =>  $request->get('quantity'),
            'price'             =>  $request->get('price'),
            'totalAmount'       =>  $total,
            'start_date'        =>  $date_started,
            'end_date'          =>  $date_end,
            'status'            =>  "1",
            'target'            =>  $target,
            'organization_id'   =>  $oid,
        ]);

        if ($fee->save()) {
            $parent_id = DB::table('organization_user')
                ->where('organization_id', $oid)
                ->where('role_id', 6)
                ->where('status', 1)
                ->get();

            for ($i = 0; $i < count($parent_id); $i++) {
                $array[] = array(
                    'status' => 'Debt',
                    'fees_new_id' => $fee->id,
                    'organization_user_id' => $parent_id[$i]->id,

                );

                $fees_parent = DB::table('organization_user')
                    ->where('id', $parent_id[$i]->id)
                    ->update(['fees_status' => 'Not Complete']);
            }

            DB::table('fees_new_organization_user')->insert($array);

            return redirect('/fees/A')->with('success', 'Yuran Kategori A telah berjaya dimasukkan');
        }
    }

    public function getCategoryDatatable(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;
            $category = $request->category;
            $userId = Auth::id();

            if ($oid != '') {

                // $data = DB::table('fees')->orderBy('nama')->get();

                if ($category == "A") {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategory A")
                        ->where('status', "1")
                        ->get();
                } elseif ($category == "B") {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategory B")
                        ->where('status', "1")
                        ->get();
                } else {
                    $data     = DB::table('fees_new')
                        ->where('organization_id', $oid)
                        ->where('category', "Kategory C")
                        ->where('status', "1")
                        ->get();
                }


                // dd($data);
            }
            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-success">Aktif</span></div>';
                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Tidak Aktif </span></div>';
                    return $btn;
                }
            });

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                // $btn = $btn . '<a href="' . route('fees.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                // $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['status', 'action']);
            return $table->make(true);
        }
    }

    public function CategoryB()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_B.index', compact('organization'));
    }

    public function createCategoryB()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_B.add', compact('organization'));
    }


    public function StoreCategoryB(Request $request)
    {
        // dd($request->toArray());
        $dt = Carbon::now();

        $gender      = "";
        $class      = $request->get('cb_class');
        $level      = $request->get('level');
        $year       = $request->get('year');
        $name       = $request->get('name');
        $price          = $request->get('price');
        $quantity       = $request->get('quantity');
        $desc           = $request->get('description');
        $oid            = $request->get('organization');
        $date_started   = $dt->toDateString($request->get('date_started'));
        $date_end       = $dt->toDateString($request->get('date_end'));
        $total          = $price * $quantity;
        $category       = "Kategory B";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function CategoryC()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_C.index', compact('organization'));
    }

    public function createCategoryC()
    {
        $organization = $this->getOrganizationByUserId();

        return view('fee.category_C.add', compact('organization'));
    }

    public function StoreCategoryC(Request $request)
    {
        // dd($request->toArray());
        $dt = Carbon::now();

        $gender     = $request->get('gender');
        $class      = $request->get('cb_class');
        $level      = $request->get('level');
        $year       = $request->get('year');
        $name       = $request->get('name');
        $price          = $request->get('price');
        $quantity       = $request->get('quantity');
        $desc           = $request->get('description');
        $oid            = $request->get('organization');
        $date_started   = $dt->toDateString($request->get('date_started'));
        $date_end       = $dt->toDateString($request->get('date_end'));
        $total          = $price * $quantity;
        $category       = "Kategory C";

        if ($level == "All_Level") {
            return $this->allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } elseif ($year == "All_Year") {
            return $this->allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category);
        } else {
            return $this->allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category);
        }
    }

    public function fetchClassYear(Request $request)
    {

        // dd($request->get('level'));
        $level = $request->get('level');
        $oid = $request->get('oid');
        if ($level == "1") {
            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        } elseif ($level == "2") {
            $list = DB::table('organizations')
                ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
                ->where('organizations.id', $oid)
                ->first();

            $class_organization = DB::table('classes')
                ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
                ->distinct()
                ->where('classes.levelid', $level)
                ->where('class_organization.organization_id', $oid)
                ->get();

            // dd($class_organization);

            return response()->json(['data' => $list, 'datayear' => $class_organization]);
        }
    }

    public function allLevel($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
                ->where('classes.status', "1")
                ->where('students.gender', $gender)
                ->get();

            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } else {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
                ->where('classes.status', "1")
                ->get();

            $data = array(
                'data' => $level
            );
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name'          => $name,
            'desc'          => $desc,
            'category'      => $category,
            'quantity'      => $quantity,
            'price'         => $price,
            'totalAmount'       => $total,
            'start_date'        => $date_started,
            'end_date'          => $date_end,
            'status'            => "1",
            'target'            => $target,
            'organization_id'   => $oid,

        ]);

        // dd($oid);
        for ($i = 0; $i < count($list); $i++) {
            $array[] = array(
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,

            );

            $fees_student = DB::table('class_student')
                ->where('id', $list[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);
        }
        DB::table('student_fees_new')->insert($array);
        if ($category == "Kategory B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
    }

    public function allYear($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $gender, $category)
    {
        if ($gender) {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
                ->where('classes.levelid', $level)
                ->where('classes.status', "1")
                ->where('students.gender', $gender)
                ->get();
            $data = array(
                'data' => $level,
                'gender' => $gender
            );
        } else {
            $list = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
                ->where('classes.levelid', $level)
                ->where('classes.status', "1")
                ->get();
            $data = array(
                'data' => $level
            );
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name'          => $name,
            'desc'          => $desc,
            'category'      => $category,
            'quantity'      => $quantity,
            'price'         => $price,
            'totalAmount'       => $total,
            'start_date'        => $date_started,
            'end_date'          => $date_end,
            'status'            => "1",
            'target'            => $target,
            'organization_id'   => $oid,

        ]);

        for ($i = 0; $i < count($list); $i++) {
            $array[] = array(
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list[$i]->class_student_id,

            );

            $fees_student = DB::table('class_student')
                ->where('id', $list[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);
        }
        DB::table('student_fees_new')->insert($array);
        if ($category == "Kategory B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
    }

    public function allClasses($name, $desc, $quantity, $price, $total, $date_started, $date_end, $level, $oid, $class, $gender, $category)
    {
        // get list class checked from checkbox

        $list = DB::table('classes')
            ->where('status', "1")
            ->whereIn('id', $class)
            ->get();

        // dd(count($list));
        for ($i = 0; $i < count($list); $i++) {
            $class_arr[] = $list[$i]->nama;
        }

        if ($gender) {
            $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->join('students', 'students.id', '=', 'class_student.student_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
                ->where('classes.status', "1")
                ->where('students.gender', $gender)
                ->whereIn('classes.id', $class)
                ->get();
            $data = array(
                'data' => $class_arr,
                'gender' => $gender
            );
        } else {
            $list_student = DB::table('class_organization')
                ->join('class_student', 'class_student.organclass_id', '=', 'class_organization.id')
                ->join('classes', 'classes.id', '=', 'class_organization.class_id')
                ->select('class_student.id as class_student_id')
                ->where('class_organization.organization_id', $oid)
                ->where('classes.status', "1")
                ->whereIn('classes.id', $class)
                ->get();
            $data = array(
                'data' => $class_arr
            );
        }

        $target = json_encode($data);

        $fees = DB::table('fees_new')->insertGetId([
            'name'          => $name,
            'desc'          => $desc,
            'category'      => $category,
            'quantity'      => $quantity,
            'price'         => $price,
            'totalAmount'       => $total,
            'start_date'        => $date_started,
            'end_date'          => $date_end,
            'status'            => "1",
            'target'            => $target,
            'organization_id'   => $oid,

        ]);

        for ($i = 0; $i < count($list_student); $i++) {
            $array[] = array(
                'status' => 'Debt',
                'fees_id' => $fees,
                'class_student_id' => $list_student[$i]->class_student_id,

            );

            $fees_student = DB::table('class_student')
                ->where('id', $list_student[$i]->class_student_id)
                ->update(['fees_status' => 'Not Complete']);
        }
        DB::table('student_fees_new')->insert($array);
        if ($category == "Kategory B") {
            return redirect('/fees/B')->with('success', 'Yuran Kategori B telah berjaya dimasukkan');
        } else {
            return redirect('/fees/C')->with('success', 'Yuran Kategori C telah berjaya dimasukkan');
        }
    }

    public function dependent_fees()
    {
        $userid = Auth::id();

        // ************************* get list dependent from user id  *******************************

        $list = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('users', 'users.id', '=', 'organization_user.user_id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('organizations.id as oid', 'organizations.nama as nschool', 'students.id as studentid', 'students.nama as studentname', 'classes.nama as classname')
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->orderBy('organizations.id')
            ->orderBy('classes.nama')
            ->get();

        // ************************* get list organization by parent  *******************************

        $organization = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->select('organizations.*', 'organization_user.user_id')
            ->distinct()
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->orderBy('organizations.nama')
            ->get();


        // dd($organization);
        // ************************* get list fees  *******************************

        $getfees     = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.category', 'fees_new.organization_id', 'students.id as studentid')
            ->distinct()
            ->orderBy('students.id')
            ->orderBy('fees_new.category')
            ->where('student_fees_new.status', 'Debt')
            ->get();

        $getfees_bystudent     = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.*', 'students.id as studentid')
            ->where('student_fees_new.status', 'Debt')
            ->get();

        // ************************* get fees category A  *******************************

        $getfees_category_A = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.category', 'organization_user.organization_id')
            ->distinct()
            ->orderBy('fees_new.category')
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->get();

        // dd($getfees_category_A);
        $getfees_category_A_byparent  = DB::table('fees_new')
            ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
            ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
            ->select('fees_new.*')
            ->orderBy('fees_new.category')
            ->where('organization_user.user_id', $userid)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.status', 1)
            ->where('fees_new_organization_user.status', 'Debt')
            ->get();

        // dd($getfees_category_A);
        return view('fee.pay.index', compact('list', 'organization', 'getfees', 'getfees_bystudent', 'getfees_category_A', 'getfees_category_A_byparent'));
    }

    public function student_fees(Request $request)
    {
        $student_id = $request->student_id;
        $getfees_bystudent     = DB::table('students')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
            ->join('fees_new', 'fees_new.id', '=', 'student_fees_new.fees_id')
            ->select('fees_new.*', 'students.id as studentid', 'student_fees_new.status')
            ->where('students.id', $student_id)
            ->orderBy('fees_new.name')
            ->get();

        return response()->json($getfees_bystudent, 200);
    }

    public function parent_dependent(Request $request)
    {
        $case = explode("-", $request->data);

        $user_id         = $case[0];
        $organization_id = $case[1];

        $get_dependents = DB::table('organizations')
            ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
            ->join('organization_user_student', 'organization_user_student.organization_user_id', '=', 'organization_user.id')
            ->join('students', 'students.id', '=', 'organization_user_student.student_id')
            ->join('class_student', 'class_student.student_id', '=', 'students.id')
            ->join('class_organization', 'class_organization.id', '=', 'class_student.organclass_id')
            ->join('classes', 'classes.id', '=', 'class_organization.class_id')
            ->select('students.*', 'classes.nama as classname')
            ->where('organization_user.user_id', $user_id)
            ->where('organization_user.role_id', 6)
            ->where('organization_user.organization_id', $organization_id)
            ->where('organization_user.status', 1)
            ->where('class_student.status', 1)
            ->get();

        return response()->json($get_dependents, 200);
    }

    public function searchreport()
    {
        $organization = $this->getOrganizationByUserId();

        $listclass = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select('classes.id as id', 'classes.nama', 'classes.levelid')
            ->where([
                ['class_organization.organization_id', $organization[0]->id]
            ])
            ->orderBy('classes.nama')
            ->get();

        return view('fee.report-search.index', compact('organization', 'listclass'));
    }
}

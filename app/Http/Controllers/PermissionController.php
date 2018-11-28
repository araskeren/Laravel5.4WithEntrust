<?php namespace App\Http\Controllers;

use DB;
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;

use App\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder){
        if ($request->ajax()){
            $permissions = Permission::orderBy('created_at','asc');
            dd($permission);
            return DataTables::of($permissions)
            ->addColumn('action', function($permissions) {
                return view('_action', [
                    'model' => $permissions,
                    'edit_modal' => route('permission.edit',$permissions->id),
                    'delete' => route('permission.delete',$permissions->id),
                ]);
            })
            ->make(true);
        }

         $html = $htmlBuilder
        ->addColumn(['data'=>'display_name', 'name'=>'display_name', 'title'=>'NAME'])
        ->addColumn(['data'=>'description', 'name'=>'description', 'title'=>'DESCRIPTION'])
        ->addColumn(['data' => 'action','name' => 'action', 'title' => 'ACTION', 'orderable' => false, 'searchable' => false, 'class' => 'text-center', 'style'=>'width: 30px;']);

        return view('permission.index')
        ->with(compact('html'));
    }

    public function store(Request $request){

        $this->validate($request, [
            'name' => 'required|min:3'
        ]);

        if(Permission::where('name',str_slug($request->name))->exists())
            return response()->json(['message' => 'Permission Name already exists.'
                ], 422);

        $permission = Permission::create([
            'name' => str_slug($request->name),
            'display_name' => $request->name,
            'description' => $request->description
        ]);
        $permission->save();
        return response()->json('success', 200)->redirect()->back();
    }

    public function edit($id){
        $permission = Permission::find($id);
        return response()->json($permission, 200);
    }

    public function update(Request $request){
        $this->validate($request, [
            'name' => 'required|min:3'
        ]);

        if(Permission::where('name',str_slug($request->name))->where('id','!=',$request->id)->exists())
            return response()->json(['message' => 'Permission Name already exists.'
                ], 422);

        $permission = Permission::find($request->id);
        $permission->name = str_slug($request->name);
        $permission->display_name = $request->name;
        $permission->description = $request->description;
        $permission->save();

        return response()->json('success', 200);
    }

    public function destroy($id){
        $permission = Permission::findorFail($id)
                ->delete();
        return response()->json(200);
    }

    public function picklist(Request $request){
		$query = $request->input('q');
		$state = $request->state;
		$items = Permission::orderBy('display_name')
            ->Where('display_name','like',"%$query%")
            ->orWhere('name','like',"%$query%");


		return view('modal_lov._permission_list')
			->with('items', $items->paginate(5));
	}
}

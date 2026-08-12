<?php

namespace App\Http\Controllers\Generated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunicationHubTaskManagerTestMoinController extends Controller
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $sqlQuery = "select * from users";

    public function index()
    {
        if (!request()->ajax()) {
            return redirect('/');
        }

        return view('modules.loader', [
            'pageDir' => 'modules/communication-hub/task-manager-test-moin'
        ]);
    }

    public function getData()
    {
        $data = DB::select($this->sqlQuery);
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $payload = $request->except(['_token']);
        $payload['created_at'] = now();
        $payload['updated_at'] = now();

        $id = DB::table($this->table)->insertGetId($payload);
        return response()->json(['success' => true, 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $payload = $request->except(['_token']);
        $payload['updated_at'] = now();

        DB::table($this->table)->where($this->primaryKey, $id)->update($payload);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::table($this->table)->where($this->primaryKey, $id)->delete();
        return response()->json(['success' => true]);
    }
}
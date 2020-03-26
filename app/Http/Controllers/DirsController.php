<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;
use App\Dir;
use Session;
use Carbon\Carbon;
class DirsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();

        if($request->get('search')){
            $items = Dir::where("name", "LIKE", "%{$request->get('search')}%")
                ->paginate(50);      
        }else{
		  $items = Dir::paginate(50);
        }
		
        return response($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
    	$input = $request->all();
        $create = dir::create($input);
        return response($create);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $item = dir::find($id);
        return response($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
    	$input = $request->all();

        dir::where("id",$id)->update($input);
        $item = dir::find($id);
        return response($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return dir::where('id',$id)->delete();
    }
	public function back_up()
	{
		$date = Carbon::now()->format('d-m-Y');            
        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');
        $command = "mysqldump --user={$user} --password={$password} {database} > {$date}.sql";
        $path = 'User Database Backup/'.Carbon::now()->format('F').' '.Carbon::now()->format('Y');
        $process = new Process($command);        
        $process->start();    

        while ($process->isRunning()) {

            $public = Storage::disk('public');
            $public->put('users/'.$path.'/'.$date.".sql", file_get_contents("{$date}.sql"));       
            unlink("{$date}.sql");      
        }   
	}
}

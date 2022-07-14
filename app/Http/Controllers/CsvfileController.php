<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\csvfile;
use App\Http\Requests\StorecsvfileRequest;
use App\Http\Requests\UpdatecsvfileRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;

class CsvfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function upload(Request $request)
    {
        
        $file = $request->file('uploaded_csvfile');
        if($file){
            //get name of uploaded file
            $file_name = $file->getClientOriginalName();
            //get file extension of uploaded file
            $file_extension = $file->getClientOriginalExtension();
            //get path of file
            $file_path = $file->getRealPath();
            //get size (in bytes) of uploaded file 
            $file_size = $file->getSize();
            //check size and extension
            $this->checkUploadedFile($file_extension, $file_size);
            //Read file contents
            $file = fopen($file_path, "r");
            //array for holding file contents
            $filedata_array = array(); 
            //counter is starting from zero since file has no header
            $i = 0; $echo=""; $sum=0; $multiply=1;
            //parse through contents of uploaded file
            while(($file_data = fgetcsv($file, 1000, ",")) !== FALSE){
                $num = count($file_data);
                for($k=0; $k<$num; $k++){
                    $filedata_array[$i][] = $file_data[$k];
                   $echo = $echo.$file_data[$k].',';
                   if(is_numeric($file_data[$k])){ //check if input contains non-integers or is empty
                       $sum = $sum + $file_data[$k]; 
                       $multiply = $multiply * $file_data[$k];
                   }else{
                    return response()->json([
                
                        'error'=>'Input contains non-integers or Input is an empty file'
                     ]);
                   }
                   
                  
                }
                $echo = rtrim($echo,',');
               $echo = $echo.PHP_EOL;
                $i++;

            }
            fclose($file); 
                      
            
        //check for square matrix  
        if($num != $i ){
            return response()->json([
                
                'error'=>'Input not a square matrix '
             ]);
        }else{    

        switch ($request->input('action')) {
            case 'echomatrix':                 

                return response()->json([
                print_r($echo)
                ]);
            break;
    
            case 'invert':
                // Invert Matrix
                $t=0;
                $invert = array();
                    while ($columns = array_column($filedata_array, $t++))
                    {
                        $invert[] = $columns;
                    
                    }
                    $j = 0;
                    $col1 = '';
                    //$tmp= array();
                    foreach($invert as $inverted){
                    $col1=$col1.implode(', ', $inverted).PHP_EOL; 
                    $j++;
                    }

                    return response()->json([
                    print_r($col1)
                        
                    ]);
            break;
    
            case 'flatten':
                // flatten matrix
                $flatten = Arr::flatten($filedata_array);
                $str_flatten = implode (", ", $flatten);
                return response()->json([
                    'flatten'=>$str_flatten
                 ]);
            break;

            case 'sum':
                // Add items in matrix
                return response()->json([
                    'sum'=>$sum
                 ]);
                break;
                
            case 'multiply':
                    // multiply items in matrix
                    //for large matrices, multiply will give result of zero
                    $mply = json_encode($multiply,JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR);
                    return response()->json([
                        'multiply'=>$mply
                     ]);
                break;
        }
    }
        }else{
            return response()->json([
                'error'=>'No file selected Error'.Response::HTTP_BAD_REQUEST
             ]);
            
        } 
    }

   
    
    public function checkUploadedFile($file_extension, $file_size)
    {
        //to check the file type and file size
    $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
    $maxFileSize = 2097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($file_extension), $valid_extension)) {
            if ($file_size <= $maxFileSize) {
            } else {
            throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE); //413 error
            }
        } else {
        throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE); //415 error
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorecsvfileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorecsvfileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\csvfile  $csvfile
     * @return \Illuminate\Http\Response
     */
    public function show(csvfile $csvfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\csvfile  $csvfile
     * @return \Illuminate\Http\Response
     */
    public function edit(csvfile $csvfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatecsvfileRequest  $request
     * @param  \App\Models\csvfile  $csvfile
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatecsvfileRequest $request, csvfile $csvfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\csvfile  $csvfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(csvfile $csvfile)
    {
        //
    }
}

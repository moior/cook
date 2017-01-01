<?php namespace Cooka\Sample\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Sample\Models\Sample;
use Illuminate\Support\Facades\Input;
use RainLab\User\Facades\Auth;

class SampleControl extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Order list',
            'description' => ''
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description'       => 'choose column to display at order list',
                'title'             => 'Choose column',
                'default'           => '',
                'type'              => 'string',
                'validationPattern' => '^[1-9a-z]+$',
                'validationMessage' => 'Please submit correct column type.'
            ]
        ];
    }



    public function onCreateSample()
    {
        $sample = new Sample;
        $form = post();
        $sample->cate        = isset($form['cate'])?$form['cate']:"";
        $sample->title        = isset($form['title'])?$form['title']:"";
        $sample->menu        = isset($form['menu'])?$form['menu']:"";
        $sample->spec        = isset($form['spec'])?$form['spec']:"";
        $sample->comment        = isset($form['comment'])?$form['comment']:"";
        $sample->cook_data        = isset($form['cook_data'])?$form['cook_data']:"";
        $sample->bill        = isset($form['bill'])?$form['bill']:"";
        $sample->status_show        = isset($form['status_show'])?$form['status_show']:"";
        $sample->ord        = isset($form['ord'])?$form['ord']:"100";
        $sample->is_hidden        = isset($form['is_hidden'])?$form['is_hidden']:0;

        $sample->save();

        //attatchMany 복수일경우, save다음에!! (또는 Deferred binding해야. , $form['_session_key'])
        if(Input::hasFile('sample_images')){
            foreach(Input::file('sample_images') as $file){
                if($file) {
                    $sample->sample_images()->create(['data' => $file]);
                }
            }
        }




    }
    public function onDeleteSample()
    {
        // if owner
        $form = post();

        Sample::destroy($form['id']);
    }

    public function onEditSample()
    {
        $form = post();

        $sample = Sample::find( $form["sample_id"] );
        if($sample){
            if(isset($form['cate']))        $sample->cate       = $form['cate'];
            if(isset($form['title']))       $sample->title      = $form['title'];
            if(isset($form['menu']))        $sample->menu      = $form['menu'];
            if(isset($form['spec']))        $sample->spec       = $form['spec'];
            if(isset($form['comment']))     $sample->comment    = $form['comment'];
            if(isset($form['cook_data']))   $sample->cook_data  = $form['cook_data'];
            if(isset($form['bill']))        $sample->bill       = $form['bill'];
            if(isset($form['status_show'])) $sample->status_show= $form['status_show'];
            if(isset($form['ord']))         $sample->ord        = $form['ord'];
            if(isset($form['is_hidden']))   $sample->is_hidden  = $form['is_hidden'];
            if(Input::hasFile('sample_images')){
                // $sample->sample_images     = Input::file('upload_file'); //$form["upload_file"]; //
                $sample->sample_images()->create(['data' => Input::file('sample_images')]); //attatchMany 복수일경우

            }
            $sample->save();
        }else{
            //
        }
    }


}

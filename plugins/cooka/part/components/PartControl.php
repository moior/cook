<?php namespace Cooka\Part\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Part\Models\Part;
use Cooka\Sample\Models\Sample;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use System\Models\File;
use RainLab\User\Facades\Auth;

class PartControl extends ComponentBase
{
    private $user;
    private $admin;

    public function componentDetails()
    {
        return [
            'name'        => 'Part control list',
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

    public function onRun()
    {

        $this->user = Auth::getUser();
        $this->admin = BackendAuth::getUser();
        if(!$this->admin){
            $this->page['admin'] = false;
            $order = null;
            return; /*어드민 로그인이 아니라면 절대 보이지 않음.*/
        }else{
            $this->page['admin'] = true;
        }

        // 실서버에서 : Creating default object from empty value에러
        $level = error_reporting(0); //$order->name = "[빈값]"; 때문
        // error_reporting($level); // xe 소스 보니 리포팅 해제부분에 이걸 넣는듯한데 왜?

        $team_id = $this->property('team_id');
        if(Request::input('key0') ){
            $parts = Part::where('team_id', $team_id)->orderBy('ord')->orderBy('name');
            $parts = $parts->where('key0', Request::input('key0'));
            if( Request::input('key1')) {
                $parts = $parts->where('key1', Request::input('key1'));
            }
            $parts = $parts->get();
        }else{
            $parts = null;
        }

        $this->page['parts'] = $parts; // $tmpOrder; //bill 에서 값읽을떄 오류

    }

    public function onAddPart()
    {
        $part = new Part();
        $form = post();

        $part->team_id        = isset($form['team_id'])?$form['team_id']:"";
        $part->cate        = isset($form['cate'])?$form['cate']:"";
        //$part->title        = isset($form['title'])?$form['title']:"";
        //$part->name        = isset($form['name'])?$form['name']:"";
        $part->key0          = isset($form['key0'])?$form['key0']:"";
        $part->key1          = isset($form['key1'])?$form['key1']:"";
        $part->key2          = isset($form['key2'])?$form['key2']:"";
        $part->key3          = isset($form['key3'])?$form['key3']:"";
        $part->qtnum          = isset($form['qtnum'])?$form['qtnum']:"";
        $part->value        = isset($form['value'])?str_replace(',', '', $form['value']):"";
        $part->comment        = isset($form['comment'])?$form['comment']:"";
        $part->ord        = !empty($form['ord'])?$form['ord']:"0";

        $part->save();
    }

    public function onEditPart()
    {
        $form = post();
        $part = Part::find( $form["part_id"] );
        if($part){
            //if(isset($form['cate']))        $part->cate       = $form['cate'];
            //if(isset($form['title']))       $part->title      = $form['title'];
            //if(isset($form['name']))        $part->name      = $form['name'];
            if(isset($form['key0']))         $part->key0        = $form['key0'];
            if(isset($form['key1']))         $part->key1        = $form['key1'];
            if(isset($form['key2']))         $part->key2        = $form['key2'];
            if(isset($form['key3']))         $part->key3        = $form['key3'];
            if(isset($form['qtnum']))         $part->qtnum        = $form['qtnum'];
            if(isset($form['value']))        $part->value       = str_replace(',', '', $form['value']);
            if(isset($form['comment']))     $part->comment    = $form['comment'];
            if(isset($form['ord']))         $part->ord        = $form['ord'];

            $part->save();
        }else{
            //
        }
    }
    public function onDelPart()
    {
        // if owner
        $form = post();

        Part::destroy($form['part_id']);
    }

    public function onAddAttachment()
    {
        // if owner
        $form = post();
        $sample = Sample::find( $form["sample_id"] );
        if($sample){


            if(Input::hasFile('sample_images')){
                foreach(Input::file('sample_images') as $file){
                    if($file) {
                        $sample->sample_images()->create(['data' => $file]);
                    }
                }
            }
            $sample->save();
        }else{
            //
        }

    }

    public function onRotateAttachment()
    {
        $form = post();
        $sample = Sample::find($form['sample_id']);
        if($sample) {
            foreach($sample->sample_images as $imagefile){
                if($imagefile->id == $form['attatch_id']){
                    header('Content-type: image/jpeg');
                    $source_path = $_SERVER['DOCUMENT_ROOT']. $imagefile->getPath();
                    $original    = imagecreatefromjpeg($source_path);
                    $rotated = imagerotate($original, 90, 0);
                    //Log::info('KSK 겸1: '.$source_path);
                    imagejpeg($rotated, $source_path);
                    imagedestroy($original);
                    imagedestroy($rotated);

                }
            }
        }else{

        }

        //`File::destroy($form['attatch_id']); //////////// 근데......... 이건 db만 지우지 실제 파일은 안지우는듯함..
    }
    public function onDelAttachment()
    {
        // if owner
        $form = post();
        $sample = Sample::find($form['sample_id']);
        if($sample) {
            foreach($sample->sample_images as $imagefile){
                if($imagefile->id == $form['attatch_id'])
                    $imagefile->delete();
            }
        }else{

        }

        //`File::destroy($form['attatch_id']); //////////// 근데......... 이건 db만 지우지 실제 파일은 안지우는듯함..
    }




}

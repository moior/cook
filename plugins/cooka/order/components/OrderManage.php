<?php namespace Cooka\Order\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use ApplicationException;
use Cook\Classes\FeeCalculator;
use Cooka\Order\Models\Order;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use RainLab\User\Facades\Auth;
use Renatio\DynamicPDF\Classes\PDF;


class OrderManage extends ComponentBase
{
    private $user;
    private $admin;

    public function componentDetails()
    {
        return [
            'name' => 'Notecooker',
            'description' => ''
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'description' => 'choose column to display at order list',
                'title' => 'Choose column',
                'default' => '',
                'type' => 'string',
                'validationPattern' => '^[1-9a-z]+$',
                'validationMessage' => 'Please submit correct column type.'
            ]
        ];
    }


    /* This code will be executed when the page or layout is
    // loaded and the component is attached to it.*/
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
        $orderId = $this->property('id');
        $order = Order::find($orderId);

        if( empty($order->name) ) {
            if( !empty($order->phone) )
                $order->name = substr($order->phone, -4);
            else $order->name = "[빈값]";
        }
        if( empty($order->title)) { $order->title = "[빈값]"; }
        if( empty($order->phone)) { $order->phone = "[빈값]"; }
        if( empty($order->addr)) { $order->addr = "[빈값]"; }
        if( empty($order->email)) { $order->email = "[빈값]"; }
        if( empty($order->comment)) { $order->comment = "[빈값]"; }

        $this->page['client_name'] = $order->name;

        $this->page['order'] = $order; // $tmpOrder; //bill 에서 값읽을떄 오류

        $this->page['file개수'] = 0;

        if($order->upload_file) {
            /*삭제해야*/ $this->page['upload_file'] = $order->upload_file->getPath();
            $this->page['upload_file_origin'] = $order->upload_file->getPath();
            $this->page['upload_file_thumb'] = $order->upload_file->getThumb(100, 100, ['mode' => 'auto']); /*	auto, exact, portrait, landscape, crop. Default: auto*/
            $this->page['file이름'] = $order->upload_file->getFilename();
            $this->page['file타입'] = $order->upload_file->getContentType();
            $this->page['file개수'] += 1;
        }
        foreach($order->upload_files as $image){
            $image_arr[] = $image->getPath();
            $files_name[] = $image->getFilename();
            $files_type[] = $image->getContentType();
            $thumb_arr[] = $image->getThumb(100, 100, ['mode' => 'crop']);
        }
        if(isset($image_arr)){
            $this->page['image_arr'] = $image_arr;
            $this->page['files이름'] = $files_name;
            $this->page['files타입'] = $files_type;
            $this->page['file개수'] += $order->upload_files->count();

        }
        if(isset($thumb_arr)) {
            $this->page['thumb_arr'] = $thumb_arr;
        }
        if (isset($order->cook_data)) {
            $this->page['cook_data'] = json_decode($order->cook_data, true);
        } else $this->page['cook_data'] = null;


    }

    public function onUpdateStatus(){

        $orderId = post('order_id');
        $order = Order::find($orderId);
        $order->status_show = post('status_show');
        $order->save();
    }
    public function onDeleteOrder(){
        $orderId = post('order_id');
        $order = Order::find($orderId);
        $order->delete();
    }

    /*텍스트 클릭시 입력창이 뜨고 바로수정 가능케*/
    public function onUpdateColumnEasy(){
        $post = post();
        if( $post["order_id"] ){
            $orderId = $post["order_id"];
            $order = Order::find($orderId);
            /*$order->update(['name' => $post["column_value"]]);*/
            $column_name = $post["column_name"];
            $order->$column_name = $post["column_value"];
            $order->save();
        }
    }
    public function showPdf()
    {
        $templateCode = 'Invoice::note'; // unique code of the template
        $data = ['name' => 'Kenny']; // optional data used in template

        return PDF::loadTemplate($templateCode, $data)->stream();
    }
    /* 문의페이지에서 파일첨부 버튼 클릭시 */
    public function onAddAttachment()
    {
        // if owner
        $form = post();
        $sample = Order::find( $form["order_id"] );
        if($sample){
            if(Input::hasFile('upload_files')){
                foreach(Input::file('upload_files') as $file){
                    if($file) {
                        $sample->upload_files()->create(['data' => $file]);
                    }
                }
            }
            $sample->save();
        }else{
            //
        }
    }
}

<?php

/**
 * Created by PhpStorm.
 * User: sang
 * Date: 2016-12-25
 * Time: 오후 11:05
 */
class skhelper
{

    //    파일 이름(확장자 제외) 얻기
    function file_name($filename)
    {
        return basename($filename, strrchr($filename, '.'));
    }

    /*파일 확장자를 뽑는 방법들*/
    function file_ext(){

        $filename = "mypage.aaa.asp";
        $filename = "알림장-수학-02.jpg";
        //1 strrchr함수를 사용해서 확장자 구하기
        $ext = substr(strrchr($filename, '.'), 1);
        echo $ext;
        echo "<br>";
        //2 strrpos 함수와 substr함수를 사용해서 확장자 구하기
        $ext = substr($filename, strrpos($filename, '.') + 1);
        echo $ext;
        echo "<br>";
        //3 expload 함수와 end 함수를 사용해서 확장자 구하기
        //$ext = end(explode('.', $filename));
        //echo $ext;
        echo "<br>";
        //4 preg_replace 함수에 정규식을 대입해서 확장자 구하기
        $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $filename);
        echo $ext;
        echo "<br>";
        //5 pathinfo 함수를 사용해서 확장자 구하기
        $fileinfo = pathinfo($filename);
        $ext = $fileinfo['extension'];
        echo $ext;
    }
}
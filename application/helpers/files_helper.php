<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!function_exists('getFile')) {

    function getFile($filename) {
        return $file;
    }

}

if (!function_exists('getFileLink')) {

    function getFileLink($filename) {
        return (UPLOAD_BASE_URL . UPLOAD_BACKET . "/" . $filename);
    }

}
if (!function_exists('getFileRoot')) {

    function getFileRoot($filename) {
        return (UPLOAD_FOLDER . $filename);
    }

}

if (!function_exists('calcFileSize')) {

    function calcFileSize($filesize) {
        return number_format($filesize / (1024 * 1024), 1) . " MB ";
    }

}

if (!function_exists('getFileSize')) {

    function getFileSize($filename) {
        return 0;
        $filelink = getFileRoot($filename);
        $filesize = file_exists($filelink) ? filesize($filelink) : 0;
        return calcFileSize($filesize);
    }

}



if (!function_exists('img_src')) {

    function img_src($filename, $width = 300, $height = 300) {
        $ci = & get_instance();
        $ci->load->model('Documents/cropModel', "crop");
        $file = $ci->crop->crop($filename, $width, $height);
        return UPLOAD_BASE_URL . UPLOAD_BACKET. "/". $file;
    }

}


if (!function_exists('files_upload')) {

    function files_upload($dept = NULL, $folder = NULL, $message = "") {
//        return __METHOD__;
$dept = $dept['Department_ID'];
$folder = $folder['id'];
        $html = "
        <div class='row'>
            <div class='col-sm-12'>

                <div class='row border-r'>

                    <div class='col-xs-12'><label class='control-label'> </label></div>
                    <div class='col-xs-4'>
                    </div>

                    <br>
                </div>
                <script>
                    
                </script>
                
                <table class='table table-striped table-condensed table-sm table-small-font table-bordered'>
                    <thead>
                        <tr>
                            <th><input type='file'  multiple='multiple' onchange=\"uploadFiles(this.id, 'files-'+this.id)\" name='filename[]' id='$dept-$folder' placeholder=''  /></th>
                            <th width='100'>Size</th>
                        </tr>
                    </thead>
                    <tbody id='files-$dept-$folder'>
                    </tbody>
                </table>
                ";
        // $html .= show_documents($dept, $folder);
        $html .= "
            </div>
        </div>";
        return $html;
    }

}

if (!function_exists('show_documents')) {

    function show_documents($folder = NULL, $modal = false, $callback = false) {
        $modal_link = !$modal ? MODAL_LINK : MODAL_AJAX;
        $ci = & get_instance();
        $ci->load->model("FilesModel", "doc");
        $files = $ci->doc->getFiles($folder);
        $html = "
        <table class='table table-striped table-condensed table-sm '>
            <thead>
                <tr>
                    <th style='width:40px;'>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>";
        $num = 0;
        foreach ($files as $key => $value):
            $num++;
            $expname = explode("][", $value['filename']);
            $html .= "<tr class='upload_" . $value['id'] . "'>
                        <td scope='row'> " . $num . " </td>
                        <td> <a href='" . (getFileLink($value['filename'])) . "' target='_blank'>" . $expname[1] . "</a> </td>
                        <td> " . $value['filetype'] . " </td>
                        <td> " . getFileSize($value['filename']) . " </td>
                        <td style='widtd:170px;'>
                            <div class='btn-group btn-group-sm pull-right'>
                                <a class='btn btn-dark-outline waves-effect waves-light' target='_blank' href='" . (getFileLink($value['filename'])) . "'><i class='icon ion-eye'></i></a>
                                <a class='btn btn-success-outline waves-effect waves-light' download='" . $expname[1] . "'href='" . (getFileLink($value['filename'])) . "' ><i class='icon ion-android-download'></i></a>
                                <a class='btn  btn-danger-outline waves-effect waves-light' " . $modal_link . " href='" . base_url("index.php/Documents/deleteDocument/{$value['id']}") . "'><i class='icon icon-trash'></i></a>
                            </div>
                        </td>
                    </tr>";
        endforeach;
        $html .= "</tbody>
        </table>";
        return $html;
    }

}

if (!function_exists('documents_properties')) {

    function documents_properties($module = NULL, $table = NULL, $record_id = 0) {
        $ci = & get_instance();
        $ci->load->model("Documents/documentsModel", "doc");
        $where = array("table_name" => $table, "record_id" => $record_id);
        $files = $ci->doc->getRecordDocuments($where);
        $properties = array("files" => 0, "filesSize" => 0);
        $filesize_sum = 0;
        foreach ($files as $key => $value):
            $filesize_sum += file_exists(getFileRoot($value['filename'])) ? filesize(getFileRoot($value['filename'])) : 0;
        endforeach;
        $properties['files'] = count($files);
        $properties['size'] = calcFileSize($filesize_sum);
        return $properties;
    }

}





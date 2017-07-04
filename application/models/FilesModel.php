<?php

// require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';

// use google\appengine\api\cloud_storage\CloudStorageTools;
    
class FilesModel extends CI_Model {

    private $table, $fields;

    function __construct() {
        parent::__construct();
        $this->table = "files";
    }

    function moveUploaded($temp_name, $new_name) {
        return move_uploaded_file($temp_name, $new_name);
    }

    function upload($index, $file) {
        
    }
    function checkfolder($data){
        $dept = $data['dept'];
        $folder = $data['folder'];
        $name = $data['name'];
        $sql = "SELECT * FROM folder WHERE `dept` = '$dept' AND `folder` = '$folder' AND `name` = '$name'";
        $result = $this->db->query($sql)->num_rows();
        return $result; 
    }

    function multiple($index, $folder , $dept) {
        // $uploads = array();
        // foreach ($_FILES['filename']['name'] as $key => $value) {
        //     $option = ['gs_bucket_name' => UPLOAD_BACKET];
        //     $upload_ur = CloudStorageTools::createUploadUrl('/upload', $option);
        //     $upload_ur;
            
        //     $new_name = (md5(time() + rand(1, 9999))).'][' . $value;
        //     if ($this->moveUploaded($_FILES['filename']['tmp_name'][$key], UPLOAD_PATH . $new_name)) {
        //         $this->db->insert($this->table, array("filename" => $new_name, "folder" => $folder, "filetype" => $_FILES['filename']['type'][$key]));
        //     }

            $new_name = (md5(time() + rand(1, 9999))).'][' . $value;
            if ($this->moveUploaded($_FILES['filename']['tmp_name'][$key], "assets/" . $new_name)) {
                $this->db->insert($this->table, array("filename" => $new_name, "folder" => $folder, "filetype" => $_FILES['filename']['type'][$key]));
            
        }
    }

    function getFolderPath($dept, $folder) {
        $tree = [];
        $tree[] = $this->getFolder($dept, $folder);
        $num = 0;
        do {
            $tree[] = $this->getFolder($dept, $tree[count($tree) - 1]['folder']);
            $num++;
        } while ($tree[count($tree) - 1]['folder']);
        return $tree;
    }

    function createFolder($data) {
        $this->db->insert("folder", $data);
        $this->db->order_by("id", "DESC");
        return $this->db->get("folder")->row_array();
    }

    function getdepts(){
        return $this->db->get("departments")->result_array();
    }
    function getdept($id){
        $this->db->where("Department_ID", $id);
        return $this->db->get("departments")->row_array();
    }

    function getFolder($dept, $id) {
        $this->db->where("dept", $dept);
        $this->db->where("id", $id);
        return $this->db->get("folder")->row_array();
    }

    function getFolders($dept, $folder) {
        $sql = "SELECT * FROM folder WHERE `dept` = '$dept' AND `folder` = '$folder'";
        $result = $this->db->query($sql)->result_array();
        return $result; 
    }

    function getFiles($folder) {
        $this->db->where("folder", $folder);
        return $this->db->get($this->table)->result_array();
    }

}

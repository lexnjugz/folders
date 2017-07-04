<?php
use Google\Cloud\Storage\StorageClient;

class FilesControllers extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("FilesModel", "files");
    }

    function index() {
        $data['depts'] = $this->files->getDepts();
        $this->load->view("DeptView", $data);
    }

    function dept($dept, $folder = 0, $message = "")
    {
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // die;
        $data['message'] = $message;
        $data['dept'] = $this->files->getdept($dept);
        $data['folders'] = $this->files->getFolders($dept, $folder);
        $data['folder'] = $this->files->getFolder($dept, $folder);
        $data['files'] = $this->files->getFiles($folder);
        $data['folder_path'] = $this->files->getFolderPath($dept, $folder);
        $data['folder_path'] = array_reverse($data['folder_path']);
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // // die;
        $this->load->view("FilesView", $data);
    }

    function post() {
        $this->files->multiple("filename", $this->input->post('folder'), $this->input->post('dept'));
        redirect("FilesControllers/dept/".$this->input->post('dept') ."/". $this->input->post("folder"));
    }

    function folderPost() {
        $data['folder'] = $this->input->post("folder");
        $data['dept'] = $this->input->post("dept");
        $data['name'] = $this->input->post("name");
        $check = $this->files->checkfolder($data);
        if ($check > 0 ){
            $message = "Folder Exists";
            // $this->dept($this->input->post('dept'),$this->input->post("folder"), $message);
            redirect("FilesControllers/dept/".$this->input->post('dept') ."/". $this->input->post("folder"),$message);
        }else{
        $folder = $this->files->createFolder($data);
        $message = "Folder Created Successfully";
        redirect("FilesControllers/dept/".$this->input->post('dept') ."/". $this->input->post("folder"),$message);
        }
    }

}

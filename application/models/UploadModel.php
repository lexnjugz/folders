<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UploadModel extends CI_Model {

    private $tableName, $tableFields;

    function __construct() {

        $this->tableFields = array('filetype' => NULL, 'filename' => NULL, 'newname' => NULL, 'title' => NULL, 'caption' => NULL, 'uploaded' => time(), 'created' => time(), 'accessed' => time(), 'modified' => time(), 'fileinfo' => time(), 'status' => time());
        $this->tableName = "upload";
        parent::__construct();
    }

    function upload($config, $index) {
        $newFileName = $_FILES[$index]['name'];
        $fileArray = explode(".", $newFileName);
        $fileExt = $fileArray[count($fileArray) - 1];
        $filename = md5(time() . rand(1, 10000)) . "." . $fileExt;
        $config['file_name'] = $filename;
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($index)) {
            unset($config);
            return false;
        } else {
            $data = $this->upload->data();
            $this->load->model("cropModel");
            $data['file_name'] = $this->cropModel->toJpeg(UPLOAD_FOLDER, $data['file_name']);
            $this->add($data);
            unset($config);
            return $data['file_name'];
        }
    }

    function uploadImage($index) {
        $config['upload_path'] = UPLOAD_PATH;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '1000000';
        $config['max_width'] = '5000';
        $config['max_height'] = '5000';

        $filename = $this->upload($config, $index);
        if ($filename) {
            $this->load->model("cropModel");
            $this->cropModel->crop($filename, 300, 300);
            $this->cropModel->crop($filename, 500, 300);
            
        }
        return $filename;
    }

    function add($data) {
        $this->tableFields['filetype'] = $data['file_type'];
        $this->tableFields['filename'] = $data['file_name'];
        $this->tableFields['newname'] = $data['file_name'];
        $this->tableFields['title'] = $data['client_name'];
        $this->tableFields['caption'] = $data['client_name'];
        $this->db->insert($this->tableName, $this->tableFields);
    }

}

class cropModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    function crop($file, $width, $height) {
        return $this->get_aspect_image($file, array($width, $height));
    }

    function get_aspect_image($filename, $dimensions = array('widht' => 1, 'height' => 1)) {
        $file_name = $filename;

        if (!file_exists(UPLOAD_FOLDER . $filename)) {
            return false;
        }
        if (!$file_name) {
            return false;
        }
        if (count($dimensions) != 2) {
            return false;
        }
        if (is_int($dimensions[0]) and is_int($dimensions[1])) {
            //if(is_int($dimensions[0]) or )
            $new_name = $dimensions[0] . 'x' . $dimensions[1] . '-' . $filename;
            if (file_exists(UPLOAD_FOLDER . $new_name)) {
                return base_url() . '/' . UPLOAD_FOLDER . $new_name;
            }
            if (!file_exists(UPLOAD_FOLDER . $filename)) {
                return false;
            }
            $width = $dimensions[0];
            $height = $dimensions[1];
            $this->crop_imge($filename, $new_name, $width, $height);
            //echo  UPLOAD_FOLDER.$new_name;
            return base_url() . '/' . UPLOAD_FOLDER . $new_name;
        } else {
            $new_name = ($dimensions[0] ? $dimensions[0] : 'auto') . 'x' . ($dimensions[1] ? $dimensions[1] : 'auto') . '-' . $filename;
            //echo $new_name;
            if (file_exists(UPLOAD_FOLDER . $new_name)) {
                return base_url() . '/' . UPLOAD_FOLDER . $new_name;
            }
            if (!file_exists(UPLOAD_FOLDER . $filename)) {
                return false;
            }
            $file_name = UPLOAD_FOLDER . $filename;


            list($w, $h) = getimagesize($file_name);
            $width = $dimensions[0];
            $height = $dimensions[1];

            if (!is_int($width) and is_int($height)) {
                $width = ($w * $height) / $h;
            } else if (is_int($width) and ! is_int($height)) {
                $height = ($h * $width) / $w;
            } else {
                //return false;
            }

            $this->crop_imge($filename, $new_name, $width, $height);
            //echo  UPLOAD_FOLDER.$new_name;
            return base_url() . '/' . UPLOAD_FOLDER . $new_name;
        }
    }

    function crop_imge($filename, $new_name, $width, $height) {
        $pass_width = $width;
        $pass_height = $height;

        $filename = UPLOAD_FOLDER . $filename;
        $save_name = UPLOAD_FOLDER . $new_name;

        $image = imagecreatefromjpeg($filename);

        $thumb_width = $width;
        $thumb_height = $height;

        $width = imagesx($image);
        $height = imagesy($image);

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        // Resize and crop
        imagecopyresampled($thumb, $image, 0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                0, 0, $new_width, $new_height, $width, $height);
        imagejpeg($thumb, $save_name, 80);
        imagedestroy($thumb);

        $image = UPLOAD_FOLDER . $new_name;
        list($w, $h) = getimagesize($image);
        $nh = $pass_height;
        $nw = $pass_width;
        //$nw = 1300;
        //$nh = ($nw * $h)/$w;
        $type = $this->alt_exif_imagetype($image);
        $area = $w * $h;
        $n_area = $nh * $nh;
        $src = imagecreatefromjpeg($image);

        $thumb = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        $save = UPLOAD_FOLDER . $new_name;
        imagejpeg($thumb, $save, 100);
    }

    function alt_exif_imagetype($file) {
        
        $len = strlen($file);
        $ext = substr($file, ($len - 4), $len);
        if ($ext == '.gif') {
            return 1;
        }
        if ($ext == '.jpg') {
            return 2;
        }
        if ($ext == '.png') {
            return 3;
        }
        if ($ext == 'jpeg') {
            return 2;
        }
    }

    function get_extension($filename) {
        $filename = substr($filename, strlen($filename) - 5, 5);
        $filename = strstr($filename, '.');
        return $filename;
    }

    function renameImage($image) {
        $image = strtolower($image);
        $image = str_replace('.jpeg', '.jpg', $image);
        $image = str_replace('.png', '.jpg', $image);
        $image = str_replace('.gif', '.jpg', $image);
        return $image;
    }

    function png2jpg($dir, $file) {
        $filePath = $dir . $file;
        $image = imagecreatefrompng($filePath);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file 
        $file = $this->renameImage($file);
        $filePath = $dir . $file;
        imagejpeg($bg, $filePath, $quality);
        imagedestroy($bg);
        return $file;
    }

    function gif2jpg($dir, $file) {
        $filePath = $dir . $file;
        $image = imagecreatefromgif($filePath);
        $file = $this->renameImage($file);
        $filePath = $dir . $file;
        imagejpeg($image, $filePath, 100);
        file_put_contents('error.txt', $filePath);
        imagedestroy($image);
        return $file;
    }

    function jpeg2jpg($dir, $file) {
        $filepath = $dir . $file;
        $image = imagecreatefromjpeg($filepath);

        unlink($filepath);
        $file = $this->renameImage($file);
        $filePath = $dir . $file;
        imagejpeg($image, $filePath, 100);
        imagedestroy($image);
        return $file;
    }

    function toJpeg($dir, $file) {
        $newName = $file;
        $filePath = $dir . $file;
        $type = $this->alt_exif_imagetype($filePath);
        switch ($type) {
            case 1 : {
                    $newName = $this->gif2jpg($dir, $file);
                    break;
                }
            case 2 : {
                    $newName = $this->jpeg2jpg($dir, $file);
                    break;
                }
            case 3 : {
                    $newName = $this->png2jpg($dir, $file);
                    break;
                }
            default : {
                    $newName = $file;
                }
        }
        return $newName;
    }

}

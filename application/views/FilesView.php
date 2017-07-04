

<?= form_open("FilesControllers/folderPost") ?>
<input type="text" class="form-control" name="dept" value="<?= isset($dept['Department_ID']) ? $dept['Department_ID'] : 0 ?>" />  <br>
<input type="text" class="form-control" name="folder" value="<?= isset($folder['id']) ? $folder['id'] : 0 ?>" />  <br>
<input type="text" class="form-control" name="name" value="" required/>  <br>
<p style="color: #ff0000">
<?php 
if ($message){
    echo $message;
}
?>
</p>

<input type="submit" value="Create Folder"/>
<?= form_close() ?>

<table>
    <tr>
        <?php
        echo "<a href='" . base_url("FilesControllers/dept/{$dept['Department_ID']}") . "'> ". $dept['Department_Name'] . "</a> ";

        foreach ($folder_path as $key => $value) {
            if(!$value){ continue;}
            echo "/";
            if ($value['id'] != $folder['id']) {
                echo " <a href='" . base_url("/FilesControllers/dept/{$dept['Department_ID']}/{$value['id']}") . "'>" . $value['name'] . "</a>";
            } else {
                echo " " . $value['name'];
            }
        }
        ?>


    </tr>

<tr>
        <td colspan="3">Department </td>
    </tr>
    
        <tr>
            <td><?= $dept['Department_ID'] ?></td>
            <td><a href="<?= base_url("FilesControllers/dept/{$dept['Department_ID']}"); ?>"><?= $dept['Department_Name'] ?></a></td>
        </tr>

    <tr>
        <td colspan="3">All Folders </td>
    </tr>
    <?php foreach ($folders as $key => $value): ?>
        <tr>
            <td><?= $value['id'] ?></td>
            <td><a href="<?= base_url("FilesControllers/dept/{$dept['Department_ID']}/{$value['id']}"); ?>"><?= $value['name'] ?></a></td>
        </tr>
    <?php endforeach; ?>
</table>




<hr>
<?= form_open_multipart("FilesControllers/post") ?>
<!--<input type="file" name="filename[]" multiple="" />  <br>-->
<input type="text" name="dept" value="<?= isset($dept['Department_ID']) ? $dept['Department_ID'] : 0 ?>" /> <br>
<input type="text" name="folder" value="<?= isset($folder['id']) ? $folder['id'] : 0 ?>" />  <br>
<?= files_upload($dept, $folder); ?>
<input type="submit" value="Upload FIles"/>
<?= form_close() ?>
<table>
    <tr>
        <td colspan="3">All Files in <?= $folder['name'] ?></td>
    </tr>
    <tr>
    <?= show_documents($folder['id']); ?>
    </tr>
    <?php //foreach ($files as $key => $value): ?>
        <!--<tr>
            <td><?= $value['id'] ?></td>
            <td><?php $expname = explode("][", $value['filename']); echo $expname[1]; ?></td>
            <td><?= $value['filetype'] ?></td>

        </tr>-->
    <?php //endforeach; ?>

</table>
<script>
function uploadFiles(inputId, outputId) {
        var inp = document.getElementById(inputId);
        var string = '';
        var name;
        var size;
        for (var i = 0; i < inp.files.length; i++) {
            name = inp.files.item(i).name;
            size = (inp.files.item(i).size / (1024 * 1024)).toFixed(2);
            if (size > 10) {
                alert("File is too large. Only 10MB size for one file is allowed");
                return;
            }
            filetype = inp.files.item(i).type;
            string +=
                    "<tr>" +
                    "<td>" + name + "</td>" +
                    "<td>" + size + " MB</td>" +
                    "</tr>";
        }
        var html = string;
        document.getElementById(outputId).innerHTML = html;
        /*
         CONSIDER IMPLIMENTING THIS
         
         var validatedFiles = [];
         $("#fileToUpload").on("change", function (event) {
         var files = event.originalEvent.target.files;
         files.forEach(function (file) {
         if (file.name.matches(/something.txt/)) {
         validatedFiles.push(file); // Simplest case
         } else { 
         
         }
         });
         });
         */
    }
</script>
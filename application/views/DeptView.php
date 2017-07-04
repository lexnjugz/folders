<table>
<tr>
        <td colspan="3">All Departments </td>
    </tr>
    <?php foreach ($depts as $key => $value): ?>
        <tr>
            <td><?= $value['Department_ID'] ?></td>
            <td><a href="<?= base_url("FilesControllers/dept/{$value['Department_ID']}"); ?>"><?= $value['Department_Name'] ?></a></td>
        </tr>
    <?php endforeach; ?>

</table>
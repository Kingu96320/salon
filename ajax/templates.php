<?php
include "../includes/db_include.php";
$branch_id = $_SESSION["branch_id"];

// get template type

if(isset($_POST['action']) && $_POST['action'] == 'getTemplate'){
    $category = $_POST['category'];
    $data = query_by_id("SELECT DISTINCT(group_name) FROM templates WHERE status='1' and message_type='".$category."'",[],$conn);
    if($data){
        foreach($data as $data){
            ?>
            <option value="<?= $data['group_name'] ?>"><?= $data['group_name'] ?></option>
            <?php
        }
    }
}

// get message templates

if(isset($_POST['action']) && $_POST['action'] == 'getMessages'){
    $group = $_POST['group_name'];
    $template_category = $_POST['template_category'];
    $data = query_by_id("SELECT message, template_id, send_as FROM templates WHERE status='1' and group_name='".$group."' and message_type='".$template_category."'",[],$conn);
    if($data){
        $count = 1;
        foreach($data as $data){
            ?>
            <div class="col-md-6">
                <div class="tmps" data-templateid="<?= $data['template_id'] ?>" data-message="<?= $data['message'] ?>" data-sendas="<?= $data['send_as'] ?>" onclick="useTemplate('<?= $data['template_id'] ?>', '<?= $data['send_as'] ?>', $(this))"><?= $data['message'] ?></div>
            </div>
            <?php
            if($count%2 == 0){
                ?>
                <div class="clearfix"></div>
                <?php
            }
        }
    }
}

?>
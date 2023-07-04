<?php
if(!empty($data)) :
//--------------------------------------------------------

?>

<thead>
  <tr>

      <th width="1%"></th>
      <?php
      if(isset($data['listSetting']['delete_check']) && $data['listSetting']['delete_check'] === true) : 
      ?>
      <th width="5%">
      <input type="checkbox" name="" value="" class="master" />
      </th>
      <?php
      endif;
      ?>

    <?php
    if(!empty($data['listSetting']['list_items'])) :
      foreach($data['listSetting']['list_items'] as $item_key => $item) :

        $th_class = '';
        if($item['type'] != 'title') {
          $th_class = ' class="text-center"';
        }
        $th_w = '';
        if(!empty($item['width'])) { 
          $th_w = $item['width'];
        }
    ?>
      <th width="<?=$th_w?>"<?=$th_class?>><?=$item['label']?></th>
    <?php
      endforeach;
    endif;
    ?>

    <?php
    if(isset($data['listSetting']['sort_change']) && $data['listSetting']['sort_change'] === true) :
      if(!empty($data['sort_order_key']) && $data['sort_order_key'] == 'sort_num') :
    ?>
      <th width="10%" class="text-center">表示順</th>
    <?php
      endif;
    endif;
    ?>

    <?php
    if(isset($data['listSetting']['output_change']) && $data['listSetting']['output_change'] === true) : 
    ?>
      <th width="10%" class="text-center">
      <?php 
      if(!empty($data['listSetting']['output_flag_str'])) {
        echo $data['listSetting']['output_flag_str'];
      } else {
        echo '公開';
      }
      ?>
      </th>
    <?php
    endif;
    ?>

      <th width="8%" class="text-center">操作</th>
  </tr>
</thead>

<?php
//--------------------------------------------------------
endif;
?>
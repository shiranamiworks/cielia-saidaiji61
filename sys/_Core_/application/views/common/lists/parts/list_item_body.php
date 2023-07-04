<?php
if(!empty($data)) :
//--------------------------------------------------------

?>

<!-- row -->
<tr<?= $_data['tr_class'] ?> id="dataTable<?= $data['value']['id'] ?>">
  
  <td></td>
  <?php
  //権限チェック(削除権限があるか、もしくは自分自身が作成した記事で公開されていないかどうかのチェック）
  if($data["delete"] && !empty($data['listSetting']['delete_check'])) :
  ?>
  <td>
  <input type="checkbox" name="del_posts[]" class="delete_check" value="<?=$data['value']["id"]?>" />
  </td>
  <?php endif; ?>

  <?php
  if(!empty($data['listSetting']['list_items'])) :
      foreach($data['listSetting']['list_items'] as $item_key => $item) :

        $attr = '';
        if(!empty($item['attr'])) {
          foreach($item['attr'] as $key => $attr_val) {
            $attr .= ' ' . $key . '="' . $attr_val . '"';
          }
        }

  ?>

    <?php
    //--------------------------------------------------------
    // TITLE
    //--------------------------------------------------------
    if($item['type'] == 'title') :
    ?>
    <td class="title">
        <?php

        $value = !empty($data['value'][$item_key]) ? $data['value'][$item_key] : '';
        if(!empty($item['exec_func'])) {
          $value = call_user_func($item['exec_func'] , $value);
        }

        $account_info = '';
        $title_link_attr = '';
        if(isset($data['listSetting']['display_account']) && $data['listSetting']['display_account'] === true) {
          $account_info = "作成者　 : ".$data['account_name']."（".date("Y-m-d H:i" , strtotime($data['value']["created"]))."）";
          if(!empty($data["last_edit_username"])) {
              $account_info .= "<br>";
              $account_info .= "最終更新 : ".$data['last_edit_username'];
              if($data["last_edit_username"] != " － ") {
                  $account_info .= "（".date("Y-m-d H:i" , strtotime($data['value']["modified"]))."）";
              }
          }
          $title_link_attr = 'data-html="true" data-toggle="popover" data-trigger="hover" title="作成アカウント" data-placement="right" data-content="'."<span style='font-size:11px'>".$account_info.'</span>"';
        }
        ?>
        <a href="<?php echo $data['edit_link']; ?>" class="btn-edit list-title" <?=$title_link_attr?>><?= $value ?></a>
        
    </td>
    
    <?php
    //--------------------------------------------------------
    // 掲載期間
    //--------------------------------------------------------
    elseif($item['type'] == 'distribution') :
    ?>

    <td<?=$attr?>><small>
    <?php
    echo fn_dateFormat($data['value']['distribution_start_date'],"Y-m-d H:i")." 〜";
    if(!empty($data['value']['distribution_end_date']) && $data['value']['distribution_end_date'] != '0000-00-00 00:00:00') {
        echo "<br>".fn_dateFormat($data['value']['distribution_end_date'],"Y-m-d H:i")." 　";
    }
    ?>

    <?php
    $status_info = '';
    $status_info_class = '';
    $status_info_2 = '';
    $status_info_2_class = '';

    if(strtotime($data['value']["distribution_start_date"]) >= strtotime("now")) {
        $status_info = "掲載開始待ち";
        $status_info_class = 'success';
    }elseif($data['value']['distribution_end_date'] != '0000-00-00 00:00:00' && strtotime($data['value']["distribution_start_date"]) <= strtotime("now") && strtotime("now") >= strtotime($data['value']["distribution_end_date"])) {
        $status_info = "掲載期間終了";
        $status_info_class = 'default';
    }

    if(!empty($status_info)) $status_info = "<span class=\"label label-".$status_info_class."\"> ".$status_info." </span>";

    if(!$data['value']["output_flag"]) {
        if(!empty($data['value']["status"]) && $data['value']["status"] == "wait") {
            $status_info_2 = "承認待ち";
            $status_info_2_class = 'primary';
        } else {
            $status_info_2 = "非公開状態";
            $status_info_2_class = 'warning';
        }
        if(!empty($status_info_2)) {
          $status_info_2 = "<span class=\"label label-".$status_info_2_class."\"> ".$status_info_2." </span>";
          $status_info = '';
        }
    }

    if(!empty($status_info)) {
      echo "<br>";
      echo "<strong>".$status_info;
    }
    if(!empty($status_info_2)) {
        echo "<br>";
        echo $status_info_2;
    }
    if(!empty($status_info)) {
      echo "</strong>";
    }
    
    if(!empty($status_info) || !empty($status_info_2)) {
      echo "<script>$(function(){ $('#dataTable".$data['value']['id']."').addClass('hide-data').find('.title a').css('color','#666'); });</script>";
    }
    ?>

    </small></td>


    <?php
    //--------------------------------------------------------
    // カテゴリ
    //--------------------------------------------------------
    elseif($item['type'] == 'category') :
    ?>
    <td<?=$attr?>>
    <?php

      $value = !empty($data['value'][$item_key]) ? $data['value'][$item_key] : '';
      $value = (isset($item['data'][$value]) ? $item['data'][$value] : '');
      if(!empty($item['exec_func'])) {
        $value = call_user_func($item['exec_func'] , $value);
      }
      echo $value;
    ?>
    </td>


    <?php
    //--------------------------------------------------------
    // セレクトボックスタイプのデータ
    //--------------------------------------------------------
    elseif($item['type'] == 'select') :
    ?>
    <td<?=$attr?>>
    <?php

      $value = !empty($data['value'][$item_key]) ? $data['value'][$item_key] : '';
      $value = (isset($item['data'][$value]) ? $item['data'][$value] : '');
      if(!empty($item['exec_func'])) {
        $value = call_user_func($item['exec_func'] , $value);
      }
      echo $value;
    ?>
    </td>


    <?php
    //--------------------------------------------------------
    // チェックボックスタイプのデータ
    //--------------------------------------------------------
    elseif($item['type'] == 'checkbox') :
    ?>
    <td<?=$attr?>>
    <?php

      $value = !empty($data['value'][$item_key]) ? $data['value'][$item_key] : '';
      $value = fn_convertChkboxValue($value);
      if(is_array($value)) {
        $_v = array_map(function($c) use($item) { 
          if(isset($item['data'][$c])) {
            return $item['data'][$c];
          } 
        } , $value);
        $value = implode("<br>" , $_v);
      }else{
        $value = (isset($item['data'][$value]) ? $item['data'][$value] : '');
      }
      if(!empty($item['exec_func'])) {
        $value = call_user_func($item['exec_func'] , $value);
      }
      echo $value;
    ?>
    </td>


    <?php
    //--------------------------------------------------------
    // テキスト
    //--------------------------------------------------------
    elseif($item['type'] == 'text') :
    ?>
    <td<?=$attr?>>
    <?php
    
      $value = !empty($data['value'][$item_key]) ? $data['value'][$item_key] : '';
      if(!empty($item['exec_func'])) {
        $value = call_user_func($item['exec_func'] , $value);
      }
      echo $value;
    ?>
    </td>


    <?php
    //--------------------------------------------------------
    // 画像
    //--------------------------------------------------------
    elseif($item['type'] == 'image') :
    ?>
    <td<?=$attr?>>
    <?php

      $value = !empty($data['value'][$item_key]) ? $data['value'][$item_key] : '';
      if(!empty($item['exec_func'])) {
        $value = call_user_func($item['exec_func'] , $value);
      }
    ?>
    <img src="<?php echo $value; ?>" width="<?=(!empty($item['image_w']) ? $item['image_w'] : '150')?>">
    </td>

    <?php
    endif;

    endforeach;
    ?>

    <?php
    //--------------------------------------------------------
    // 順序入れ替え
    //--------------------------------------------------------
    if(isset($data['listSetting']['sort_change']) && $data['listSetting']['sort_change'] === true) :
      //表示順序入れ替え（sort_numソートNoが基準値の場合 入れ替えが有効　ただし検索時は無効）
      if(!empty($data['sort_order_key']) && $data['sort_order_key'] == 'sort_num') :
    ?>

    <td class="text-center">
    <p class="sort-link">
    <?php
      if(empty($data['search_result_text']) && $data['value']['sort_num'] >= 0) :
    ?>
    <?php
        //dort down
        if(($data['sort_order_type'] == 'ASC' && $data['value']['sort_num'] < $data['max_sort_num']) || 
           ($data['sort_order_type'] == 'DESC' && $data['value']['sort_num'] > $data['min_sort_num'])) :
    ?>
    <a href="#" data-id="<?=$data['value']['id']?>" data-sortchange-type="down"><i class="icon-download"></i></a>
    <?php
        endif;

        if(($data['sort_order_type'] == 'ASC' && $data['value']['sort_num'] > $data['min_sort_num']) ||
           ($data['sort_order_type'] == 'DESC' && $data['value']['sort_num'] < $data['max_sort_num'])):
    ?>
    <a href="#" data-id="<?=$data['value']['id']?>" data-sortchange-type="up"><i class="icon-upload"></i></a> 
    </p>
    </td>
    <?php
        endif;
    ?>
    <?php
      endif;

     endif;

    endif;
    ?>

    <?php
    //--------------------------------------------------------
    // 公開切り替え
    //--------------------------------------------------------
    if(isset($data['listSetting']['output_change']) && $data['listSetting']['output_change'] === true) :
    ?>
    <td class="text-center">
    <?php echo $_data['publish_menu']; ?>
    </td>
    <?php
    endif;

    ?>

    <td class="edit-menu text-center">
        <a href="<?php echo $data['edit_link']; ?>" class="btn-edit" style="font-size:20px"><i class="icon-pencil" data-toggle="tooltip" title="編集"></i></a>
        <?php
        if($data['delete']) :
        ?>
        　<a href="#" class="btn-delete tooltip-item" data-name="<?= $data['value']['title'] ?>" data-id="<?= $data['value']['id'] ?>" style="font-size:20px"><i class="icon-trash" data-toggle="tooltip" title="削除"></i></a>
        <?php
        endif;
        ?>
        
    </td>

  <?php
  endif;
  ?>
</tr>

<?php
//--------------------------------------------------------
endif;
?>

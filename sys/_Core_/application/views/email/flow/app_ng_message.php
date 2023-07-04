承認待ちとなっていた<?= $KIJI_TYPE ?>が承認されず差戻しされましたので
お知らせします。


作成（編集）者：<?=$editor_name?>　
タイトル：<?=$title?> （<?= $ADMIN_PAGE_NAME ?>）　　
作成（編集）日時：<?=$modified?>　
記事確認担当者：<?=$admin_user_name?>　
差戻し理由：　
<?=$message?>


--------------------------------------------------
送信日時：<?=fn_get_date()?>　
ホスト情報：<?=$_SERVER["SERVER_NAME"]?>　

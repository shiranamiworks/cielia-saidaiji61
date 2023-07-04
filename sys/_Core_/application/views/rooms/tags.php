<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<dl class="tags-container">
    <dt>間取り</dt>
    <dd>
        <ul class="tag-list">
            <?php if (isset($Tags['ldk']) == true && count($Tags['ldk']) > 0) {
                foreach ($Tags['ldk'] as $ldkTag) { ?>
                    <li class="tag-item" data-tag="ldk-<?php echo $ldkTag; ?>"><?php echo $ldkTag; ?></li>
                <?php } } ?>
        </ul>
    </dd>
    <dt>面積</dt>
    <dd>
        <ul class="tag-list">
            <?php if (isset($Tags['breadth']) == true && count($Tags['breadth']) > 0) {
                foreach ($Tags['breadth'] as $breadthTag) { ?>
                    <li class="tag-item" data-tag="breadth-<?php echo $breadthTag; ?>"><?php echo $breadthTag; ?>㎡台</li>
                <?php } } ?>
        </ul>
    </dd>
    <dt>価格帯</dt>
    <dd>
        <ul class="tag-list">
            <?php if (isset($Tags['price']) == true && count($Tags['price']) > 0) {
                foreach ($Tags['price'] as $priceTag) { ?>
                    <li class="tag-item" data-tag="price-<?php echo $priceTag; ?>"><?php echo number_format($priceTag); ?>万円台</li>
                <?php } } ?>
        </ul>
    </dd>
    <dt>方位</dt>
    <dd>
        <ul class="tag-list">
            <?php if (isset($Tags['orientation']) == true && count($Tags['orientation']) > 0) {
                foreach ($Tags['orientation'] as $orienTag) { ?>
                    <li class="tag-item" data-tag="orientation-<?php echo $orienTag; ?>"><?php echo $orienTag; ?></li>
                <?php } } ?>
        </ul>
    </dd>
    <dt>その他</dt>
    <dd>
        <ul class="tag-list">
            <?php if (isset($Tags['free_tag']) == true && count($Tags['free_tag']) > 0) {
                foreach ($Tags['free_tag'] as $freeTag) { ?>
                    <li class="tag-item" data-tag="free_tag-<?php echo $freeTag; ?>"><?php echo $freeTag; ?></li>
                <?php } } ?>
        </ul>
    </dd>
</dl>
<p class="btn-drillDown">絞り込む</p>
<p class="caption">初期の状態へ戻す場合は、条件を外してから、再度、絞り込むボタンをクリックしてください。</p>
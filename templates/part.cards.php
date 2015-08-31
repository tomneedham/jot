<div class="jot-wrapper">
	<div id="jot-items" style="" data-count=<?php p(count($_['items'])); ?> class="jot-items">
		<!-- Start items-->
		<div class="jot-item add-jot-item" style="position: absolute; left: 0px; top: 0px;"><div class="jot-item-content" style="padding-bottom: 5px;height: 80px;"><span class="icon-add" style="
    height: 25px;
    /* width: 49px; */
    display: block;
    margin: auto auto;
    opacity: 0.2;
    background-size: 25px;
    margin-top: 13px;
"></span></div></div>
		<?php foreach($_['jots'] as $jot) { ?>
		<div class="jot-item" data-id="<?php p($jot->getId()); ?>" data-type="text">
			<div class="jot-item-content" style="padding-bottom: 5px;">
				<a class="item-state-icon icon-close"></a>
				<textarea placeholder="Title" rows=1 class="jot-input jot-title"><?php p($jot->getTitle()); ?></textarea>
				<textarea class="jot-input jot-content" placeholder="..." rows=1 style=""><?php p($jot->getContent()); ?></textarea>
			</div>
		</div>
		<?php } ?>
		<!-- End items -->
	</div>
</div>

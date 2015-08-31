<div class="jot-wrapper">
	<div id="jot-items" style="" data-count=<?php p(count($_['items'])); ?> class="jot-items">
		<!-- Start items-->
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

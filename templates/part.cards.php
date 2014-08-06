<div class="jot-wrapper">
	<div id="jot-items" style=""class="jot-items">

		<!-- Start items-->
		<?php if(!empty($_['items'])) { foreach($_['items'] as $item) { ?>
		<div class="jot-item" data-id="<?php p($item->getId()); ?>" data-type="text">
			<div class="jot-item-content" style="padding-bottom: 5px;">
				<a class="item-state-icon icon-close"></a>
				<textarea placeholder="Title" rows=1 class="jot-input jot-title"><?php p($item->getTitle()); ?></textarea>
				<textarea class="jot-input jot-content" placeholder="An interesting note..." rows=1 style=""><?php p($item->getContent()); ?></textarea>
			</div>
		</div>
		<?php }} else { ?>
		<div class="jot-item" data-type="text">
			<div class="jot-item-content" style="padding-bottom: 5px;">
				<a class="item-state-icon icon-close"></a>
				<textarea placeholder="Title" rows=1 class="jot-input jot-title">Welcome to Jot!</textarea>
				<textarea class="jot-input jot-content" placeholder="An interesting note..." rows=1 style="">Click the title, or on this, to start editing. Or click '+' to add a new note :)&#010;&#010;Version: <?php p($_['appversion']); ?></textarea>
			</div>
		</div>
		<?php } ?>

		<!-- End items --> 
		
	</div>
</div>